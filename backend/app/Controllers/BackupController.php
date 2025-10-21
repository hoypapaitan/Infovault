<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\AuthModel;
use CodeIgniter\RESTful\ResourceController;

class BackupController extends BaseController
{
    public function __construct(){
        //Models
        $this->authModel = new AuthModel();
    }

    public function backupDatabase(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 
        $hasPass = sha1($data->password);

        //Select Query for finding User Information
        $user = $this->authModel->where(['id' => $data->userId, 'password' => $hasPass])->get()->getRow();
        
        //Set Api Response return to the FE
        if($user){
            $db = db_connect();
            $dbutil = \Config\Database::utils();
            $tables = $db->listTables();

            $prefs = array(
                'tables'        => $tables,   // Array of tables to backup.
                'ignore'        => array(),                     // List of tables to omit from the backup
                'format'        => 'txt',                       // gzip, zip, txt
                'filename'      => 'genan_db.sql',              // File name - NEEDED ONLY WITH ZIP FILES
                'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
                'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
                'newline'       => "\n"                         // Newline character used in backup file
            );

            $backup = $dbutil->backup($prefs);

            ob_start();

            return $this->response
                ->setStatusCode(200)
                ->setContentType('text/plain')
                ->setBody(json_encode($backup));
        } else {
            $response = [
                'error' => 401,
                'title' => 'Backup Denied',
                'message' => 'Unauthoried user is trying to backup'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }
    
    public function restoreDatabase()
    {
        // Check if a file is uploaded
        $file = $this->request->getFile('backup_file');
        if (!$file->isValid()) {
            return $this->response
                ->setStatusCode(400)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'No valid file uploaded']));
        }

        // Generate a new name for the file and move it to the writable/uploads directory
        $newName = $file->getRandomName();
        $filePath = WRITEPATH . 'uploads/' . $newName;
        if (!$file->move(WRITEPATH . 'uploads', $newName)) {
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'Failed to move uploaded file']));
        }

        // Read the file content
        if (!file_exists($filePath)) {
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'File does not exist after moving']));
        }

        $sql = file_get_contents($filePath);
        if ($sql === false) {
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'Failed to read file content']));
        }

        $sql = str_replace("\n", " ", $sql);
        
        // Log the SQL content for debugging
        log_message('debug', 'SQL Content: ' . $sql);

        // Execute the SQL commands to restore the database
        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $db->query($sql);
        } catch (\Exception $e) {
            log_message('error', 'SQL Execution Error: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'Failed to execute SQL commands', 'details' => $e->getMessage()]));
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'Failed to restore database']));
        }

        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/json')
            ->setBody(json_encode(['message' => 'Database restored successfully']));
    }

}