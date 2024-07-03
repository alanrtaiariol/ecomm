<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class UserController extends BaseController
{
    public function setUserRole() {
        try {
            $csrfToken = csrf_hash();
            $userRole = $this->request->getVar('role');
            if (!empty($userRole)) {


                if ($this->session->has('UserRole')) {
                    $this->session->remove('UserRole');
                } 
                $this->session->set('UserRole', $userRole);

                return $this->response->setJSON([
                    'status' => 'success',
                    'role' => $userRole, 
                    'csrf_token' => $csrfToken
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'csrf_token' => $csrfToken
                ]);
            }
        } catch (Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
}
