<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class ProductModel extends Model
{
    private $path;
    private $products;

    public function __construct($path = null) {
        parent::__construct();
            $this->path = $path ?? WRITEPATH . 'products.json';
            $this->createProductsFile();
    }

    public function createProductsFile() {
        try {
            if(!file_exists($this->path)) {
                if(file_put_contents($this->path, json_encode([])) === false){
                    throw new Exception("Error creando el archivo JSON");
                }
            }
    
            $this->products = json_decode(file_get_contents($this->path),true);
    
        } catch (Exception $e) {
            throw new Exception("Error creando el archivo JSON", $e->getMessage());
            
        }
    }

    public function loadProductsFile() {
        try {
            $file = [];
            if(!file_exists($this->path)) {
                $file = file_get_contents($this->path);                
                if($file === false) {
                    throw new Exception("Error al cargar el archivo JSON");
                }
            }
    
            $this->products = json_decode($file, true);
    
        } catch (Exception $e) {
            throw new Exception("Error al cargar el archivo JSON", $e->getMessage());
        }
    }
     
    public function getProducts() {
        
        return $this->products;
    }

    public function store($product) {
        try {

            $this->products[] = $product;
            if(file_put_contents($this->path, json_encode($this->products)) == false) {
                throw new Exception("Error, no se pudo almacenar el producto en el archivo JSON");
            }
            return $this->products;
        } catch (Exception $e) {
            throw new Exception("Error, no se pudo almacenar el producto en el archivo JSON", $e->getMessage());
        }
    }

    public function delete($id = null, bool $purge = true) {
    try {
            $new_products = array_filter($this->products, function($p) use ($id) {
                return $p['id'] !== $id;
            });
            
            $this->products = array_values($new_products);

            if(file_put_contents($this->path, json_encode($this->products)) === false) {
                throw new Exception("Error, no se pudo eliminar el producto del archivo JSON");
            }
        } catch (Exception $e) {
            throw new Exception("Error, no se pudo eliminar el producto del archivo JSON", $e->getMessage());
        }
    }

    public function update($id = null, $product = null): bool {
        
        try {
                if(!empty($id) && !empty($product)) {
                    foreach ($this->products as $kp => $p) {
                        if ($p['id'] == $id) {
                            $this->products[$kp]= $product;
                            break;
                        }
                    }
            
                if(file_put_contents($this->path, json_encode($this->products)) === false){
                    throw new Exception("Error, no se pudo actualizar el producto");
                }

                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Error, insertar contenido en producst.json", $e->getMessage());
        }
    }
}