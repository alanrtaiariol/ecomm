<?php

namespace App\Models;

use CodeIgniter\Model;


class ProductModel extends Model
{
    private $path;
    private $products;

    public function __construct() {
        parent::__construct();

        $this->path = WRITEPATH . 'products.json';

        if(!file_exists($this->path)) {
            file_put_contents($this->path, json_encode([]));
        }

        $this->products = json_decode(file_get_contents($this->path),true);
    }

    public function getProducts() {
        
        return $this->products;
    }

    public function store($product) {
        $this->products[] = $product;
        return file_put_contents($this->path, json_encode($this->products));
    }

    public function delete($id = null, bool $purge = true) {

        $new_products = array_filter($this->products, function($p) use ($id) {
            return $p['id'] !== $id;
        });

        $this->products = array_values($new_products);

        file_put_contents($this->path, json_encode($this->products));
    }

    public function update($id = null, $product = null): bool {
        
        if(!empty($id) && !empty($product)) {
            foreach ($this->products as &$p) {
                if ($p['id'] == $id) {
                    $p = $product;
                    break;
                }
            }
    
            return file_put_contents($this->path, json_encode($this->products));
        }
    }
}