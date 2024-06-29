<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CsrfController extends BaseController
{
    public function updateCsrfToken()
    {
        $newCsrfToken = csrf_hash(); 
    
        return $this->response->setJSON(['csrf_token' => $newCsrfToken]);
    }
}
