<?php

namespace App\Models;

use CodeIgniter\Model;

class UserLogModel extends Model
{
    protected $table = 'user_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'action', 'ip_address', 'user_agent', 'timestamp'];
    protected $useTimestamps = false;
}
