<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_raw_rows', function (Blueprint $table) {
            $table->id();

            $table->string('source_file')->index();
            $table->unsignedInteger('source_row_number')->index();

            $table->string('sheet_name')->nullable();

            $table->json('header_row')->nullable();
            $table->json('raw_row')->nullable();

            $table->string('membership_no')->nullable()->index();
            $table->string('organization_name')->nullable()->index();
            $table->string('commercial_reg_no')->nullable()->index();
            $table->string('org_national_no')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_raw_rows');
    }
};
