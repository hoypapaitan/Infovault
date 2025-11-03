<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalyticsModel extends Model
{
    protected $table      = 'tblanalytics';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'course', 
        'schoolYear', 
        'yearFrom', 
        'yearTo', 
        'reportType', 
        'term', 
        'classYear', 
        'male',
        'female',
        'vacant',
        'createdBy'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Enhanced analytics methods for comprehensive dashboard
    public function getGraduatesAnalytics($fromYear, $toYear){
        $sql = "SELECT 
                    yearGraduated as year,
                    course,
                    COUNT(*) as total,
                    SUM(CASE WHEN tblusers.sex = 'Male' THEN 1 ELSE 0 END) as male,
                    SUM(CASE WHEN tblusers.sex = 'Female' THEN 1 ELSE 0 END) as female
                FROM tblgraduates 
                LEFT JOIN tblusers ON tblgraduates.created_by = tblusers.id
                WHERE yearGraduated BETWEEN :fromYear: AND :toYear: 
                AND tblgraduates.deleted_at IS NULL
                GROUP BY yearGraduated, course
                ORDER BY yearGraduated ASC, course ASC";
                
        $query = $this->db->query($sql, [
            'fromYear' => $fromYear,
            'toYear' => $toYear
        ]);
        
        return $query->getResult();
    }
    
    public function getCombinedDashboardStats($fromYear, $toYear){
        // Get analytics data
        $analyticsQuery = $this->select('*')
                              ->where('yearFrom >=', $fromYear)
                              ->where('yearFrom <=', $toYear)
                              ->orderBy('yearFrom', 'ASC')
                              ->get()
                              ->getResult();
        
        // Get actual graduates data
        $graduatesQuery = $this->getGraduatesAnalytics($fromYear, $toYear);
        
        return [
            'analytics' => $analyticsQuery,
            'graduates' => $graduatesQuery
        ];
    }
    
    public function getComparisonData($fromYear, $toYear){
        // Enrollment vs Graduation comparison
        $sql = "SELECT 
                    a.yearFrom as year,
                    a.course,
                    SUM(CASE WHEN a.reportType = 'enrollment' THEN a.male + a.female ELSE 0 END) as enrolled,
                    SUM(CASE WHEN a.reportType = 'graduate' THEN a.male + a.female ELSE 0 END) as graduated_analytics,
                    COALESCE(g.actual_graduates, 0) as actual_graduates
                FROM tblanalytics a
                LEFT JOIN (
                    SELECT 
                        yearGraduated as year,
                        course,
                        COUNT(*) as actual_graduates
                    FROM tblgraduates 
                    WHERE yearGraduated BETWEEN :fromYear: AND :toYear: 
                    AND deleted_at IS NULL
                    GROUP BY yearGraduated, course
                ) g ON a.yearFrom = g.year AND a.course = g.course
                WHERE a.yearFrom BETWEEN :fromYear: AND :toYear:
                GROUP BY a.yearFrom, a.course
                ORDER BY a.yearFrom ASC, a.course ASC";
                
        $query = $this->db->query($sql, [
            'fromYear' => $fromYear,
            'toYear' => $toYear
        ]);
        
        return $query->getResult();
    }
    
    public function getCoursePerformanceMetrics($fromYear, $toYear){
        $sql = "SELECT 
                    course,
                    SUM(CASE WHEN reportType = 'enrollment' THEN male + female ELSE 0 END) as total_enrolled,
                    SUM(CASE WHEN reportType = 'graduate' THEN male + female ELSE 0 END) as analytics_graduated,
                    COUNT(DISTINCT CASE WHEN reportType = 'enrollment' THEN yearFrom END) as years_active,
                    AVG(CASE WHEN reportType = 'enrollment' THEN male + female END) as avg_enrollment_per_year,
                    (SUM(CASE WHEN reportType = 'graduate' THEN male + female ELSE 0 END) / 
                     NULLIF(SUM(CASE WHEN reportType = 'enrollment' THEN male + female ELSE 0 END), 0) * 100) as graduation_rate
                FROM tblanalytics 
                WHERE yearFrom BETWEEN :fromYear: AND :toYear:
                GROUP BY course
                ORDER BY total_enrolled DESC";
                
        $query = $this->db->query($sql, [
            'fromYear' => $fromYear,
            'toYear' => $toYear
        ]);
        
        return $query->getResult();
    }

    public function getDashboardAnalytics($where){

        // $query = $this->db->table($this->table)->where($where)->get();
        // $results = $query->getResult();

        // return $results;
        $sql = "SELECT * FROM ".$this->table." WHERE yearFrom BETWEEN :yearFrom: AND :yearTo:";
   
        $query = $this->db->query($sql, $where);
        $results = $query->getResult();

        return $results;
    }
    public function deleteAnalyticData($where){
        $query = $this->db->table($this->table)->where($where)->delete();
        return $query ? true : false;
    }
    public function getDashboardGraphAnalytics($where){

        // $query = $this->db->table($this->table)->where($where)->get();
        // $results = $query->getResult();

        // return $results;
        if($where["page"] === "analytics"){
            $sql = "SELECT * FROM ".$this->table." WHERE yearFrom BETWEEN :yearFrom: AND :yearTo: AND reportType = :reportType: AND course = :course:";
        } else {
            $sql = "SELECT * FROM ".$this->table." WHERE yearFrom BETWEEN :yearFrom: AND :yearTo: AND reportType = :reportType:";
        }
   
        $query = $this->db->query($sql, $where);
        $results = $query->getResult();

        return $results;
    }
    public function getAllYearRangeData($where){

        // $query = $this->db->table($this->table)->where($where)->get();
        // $results = $query->getResult();

        // return $results;
        // $sql = "";
        // if($where['reportType'] === 'employee'){
        //     $sql = "SELECT * FROM ".$this->table." WHERE yearFrom BETWEEN :yearFrom: AND :yearTo: AND reportType = :reportType: AND course = :course: AND term = :term:";
        // } else {
        //     $sql = "SELECT * FROM ".$this->table." WHERE yearFrom BETWEEN :yearFrom: AND :yearTo: AND reportType = :reportType: AND course = :course:";
        // }
        $sql = "SELECT * FROM ".$this->table." WHERE yearFrom BETWEEN :yearFrom: AND :yearTo: AND reportType = :reportType: AND course = :course: AND term = :term:";
       
        $query = $this->db->query($sql, $where);
        $results = $query->getResult();

        return $results;
    }

    public function getOptionsGraph($where){

        $sql = "SELECT term,course FROM ".$this->table." WHERE yearFrom BETWEEN :yearFrom: AND :yearTo: AND reportType = :reportType:";
       
        $query = $this->db->query($sql, $where);
        $results = $query->getResult();

        return $results;
    }
    public function getOptionsEmployeeGraph($where){

        $sql = "SELECT term, course FROM ".$this->table." WHERE yearFrom BETWEEN :yearFrom: AND :yearTo: AND reportType = :reportType:";
       
        $query = $this->db->query($sql, $where);
        $results = $query->getResult();

        return $results;
    }

}