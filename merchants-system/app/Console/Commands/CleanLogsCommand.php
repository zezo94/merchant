<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\LoginLog;
use Illuminate\Console\Command;

class CleanLogsCommand extends Command
{
    protected $signature = 'logs:clean {days=90}';
    protected $description = 'Delete old audit logs and login logs';

    public function handle(): int
    {
        $days = (int) $this->argument('days');

        $auditDeleted = AuditLog::where('created_at', '<', now()->subDays($days))->delete();
        $loginDeleted = LoginLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Old logs deleted successfully.");
        $this->line("Audit logs deleted: {$auditDeleted}");
        $this->line("Login logs deleted: {$loginDeleted}");

        return self::SUCCESS;
    }
}
