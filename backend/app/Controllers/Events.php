<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\EventModel;
use \Firebase\JWT\JWT;

class Events extends BaseController
{
    public function __construct(){
        //Models
        $this->eventModel = new EventModel();
    }

    public function deleteEventCalendar(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();

        $where = [
            'id' => $data->dataId
        ];
        // before delete validate if there is anyone applied
        
        
        //Select Query for finding User Information
        $query = $this->eventModel->deleteEvent($where);

        if($query){

            $response = [
                'title' => 'Delete successful',
                'message' => 'Data is deleted'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            $response = [
                'title' => 'Update Failed!',
                'message' => 'Please check your data and connect to your Admin'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }
    public function editEventCalendar(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        $setData = json_decode(json_encode($data->form), true);


        $where = [
            "id" => $data->dataId
        ];
    
        $query = $this->eventModel->updateEventInfo($where, $setData);

        if($query){

            $response = [
                'title' => 'Update Information',
                'message' => 'Event data has been successfully updated.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            $response = [
                'title' => 'Registration Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        // print_r($data);
        // exit();
        
    }
    public function addEventCalendar(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        $data->eventDate = json_encode($data->eventDate);
        $data = json_decode(json_encode($data), true);
        
        $query = $this->eventModel->insert($data);

        if($query){

            $response = [
                'title' => 'Data Added',
                'message' => 'Event successfully added'
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

    public function getListEvents(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        
        $query = $this->eventModel->where([
            "month" => $data->month,
            "year" => $data->year,
        ])->get()->getResult();

        $list = [];

        foreach ($query as $key => $value){
            $list[$key] = $value;
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

}