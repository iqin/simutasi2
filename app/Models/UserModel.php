<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'role',
        'nama',       // Tambahkan nama
        'no_hp',      // Tambahkan no_hp
        'status',
        'cabang_dinas_id',
        'created_at',
        'updated_at',
        'login_attempts',
        'last_attempt', 
    ];
    protected $useTimestamps = true;

    public function getUsersWithCabang($role = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('users.*, cabang_dinas.kode_cabang, cabang_dinas.nama_cabang')
                ->join('cabang_dinas', 'users.cabang_dinas_id = cabang_dinas.id', 'left');

        if ($role) {
            $builder->where('users.role', $role);
        }

        return $builder->get()->getResultArray();
    }

    public function countAll()
    {
        return $this->db->table($this->table)->countAllResults();
    }
}
