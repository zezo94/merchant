<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

set_time_limit(0);
ini_set('memory_limit', '1024M');

/*
|--------------------------------------------------------------------------
| الإعدادات
|--------------------------------------------------------------------------
*/

$inputDir             = __DIR__ . '/storage/app/import';
$outputSqlFile        = __DIR__ . '/storage/app/import/merchants_all_import.sql';
$outputDuplicatesFile = __DIR__ . '/storage/app/import/duplicates_report.csv';

/*
|--------------------------------------------------------------------------
| دوال مساعدة
|--------------------------------------------------------------------------
*/

function sqlValue($value): string
{
    if ($value === null) {
        return 'NULL';
    }

    $value = trim((string) $value);

    if ($value === '' || strtoupper($value) === 'NULL') {
        return 'NULL';
    }

    $value = str_replace("\\", "\\\\", $value);
    $value = str_replace("'", "\\'", $value);

    return "'" . $value . "'";
}

function cleanText($value): ?string
{
    if ($value === null) {
        return null;
    }

    $value = trim((string) $value);

    if ($value === '' || strtoupper($value) === 'NULL') {
        return null;
    }

    return $value;
}

function normalizeDate($value): ?string
{
    if ($value === null) {
        return null;
    }

    if ($value instanceof DateTimeInterface) {
        return $value->format('Y-m-d');
    }

    $value = trim((string) $value);

    if ($value === '' || strtoupper($value) === 'NULL') {
        return null;
    }

    if (is_numeric($value)) {
        try {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    $formats = [
        'Y-m-d',
        'd/m/Y',
        'm/d/Y',
        'd-m-Y',
        'm-d-Y',
        'Y/m/d',
        'd.m.Y',
        'Y-m-d H:i:s',
        'd/m/Y H:i:s',
        'm/d/Y H:i:s',
    ];

    foreach ($formats as $format) {
        $dt = DateTime::createFromFormat($format, $value);
        if ($dt) {
            return $dt->format('Y-m-d');
        }
    }

    try {
        return (new DateTime($value))->format('Y-m-d');
    } catch (\Throwable $e) {
        return null;
    }
}

function splitList(?string $value): array
{
    if ($value === null) {
        return [];
    }

    $value = trim($value);

    if ($value === '' || strtoupper($value) === 'NULL') {
        return [];
    }

    $parts = preg_split('/[\n\r,;|]+/u', $value);

    $clean = [];
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part !== '' && strtoupper($part) !== 'NULL') {
            $clean[] = $part;
        }
    }

    return array_values(array_unique($clean));
}

function normalizeHeader(string $header): string
{
    $header = trim($header);
    $header = preg_replace('/\s+/u', '', $header);
    return mb_strtolower($header);
}

function mapHeaders(array $headerRow): array
{
    $map = [];

    foreach ($headerRow as $index => $header) {
        $normalized = normalizeHeader((string) $header);

        if (!isset($map[$normalized])) {
            $map[$normalized] = [];
        }

        $map[$normalized][] = $index;
    }

    return $map;
}

function getCellByHeader(array $row, array $headerMap, string $headerName, int $occurrence = 0)
{
    $key = normalizeHeader($headerName);

    if (!isset($headerMap[$key][$occurrence])) {
        return null;
    }

    $index = $headerMap[$key][$occurrence];

    return $row[$index] ?? null;
}

function extractDescriptions(array $row, array $headerMap): array
{
    $key = normalizeHeader('Desc_AR');

    if (!isset($headerMap[$key])) {
        return [];
    }

    $descs = [];

    foreach ($headerMap[$key] as $index) {
        $value = cleanText($row[$index] ?? null);
        if ($value !== null) {
            $descs[] = $value;
        }
    }

    return array_values(array_unique($descs));
}

function detectSector(array $descs): string
{
    if (empty($descs)) {
        return 'غير محدد';
    }

    foreach ($descs as $desc) {
        if (mb_strpos($desc, 'قطاع') !== false) {
            return $desc;
        }
    }

    return 'غير محدد';
}

function buildDescription(array $descs): ?string
{
    if (empty($descs)) {
        return null;
    }

    return implode(' | ', $descs);
}

function isEffectivelyEmptyRow(array $row): bool
{
    foreach ($row as $cell) {
        if (cleanText($cell) !== null) {
            return false;
        }
    }

    return true;
}

function getFilesFromDirectory(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }

    $files = scandir($dir);
    $result = [];

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $fullPath = $dir . DIRECTORY_SEPARATOR . $file;

        if (!is_file($fullPath)) {
            continue;
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if (in_array($ext, ['xlsx', 'xls', 'csv'], true)) {
            $result[] = $fullPath;
        }
    }

    sort($result);

    return $result;
}

function loadRowsFromFile(string $filePath): array
{
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    return $sheet->toArray(null, true, true, false);
}

