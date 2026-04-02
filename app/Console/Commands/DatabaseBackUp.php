<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:database-back-up';
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create daily database backup';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $filename = 'backup-' . now()->format('Y-m-d_H-i-s') . '.sql';
        $backupPath = storage_path('app/backup');

        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        $mysqldump = '"D:\\xampp\\mysql\\bin\\mysqldump.exe"';

        $host = env('DB_HOST');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $db   = env('DB_DATABASE');

        //  IMPORTANT: password space
        $command = "{$mysqldump} -h {$host} -u {$user}";

        if (!empty($pass)) {
            $command .= " -p{$pass}";
        }

        $command .= " {$db} > \"{$backupPath}\\{$filename}\"";

        // debug
        $this->info("Running: " . $command);

        exec($command, $output, $result);

        if ($result === 0) {
            $this->info("✅ Backup created: {$filename}");
        } else {
            $this->error("❌ Backup failed!");
        }
    }
    // public function handle()
    // {
    //     // Backup file name
    //     $filename = 'backup-' . now()->format('Y-m-d') . '.sql';

    //     // Backup directory
    //     $backupPath = storage_path('app/backup');

    //     // Create directory if not exists
    //     if (!is_dir($backupPath)) {
    //         mkdir($backupPath, 0755, true);
    //     }

    //     // mysqldump path (update as per your XAMPP version)
    //     $mysqldumpPath = 'D:\\xampp\\mysql\\bin\\mysqldump.exe';

    //     // Build Windows compatible command
    //     $user = env('DB_USERNAME')
    //         ? "--user=\"" . env('DB_USERNAME') . "\""
    //         : "";

    //     $password = env('DB_PASSWORD')
    //         ? "--password=\"" . env('DB_PASSWORD') . "\""
    //         : "";

    //     $database = env('DB_DATABASE')
    //         ? "\"" . env('DB_DATABASE') . "\""
    //         : "";

    //     $command = "\"{$mysqldumpPath}\" {$user}" .
    //         " {$password}" .
    //         " --host=\"" . env('DB_HOST') . "\"" .
    //         " {$database}" .
    //         " > \"{$backupPath}\\{$filename}\"";

    //     // Execute command
    //     exec($command, $output, $result);

    //     if ($result === 0) {
    //         $this->info('Database backup created successfully!');
    //     } else {
    //         $this->error('Database backup failed!');
    //     }
    // }

}
