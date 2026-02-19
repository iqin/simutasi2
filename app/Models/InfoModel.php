<?php
namespace App\Models;
use CodeIgniter\Model;

class InfoModel extends Model {
    protected $table = 'info_pengembangan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['judul', 'deskripsi', 'tanggal', 'status']; // 🔹 Tambahkan status

    // Ambil data dengan status "public" untuk ditampilkan di halaman Info Pengembangan
    public function getPublicInfo() {
        return $this->where('status', 'public')->orderBy('tanggal', 'DESC')->findAll();
    }
}