function duplicateKey(?string $membershipNo, ?string $commercialRegNo, ?string $orgNationalNo): ?string
{
    $parts = [
        $membershipNo ? mb_strtolower(trim($membershipNo)) : '',
        $commercialRegNo ? mb_strtolower(trim($commercialRegNo)) : '',
        $orgNationalNo ? mb_strtolower(trim($orgNationalNo)) : '',
    ];

    if ($parts[0] === '' && $parts[1] === '' && $parts[2] === '') {
        return null;
    }

    return implode('|', $parts);
}

/*
|--------------------------------------------------------------------------
| فحص المجلد
|--------------------------------------------------------------------------
*/

$files = getFilesFromDirectory($inputDir);

if (empty($files)) {
    exit("No Excel/CSV files found in: {$inputDir}\n");
}

/*
|--------------------------------------------------------------------------
| توليد SQL + تقرير التكرار
|--------------------------------------------------------------------------
*/

$sql = [];
$sql[] = "-- Auto generated SQL import from folder";
$sql[] = "-- Generated at: " . date('Y-m-d H:i:s');
$sql[] = "-- Source dir: {$inputDir}";
$sql[] = "SET NAMES utf8mb4;";
$sql[] = "SET FOREIGN_KEY_CHECKS=0;";
$sql[] = "";

$duplicateTracker = [];
$duplicatesRows = [];

$totalFiles = 0;
$totalRows = 0;
$totalInserted = 0;

foreach ($files as $filePath) {
    $totalFiles++;

    $sql[] = "-- ==================================================";
    $sql[] = "-- File: " . basename($filePath);
    $sql[] = "-- ==================================================";

    $rows = loadRowsFromFile($filePath);

    if (count($rows) < 2) {
        $sql[] = "-- Skipped: file has no data rows";
        $sql[] = "";
        continue;
    }

    $headerRow = $rows[0];
    $headerMap = mapHeaders($headerRow);

    $sql[] = "-- Headers detected:";
    $sql[] = "-- " . implode(' | ', array_map(fn($v) => trim((string) $v), $headerRow));
    $sql[] = "";

    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        $totalRows++;

        if (isEffectivelyEmptyRow($row)) {
            continue;
        }

        $membershipNo      = cleanText(getCellByHeader($row, $headerMap, 'MembershipNo'));
        $organizationName  = cleanText(getCellByHeader($row, $headerMap, 'OrganizationName_ar'));
        $registeredDate    = normalizeDate(getCellByHeader($row, $headerMap, 'RegistedDate'));
        $subDate           = normalizeDate(getCellByHeader($row, $headerMap, 'SubDate'));
        $orgNationalNo     = cleanText(getCellByHeader($row, $headerMap, 'OrgNationalNo'));
        $commercialRegNo   = cleanText(getCellByHeader($row, $headerMap, 'CommercialRegNo'));
        $commercialRegDate = normalizeDate(getCellByHeader($row, $headerMap, 'CommercialRegDate'));
        $commercialName    = cleanText(getCellByHeader($row, $headerMap, 'CommercialName_ar'));
        $ccateId           = cleanText(getCellByHeader($row, $headerMap, 'CCateID'));
        $delegate          = cleanText(getCellByHeader($row, $headerMap, 'DelegateToSignOnManagement'));
        $members           = cleanText(getCellByHeader($row, $headerMap, 'Members'));
        $street            = cleanText(getCellByHeader($row, $headerMap, 'Street'));
        $mobileList        = cleanText(getCellByHeader($row, $headerMap, 'MobileList'));
        $phoneList         = cleanText(getCellByHeader($row, $headerMap, 'PhoneList'));
        $faxList           = cleanText(getCellByHeader($row, $headerMap, 'FaxList'));
        $poBox             = cleanText(getCellByHeader($row, $headerMap, 'POBox'));
        $zipcodeDesc       = cleanText(getCellByHeader($row, $headerMap, 'ZIPCodeDesc_ar'));
        $zipcode           = cleanText(getCellByHeader($row, $headerMap, 'ZIPCode'));
        $emailList         = cleanText(getCellByHeader($row, $headerMap, 'EmailList'));

        $descs = extractDescriptions($row, $headerMap);
        $sector = detectSector($descs);
        $description = buildDescription($descs);

        if ($organizationName === null && $membershipNo === null && $commercialName === null) {
            continue;
        }

        $dupKey = duplicateKey($membershipNo, $commercialRegNo, $orgNationalNo);

        if ($dupKey !== null) {
            if (!isset($duplicateTracker[$dupKey])) {
                $duplicateTracker[$dupKey] = [];
            }

            $duplicateTracker[$dupKey][] = [
                'file' => basename($filePath),
                'row' => $i + 1,
                'membership_no' => $membershipNo,
                'organization_name' => $organizationName,
                'commercial_reg_no' => $commercialRegNo,
                'org_national_no' => $orgNationalNo,
            ];
        }

        $totalInserted++;

        $sql[] = "-- Row " . ($i + 1);
        $sql[] = "INSERT INTO merchants (
    membership_no,
    organization_name,
    registered_date,
    sub_date,
    description,
    org_national_no,
    commercial_reg_no,
    commercial_reg_date,
    commercial_name,
    sector,
    ccate_id,
    delegate_to_sign_on_management,
    members,
    street,
    po_box,
    zipcode_desc,
    zipcode,
    contacted,
    invited,
    notes,
    created_at,
    updated_at
) VALUES (
    " . sqlValue($membershipNo) . ",
    " . sqlValue($organizationName) . ",
    " . sqlValue($registeredDate) . ",
    " . sqlValue($subDate) . ",
    " . sqlValue($description) . ",
    " . sqlValue($orgNationalNo) . ",
    " . sqlValue($commercialRegNo) . ",
    " . sqlValue($commercialRegDate) . ",
    " . sqlValue($commercialName) . ",
    " . sqlValue($sector) . ",
    " . sqlValue($ccateId) . ",
    " . sqlValue($delegate) . ",
    " . sqlValue($members) . ",
    " . sqlValue($street) . ",
    " . sqlValue($poBox) . ",
    " . sqlValue($zipcodeDesc) . ",
    " . sqlValue($zipcode) . ",
    0,
    0,
    NULL,
    NOW(),
    NOW()
);";

        $sql[] = "SET @merchant_id = LAST_INSERT_ID();";

        foreach (splitList($phoneList) as $phone) {
            $sql[] = "INSERT INTO merchant_phones (merchant_id, phone, created_at, updated_at)
VALUES (@merchant_id, " . sqlValue($phone) . ", NOW(), NOW());";
        }

        foreach (splitList($mobileList) as $mobile) {
            $sql[] = "INSERT INTO merchant_mobiles (merchant_id, mobile, created_at, updated_at)
VALUES (@merchant_id, " . sqlValue($mobile) . ", NOW(), NOW());";
        }

        foreach (splitList($emailList) as $email) {
            $sql[] = "INSERT INTO merchant_emails (merchant_id, email, created_at, updated_at)
VALUES (@merchant_id, " . sqlValue($email) . ", NOW(), NOW());";
        }

        foreach (splitList($faxList) as $fax) {
            $sql[] = "INSERT INTO merchant_faxes (merchant_id, fax, created_at, updated_at)
VALUES (@merchant_id, " . sqlValue($fax) . ", NOW(), NOW());";
        }

        $sql[] = "";
    }

    $sql[] = "";
}

