<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class HelpdeskController extends Controller
{
    public function index()
    {
        return view('helpdesk');
    }
}
