<?php namespace App\Controllers;

use App\Models\AuthModel;
use CodeIgniter\Controller;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Files\File;

class BackupController extends BaseController
{
    protected AuthModel $authModel;
    protected ConnectionInterface $db;
    
    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->db = \Config\Database::connect();
    }

    public function backupDatabase()
    {
        $data = $this->request->getJSON();
        if (!$data || !property_exists($data, 'password') || !property_exists($data, 'userId')) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Invalid or missing request data'
            ]);
        }

        $hasPass = sha1($data->password);
        $user = $this->authModel->where([
            'id' => $data->userId,
            'password' => $hasPass
        ])->first();

        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON([
                'error' => 'Unauthorized',
                'title' => 'Backup Denied',
                'message' => 'Unauthorized user is trying to backup'
            ]);
        }

        // Create backup filename with timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "infovault_backup_{$timestamp}.sql";
        $backupPath = WRITEPATH . 'backups/';

        // Ensure backup directory exists
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        // Get all tables
        $tables = [];
        $result = $this->db->query("SHOW TABLES")->getResult();
        foreach ($result as $row) {
            $tables[] = reset($row); // Get the first value from the row
        }
        
        $output = "-- Database Backup - Generated on " . date('Y-m-d H:i:s') . "\n\n";

        // Disable foreign key checks at start
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // Get create table syntax
            $query = $this->db->query("SHOW CREATE TABLE `{$table}`");
            $row = $query->getRow();
            $createTableSql = $row->{'Create Table'} ?? '';
            
            if ($createTableSql) {
                $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $output .= $createTableSql . ";\n\n";

                // Get table data
                $query = $this->db->query("SELECT * FROM `{$table}`");
                $rows = $query->getResultArray();

                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $keys = array_keys($row);
                        $values = array_map(function($value) {
                            if ($value === null) return 'NULL';
                            return $this->db->escape($value);
                        }, $row);

                        $output .= "INSERT INTO `{$table}` (`" . implode('`, `', $keys) . "`) VALUES (" . implode(", ", $values) . ");\n";
                    }
                    $output .= "\n";
                }
            }
        }

        // Re-enable foreign key checks at end
        $output .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

        // Save the SQL file
        $fullPath = $backupPath . $filename;
        if (file_put_contents($fullPath, $output) === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to create backup file'
            ]);
        }

        // Return the file as download
        return $this->response->download($fullPath, null)->setFileName($filename);
    }
    
    public function restoreDatabase()
    {
        // Verify file upload
        $file = $this->request->getFile('backup_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'No valid file uploaded'
            ]);
        }

        // Verify file type
        $ext = $file->getExtension();
        if ($ext !== 'sql') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Invalid file type. Only .sql files are allowed'
            ]);
        }

        // Move file to temporary location
        $newName = $file->getRandomName();
        $filePath = WRITEPATH . 'uploads/' . $newName;
        if (!$file->move(WRITEPATH . 'uploads', $newName)) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to move uploaded file'
            ]);
        }

        try {
            // Read and process SQL file
            $sql = file_get_contents($filePath);
            if ($sql === false) {
                throw new \RuntimeException('Failed to read SQL file');
            }

            // Disable foreign key checks before restore
            $this->db->query('SET FOREIGN_KEY_CHECKS=0');

            // Split and execute SQL statements
            $statements = array_filter(
                explode(";\n", str_replace("\r", '', $sql)),
                function($statement) {
                    return trim($statement) !== '';
                }
            );

            foreach ($statements as $statement) {
                if (trim($statement) !== '') {
                    $this->db->query($statement);
                }
            }

            // Re-enable foreign key checks
            $this->db->query('SET FOREIGN_KEY_CHECKS=1');

            // Cleanup
            unlink($filePath);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Database restored successfully'
            ]);

        } catch (\Exception $e) {
            // Cleanup on error
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Re-enable foreign key checks in case of error
            try {
                $this->db->query('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $ex) {
                // Ignore any errors here
            }

            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to restore database: ' . $e->getMessage()
            ]);
        }

        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = \Config\Database::connect();
        $db->transStart();
        
        $error = false;
        try {
            foreach ($sqls as $statement) {
                // Clean up the statement and skip if empty or a comment
                $statement = trim($statement);
                if (!empty($statement) && !str_starts_with($statement, '--')) {
                    $db->query($statement);
                }
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'SQL Execution Error: ' . $e->getMessage());
            $error = $e->getMessage();
        }
        $db->transComplete();

        // Check for any transaction failure
        if ($db->transStatus() === false || $error) {
            @unlink($filePath); // Cleanup
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'error' => 'Failed to restore database', 
                    'details' => $error ? $error : 'Transaction failed.'
                ]));
        }

        // Final cleanup and success response
        @unlink($filePath);
        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/json')
            ->setBody(json_encode(['message' => 'Database restored successfully']));
    }
}