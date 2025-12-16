<?php

namespace App\Models;

use CodeIgniter\Model;

class GraduatesModel extends Model
{
    // Use the tblgraduate table provided by the user
    protected $table      = 'tblgraduates';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    // fields based on provided schema
    protected $allowedFields = [
        'studentId',
        'name',
        'address',
        'program',
        'yearGraduated',
        'advisoryId',
        'section',
        'school',
        'course',
        'major',
        'achievement',
        'additionalAchievement',
        'gender',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // no validation rules per request
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;

    public function deleteGraduate($where){
        $query = $this->db->table($this->table)->where($where)->delete();
        return $query ? true : false;
    }

}
