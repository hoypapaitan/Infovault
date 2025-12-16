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

        // Map incoming data and enforce required fields
        $row = [
            'studentId'     => $data['studentId'],
            'name'          => $data['name'] ?? null,
            'address'       => $data['address'] ?? null,
            'yearGraduated' => $data['yearGraduated'] ?? ($data['year_graduated'] ?? null),
            'school'        => $data['school'] ?? '',
            'course'        => $data['course'] ?? '',
            'major'         => $data['major'] ?? '',
            'program'       => $data['program'] ?? '',
            'achievement'   => $data['achievement'] ?? null,
            'additionalAchievement'   => isset($data['additionalAchievement']) ? json_encode($data['additionalAchievement']) : null,
            'gender'        => $data['gender'] ?? null,
            'created_by'    => $data['created_by'] ?? ($data['createdBy'] ?? 0),
        ];
        // Validate required fields for dashboard navigation
        if(empty($row['program']) || empty($row['course']) || empty($row['major']) || empty($row['yearGraduated'])){
            $response = ['error' => true, 'title'=>'Missing Data','message'=>'Program, Course, Major, and Year Graduated are required.'];
            return $this->response->setStatusCode(400)->setJSON($response);
        }

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
                'studentId'     => $payload['studentId'] ?? ($payload['student_id'] ?? null),
                'name'          => $payload['name'] ?? ($payload['fullName'] ?? null),
                'address'       => $payload['address'] ?? null,
                'batch'         => $payload['batch'] ?? null,
                'yearGraduated' => $payload['yearGraduated'] ?? ($payload['year_graduated'] ?? null),
                'advisoryId'    => $payload['advisoryId'] ?? ($payload['advisory_id'] ?? null),
                'section'       => $payload['section'] ?? null,
                'school'        => $payload['school'] ?? '',
                'course'        => $payload['course'] ?? '',
                'major'         => $payload['major'] ?? '',
                'program'       => $payload['program'] ?? '',
                'achievement'   => $payload['achievement'] ?? null,
                'additionalAchievement'   => isset($payload['additionalAchievement']) ? json_encode($payload['additionalAchievement']) : null,
                'gender'        => $payload['gender'] ?? null,
                'created_by'    => $payload['created_by'] ?? ($payload['createdBy'] ?? 0),
            ];
            // Validate required fields for dashboard navigation
            if(empty($row['program']) || empty($row['course']) || empty($row['major']) || empty($row['yearGraduated'])){
                continue; // skip incomplete records
            }
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
        $allowed = ['studentId', 'name', 'gender', 'school', 'course', 'achievement', 'yearGraduated', 'address', 'major', 'program', 'additionalAchievement'];
        
        foreach($allowed as $field) {
            if(isset($data[$field])) {
                if($field === 'additionalAchievement') {
                    $updateData[$field] = json_encode($data[$field]);
                } else {
                    $updateData[$field] = $data[$field];
                }
            }
        }
        // Validate required fields for dashboard navigation
        if(isset($updateData['program']) && empty($updateData['program']) ||
           isset($updateData['course']) && empty($updateData['course']) ||
           isset($updateData['major']) && empty($updateData['major']) ||
           isset($updateData['yearGraduated']) && empty($updateData['yearGraduated'])){
            return $this->response->setStatusCode(400)->setJSON(['error'=>true,'message'=>'Program, Course, Major, and Year Graduated are required.']);
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
        // Ensure all records have program, course, major, yearGraduated fields (for frontend navigation)
        foreach ($list as &$rec) {
            $rec['program'] = $rec['program'] ?? '';
            $rec['school'] = $rec['school'] ?? '';
            $rec['course'] = $rec['course'] ?? '';
            $rec['major'] = $rec['major'] ?? '';
            $rec['yearGraduated'] = $rec['yearGraduated'] ?? '';
            // Decode additionalAchievement JSON string to array
            if (isset($rec['additionalAchievement']) && !is_array($rec['additionalAchievement'])) {
                $decoded = json_decode($rec['additionalAchievement'], true);
                $rec['additionalAchievement'] = is_array($decoded) ? $decoded : [];
            }
        }
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