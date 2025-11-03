<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\GraduatesModel;

class GraduateAnalytics extends BaseController
{
    protected $graduatesModel;
    
    public function __construct(){
        $this->graduatesModel = new GraduatesModel();
    }

    public function getDashboardSummary(){
        $fromYear = $_GET['from'] ?? date('Y') - 4;
        $toYear = $_GET['to'] ?? date('Y');
        
        // Get graduate statistics
        $graduateStats = $this->getGraduatesSummary($fromYear, $toYear);
        $yearlyTrends = $this->getYearlyTrends($fromYear, $toYear);
        $courseDistribution = $this->getCourseDistribution($fromYear, $toYear);
        $genderAnalysis = $this->getGenderAnalysis($fromYear, $toYear);
        $courseMetrics = $this->getCourseMetrics($fromYear, $toYear);
        
        $result = [
            'graduates' => $graduateStats,
            'yearlyTrends' => $yearlyTrends,
            'courseDistribution' => $courseDistribution,
            'genderAnalysis' => $genderAnalysis,
            'courseMetrics' => $courseMetrics,
            'timeRange' => ['from' => $fromYear, 'to' => $toYear]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    
    public function getFilterOptions(){
        // Get available years from graduates table
        $years = $this->graduatesModel->select('DISTINCT yearGraduated as year')
                                     ->where('yearGraduated IS NOT NULL')
                                     ->where('deleted_at IS NULL')
                                     ->orderBy('yearGraduated', 'DESC')
                                     ->get()
                                     ->getResult();
        
        // Get available courses from graduates table
        $courses = $this->graduatesModel->select('DISTINCT course')
                                       ->where('course IS NOT NULL')
                                       ->where('course !=', '')
                                       ->where('deleted_at IS NULL')
                                       ->orderBy('course', 'ASC')
                                       ->get()
                                       ->getResult();
        
        // Get available batches
        $batches = $this->graduatesModel->select('DISTINCT batch')
                                       ->where('batch IS NOT NULL')
                                       ->where('batch !=', '')
                                       ->where('deleted_at IS NULL')
                                       ->orderBy('batch', 'DESC')
                                       ->get()
                                       ->getResult();
        
        $result = [
            'years' => array_map(function($item) { return $item->year; }, $years),
            'courses' => array_map(function($item) { return $item->course; }, $courses),
            'batches' => array_map(function($item) { return $item->batch; }, $batches),
        ];
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    
    public function getGraduatesList(){
        // Get all graduates with pagination support
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 50;
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $total = $this->graduatesModel->where('deleted_at IS NULL')->countAllResults(false);
        
        // Get graduates data
        $graduates = $this->graduatesModel->where('deleted_at IS NULL')
                                         ->orderBy('yearGraduated', 'DESC')
                                         ->orderBy('fullName', 'ASC')
                                         ->limit($limit, $offset)
                                         ->get()
                                         ->getResultArray();
        
        $result = [
            'status' => 'success',
            'data' => [
                'graduates' => $graduates,
                'pagination' => [
                    'page' => (int)$page,
                    'limit' => (int)$limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    
    private function getGraduatesSummary($fromYear, $toYear){
        // Get total graduates count with gender breakdown using name patterns (basic approach)
        $graduates = $this->graduatesModel->select('
                COUNT(*) as total,
                SUM(CASE 
                    WHEN name LIKE "%a" OR name LIKE "%e" OR name LIKE "%i" 
                    THEN 1 ELSE 0 
                END) as estimated_female,
                SUM(CASE 
                    WHEN name NOT LIKE "%a" AND name NOT LIKE "%e" AND name NOT LIKE "%i" 
                    THEN 1 ELSE 0 
                END) as estimated_male
            ')
            ->where('yearGraduated >=', $fromYear)
            ->where('yearGraduated <=', $toYear)
            ->where('deleted_at IS NULL')
            ->get()
            ->getRow();
        
        // Get yearly breakdown
        $yearlyGraduates = $this->graduatesModel->select('
                yearGraduated,
                COUNT(*) as total,
                SUM(CASE 
                    WHEN name LIKE "%a" OR name LIKE "%e" OR name LIKE "%i" 
                    THEN 1 ELSE 0 
                END) as estimated_female,
                SUM(CASE 
                    WHEN name NOT LIKE "%a" AND name NOT LIKE "%e" AND name NOT LIKE "%i" 
                    THEN 1 ELSE 0 
                END) as estimated_male
            ')
            ->where('yearGraduated >=', $fromYear)
            ->where('yearGraduated <=', $toYear)
            ->where('deleted_at IS NULL')
            ->groupBy('yearGraduated')
            ->orderBy('yearGraduated', 'ASC')
            ->get()
            ->getResult();
        
        return [
            'total' => [
                'combined' => (int)$graduates->total,
                'male' => (int)$graduates->estimated_male,
                'female' => (int)$graduates->estimated_female
            ],
            'yearly' => $yearlyGraduates
        ];
    }
    
    private function getYearlyTrends($fromYear, $toYear){
        $trends = $this->graduatesModel->select('yearGraduated, COUNT(*) as total')
                                      ->where('yearGraduated >=', $fromYear)
                                      ->where('yearGraduated <=', $toYear)
                                      ->where('deleted_at IS NULL')
                                      ->groupBy('yearGraduated')
                                      ->orderBy('yearGraduated', 'ASC')
                                      ->get()
                                      ->getResult();
        
        $trendData = [];
        foreach($trends as $trend){
            $trendData[$trend->yearGraduated] = (int)$trend->total;
        }
        
        return $trendData;
    }
    
    private function getCourseDistribution($fromYear, $toYear){
        $distribution = $this->graduatesModel->select('
                course,
                COUNT(*) as total,
                SUM(CASE 
                    WHEN name LIKE "%a" OR name LIKE "%e" OR name LIKE "%i" 
                    THEN 1 ELSE 0 
                END) as estimated_female,
                SUM(CASE 
                    WHEN name NOT LIKE "%a" AND name NOT LIKE "%e" AND name NOT LIKE "%i" 
                    THEN 1 ELSE 0 
                END) as estimated_male
            ')
            ->where('yearGraduated >=', $fromYear)
            ->where('yearGraduated <=', $toYear)
            ->where('deleted_at IS NULL')
            ->groupBy('course')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResult();
        
        $courseData = [];
        foreach($distribution as $item){
            $courseData[] = [
                'name' => $item->course,
                'total' => (int)$item->total,
                'male' => (int)$item->estimated_male,
                'female' => (int)$item->estimated_female
            ];
        }
        
        return $courseData;
    }
    
    private function getGenderAnalysis($fromYear, $toYear){
        $genderData = $this->graduatesModel->select('
                yearGraduated,
                SUM(CASE 
                    WHEN name LIKE "%a" OR name LIKE "%e" OR name LIKE "%i" 
                    THEN 1 ELSE 0 
                END) as estimated_female,
                SUM(CASE 
                    WHEN name NOT LIKE "%a" AND name NOT LIKE "%e" AND name NOT LIKE "%i" 
                    THEN 1 ELSE 0 
                END) as estimated_male
            ')
            ->where('yearGraduated >=', $fromYear)
            ->where('yearGraduated <=', $toYear)
            ->where('deleted_at IS NULL')
            ->groupBy('yearGraduated')
            ->orderBy('yearGraduated', 'ASC')
            ->get()
            ->getResult();
        
        $analysis = [];
        foreach($genderData as $item){
            $analysis[$item->yearGraduated] = [
                'male' => (int)$item->estimated_male,
                'female' => (int)$item->estimated_female
            ];
        }
        
        return $analysis;
    }
    
    private function getCourseMetrics($fromYear, $toYear){
        $metrics = $this->graduatesModel->select('
                course,
                COUNT(*) as total_graduates,
                COUNT(DISTINCT yearGraduated) as years_active,
                AVG(CASE WHEN yearGraduated != "" THEN 1 ELSE 0 END) * 100 as completion_rate
            ')
            ->where('yearGraduated >=', $fromYear)
            ->where('yearGraduated <=', $toYear)
            ->where('deleted_at IS NULL')
            ->groupBy('course')
            ->orderBy('total_graduates', 'DESC')
            ->get()
            ->getResult();
        
        $courseMetrics = [];
        foreach($metrics as $metric){
            $courseMetrics[] = [
                'course' => $metric->course,
                'total_graduates' => (int)$metric->total_graduates,
                'years_active' => (int)$metric->years_active,
                'avg_graduates_per_year' => round((int)$metric->total_graduates / max(1, (int)$metric->years_active), 1),
                'completion_rate' => round((float)$metric->completion_rate, 1)
            ];
        }
        
        return $courseMetrics;
    }
}