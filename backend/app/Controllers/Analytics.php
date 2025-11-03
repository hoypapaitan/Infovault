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

    // Enhanced Dashboard Analytics Methods
    public function getDashboardSummary(){
        $data = $this->request->getJSON();
        $fromYear = $data->from ?? date('Y') - 4;
        $toYear = $data->to ?? date('Y');
        
        // Get comprehensive analytics including actual graduates data
        $combinedStats = $this->analyticsModel->getCombinedDashboardStats($fromYear, $toYear);
        $comparisonData = $this->analyticsModel->getComparisonData($fromYear, $toYear);
        $courseMetrics = $this->analyticsModel->getCoursePerformanceMetrics($fromYear, $toYear);
        
        // Get summary statistics
        $enrollmentStats = $this->getEnrollmentSummary($fromYear, $toYear);
        $graduateStats = $this->getGraduatesSummary($fromYear, $toYear);
        $actualGraduateStats = $this->getActualGraduatesSummary($fromYear, $toYear);
        $trendsData = $this->getTrendsData($fromYear, $toYear);
        $courseDistribution = $this->getCourseDistribution($fromYear, $toYear);
        $genderAnalysis = $this->getGenderAnalysis($fromYear, $toYear);
        
        $result = [
            'enrollment' => $enrollmentStats,
            'graduates' => $graduateStats,
            'actualGraduates' => $actualGraduateStats,
            'trends' => $trendsData,
            'courseDistribution' => $courseDistribution,
            'genderAnalysis' => $genderAnalysis,
            'comparisonData' => $comparisonData,
            'courseMetrics' => $courseMetrics,
            'timeRange' => ['from' => $fromYear, 'to' => $toYear]
        ];
        
        return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode($result));
    }
    
    public function getCourseAnalytics(){
        $data = $this->request->getJSON();
        $fromYear = $data->from ?? date('Y') - 4;
        $toYear = $data->to ?? date('Y');
        $course = $data->course ?? null;
        
        $query = $this->analyticsModel->select('*');
        
        if($course && $course !== 'all'){
            $query = $query->where('course', $course);
        }
        
        $analytics = $query->where('yearFrom >=', $fromYear)
                          ->where('yearFrom <=', $toYear)
                          ->orderBy('yearFrom', 'ASC')
                          ->orderBy('course', 'ASC')
                          ->get()
                          ->getResult();
        
        $courseData = [];
        $yearlyTotals = [];
        
        foreach($analytics as $record){
            $year = $record->yearFrom;
            $courseName = $record->course;
            $total = (int)$record->male + (int)$record->female;
            
            if(!isset($courseData[$courseName])){
                $courseData[$courseName] = [
                    'name' => $courseName,
                    'data' => [],
                    'male' => [],
                    'female' => []
                ];
            }
            
            if(!isset($courseData[$courseName]['data'][$year])){
                $courseData[$courseName]['data'][$year] = 0;
                $courseData[$courseName]['male'][$year] = 0;
                $courseData[$courseName]['female'][$year] = 0;
            }
            
            $courseData[$courseName]['data'][$year] += $total;
            $courseData[$courseName]['male'][$year] += (int)$record->male;
            $courseData[$courseName]['female'][$year] += (int)$record->female;
            
            if(!isset($yearlyTotals[$year])){
                $yearlyTotals[$year] = 0;
            }
            $yearlyTotals[$year] += $total;
        }
        
        $result = [
            'courses' => array_values($courseData),
            'yearlyTotals' => $yearlyTotals,
            'timeRange' => ['from' => $fromYear, 'to' => $toYear]
        ];
        
        return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode($result));
    }
    
    public function getFilterOptions(){
        // Get available years
        $years = $this->analyticsModel->select('DISTINCT yearFrom as year')
                                     ->orderBy('yearFrom', 'DESC')
                                     ->get()
                                     ->getResult();
        
        // Get available courses
        $courses = $this->analyticsModel->select('DISTINCT course')
                                       ->orderBy('course', 'ASC')
                                       ->get()
                                       ->getResult();
        
        // Get available report types
        $reportTypes = $this->analyticsModel->select('DISTINCT reportType')
                                           ->get()
                                           ->getResult();
        
        // Get available terms
        $terms = $this->analyticsModel->select('DISTINCT term')
                                     ->where('term IS NOT NULL')
                                     ->where('term !=', '')
                                     ->get()
                                     ->getResult();
        
        // Get available class years
        $classYears = $this->analyticsModel->select('DISTINCT classYear')
                                          ->where('classYear IS NOT NULL')
                                          ->where('classYear !=', '')
                                          ->orderBy('classYear', 'ASC')
                                          ->get()
                                          ->getResult();
        
        $result = [
            'years' => array_map(function($item) { return $item->year; }, $years),
            'courses' => array_map(function($item) { return $item->course; }, $courses),
            'reportTypes' => array_map(function($item) { return $item->reportType; }, $reportTypes),
            'terms' => array_map(function($item) { return $item->term; }, $terms),
            'classYears' => array_map(function($item) { return $item->classYear; }, $classYears),
        ];
        
        return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode($result));
    }
    
    private function getEnrollmentSummary($fromYear, $toYear){
        $enrollment = $this->analyticsModel->select('SUM(male) as totalMale, SUM(female) as totalFemale, COUNT(*) as totalRecords')
                                          ->where('reportType', 'enrollment')
                                          ->where('yearFrom >=', $fromYear)
                                          ->where('yearFrom <=', $toYear)
                                          ->get()
                                          ->getRow();
        
        $yearlyEnrollment = $this->analyticsModel->select('yearFrom, SUM(male) as male, SUM(female) as female')
                                                ->where('reportType', 'enrollment')
                                                ->where('yearFrom >=', $fromYear)
                                                ->where('yearFrom <=', $toYear)
                                                ->groupBy('yearFrom')
                                                ->orderBy('yearFrom', 'ASC')
                                                ->get()
                                                ->getResult();
        
        return [
            'total' => [
                'male' => (int)$enrollment->totalMale,
                'female' => (int)$enrollment->totalFemale,
                'combined' => (int)$enrollment->totalMale + (int)$enrollment->totalFemale
            ],
            'yearly' => $yearlyEnrollment
        ];
    }
    
    private function getGraduatesSummary($fromYear, $toYear){
        $graduates = $this->analyticsModel->select('SUM(male) as totalMale, SUM(female) as totalFemale, COUNT(*) as totalRecords')
                                         ->where('reportType', 'graduate')
                                         ->where('yearFrom >=', $fromYear)
                                         ->where('yearFrom <=', $toYear)
                                         ->get()
                                         ->getRow();
        
        $yearlyGraduates = $this->analyticsModel->select('yearFrom, SUM(male) as male, SUM(female) as female')
                                               ->where('reportType', 'graduate')
                                               ->where('yearFrom >=', $fromYear)
                                               ->where('yearFrom <=', $toYear)
                                               ->groupBy('yearFrom')
                                               ->orderBy('yearFrom', 'ASC')
                                               ->get()
                                               ->getResult();
        
        return [
            'total' => [
                'male' => (int)$graduates->totalMale,
                'female' => (int)$graduates->totalFemale,
                'combined' => (int)$graduates->totalMale + (int)$graduates->totalFemale
            ],
            'yearly' => $yearlyGraduates
        ];
    }
    
    private function getTrendsData($fromYear, $toYear){
        $trends = $this->analyticsModel->select('yearFrom, reportType, SUM(male + female) as total')
                                      ->where('yearFrom >=', $fromYear)
                                      ->where('yearFrom <=', $toYear)
                                      ->groupBy('yearFrom, reportType')
                                      ->orderBy('yearFrom', 'ASC')
                                      ->get()
                                      ->getResult();
        
        $trendData = [];
        foreach($trends as $trend){
            if(!isset($trendData[$trend->reportType])){
                $trendData[$trend->reportType] = [];
            }
            $trendData[$trend->reportType][$trend->yearFrom] = (int)$trend->total;
        }
        
        return $trendData;
    }
    
    private function getCourseDistribution($fromYear, $toYear){
        $distribution = $this->analyticsModel->select('course, reportType, SUM(male) as male, SUM(female) as female')
                                            ->where('yearFrom >=', $fromYear)
                                            ->where('yearFrom <=', $toYear)
                                            ->groupBy('course, reportType')
                                            ->orderBy('course', 'ASC')
                                            ->get()
                                            ->getResult();
        
        $courseData = [];
        foreach($distribution as $item){
            if(!isset($courseData[$item->course])){
                $courseData[$item->course] = [
                    'name' => $item->course,
                    'enrollment' => ['male' => 0, 'female' => 0],
                    'graduates' => ['male' => 0, 'female' => 0]
                ];
            }
            
            if($item->reportType === 'enrollment'){
                $courseData[$item->course]['enrollment']['male'] += (int)$item->male;
                $courseData[$item->course]['enrollment']['female'] += (int)$item->female;
            } else if($item->reportType === 'graduate'){
                $courseData[$item->course]['graduates']['male'] += (int)$item->male;
                $courseData[$item->course]['graduates']['female'] += (int)$item->female;
            }
        }
        
        return array_values($courseData);
    }
    
    private function getGenderAnalysis($fromYear, $toYear){
        $genderData = $this->analyticsModel->select('yearFrom, reportType, SUM(male) as male, SUM(female) as female')
                                          ->where('yearFrom >=', $fromYear)
                                          ->where('yearFrom <=', $toYear)
                                          ->groupBy('yearFrom, reportType')
                                          ->orderBy('yearFrom', 'ASC')
                                          ->get()
                                          ->getResult();
        
        $analysis = [
            'enrollment' => [],
            'graduates' => []
        ];
        
        foreach($genderData as $item){
            $type = $item->reportType;
            $year = $item->yearFrom;
            
            if(!isset($analysis[$type][$year])){
                $analysis[$type][$year] = ['male' => 0, 'female' => 0];
            }
            
            $analysis[$type][$year]['male'] += (int)$item->male;
            $analysis[$type][$year]['female'] += (int)$item->female;
        }
        
        return $analysis;
    }
    
    private function getActualGraduatesSummary($fromYear, $toYear){
        // Get actual graduates from tblgraduates table
        $graduatesQuery = $this->analyticsModel->getGraduatesAnalytics($fromYear, $toYear);
        
        $totalMale = 0;
        $totalFemale = 0;
        $yearlyData = [];
        
        foreach($graduatesQuery as $graduate){
            $totalMale += (int)$graduate->male;
            $totalFemale += (int)$graduate->female;
            
            if(!isset($yearlyData[$graduate->year])){
                $yearlyData[$graduate->year] = ['male' => 0, 'female' => 0];
            }
            
            $yearlyData[$graduate->year]['male'] += (int)$graduate->male;
            $yearlyData[$graduate->year]['female'] += (int)$graduate->female;
        }
        
        return [
            'total' => [
                'male' => $totalMale,
                'female' => $totalFemale,
                'combined' => $totalMale + $totalFemale
            ],
            'yearly' => $yearlyData
        ];
    }

}