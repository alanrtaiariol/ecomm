<?php

namespace App\Controllers;

use App\Controllers\BaseController;
//use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProductModel;
use DateTime;
use Exception;

use function PHPSTORM_META\type;

class ProductController extends BaseController
{

    public function __construct()
    {
        $this->logger = service('logger');
    }

    public function index()
    {
        $productsModel = new ProductModel();
        $products = $productsModel->getProducts();
        log_crud_action('READ', 'Se obtuvo el listado de productos');
        return $this->response->setJSON($products);
    }

    public function store() {
        $csrfToken = csrf_hash();
        $validation = \Config\Services::validation();
        try {
            $rules = [
                'price' => 'required|numeric',
                'title' => 'required|min_length[3]|max_length[100]',
            ];
            $new_id = 1;
            if($this->request->getMethod() === 'POST' && $validation->setRules($rules)){
                $productsModel = new ProductModel();
                $products = $productsModel->getProducts();
                $title = $this->request->getVar('title');
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
                
                $now = new DateTime();

                
            
                $product = [
                    'id' => $new_id,
                    'title' => $title,
                    'price' => $this->request->getVar('price'),
                    'created_at' => $now->format('Y-m-d H:i:s')
                ];
                
                $store = $productsModel->store($product);
                // log_crud_action('CREATE', 'Se creó un producto');

                if($store !== false) {
                    return $this->response->setJSON([
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
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function update($id) {
        $validation = \Config\Services::validation();
        $csrfToken = csrf_hash();

        try {
            if(!empty($id)) { 
                $rules = [
                    'price' => 'required|numeric',
                    'title' => 'required|min_length[3]|max_length[100]',
                ];

                if($this->request->getMethod() === 'POST' && $validation->setRules($rules)){
                    $productsModel = new ProductModel();
                    $products = $productsModel->getProducts();
                    $title = $this->request->getVar('title');
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
                        'title' => $title,
                        'price' => $this->request->getVar('price'),
                        'created_at' => $now->format('Y-m-d H:i:s')
                    ];
                    
                    $store = $productsModel->update((int)$id, $product);
                    // log_crud_action('CREATE', 'Se creó un producto');

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
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function delete() {
        try {
            $csrfToken = csrf_hash();
            $id = $this->request->getVar('id');
            
            if($id){
                $productsModel = new ProductModel();
                $productsModel->delete((int)$id);
    
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Producto eliminado correctamente',
                    'csrf_token' => $csrfToken
                ]);
            }
    
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
            
        }
    }
}
