<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupRestoreController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:superadmin']);
    }

    private function backupDir()
    {
        $dir = storage_path('app/backups');
        if (!file_exists($dir)) mkdir($dir, 0755, true);
        return $dir;
    }

    public function index()
    {
        $files = collect(glob($this->backupDir() . '/*.sql'))
            ->map(function ($path) {
                return [
                    'name' => basename($path),
                    'size' => round(filesize($path) / 1024, 1) . ' KB',
                    'date' => date('M d, Y h:i A', filemtime($path)),
                ];
            })
            ->sortByDesc('date')
            ->values();

        $tables = $this->getAllTables();

        return view('pages.backup_restore', compact('files', 'tables'));
    }

    private function getAllTables()
    {
        $dbName = config('database.connections.mysql.database');
        $tables = DB::select('SHOW TABLES');
        $key = "Tables_in_{$dbName}";

        return collect($tables)->map(fn($t) => $t->$key)->values();
    }

    public function backupFull()
    {
        $filename = 'backup_full_' . now()->format('Y_m_d_His') . '.sql';
        $path = $this->backupDir() . '/' . $filename;

        $this->runMysqldump($this->getAllTables()->toArray(), $path);

        ActivityLog::record('backup', 'System', "Created full database backup: {$filename}");

        return back()->with('success', "Full backup created: {$filename}");
    }

    public function backupSelective(Request $request)
    {
        $request->validate(['tables' => 'required|array|min:1']);

        $filename = 'backup_selective_' . now()->format('Y_m_d_His') . '.sql';
        $path = $this->backupDir() . '/' . $filename;

        $this->runMysqldump($request->tables, $path);

        ActivityLog::record('backup', 'System', "Created selective backup (" . count($request->tables) . " tables): {$filename}");

        return back()->with('success', "Selective backup created: {$filename}");
    }

    private function runMysqldump(array $tables, string $outputPath)
    {
        $config = config('database.connections.mysql');

        $command = [
            'mysqldump',
            '-h', $config['host'],
            '-P', $config['port'] ?? 3306,
            '-u', $config['username'],
        ];

        if (!empty($config['password'])) {
            $command[] = '-p' . $config['password'];
        }

        $command[] = $config['database'];
        $command = array_merge($command, $tables);

        $process = new Process($command);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Backup failed: ' . $process->getErrorOutput());
        }

        file_put_contents($outputPath, $process->getOutput());
    }

    public function download(string $file)
    {
        $path = $this->backupDir() . '/' . basename($file);
        if (!file_exists($path)) abort(404);

        return response()->download($path);
    }

    public function deleteBackup(string $file)
    {
        $path = $this->backupDir() . '/' . basename($file);
        if (file_exists($path)) {
            unlink($path);
            ActivityLog::record('delete', 'System', "Deleted backup file: {$file}");
        }
        return back()->with('success', 'Backup file deleted.');
    }

    public function restore(Request $request)
    {
        $request->validate(['backup_file' => 'required|string']);

        $path = $this->backupDir() . '/' . basename($request->backup_file);
        if (!file_exists($path)) {
            return back()->with('error', 'Backup file not found.');
        }

        try {
            $this->executeSqlFile($path);
            ActivityLog::record('restore', 'System', "Restored database from backup: {$request->backup_file}");
            return back()->with('success', 'Database restored successfully from backup.');
        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    public function importSql(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|max:51200', // 50MB max
        ]);

        $uploaded = $request->file('sql_file');

        if (strtolower($uploaded->getClientOriginalExtension()) !== 'sql') {
            return back()->with('error', 'Only .sql files are allowed.');
        }

        $tempPath = $uploaded->getRealPath();

        try {
            $this->executeSqlFile($tempPath);
            ActivityLog::record('import', 'System', "Imported data from uploaded file: {$uploaded->getClientOriginalName()}");
            return back()->with('success', 'Data imported successfully from ' . $uploaded->getClientOriginalName());
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function executeSqlFile(string $path)
    {
        $config = config('database.connections.mysql');

        $command = [
            'mysql',
            '-h', $config['host'],
            '-P', $config['port'] ?? 3306,
            '-u', $config['username'],
        ];

        if (!empty($config['password'])) {
            $command[] = '-p' . $config['password'];
        }

        $command[] = $config['database'];

        $process = new Process($command);
        $process->setInput(file_get_contents($path));
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }
}