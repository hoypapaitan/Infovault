<?php

namespace App\Models;

use CodeIgniter\Model;

class EvaluationModel extends Model
{
    protected $table      = 'tblevaluation_templates';
    protected $responseTable      = 'tblevaluation_response';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'order', 
        'title', 
        'questionaire', 
        'noScore', 
        'partlyScore', 
        'yesScore', 
        'toolTip', 
        'eventId', 
        'createdBy',
        'isCounted'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function checkDuplicateResponse($where){
        $query = $this->db->table($this->responseTable)->where($where)->get();
        $results = $query->getRow();

        return $results;
    }
    public function checkResponse($where){
        $query = $this->db->table($this->responseTable)->where($where)->get();
        $results = $query->getResult();

        return $results;
    }
    public function addResponse($payload){
        $query = $this->db->table($this->responseTable)->insert($payload);
        return $query ? true : false;
    }

    public function queryAnswers($where){

        $sql = "SELECT * FROM ".$this->table." a LEFT JOIN ". $this->responseTable ." b ON a.id = b.questionId WHERE a.eventId = :eventId: ORDER BY a.order ";
       
        $query = $this->db->query($sql, $where);
        $results = $query->getResult();

        return $results;
    }

}