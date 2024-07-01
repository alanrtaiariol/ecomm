<?php

namespace App\Controllers;


use DateTime;
use Exception;
use App\Controllers\BaseController;
use App\Models\ProductModel;

class ProductController extends BaseController
{
    public function index()
    {
        try {
            $csrfToken = csrf_hash();
            $userRole = $this->session->UserRole;
            if(in_array($userRole,['admin', 'usuario'])) {
                
                $productsModel = new ProductModel();
                $products = $productsModel->getProducts();
                log_event('READ', "Se consulo el listado de productos");

                return $this->response->setJSON([
                    "data" => $products,
                    'csrf_token' => $csrfToken
                ]);
            } else {

                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access Denied',
                    'csrf_token' => $csrfToken
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $th->getMessage(),
                'csrf_token' => csrf_hash()
            ]); //throw $th;
        }

    }

    public function store() {
        
        try {
            $userRole = $this->session->UserRole;
            if($userRole === 'admin') {
                $csrfToken = csrf_hash();
                $validation = \Config\Services::validation();
                $rules = [
                    'price' => 'required|numeric',
                    'title' => 'required|min_length[3]|max_length[100]',
                ];
                $new_id = 1;
                if($this->request->getMethod() === 'POST' && $validation->setRules($rules)){
                    $productsModel = new ProductModel();
                    $products = $productsModel->getProducts();
                    $title = $this->request->getVar('title');
                    $cleanTitle = htmlspecialchars(trim($title));
                    $cleanPrice = htmlspecialchars(trim($this->request->getVar('price')));

                    if(count($products) > 0 && is_array($products)){
                        foreach($products as $p) {
                            if($p['title'] == $title) {
                                return $this->response->setJSON([
                                    'success' => false,
                                    'errors' => 'Ya existe un producto con el mismo titulo',
                                    'csrf_token' => $csrfToken
                                ]);
                            }
                        }

                        $last_product = $products[count($products)-1];
                        $new_id = (int)$last_product['id'] + 1;
                    }
                    
                    $product = [
                        'id' => $new_id,
                        'title' => $cleanTitle,
                        'price' => (float)$cleanPrice,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $store = $productsModel->store($product);
                    log_event('CREATE', "Se creó el producto $new_id");

                    if($store !== false) {
                        return $this->response->setStatusCode(200)->setJSON([
                            'success' => true,
                            'message' => "El producto se creo correctamente",
                            'csrf_token' => $csrfToken
                        ]);
                    } 
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'errors' => $validation->getErrors(),
                        'csrf_token' => $csrfToken
                    ]);
                }
            } else {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Acceso no autorizado']);
            }
            
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function update($id) {
        try {
            $userRole = $this->session->UserRole;
            if($userRole === 'admin') {
                $validation = \Config\Services::validation();
                $csrfToken = csrf_hash();
                if(!empty($id)) { 
                    $rules = [
                        'price' => 'required|numeric',
                        'title' => 'required|min_length[3]|max_length[100]',
                    ];

                    if($this->request->getMethod() === 'POST' && $validation->setRules($rules)){
                        $productsModel = new ProductModel();
                        $products = $productsModel->getProducts();
                        $title = $this->request->getVar('title');
                            $cleanTitle = htmlspecialchars(trim($title));
                        $cleanPrice = htmlspecialchars(trim($this->request->getVar('price')));

                        if(count($products) > 0 && is_array($products)){
                            foreach($products as $p) {
                                if($p['title'] == $title) {
                                    return $this->response->setJSON([
                                        'success' => false,
                                        'errors' => 'Ya existe un producto con el mismo titulo',
                                        'csrf_token' => $csrfToken
                                    ]);
                                }
                            }
                        }
                        
                        $now = new DateTime();
                        $product = [
                            'id' => $id,
                            'title' => $cleanTitle,
                            'price' => (float)$cleanPrice,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        
                        $store = $productsModel->update((int)$id, $product);
                        log_event('UPDATE', "Se modifico el producto $id");

                        if($store !== false) {
                            return $this->response->setJSON([
                                'success' => true,
                                'message' => "El producto se modifico correctamente",
                                'csrf_token' => $csrfToken
                            ]);
                        } 
                        
                    } else {
                        return $this->response->setJSON([
                            'success' => false,
                            'errors' => $validation->getErrors(),
                            'csrf_token' => $csrfToken
                        ]);
                    }
                }
            } else {
                $this->response->setStatusCode(403)->setJSON(['error' => 'Acceso no autorizado']);
            }
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function delete() {
        try {
            $userRole = $this->session->UserRole;
            if($userRole === 'admin') {
                $csrfToken = csrf_hash();
                $id = $this->request->getVar('id');
                
                if($id){
                    $productsModel = new ProductModel();
                    $productsModel->delete((int)$id);
                    log_event('DELETE', "Se elimino el producto $id");
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Producto eliminado correctamente',
                        'csrf_token' => $csrfToken
                    ]);
                }
            } else {
                $this->response->setStatusCode(403)->setJSON(['error' => 'Acceso no autorizado']);  
            } 
    
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
            
        }
    }
}
