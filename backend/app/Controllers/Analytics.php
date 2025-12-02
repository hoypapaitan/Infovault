<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\AnalyticsModel;
use App\Models\DocumentModel;
use App\Models\GraduatesModel;
use \Firebase\JWT\JWT;

class Analytics extends BaseController
{
    protected $analyticsModel;
    protected $documentModel;
    protected $graduatesModel;

    public function __construct(){
        $this->analyticsModel = new AnalyticsModel();
        $this->documentModel = new DocumentModel();
        $this->graduatesModel = new GraduatesModel(); 
    }

    // ... [addAnalyticsData and deleteAnalyticsData remain unchanged] ...

    public function getDashboardSummary(){
        $range = $this->request->getVar('range'); 
        $fromReq = $this->request->getVar('from');
        $toReq = $this->request->getVar('to');

        // Fallback for JSON body
        if (!$range && !$fromReq) {
            $json = $this->request->getJSON();
            if ($json) {
                $range = $json->range ?? 'all'; 
                $fromReq = $json->from ?? null;
                $toReq = $json->to ?? null;
            }
        }

        // Default to 'all' if no range provided
        if (!$range) {
            $range = 'all';
        }

        $currentYear = date('Y');

        // --- DATE RANGE LOGIC ---
        // Defaults to wide range (1900-3000) to ensure we capture all your data
        if ($range === 'all' && !$fromReq && !$toReq) {
            $fromYear = 1900;
            $toYear = 3000;
        } else {
            $toYear = $toReq ?? 3000; 
            
            if ($fromReq) {
                $fromYear = $fromReq;
            } else {
                switch ($range) {
                    case '1year':
                        $fromYear = $currentYear - 1;
                        $toYear = $currentYear;
                        break;
                    case '3years':
                        $fromYear = $currentYear - 3;
                        $toYear = $currentYear;
                        break;
                    case '5years':
                        $fromYear = $currentYear - 5;
                        $toYear = $currentYear;
                        break;
                    default:
                        $fromYear = 1900;
                        $toYear = 3000;
                        break;
                }
            }
        }
        

        $actualGraduateStats = $this->getActualGraduatesSummary($fromYear, $toYear);

        $yearlyTrends = [];
        if (isset($actualGraduateStats['yearly'])) {
            foreach($actualGraduateStats['yearly'] as $year => $counts) {
                $yearlyTrends[$year] = $counts['male'] + $counts['female'];
            }
        }

        $enrollmentStats = $this->getEnrollmentSummary($fromYear, $toYear);
        
        $genderAnalysis = $this->getGenderAnalysis($fromYear, $toYear); 
        
        $result = [
            'status' => 'success',
            'summary' => [
                'graduates' => $actualGraduateStats,
                'yearlyTrends' => $yearlyTrends
            ],
            'courseDistribution' => $this->getCourseDistributionFromGraduates($fromYear, $toYear),
            'genderAnalysis' => $genderAnalysis,
            'courseMetrics' => $this->getCourseMetricsFromGraduates($fromYear, $toYear),
            'achievementAnalysis' => $this->getAchievementAnalysis($fromYear, $toYear),
            'timeRange' => ['from' => $fromYear, 'to' => $toYear]
        ];
        
        return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode($result));
    }
    
    public function getCourseAnalytics(){
        $range = $this->request->getVar('range'); 
        $fromReq = $this->request->getVar('from');
        $toReq = $this->request->getVar('to');
        $courseReq = $this->request->getVar('course');

        if (!$range && !$fromReq) {
            $data = $this->request->getJSON();
            if ($data) {
                $fromReq = $data->from ?? null;
                $toReq = $data->to ?? null;
                $courseReq = $data->course ?? null;
                $range = $data->range ?? null;
            }
        }

        $currentYear = date('Y');

        if ((!$range || $range === 'all') && !$fromReq) {
            $fromYear = 1900;
            $toYear = 3000;
        } else {
            $toYear = $toReq ?? 3000;
            if ($fromReq) {
                $fromYear = $fromReq;
            } else {
                if ($range === '1year') { $fromYear = $currentYear - 1; $toYear = $currentYear; }
                elseif ($range === '3years') { $fromYear = $currentYear - 3; $toYear = $currentYear; }
                elseif ($range === '5years') { $fromYear = $currentYear - 5; $toYear = $currentYear; }
                else { $fromYear = 1900; $toYear = 3000; }
            }
        }

        $course = $courseReq ?? null;
        
        $query = $this->graduatesModel->select('yearGraduated as yearFrom, course, gender');
        
        if($course && $course !== 'all'){
            $query = $query->where('course', $course);
        }
        
        $analytics = $query->where('yearGraduated >=', $fromYear)
                           ->where('yearGraduated <=', $toYear)
                           ->orderBy('yearGraduated', 'ASC')
                           ->orderBy('course', 'ASC')
                           ->findAll();
        
        $courseData = [];
        $yearlyTotals = [];
        
        foreach($analytics as $record){
            // CodeIgniter 4 findAll() returns arrays by default usually, but we handle objects too
            $year = is_array($record) ? $record['yearFrom'] : $record->yearFrom;
            $courseName = is_array($record) ? $record['course'] : $record->course;
            $gender = is_array($record) ? $record['gender'] : $record->gender;
            
            $isMale = (strcasecmp($gender, 'Male') === 0) ? 1 : 0;
            $isFemale = (strcasecmp($gender, 'Female') === 0) ? 1 : 0;
            $total = 1; 
            
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
            $courseData[$courseName]['male'][$year] += $isMale;
            $courseData[$courseName]['female'][$year] += $isFemale;
            
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

    // ... [getFilterOptions and getEnrollmentSummary remain unchanged] ...
    
    // MERGED: Enrollment (AnalyticsModel) + Graduates (GraduatesModel)
    private function getGenderAnalysis($fromYear, $toYear){
        $analysis = [
            'enrollment' => [],
            'graduates' => [] 
        ];

        // 1. Enrollment from CSV Data
        $enrollmentData = $this->analyticsModel->select('yearFrom, SUM(male) as male, SUM(female) as female')
                                             ->where('reportType', 'enrollment')
                                             ->where('yearFrom >=', $fromYear)
                                             ->where('yearFrom <=', $toYear)
                                             ->groupBy('yearFrom')
                                             ->orderBy('yearFrom', 'ASC')
                                             ->get()
                                             ->getResult();

        foreach($enrollmentData as $item){
            $year = $item->yearFrom;
            if(!isset($analysis['enrollment'][$year])){
                $analysis['enrollment'][$year] = ['male' => 0, 'female' => 0];
            }
            $analysis['enrollment'][$year]['male'] += (int)$item->male;
            $analysis['enrollment'][$year]['female'] += (int)$item->female;
        }

        $graduatesData = $this->graduatesModel->select('yearGraduated, gender, COUNT(*) as count')
                                              ->where('yearGraduated >=', $fromYear)
                                              ->where('yearGraduated <=', $toYear)
                                              ->groupBy('yearGraduated, gender')
                                              ->findAll();

        foreach($graduatesData as $row){
            $year = $row['yearGraduated'];
            
            if(!isset($analysis['graduate'][$year])){
                $analysis['graduate'][$year] = ['male' => 0, 'female' => 0];
            }
            
            if($row['gender'] === 'Male'){
                $analysis['graduate'][$year]['male'] += (int)$row['count'];
            } else if ($row['gender'] === 'Female'){
                $analysis['graduate'][$year]['female'] += (int)$row['count'];
            }
        }
        
        return $analysis;
    }
    
    // Calculates summary based on your Schema's `yearGraduated` and `gender`
    private function getActualGraduatesSummary($fromYear, $toYear){
        $graduatesQuery = $this->graduatesModel->select('yearGraduated, gender, COUNT(*) as count')
                                             ->where('yearGraduated >=', $fromYear)
                                             ->where('yearGraduated <=', $toYear)
                                             ->groupBy('yearGraduated, gender')
                                             ->orderBy('yearGraduated', 'ASC')
                                             ->findAll();
        
        $totalMale = 0;
        $totalFemale = 0;
        $yearlyData = [];
        
        foreach($graduatesQuery as $row){
            $year = $row['yearGraduated'];
            if(!isset($yearlyData[$year])){
                $yearlyData[$year] = ['male' => 0, 'female' => 0];
            }
            
            $count = (int)$row['count'];
            
            if($row['gender'] === 'Male'){
                $totalMale += $count;
                $yearlyData[$year]['male'] += $count;
            } else if($row['gender'] === 'Female'){
                $totalFemale += $count;
                $yearlyData[$year]['female'] += $count;
            }
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

    // Matches your Schema's `course`
    private function getCourseMetricsFromGraduates($fromYear, $toYear) {
        $metricsQuery = $this->graduatesModel->select('course, COUNT(*) as total_graduates')
                                             ->where('yearGraduated >=', $fromYear)
                                             ->where('yearGraduated <=', $toYear)
                                             ->groupBy('course')
                                             ->orderBy('total_graduates', 'DESC')
                                             ->findAll();
                                             
        $totalYears = ($toYear - $fromYear) + 1;
        if($totalYears < 1) $totalYears = 1;

        $result = [];
        foreach($metricsQuery as $row) {
            $count = (int)$row['total_graduates'];
            $result[] = [
                'course' => $row['course'],
                'total_graduates' => $count,
                'completion_rate' => 100,
                'avg_graduates_per_year' => round($count / $totalYears, 1),
                'growth_rate' => 0
            ];
        }
        return $result;
    }

    // Matches your Schema's `achievement`
    private function getAchievementAnalysis($fromYear, $toYear) {
        $achievementsQuery = $this->graduatesModel->select('achievement, COUNT(*) as count')
                                                  ->where('yearGraduated >=', $fromYear)
                                                  ->where('yearGraduated <=', $toYear)
                                                  ->where('achievement IS NOT NULL')
                                                  ->where('achievement !=', '')
                                                  ->groupBy('achievement')
                                                  ->findAll();
                                                  
        $totalGrads = $this->graduatesModel->where('yearGraduated >=', $fromYear)
                                           ->where('yearGraduated <=', $toYear)
                                           ->countAllResults();
        
        $result = [];
        $colors = ['#52c41a', '#1890ff', '#722ed1', '#faad14', '#eb2f96'];
        $i = 0;

        foreach($achievementsQuery as $row) {
            $count = (int)$row['count'];
            $result[] = [
                'title' => $row['achievement'],
                'count' => $count,
                'percentage' => $totalGrads > 0 ? round(($count / $totalGrads) * 100, 1) : 0,
                'color' => $colors[$i % count($colors)],
                'type' => strtolower(str_replace(' ', '_', $row['achievement']))
            ];
            $i++;
        }
        return $result;
    }
    
    // Matches your Schema's `course` and `gender`
    private function getCourseDistributionFromGraduates($fromYear, $toYear) {
        $distQuery = $this->graduatesModel->select('course, gender, COUNT(*) as count')
                                          ->where('yearGraduated >=', $fromYear)
                                          ->where('yearGraduated <=', $toYear)
                                          ->groupBy('course, gender')
                                          ->findAll();
        
        $courseData = [];
        foreach($distQuery as $row) {
            if(!isset($courseData[$row['course']])){
                $courseData[$row['course']] = [
                    'name' => $row['course'],
                    'enrollment' => ['male' => 0, 'female' => 0],
                    'graduates' => ['male' => 0, 'female' => 0]
                ];
            }
            
            if($row['gender'] === 'Male'){
                $courseData[$row['course']]['graduates']['male'] += (int)$row['count'];
            } else if($row['gender'] === 'Female'){
                $courseData[$row['course']]['graduates']['female'] += (int)$row['count'];
            }
        }
        return array_values($courseData);
    }
}