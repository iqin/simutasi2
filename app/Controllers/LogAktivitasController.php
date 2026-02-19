<?php

namespace App\Controllers;

use App\Models\UserLogModel;
use App\Models\UserModel;

class LogAktivitasController extends BaseController
{
    public function index()
    {
        $logModel = new UserLogModel();
        $userModel = new UserModel();
        $perPage = $this->request->getVar('per_page') ?? 25; // Default 10 data per halaman
        $search = $this->request->getVar('search'); // Input pencarian

        $query = $logModel->select('user_logs.*, users.username, users.role')
                          ->join('users', 'user_logs.user_id = users.id', 'left')
                          ->orderBy('user_logs.timestamp', 'DESC');

        // Filter pencarian berdasarkan username atau role
        if ($search) {
            $query->groupStart()
                  ->like('users.username', $search)
                  ->orLike('users.role', $search)
                  ->groupEnd();
        }

        $logs = $query->paginate($perPage);
        $pager = $logModel->pager;

        return view('admin/log_aktivitas', [
            'logs' => $logs,
            'pager' => $pager,
            'perPage' => $perPage,
            'search' => $search,
        ]);
    }
}
