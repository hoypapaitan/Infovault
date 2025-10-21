<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table      = 'tblevents';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'title', 
        'description', 
        'eventCode', 
        'eventDate', 
        'month',
        'days',
        'year',
        'createdBy'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function updateEventInfo($where, $setData){

        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;

    }

    public function deleteEvent($where){
        $query = $this->db->table($this->table)->where($where)->delete();
        return $query ? true : false;
    }

}