/*
|--------------------------------------------------------------------------
| بناء تقرير التكرار دون حذف أي سجل
|--------------------------------------------------------------------------
*/

foreach ($duplicateTracker as $key => $rows) {
    if (count($rows) <= 1) {
        continue;
    }

    foreach ($rows as $row) {
        $duplicatesRows[] = [
            'duplicate_key' => $key,
            'occurrences' => count($rows),
            'file' => $row['file'],
            'row' => $row['row'],
            'membership_no' => $row['membership_no'],
            'organization_name' => $row['organization_name'],
            'commercial_reg_no' => $row['commercial_reg_no'],
            'org_national_no' => $row['org_national_no'],
        ];
    }
}

$sql[] = "-- Summary";
$sql[] = "-- Files processed: {$totalFiles}";
$sql[] = "-- Rows scanned: {$totalRows}";
$sql[] = "-- Rows inserted: {$totalInserted}";
$sql[] = "-- Duplicate groups detected (not skipped): " . count(array_filter($duplicateTracker, fn($rows) => count($rows) > 1));
$sql[] = "-- Duplicate rows detected (not skipped): " . count($duplicatesRows);
$sql[] = "";
$sql[] = "SET FOREIGN_KEY_CHECKS=1;";
$sql[] = "";

file_put_contents($outputSqlFile, implode(PHP_EOL, $sql));

$fp = fopen($outputDuplicatesFile, 'w');
fputcsv($fp, [
    'duplicate_key',
    'occurrences',
    'file',
    'row',
    'membership_no',
    'organization_name',
    'commercial_reg_no',
    'org_national_no',
]);

foreach ($duplicatesRows as $dup) {
    fputcsv($fp, $dup);
}

fclose($fp);

echo "Done.\n";
echo "Files processed: {$totalFiles}\n";
echo "Rows scanned: {$totalRows}\n";
echo "Rows inserted: {$totalInserted}\n";
echo "Duplicate groups detected (not skipped): " . count(array_filter($duplicateTracker, fn($rows) => count($rows) > 1)) . "\n";
echo "Duplicate rows detected (not skipped): " . count($duplicatesRows) . "\n";
echo "SQL file: {$outputSqlFile}\n";
echo "Duplicates report: {$outputDuplicatesFile}\n";
