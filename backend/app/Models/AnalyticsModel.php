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