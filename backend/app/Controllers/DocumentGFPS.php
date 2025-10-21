<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\DocumentModel;
use \Firebase\JWT\JWT;

class DocumentGFPS extends BaseController
{
    public function __construct(){
        //Models
        $this->documentModel = new DocumentModel();
    }

    public function addDocumentContent(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        $data = json_decode(json_encode($data), true);
        
        $query = $this->documentModel->insert($data);

        if($query){

            $response = [
                'title' => 'Data Added',
                'message' => 'Data successfully added to analytics data'
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
    }

    public function deleteDocumentContent(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        
        $query = $this->documentModel->where('id', $data->cId)->delete();

        if($query){

            $response = [
                'title' => 'Content Deleted',
                'message' => 'Data successfully deleted'
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
    }

    public function getDocumentList(){
        //Get API Request Data from NuxtJs
        
        $docs = [];
        $docs['list'] = $this->documentModel->get()->getResult();

        if($docs){

            $response = [
                'title' => 'Data Added',
                'message' => 'Data successfully added to analytics data'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($docs));
            
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
    }

}