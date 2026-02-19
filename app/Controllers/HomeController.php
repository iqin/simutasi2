<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('home/index'); // Menampilkan landing page dari Views/home/index.php
    }
}
