<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DbRestore extends Command
{
    protected $signature = 'db:restore {file}';
    protected $description = 'Restore database from backup file';

    public function handle()
    {
        $fileName = $this->argument('file');
        $filePath = storage_path('app/backups/' . $fileName);
        
        if (!File::exists($filePath)) {
            $this->error("File not found: {$fileName}");
            return Command::FAILURE;
        }
        
        $this->warn("⚠️  Restoring will overwrite current database!");
        
        if (!$this->confirm("Are you sure you want to restore from {$fileName}?")) {
            return Command::SUCCESS;
        }
        
        try {
            // Check if file is compressed
            if (str_ends_with($fileName, '.gz')) {
                $this->info("Extracting compressed file...");
                $content = gzdecode(File::get($filePath));
            } else {
                $content = File::get($filePath);
            }
            
            // Execute SQL
            $this->info("Restoring database...");
            
            // Split SQL by statements
            $statements = $this->splitSqlStatements($content);
            $total = count($statements);
            
            DB::beginTransaction();
            
            foreach ($statements as $i => $statement) {
                if (trim($statement)) {
                    DB::unprepared($statement);
                    
                    if (($i + 1) % 100 === 0) {
                        $this->info("Processed " . ($i + 1) . " of {$total} statements");
                    }
                }
            }
            
            DB::commit();
            
            $this->info("✅ Database restored successfully from {$fileName}");
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Restore failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    private function splitSqlStatements($sql)
    {
        $statements = [];
        $delimiter = ';';
        $buffer = '';
        
        $lines = explode("\n", $sql);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $buffer .= $line . "\n";
            
            if (substr($line, -1) === $delimiter) {
                $statements[] = $buffer;
                $buffer = '';
            }
        }
        
        if (!empty($buffer)) {
            $statements[] = $buffer;
        }
        
        return $statements;
    }
}