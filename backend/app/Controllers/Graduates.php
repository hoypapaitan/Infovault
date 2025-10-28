<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\GraduatesModel;

class Graduates extends BaseController
{
    public function __construct(){
        $this->graduatesModel = new GraduatesModel();
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
            // created_by may come as createdBy (camel) from frontend — map it
            $row['created_by'] = $payload['created_by'] ?? ($payload['createdBy'] ?? 0);

            $this->graduatesModel->insert($row);
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

}
