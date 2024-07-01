<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    public function setUserRole()
    {
        $csrfToken = csrf_hash();
        $this->session->set('UserRole', $this->request->getVar('role'));
        return $this->response->setJSON([
                'status' => 'success',
                'csrf_token' => $csrfToken
                ]);
    }
}
