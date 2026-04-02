<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DatabaseBackupApiController extends Controller
{
    /**
     * Trigger database backup.
     */
    public function backup()
    {
        try {
            // Run the artisan command
            $exitCode = Artisan::call('database:backup');
            $output = Artisan::output();

            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Database backup created successfully!',
                    'output' => $output
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Database backup failed!',
                    'output' => $output
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Database backup error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all database backups.
     */
    public function list()
    {
        $backupPath = 'backup'; // Within storage/app
        $files = Storage::disk('local')->files($backupPath);
        
        $backups = array_map(function($file) {
            return [
                'name' => basename($file),
                'size' => round(Storage::disk('local')->size($file) / 1024 / 1024, 2) . ' MB',
                'last_modified' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($file)),
                'download_url' => route('api.database.download', ['filename' => basename($file)])
            ];
        }, $files);

        // Sort by last modified descending
        usort($backups, function($a, $b) {
            return strcmp($b['last_modified'], $a['last_modified']);
        });

        return response()->json([
            'success' => true,
            'backups' => $backups
        ]);
    }

    /**
     * Download a specific backup file.
     */
    public function download($filename)
    {
        $path = 'backup/' . $filename;

        if (Storage::disk('local')->exists($path)) {
            return response()->download(storage_path('app/' . $path));
        }

        return response()->json([
            'success' => false,
            'message' => 'Backup file not found!'
        ], 404);
    }

    /**
     * Delete a backup file.
     */
    public function destroy($filename)
    {
        $path = 'backup/' . $filename;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            return response()->json([
                'success' => true,
                'message' => 'Backup file deleted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Backup file not found!'
        ], 404);
    }
}
