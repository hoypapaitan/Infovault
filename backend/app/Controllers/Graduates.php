<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\GraduatesModel;

class Graduates extends BaseController
{
    public function __construct(){
        $this->graduatesModel = new GraduatesModel();
    }

    /**
     * Create a single graduate record
     * Expects JSON with graduate data
     */
    public function create(){
        $data = $this->request->getJSON(true);

        if(!isset($data['name']) || empty($data['name'])){
            $response = ['title'=>'Bad Request','message'=>'Name is required'];
            return $this->response->setStatusCode(400)->setContentType('application/json')->setBody(json_encode($response));
        }

        // Map incoming data
        $row = [];
        $row['name'] = $data['name'] ?? null;
        $row['address'] = $data['address'] ?? null;
        $row['batch'] = $data['batch'] ?? null;
        $row['yearGraduated'] = $data['yearGraduated'] ?? ($data['year_graduated'] ?? null);
        $row['advisoryId'] = $data['advisoryId'] ?? ($data['advisory_id'] ?? null);
        $row['section'] = $data['section'] ?? null;
        $row['course'] = $data['course'] ?? null;
        $row['major'] = $data['major'] ?? null;
        $row['achievement'] = $data['achievement'] ?? null;
        $row['gender'] = $data['gender'] ?? null;
        $row['created_by'] = $data['created_by'] ?? ($data['createdBy'] ?? 0);

        // Check for duplicates before inserting
        $duplicate = $this->checkForDuplicate($row['name'], $row['yearGraduated'], $row['course']);
        if($duplicate){
            $response = [
                'error' => true,
                'title' => 'Duplicate Entry',
                'message' => 'A graduate with the same name, year, and course already exists'
            ];

            return $this->response
                    ->setStatusCode(409)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

        $inserted = $this->graduatesModel->insert($row);

        if($inserted){
            $response = [
                'title' => 'Data Added',
                'message' => 'Graduate data successfully added',
                'id' => $inserted
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

        $response = ['title'=>'Creation Failed','message'=>'Unable to create record'];
        return $this->response->setStatusCode(400)->setContentType('application/json')->setBody(json_encode($response));
    }

    /**
     * Create multiple graduate records from CSV payload
     * Expects JSON: { csv: [ {...}, ... ] }
     */
    public function createMultiple(){
        $data = $this->request->getJSON();

        if(!isset($data->csv) || !is_array($data->csv)){
            $response = ['title'=>'Bad Request','message'=>'Invalid payload'];
            return $this->response->setStatusCode(400)->setContentType('application/json')->setBody(json_encode($response));
        }

        foreach ($data->csv as $key => $value){
            // Map incoming keys to match tblgraduate allowedFields
            $payload = json_decode(json_encode($value), true);
            $row = [];
            // normalize common key variants
            $row['name'] = isset($payload['name']) ? $payload['name'] : (isset($payload['fullName']) ? $payload['fullName'] : null);
            $row['address'] = $payload['address'] ?? null;
            $row['batch'] = $payload['batch'] ?? null;
            $row['yearGraduated'] = $payload['yearGraduated'] ?? ($payload['year_graduated'] ?? null);
            $row['advisoryId'] = $payload['advisoryId'] ?? ($payload['advisory_id'] ?? null);
            $row['section'] = $payload['section'] ?? null;
            $row['course'] = $payload['course'] ?? null;
            $row['major'] = $payload['major'] ?? null;
            $row['achievement'] = $payload['achievement'] ?? null;
            $row['gender'] = $payload['gender'] ?? null;
            // created_by may come as createdBy (camel) from frontend — map it
            $row['created_by'] = $payload['created_by'] ?? ($payload['createdBy'] ?? 0);

            // Check for duplicates before inserting
            if (!$this->checkForDuplicate($row['name'], $row['yearGraduated'], $row['course'])) {
                $this->graduatesModel->insert($row);
            }
        }

        $response = [
            'title' => 'Data Added',
            'message' => 'Graduate data successfully added'
        ];

        return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode($response));
    }

    /**
     * Update a graduate record. Expects JSON with id and fields to update.
     */
    public function update(){
        $data = $this->request->getJSON(true);

        if(!isset($data['id'])){
            $response = ['title'=>'Bad Request','message'=>'Missing id'];
            return $this->response->setStatusCode(400)->setContentType('application/json')->setBody(json_encode($response));
        }

        $id = $data['id'];
        unset($data['id']);

        // map any camelCase to snake_case where needed
        if(isset($data['createdBy'])){ $data['created_by'] = $data['createdBy']; unset($data['createdBy']); }
        if(isset($data['yearGraduated'])){ $data['yearGraduated'] = $data['yearGraduated']; }

        $updated = $this->graduatesModel->update($id, $data);

        if($updated){
            $response = ['title'=>'Update successful','message'=>'Graduate record updated'];
            return $this->response->setStatusCode(200)->setContentType('application/json')->setBody(json_encode($response));
        }

        $response = ['title'=>'Update Failed','message'=>'Unable to update record'];
        return $this->response->setStatusCode(400)->setContentType('application/json')->setBody(json_encode($response));
    }

    /**
     * Delete a graduate record. Expects JSON: { dataId: id }
     */
    public function delete(){
        $data = $this->request->getJSON();

        if(!isset($data->dataId)){
            $response = ['title'=>'Bad Request','message'=>'Missing dataId'];
            return $this->response->setStatusCode(400)->setContentType('application/json')->setBody(json_encode($response));
        }


    $where = ['id' => $data->dataId];
    $del = $this->graduatesModel->deleteGraduate($where);

        if($del){
            $response = ['title'=>'Delete successful','message'=>'Graduate record deleted'];
            return $this->response->setStatusCode(200)->setContentType('application/json')->setBody(json_encode($response));
        }

        $response = ['title'=>'Delete Failed','message'=>'Unable to delete record'];
        return $this->response->setStatusCode(400)->setContentType('application/json')->setBody(json_encode($response));
    }

    /**
     * Return list of graduates
     */
    public function getList(){
        $list = $this->graduatesModel->get()->getResult();

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode(['list' => $list]));
        }

        $response = ['title' => 'Error', 'message' => 'No Data Found'];
        return $this->response->setStatusCode(400)->setContentType('application/json')->setBody(json_encode($response));
    }

    /**
     * Check for duplicate graduate records based on name, year, and course
     */
    private function checkForDuplicate($name, $yearGraduated, $course) {
        $existing = $this->graduatesModel
            ->where('name', $name)
            ->where('yearGraduated', $yearGraduated)
            ->where('course', $course)
            ->countAllResults();
        
        return $existing > 0;
    }

}
