<?php

namespace App\Controllers;


use DateTime;
use Exception;
use App\Controllers\BaseController;
use App\Models\ProductModel;

class ProductController extends BaseController
{

    protected $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }
    public function index()
    {
        try {
            $csrfToken = csrf_hash();
            $userRole = $this->session->UserRole;
            if(in_array($userRole,['admin', 'usuario'])) {
                
                $products = $this->productModel->getProducts();
                log_event('READ', "Se consulo el listado de productos");

                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $products,
                    'csrf_token' => $csrfToken
                ]);
            } else {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Acceso no autorizado',
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

    public function store() {
        try {
            $csrfToken = csrf_hash();
            $userRole = $this->session->UserRole;
            
            if($userRole === 'admin') {
                $validation = \Config\Services::validation();
                $rules = [
                    'price' => 'required|numeric',
                    'title' => 'required|min_length[3]|max_length[100]',
                ];

                $new_id = 1;
                if($this->request->getMethod() === 'POST' && $validation->setRules($rules)) {
                    $products = $this->productModel->getProducts();
                    $title = $this->request->getVar('title');
                    $cleanTitle = htmlspecialchars(trim($title));
                    $cleanPrice = htmlspecialchars(trim($this->request->getVar('price')));
                 
                    if(!empty($products) > 0 && is_array($products)){
                        foreach($products as $p) {
                            if($p['title'] == $title) {
                                return $this->response->setJSON([
                                    'status' =>'error',
                                    'message' => 'Ya existe un producto con el mismo titulo',
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
                    
                    $store = $this->productModel->store($product);
                    log_event('CREATE', "Se creó el producto $new_id");

                    if($store !== false) {
                        return $this->response->setStatusCode(200)->setJSON([
                            'status' =>'success',
                            'message' => "El producto se creo correctamente",
                            'csrf_token' => $csrfToken
                        ]);
                    } 
                } else {
                    return $this->response->setJSON([
                        'status' =>'error',
                        'message' => $validation->getErrors(),
                        'csrf_token' => $csrfToken
                    ]);
                }
            } else {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Acceso no autorizado',
                    'csrf_token' => $csrfToken
                ]);
            }
            
        } catch(Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function update($id) {
        try {
            $csrfToken = csrf_hash();
            $userRole = $this->session->UserRole;
            if($userRole === 'admin') {
                $validation = \Config\Services::validation();
                if(!empty($id)) { 
                    $rules = [
                        'price' => 'required|numeric',
                        'title' => 'required|min_length[3]|max_length[100]',
                    ];

                    if($this->request->getMethod() === 'POST' && $validation->setRules($rules)){
                        $products = $this->productModel->getProducts();
                        $title = $this->request->getVar('title');
                        $cleanTitle = htmlspecialchars(trim($title));
                        $cleanPrice = htmlspecialchars(trim($this->request->getVar('price')));
                        $quantityRepeatProducts = 0;

                        if(count($products) > 0 && is_array($products)){
                            foreach($products as $p) {
                                if($p['title'] == $title) {
                                    $quantityRepeatProducts++;
                                }
                            }

                            if($quantityRepeatProducts > 0) {
                                return $this->response->setJSON([
                                    'status' =>'error',
                                    'message' => 'Ya existe un producto con el mismo titulo',
                                    'csrf_token' => $csrfToken
                                ]);
                            }
                        }
                        
                        $product = [
                            'id' => (int)$id,
                            'title' => $cleanTitle,
                            'price' => (float)$cleanPrice,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        
                        $store = $this->productModel->update((int)$id, $product);
                        log_event('UPDATE', "Se modifico el producto $id");

                        if($store !== false) {
                            return $this->response->setJSON([
                                'status' =>'success',
                                'message' => "El producto se modifico correctamente",
                                'csrf_token' => $csrfToken
                            ]);
                        } 
                        
                    } else {
                        return $this->response->setJSON([
                            'status' =>'error',
                            'message' => $validation->getErrors(),
                            'csrf_token' => $csrfToken
                        ]);
                    }
                }
            } else {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Acceso no autorizado',
                    'csrf_token' => $csrfToken
                ]);
            }
        } catch(Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' =>'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete() {
        try {
            $userRole = $this->session->UserRole;
            $csrfToken = csrf_hash();
            if($userRole === 'admin') {
                $id = $this->request->getVar('id');
                if($id){
                    $this->productModel->delete((int)$id);
                    log_event('DELETE', "Se elimino el producto $id");
                    return $this->response->setStatusCode(200)->setJSON([
                        'status' =>'success',
                        'message' => 'Producto eliminado correctamente',
                        'csrf_token' => $csrfToken
                    ]);
                }
            } else {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Acceso no autorizado',
                    'csrf_token' => $csrfToken
                ]);
            } 
    
        } catch (Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' =>'error', 'message' => $e->getMessage()]);
        }
    }
}
