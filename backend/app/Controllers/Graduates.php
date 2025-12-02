<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\GraduatesModel;

class Graduates extends BaseController
{
    protected $graduatesModel;

    public function __construct(){
        $this->graduatesModel = new GraduatesModel();
    }

    /**
     * Create a single graduate record
     */
    public function create(){
        $data = $this->request->getJSON(true);

        // Validating name and studentId
        if(!isset($data['name']) || empty($data['name']) || !isset($data['studentId']) || empty($data['studentId'])){
            $response = ['title'=>'Bad Request','message'=>'Name and Student ID are required'];
            return $this->response->setStatusCode(400)->setJSON($response);
        }

        // Map incoming data
        $row = [
            'studentId'     => $data['studentId'], // Added Student ID
            'name'          => $data['name'] ?? null,
            'address'       => $data['address'] ?? null,
            'batch'         => $data['batch'] ?? null,
            'yearGraduated' => $data['yearGraduated'] ?? ($data['year_graduated'] ?? null),
            'advisoryId'    => $data['advisoryId'] ?? ($data['advisory_id'] ?? null),
            'section'       => $data['section'] ?? null,
            'course'        => $data['course'] ?? null,
            'major'         => $data['major'] ?? null,
            'achievement'   => $data['achievement'] ?? null,
            'gender'        => $data['gender'] ?? null,
            'created_by'    => $data['created_by'] ?? ($data['createdBy'] ?? 0),
        ];

        // Check for duplicates (Name+Year+Course OR StudentID)
        if($this->checkForDuplicate($row['name'], $row['yearGraduated'], $row['course'], $row['studentId'])){
            $response = ['error' => true, 'title' => 'Duplicate Entry', 'message' => 'Record (Name or Student ID) already exists'];
            return $this->response->setStatusCode(409)->setJSON($response);
        }

        $inserted = $this->graduatesModel->insert($row);

        if($inserted){
            $response = ['error' => false, 'title' => 'Success', 'message' => 'Graduate data added', 'id' => $inserted];
            return $this->response->setStatusCode(200)->setJSON($response);
        }

        $response = ['error' => true, 'title'=>'Failed','message'=>'Unable to create record'];
        return $this->response->setStatusCode(400)->setJSON($response);
    }

    /**
     * Create multiple records
     */
    public function createMultiple(){
        $data = $this->request->getJSON();

        if(!isset($data->csv) || !is_array($data->csv)){
            return $this->response->setStatusCode(400)->setJSON(['title'=>'Bad Request','message'=>'Invalid payload']);
        }

        foreach ($data->csv as $value){
            $payload = json_decode(json_encode($value), true);
            
            $row = [
                'studentId'     => $payload['studentId'] ?? ($payload['student_id'] ?? null), // Added mapping
                'name'          => $payload['name'] ?? ($payload['fullName'] ?? null),
                'address'       => $payload['address'] ?? null,
                'batch'         => $payload['batch'] ?? null,
                'yearGraduated' => $payload['yearGraduated'] ?? ($payload['year_graduated'] ?? null),
                'advisoryId'    => $payload['advisoryId'] ?? ($payload['advisory_id'] ?? null),
                'section'       => $payload['section'] ?? null,
                'course'        => $payload['course'] ?? null,
                'major'         => $payload['major'] ?? null,
                'achievement'   => $payload['achievement'] ?? null,
                'gender'        => $payload['gender'] ?? null,
                'created_by'    => $payload['created_by'] ?? ($payload['createdBy'] ?? 0),
            ];

            // Only insert if no duplicate exists based on composite key OR student ID
            if (!$this->checkForDuplicate($row['name'], $row['yearGraduated'], $row['course'], $row['studentId'])) {
                $this->graduatesModel->insert($row);
            }
        }

        return $this->response->setStatusCode(200)->setJSON(['title' => 'Success', 'message' => 'Batch upload complete']);
    }

    /**
     * Update a graduate record
     */
    public function update(){
        $data = $this->request->getJSON(true);

        if(!isset($data['id'])){
            return $this->response->setStatusCode(400)->setJSON(['error' => true, 'message'=>'Missing id']);
        }

        $id = $data['id'];
        
        // Whitelist fields to allow update - ADDED studentId
        $updateData = [];
        $allowed = ['studentId', 'name', 'gender', 'course', 'achievement', 'yearGraduated', 'address', 'section', 'major', 'batch'];
        
        foreach($allowed as $field) {
            if(isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if(empty($updateData)){
            return $this->response->setStatusCode(200)->setJSON(['message'=>'No changes detected']);
        }

        // Optional: Check if changing studentId causes collision, usually handled by DB unique constraint error, 
        // but could add specific check here if desired.

        $updated = $this->graduatesModel->update($id, $updateData);

        if($updated){
            return $this->response->setStatusCode(200)->setJSON(['error' => false, 'message'=>'Record updated successfully']);
        }

        return $this->response->setStatusCode(400)->setJSON(['error' => true, 'message'=>'Database update failed']);
    }

    /**
     * Delete a graduate record
     */
    public function delete(){
        $data = $this->request->getJSON();

        if(!isset($data->id)){
            return $this->response->setStatusCode(400)->setJSON(['error' => true, 'message'=>'Missing id']);
        }

        $deleted = $this->graduatesModel->delete($data->id);

        if($deleted){
            return $this->response->setStatusCode(200)->setJSON(['error' => false, 'message'=>'Record deleted successfully']);
        }

        return $this->response->setStatusCode(400)->setJSON(['error' => true, 'message'=>'Unable to delete record']);
    }

    /**
     * Get List
     */
    public function getList(){
        $list = $this->graduatesModel->orderBy('yearGraduated', 'DESC')->findAll();
        return $this->response->setStatusCode(200)->setJSON(['error' => false, 'list' => $list]);
    }

    /**
     * Helper: Check duplicate
     * Checks for either (Name + Year + Course) match OR (Student ID) match
     */
    private function checkForDuplicate($name, $yearGraduated, $course, $studentId = null) {
        $this->graduatesModel->groupStart()
                ->where('name', $name)
                ->where('yearGraduated', $yearGraduated)
                ->where('course', $course)
            ->groupEnd();
            
        if (!empty($studentId)) {
            $this->graduatesModel->orWhere('studentId', $studentId);
        }
        
        $count = $this->graduatesModel->countAllResults();
        
        return $count > 0;
    }
}