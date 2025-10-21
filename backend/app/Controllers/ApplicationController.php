<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\RequestModel;
use App\Models\AuthModel;
use App\Models\HistoryModel;
use \Firebase\JWT\JWT;

class ApplicationController extends BaseController
{
    public function __construct(){
        //Models
        $this->reqModel = new RequestModel();
        $this->authModel = new AuthModel();
        $this->historyModel = new HistoryModel();
    }

    public function sendCIFApplication(){
        //Check the Last Created Reference
        $suffix = "MLIS";
        $numberSequence = date('Ymd') .'0'. 101 ;
        $reference = '';

        $checkReference = $this->reqModel->getLastinsertedReference();
        
        if($checkReference){
            //if has exists generate addition 
            $lastCountDigit = substr($checkReference[0]->referenceId, -3);
            $numberSequence = date('Ymd') .'0'. $lastCountDigit + 1 ;
            $reference = $suffix . $numberSequence;
        } else {
            $reference = $suffix . $numberSequence;
        }
            
        //Get API Request Data from NuxtJs
        $payload = $this->request->getJSON();
        $payload = json_decode(json_encode($payload), true);

        $spacimentApplicationData = [
            "referenceId" => $reference,
            "status" => 'MLIS001',
            "statusDescription" => 'New specimen has ben submitted.',
            "branchId" => $payload['branchId'],
            "contactTracing" => json_encode($payload['contactTracing']),
            "interviewForm" => json_encode($payload['interviewForm']),
            "patientForm" => json_encode($payload['patientForm']),
            "caseForm" => json_encode($payload['caseForm']),
            "createdBy" => $payload['userId'],
        ];
        
        //INSERT QUERY TO APPLICATION
        $query = $this->reqModel->insertApplication($spacimentApplicationData);

        if($query){
            $lastInserted = $this->reqModel->insertID();
            $history = [
                'applicationId' => $lastInserted,
                'requestData' => json_encode($spacimentApplicationData),
                'actionStatus' => 'New specimen has been submitted.',
                'createdBy' => $payload['userId'],
            ];

            $this->historyModel->insert($history);


            $response = [
                'title' => 'Case Investigation Form',
                'message' => 'Your application has been submitted.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function getApplicationsRequest(){

        $list = [];
        $payload = $this->request->getJSON();
        
        if(isset($payload->branch)){
            $query = $this->reqModel->getAllApplicationBranch($payload->status, $payload->user, $payload->branch);
        } else {
            $query = $this->reqModel->getAllApplication($payload->status, $payload->user);
        }
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $interviewInfo = json_decode($value->interviewForm);
            $patientInfo = json_decode($value->patientForm);

            $list['list'][$key] = [
                "key" => $value->id,
                "rerun" => $value->isRerun,
                "appStatus" =>  $value->status,
                "performer" =>  $value->performedBy == "" ? [] : explode(",", $value->performedBy),
                "approver" =>  $value->approveBy == "" ? [] : explode(",", $value->approveBy),
                "verifier" =>  $value->verifiedBy,
                "encoder" =>  $value->encodedBy,
                "referenceId" => $value->referenceId,
                "name" => $patientInfo->firstName .' '. $patientInfo->lastName .' '. $patientInfo->suffix,
                "age" =>  $patientInfo->age,
                "gender" =>  $patientInfo->sex,
                "dateOfInterview" =>  date('Y-m-d', strtotime($interviewInfo->interviewDate)),
                "drUnit" =>  $interviewInfo->drUnit,
                "typeOfClient" =>  $interviewInfo->clientType,
                "status" =>  $value->statusDescription,
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function searchSpecimenApplication(){

        $list = [];
        $payload = $this->request->getJSON();
        $query = $this->reqModel->getDetails(['referenceId' => $payload->search, 'branchId'=>$payload->branch]);
        // print_r($query);]
        // exit;

        $interviewInfo = json_decode($query->interviewForm);
        $patientInfo = json_decode($query->patientForm);

        $list['list'] = [
            "key" => $query->id,
            "rerun" => $query->isRerun,
            "appStatus" =>  $query->status,
            "referenceId" => $query->referenceId,
            "name" => $patientInfo->firstName .' '. $patientInfo->lastName .' '. $patientInfo->suffix,
            "age" =>  $patientInfo->age,
            "gender" =>  $patientInfo->sex,
            "dateOfInterview" =>  date('Y-m-d', strtotime($interviewInfo->interviewDate)),
            "drUnit" =>  $interviewInfo->drUnit,
            "typeOfClient" =>  $interviewInfo->clientType,
            "status" =>  $query->statusDescription,
        ];

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function searchSpecimenApplicationByLastname(){

        $list = [];
        $payload = $this->request->getJSON();
        $query = $this->reqModel->getDetailsByPatientName(['branchId'=>$payload->branch]);
        $num = 0;
        foreach ($query as $key => $value) {
            
            $interviewInfo = json_decode($value->interviewForm);
            $patientInfo = json_decode($value->patientForm);
            if($payload->search == $patientInfo->lastName){
                $list['list'][$num] = [
                    "key" => $value->id,
                    "rerun" => $value->isRerun,
                    "appStatus" =>  $value->status,
                    "referenceId" => $value->referenceId,
                    "name" => $patientInfo->firstName .' '. $patientInfo->lastName .' '. $patientInfo->suffix,
                    "age" =>  $patientInfo->age,
                    "gender" =>  $patientInfo->sex,
                    "dateOfInterview" =>  date('Y-m-d', strtotime($interviewInfo->interviewDate)),
                    "drUnit" =>  $interviewInfo->drUnit,
                    "typeOfClient" =>  $interviewInfo->clientType,
                    "status" =>  $value->statusDescription,
                ];

                $num += 1;
            }

        }
        
        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function searchSpecimenApplicationByFirstname(){

        $list = [];
        $payload = $this->request->getJSON();
        $query = $this->reqModel->getDetailsByPatientName(['branchId'=>$payload->branch]);
        $num = 0;
        foreach ($query as $key => $value) {
            
            $interviewInfo = json_decode($value->interviewForm);
            $patientInfo = json_decode($value->patientForm);
            if($payload->search == $patientInfo->firstName){
                $list['list'][$num] = [
                    "key" => $value->id,
                    "rerun" => $value->isRerun,
                    "appStatus" =>  $value->status,
                    "referenceId" => $value->referenceId,
                    "name" => $patientInfo->firstName .' '. $patientInfo->lastName .' '. $patientInfo->suffix,
                    "age" =>  $patientInfo->age,
                    "gender" =>  $patientInfo->sex,
                    "dateOfInterview" =>  date('Y-m-d', strtotime($interviewInfo->interviewDate)),
                    "drUnit" =>  $interviewInfo->drUnit,
                    "typeOfClient" =>  $interviewInfo->clientType,
                    "status" =>  $value->statusDescription,
                ];

                $num += 1;
            }

        }
        
        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function getApplicationDetails($id){

        $query = $this->reqModel->getDetails(["id" => $id]);
        $query->interviewForm = json_decode($query->interviewForm);
        $query->patientForm = json_decode($query->patientForm);
        $query->contactTracing = json_decode($query->contactTracing);
        if($query->caseForm != ''){
            $query->caseForm = json_decode($query->caseForm);
        }
        if($query->testResult != ''){
            $query->testResult = json_decode($query->testResult);
        }
        


        if($query){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($query));
        } else {
            $response = [
                'title' => 'Error',
                'message' => $query
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }
    

}