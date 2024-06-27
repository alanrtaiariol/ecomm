<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProductModel;
use DateTime;

class ProductController extends BaseController
{
    public function index()
    {
        $productsModel = new ProductModel();
        $products = $productsModel->getProducts();
        return $this->response->setJSON($products);
    }

    public function store() {
        $validation = \Config\Services::validation();

        $rules = [
            'price' => 'required|numeric',
            'title' => 'required|min_length[3]|max_length[100]',
        ];

        if($this->request->getMethod() === 'POST' && $validation->setRules($rules)){
            $productsModel = new ProductModel();
            $products = $productsModel->getProducts();
            $title = $this->request->getVar('title');
            foreach($products as $p) {
                if($p['title'] == $title) {
                    return $this->response->setJSON([
                        'success' => false,
                        'errors' => 'Ya existe un producto con el mismo titulo'
                    ]);
                }
            }
            $last_product = $products[count($products)-1];
            $now = new DateTime();

            $new_id = (int)$last_product['id'] + 1;
            print($new_id);
            $product = [
                'id' => $new_id,
                'title' => $title,
                'price' => $this->request->getVar('price'),
                'created_at' => $now->format('Y-m-d H:i:s')
            ];
            
            $store = $productsModel->store($product);
            if($store !== false) {
                return $this->response->setJSON([
                    'success' => true,
                    'id' => $new_id
                ]);
            } else {
                
            }
            

          
        } else {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }
    }

    public function update() {
        
    }
}
