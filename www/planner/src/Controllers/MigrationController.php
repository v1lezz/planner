<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Database;

class MigrationController extends BaseController {
    public function run(): void {
        Auth::requireRole('admin');
        $dir = __DIR__ . '/../../migrations';
        $pdo = Database::pdo();
        $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            applied_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $applied = Database::query("SELECT filename FROM migrations")->fetchAll(\PDO::FETCH_COLUMN);
        $files = array_values(array_filter(scandir($dir), fn($f)=>preg_match('/\.sql$/', $f)));
        sort($files);
        $log = [];
        foreach ($files as $file) {
            if (in_array($file, $applied, true)) continue;
            $path = $dir . '/' . $file;
            $sql = file_get_contents($path);
            $this->executeSqlWithDelimiter($sql);
            Database::query("INSERT INTO migrations (filename, applied_at) VALUES (?, NOW())", [$file]);
            $log[] = "Applied: $file";
        }
        $this->render('migrate', ['log' => $log]);
    }

    private function executeSqlWithDelimiter(string $sql): void {
        $pdo = \App\Core\Database::pdo();
        $delimiter = ';';
        $buffer = '';
        $lines = preg_split("/\\r?\\n/", $sql);
        foreach ($lines as $line) {
            if (preg_match('/^DELIMITER\\s+(.+)$/', trim($line), $m)) {
                $this->execStatement($buffer, $delimiter, $pdo);
                $buffer = '';
                $delimiter = $m[1];
                continue;
            }
            $buffer .= $line . "\n";
        }
        $this->execStatement($buffer, $delimiter, $pdo);
    }

    private function execStatement(string $buffer, string $delimiter, \PDO $pdo): void {
        $buffer = trim($buffer);
        if ($buffer === '') return;
        if ($delimiter === ';') {
            foreach (array_filter(array_map('trim', explode(';', $buffer))) as $stmt) {
                if ($stmt !== '') $pdo->exec($stmt);
            }
        } else {
            $parts = explode($delimiter, $buffer);
            foreach ($parts as $stmt) {
                $stmt = trim($stmt);
                if ($stmt !== '') $pdo->exec($stmt);
            }
        }
    }
}
