<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\AnalyticsModel;
use App\Models\DocumentModel;
use \Firebase\JWT\JWT;

class Analytics extends BaseController
{
    public function __construct(){
        //Models
        $this->analyticsModel = new AnalyticsModel();
        $this->documentModel = new DocumentModel();
    }

    public function addAnalyticsData(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();

        // Check if there was existing Year Data and Department
        foreach ($data->csv as $key => $value){
            $check = $this->analyticsModel->where([
                "yearFrom"=>$value->yearFrom,
                "term"=>$value->term,
                "course"=>$value->course,
                "classYear"=>$value->classYear,
                "reportType"=>$value->reportType,
            ])->get()->getRow();
            

            if(!$check){
                $payload = json_decode(json_encode($value), true);
                $this->analyticsModel->insert($payload);
            } else {
                print_r($check);
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

    public function deleteAnalyticsData(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();

        $where = [
            'id' => $data->dataId
        ];
        // before delete validate if there is anyone applied
        
        
        //Select Query for finding User Information
        $query = $this->analyticsModel->deleteAnalyticData($where);

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

    public function getGraphAnalyticOptions(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        
        $query = $data->reportType !== 'employee' ? $this->analyticsModel->getOptionsGraph([
            "yearFrom" => $data->from,
            "yearTo" => $data->to,
            "reportType" => $data->reportType,
        ]) : $this->analyticsModel->getOptionsEmployeeGraph([
            "yearFrom" => $data->from,
            "yearTo" => $data->to,
            "reportType" => $data->reportType,
        ]);

        $list = [];

        foreach ($query as $key => $value){
            if($data->reportType === 'employee'){
                $list[$value->term][$key] = [
                    "label" => $value->course,
                    "value" => $value->course,
                ];
            } else {
                $list[$value->term][$key] = [
                    "label" => $data->reportType !== 'employee' ? $value->course : $value->term,
                    "value" => $data->reportType !== 'employee' ? $value->course : $value->term
                ];
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
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }

    public function getAllAnalyticsData(){

        // $header = $this->request->getHeader("");
        
        $list = [];
        $list['list'] = $this->analyticsModel->get()->getResult();

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

    public function getDashboard(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        $list = [];
        $graduates = 0;
        $undergraduates = 0;
        $enrollment = 0;
        $employement = 0;
        $vacant = 0;

        $gradMale = 0;
        $gradFemale = 0;
        $enrMale = 0;
        $enrFemale = 0;
        $empMale = 0;
        $empFemale = 0;

        $query = $this->analyticsModel->getDashboardAnalytics([
            "yearFrom" => $data->from,
            "yearTo" => $data->to,
        ]);
        $resources = $this->documentModel->get()->getResult();


        // print_r($query);
        // exit();
        foreach ($query as $key => $value){
            
            if($value->reportType === "graduate"){
                $graduates += (int)$value->male;
                $gradMale += (int)$value->male;
                $graduates += (int)$value->female;
                $gradFemale += (int)$value->female;
            } else if($value->reportType === "enrollment"){
                $enrollment += (int)$value->male;
                $enrMale += (int)$value->male;
                $enrollment += (int)$value->female;
                $enrFemale += (int)$value->female;
            } else if($value->reportType === "employee"){
                $employement += (int)$value->male;
                $empMale += (int)$value->male;
                $employement += (int)$value->female;
                $empFemale += (int)$value->female;
                $vacant += (int)$value->vacant;
            }
        }    
        
        $list = [
            'graduates' => $graduates,
            'undergrads' => $undergraduates,
            'enrollment' => $enrollment,
            'employee' => $employement,
            'vacant' => $vacant,
            'resource' => sizeof($resources),
            'genders' => [
                "graduate" => [
                    "male" => $gradMale,
                    "female" => $gradFemale,
                ],
                "enrollment" => [
                    "male" => $enrMale,
                    "female" => $enrFemale,
                ],
                "employee" => [
                    "male" => $empMale,
                    "female" => $empFemale,
                ]
            ]
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
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }

    public function getGraphAnalytics(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();

        $where = [
            "yearFrom" => $data->from,
            "yearTo" => $data->to,
            "reportType" => $data->reportType,
            "course" => $data->department,
            "term" => $data->course,
        ];

        $query = $this->analyticsModel->getAllYearRangeData($where);
        $list = [];
        foreach ($query as $key => $value){
            if($data->reportType === 'enrollment'){
                $list[$key] = (object)[
                    "group" => (object)[
                        "title"=> $value->yearFrom ." (". $value->classYear .")",
                        "cols"=> 2,
                    ],
                    "series" => [
                        (object)[
                            "x" =>  "Male",
                            "fillColor" =>  "#3b82f6",
                            "y" =>  (int)$value->male,
                        ],
                        (object)[
                            "x" =>  "Female",
                            "fillColor" =>  "#f43f5e",
                            "y" =>  (int)$value->female,
                        ],
                    ]
                ];
            } else if($data->reportType === 'employee'){
                // $list[$value->term] = 
                // $list[$key] = $value;
                $list[$key] = (object)[
                    "group" => $value->course,
                    "series" => (object)[
                        "name" =>  $value->course,
                        "data" =>  [(int)$value->male, (int)$value->female, (int)$value->vacant],
                    ]
                ];
            } else if($data->reportType === 'graduate'){
                $list[$key]["male"] = (int)$value->male;
                $list[$key]["female"] = (int)$value->female;
                $list[$key]["categories"] = $value->yearFrom ." (". $value->classYear ." - ". $value->term .")";
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
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }

    public function getGraphDashboardAnalytics(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();

        if($data->page === "analytics"){
            $where = [
                "yearFrom" => $data->from,
                "yearTo" => $data->to,
                "reportType" => $data->reportType,
                "course" => $data->course,
                "page" => $data->page,
            ];
        } else {
            $where = [
                "yearFrom" => $data->from,
                "yearTo" => $data->to,
                "reportType" => $data->reportType,
                "page" => $data->page,
            ];
        }
        

        $query = $this->analyticsModel->getDashboardGraphAnalytics($where);
        $list = [];
        $overall = [];

        foreach ($query as $key => $value){
            if($data->reportType === 'enrollment'){
                $list[$value->yearFrom][$key] = (object)[
                    (object)[
                        "x" =>  "Male",
                        "fillColor" =>  "#3b82f6",
                        "y" =>  (int)$value->male,
                    ],
                    (object)[
                        "x" =>  "Female",
                        "fillColor" =>  "#f43f5e",
                        "y" =>  (int)$value->female,
                    ],
                ];
            } else if($data->reportType === 'graduate'){
                $list[$value->yearFrom][$key] = (object)[
                    "male" => (int)$value->male,
                    "female" => (int)$value->female,
                ];
                // $list[$key]["male"] = (int)$value->male;
                // $list[$key]["female"] = (int)$value->female;
                // $list[$key]["categories"] = $value->yearFrom;
            }
            $overall[$value->course][$key] = (object)[
                "male" => (int)$value->male,
                "female" => (int)$value->female,
            ]; 
            
        }
        
        

        if($list){
            $result = [
                "list" => $list,
                "overall" => $overall
            ];
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($result));
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