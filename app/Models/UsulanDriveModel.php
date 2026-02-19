<?php

namespace App\Models;

use CodeIgniter\Model;

class UsulanDriveModel extends Model
{
    protected $table = 'usulan_drive_links';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nomor_usulan', 'drive_link', 'created_at'];
}
