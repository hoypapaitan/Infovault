<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\EvaluationModel;
use \Firebase\JWT\JWT;

class Evaluation extends BaseController
{
    public function __construct(){
        //Models
        $this->evalModel = new EvaluationModel();
    }

    public function addEventQuestionaire(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        $data = json_decode(json_encode($data), true);
        
        $query = $this->evalModel->insert($data);

        if($query){

            $response = [
                'title' => 'Data Added',
                'message' => 'Data successfully added to event evaluation questionaire'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            $response = [
                'error' => 400,
                'title' => 'Registration Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        // print_r($data);
        // exit();
        
    }

    public function getListEventQuestionaire(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        
        $query = $this->evalModel->where([
            "eventId" => $data->eventId
        ])->orderBy('order')->get()->getResult();

        $list = [];

        foreach ($query as $key => $value){
            $list[$key] = [
                "id" => $value->id,
                "eventId" => $value->eventId,
                "order" => $value->order,
                "isCounted" => $value->isCounted,
                "noScore" => $value->noScore,
                "partlyScore" => $value->partlyScore,
                "yesScore" => $value->yesScore,
                "scoring" => $value->noScore .' / '. $value->partlyScore .' / '. $value->yesScore,
                "scoringDesc" =>"No / Partly Yes / Yes",
                "title" => $value->title,
                "question" => $value->questionaire,
                "responseCol" => "",
                "scoreCol" => "",
                "remarks" => "",
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
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }

    public function getListEventQuestionaireResponse(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        
        

        $query = $this->evalModel->queryAnswers([
            "eventId" => $data->eventId
        ]);

        $list = [];

        foreach ($query as $key => $value){
            $list[$key] = [
                "id" => $value->id,
                "eventId" => $value->eventId,
                "order" => $value->order,
                "isCounted" => $value->isCounted,
                "noScore" => $value->noScore,
                "partlyScore" => $value->partlyScore,
                "yesScore" => $value->yesScore,
                "scoring" => $value->noScore .' / '. $value->partlyScore .' / '. $value->yesScore,
                "scoringDesc" =>"No / Partly Yes / Yes",
                "title" => $value->title,
                "question" => $value->questionaire,
                "responseCol" => $value->responseCol,
                "scoreCol" => $value->scoreCol,
                "remarks" => $value->remarks,
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
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }

    public function addEventResponse(){
        $data = $this->request->getJSON();

        // Check if there was existing Year Data and Department
        foreach ($data->csv as $key => $value){
            $check = $this->evalModel->checkDuplicateResponse([
                "createdBy"=>$value->eid,
                "questionId"=>$value->questionId,
                "eventId"=>$value->eventId,
            ]);
            

            if(!$check){
                // $payload = json_decode(json_encode($value), true);
                $payload = [
                    "eventId" => $value->eventId, 
                    "questionId" => $value->questionId, 
                    "createdBy" => $value->eid, 
                    "responseCol" => $value->responseCol, 
                    "scoreCol" => $value->scoreCol, 
                    "remarks" => $value->remarks, 
                ];
                $this->evalModel->addResponse($payload);
            }
            
        }
        
        $response = [
            'title' => 'Data Added',
            'message' => 'Data successfully added to analytics data'
        ];

        return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode($response));
        
    }

    public function getEventResponse(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        
        if(isset($data->userId)){
            $query = $this->evalModel->checkResponse([
                "eventId" => $data->eventId,
                "createdBy" => $data->userId,
            ]);
        } else {
            $query = $this->evalModel->checkResponse([
                "eventId" => $data->eventId
            ]);
        }
        


        if($query){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($query));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }

    


}