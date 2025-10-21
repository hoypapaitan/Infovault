<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table      = 'tbldocument_gfps';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'title', 
        'content', 
        'description', 
        'createdBy'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

}