<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table      = 'tblusers';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'username', 
        'password', 
        'firstName', 
        'lastName', 
        'middleName', 
        'suffix',
        'sex',
        'address',
        'userType', 
        'email', 
        'contact',
        'status',
        'status' 
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

}