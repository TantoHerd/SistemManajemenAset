<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--compress : Compress backup file}';
    protected $description = 'Backup database to SQL file';

    public function handle()
    {
        $this->info('Starting database backup...');
        
        try {
            // Create backups directory if not exists
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }
            
            // Get database configuration
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            
            // Generate filename
            $timestamp = Carbon::now()->format('Y-m-d_His');
            $filename = "backup_{$database}_{$timestamp}.sql";
            $filepath = $backupDir . '/' . $filename;
            
            // Try using mysqldump first
            $dumpSuccess = $this->mysqldumpBackup($host, $port, $username, $password, $database, $filepath);
            
            if (!$dumpSuccess) {
                $this->warn('mysqldump not available, using PHP backup method...');
                $this->phpBackup($database, $filepath);
            }
            
            // Compress if requested
            if ($this->option('compress')) {
                $this->compress($filepath);
                $filename .= '.gz';
            }
            
            // Clean old backups
            $this->cleanOldBackups($backupDir);
            
            $this->info("✅ Backup completed successfully: {$filename}");
            $this->info("📁 Location: storage/app/backups/{$filename}");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Backup failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    private function mysqldumpBackup($host, $port, $username, $password, $database, $filepath)
    {
        // Find mysqldump path
        $mysqldump = $this->findMysqldump();
        if (!$mysqldump) {
            return false;
        }
        
        $command = sprintf(
            '"%s" --user="%s" --password="%s" --host="%s" --port="%s" --single-transaction --routines --triggers "%s" > "%s" 2>&1',
            $mysqldump,
            $username,
            $password,
            $host,
            $port,
            $database,
            $filepath
        );
        
        exec($command, $output, $returnVar);
        
        return $returnVar === 0 && File::exists($filepath) && File::size($filepath) > 0;
    }
    
    private function findMysqldump()
    {
        // Common paths for mysqldump
        $paths = [
            'mysqldump', // In PATH
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];
        
        foreach ($paths as $path) {
            $output = null;
            $returnVar = null;
            exec('"' . $path . '" --version 2>&1', $output, $returnVar);
            if ($returnVar === 0) {
                return $path;
            }
        }
        
        return null;
    }
    
    private function phpBackup($database, $filepath)
    {
        $tables = DB::select('SHOW TABLES');
        $sql = "-- SIMASET Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$database}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            $tableName = reset($table);
            $this->info("Exporting: {$tableName}");
            
            // Get create table syntax
            $createTable = DB::select("SHOW CREATE TABLE {$tableName}");
            $sql .= "\n-- Table structure for `{$tableName}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            // Get data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Dumping data for `{$tableName}`\n";
                
                foreach ($rows->chunk(100) as $chunk) {
                    $valuesList = [];
                    foreach ($chunk as $row) {
                        $rowArray = (array)$row;
                        $values = array_map(function($value) {
                            if ($value === null) return 'NULL';
                            return "'" . addslashes($value) . "'";
                        }, $rowArray);
                        
                        $valuesList[] = "(" . implode(',', $values) . ")";
                    }
                    
                    $sql .= "INSERT INTO `{$tableName}` VALUES \n";
                    $sql .= implode(",\n", $valuesList) . ";\n\n";
                }
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        File::put($filepath, $sql);
    }
    
    private function compress($filepath)
    {
        if (!File::exists($filepath)) {
            return;
        }
        
        $compressedFile = $filepath . '.gz';
        $bufferSize = 4096;
        $fileHandle = fopen($filepath, 'rb');
        $compressedHandle = gzopen($compressedFile, 'wb9');
        
        while (!feof($fileHandle)) {
            gzwrite($compressedHandle, fread($fileHandle, $bufferSize));
        }
        
        fclose($fileHandle);
        gzclose($compressedHandle);
        File::delete($filepath);
    }
    
    private function cleanOldBackups($backupDir)
    {
        $keepDays = 30;
        $files = glob($backupDir . '/backup_*.sql*');
        $now = Carbon::now();
        
        foreach ($files as $file) {
            $fileDate = Carbon::createFromTimestamp(filemtime($file));
            if ($fileDate->diffInDays($now) > $keepDays) {
                File::delete($file);
                $this->info("Deleted old backup: " . basename($file));
            }
        }
    }
}