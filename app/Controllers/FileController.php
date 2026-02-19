<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;

class FileController extends BaseController
{
    public function viewRekomendasi($filename)
    {
        $filepath = WRITEPATH . 'uploads/rekomendasi/' . $filename;

        if (file_exists($filepath)) {
            return $this->response->setHeader('Content-Type', 'application/pdf')
                                  ->setBody(file_get_contents($filepath));
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    public function viewRekomkadis($filename)
    {
        $filepath = WRITEPATH . 'uploads/rekom_kadis/' . $filename;

        if (file_exists($filepath)) {
            return $this->response->setHeader('Content-Type', 'application/pdf')
                                  ->setBody(file_get_contents($filepath));
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    public function viewSkMutasi($fileName)
    {
        $filePath = WRITEPATH . 'uploads/sk_mutasi/' . $fileName;

        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File tidak ditemukan.');
        }

        return $this->response->setHeader('Content-Type', 'application/pdf')->setBody(file_get_contents($filePath));
    }

}
