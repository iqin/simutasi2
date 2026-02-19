<?php

namespace App\Controllers;
use App\Models\InfoModel;
use CodeIgniter\Controller;

class InfoController extends Controller {
    public function index() {
        $model = new InfoModel();
        $data['infos'] = $model->orderBy('tanggal', 'DESC')->getPublicInfo();
        return view('info_pengembangan/index', $data);
    }

    public function manage() {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/info_pengembangan')->with('error', 'Anda tidak memiliki akses!');
        }

        $model = new InfoModel();
        $pager = \Config\Services::pager();

        $perPage = $this->request->getVar('per_page') ?? 10;
        $page = (int) ($this->request->getVar('page') ?? 1);
        
        $data['infos'] = $model->orderBy('tanggal', 'DESC')->paginate($perPage, 'info_pengembangan');
        $data['pager'] = $model->pager;
        $data['perPage'] = $perPage;

        return view('info_pengembangan/manage', $data);
    }

    public function create() {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/info_pengembangan')->with('error', 'Anda tidak memiliki akses!');
        }
        return view('info_pengembangan/create');
    }
    public function store() {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/info_pengembangan')->with('error', 'Anda tidak memiliki akses!');
        }
    
        $status = $this->request->getPost('status'); // Ambil status dari form
    
        if (!in_array($status, ['draft', 'public'])) {
            $status = 'draft'; // Default ke draft jika tidak valid
        }
    
        $model = new InfoModel();
        $model->save([
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tanggal' => date('Y-m-d H:i:s'),
            'status' => $status
        ]);
    
        return redirect()->to('/kelola_info')->with('message', 'Info berhasil ditambahkan');
    }

    public function edit($id) {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/info_pengembangan')->with('error', 'Anda tidak memiliki akses!');
        }

        $model = new InfoModel();
        $data['info'] = $model->find($id);
        return view('info_pengembangan/edit', $data);
    }
    public function update($id) {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/kelola_info')->with('error', 'Anda tidak memiliki akses!');
        }
    
        $status = $this->request->getPost('status'); // Ambil status dari form
        if (!in_array($status, ['draft', 'public'])) {
            $status = 'draft'; // Default ke draft jika tidak valid
        }
    
        $model = new InfoModel();
        $model->update($id, [
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'status' => $status
        ]);
    
        return redirect()->to('/kelola_info')->with('message', 'Info berhasil diperbarui');
    }
    
    

    public function delete($id) {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/info_pengembangan')->with('error', 'Anda tidak memiliki akses!');
        }

        $model = new InfoModel();
        $model->delete($id);
        return redirect()->to('/kelola_info')->with('message', 'Info berhasil dihapus');
    }

  

}

