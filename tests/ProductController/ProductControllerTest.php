<?php

namespace Tests\ProductController;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\ProductModel;
use Config\Services;

class ProductControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    protected $session;
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER['REQUEST_METHOD'] = 'POST';  
        $_POST[csrf_token()] = csrf_hash();
        
        

    }


    public function testDelete()
    {
        $productModel = new ProductModel('tests/test_data/products_test.json');

        $product = [
            'id' => 1,
            'title' => "test_product",
            'price' => '1000',
            'created_at' => date('Y-m-d H:i:s')
        ];
        

        
        $productModel->store($product);

        
        $session = Services::session();
        $session->set('UserRole', 'admin');

        
        $result = $this->withSession(['UserRole'])->post('product/delete', [
            'id' => 1
        ]);
        
        $this->assertTrue($result->isOK());
        // $this->assertStringContainsString('"success":true', $result->getJSON());
        // $this->assertStringContainsString('"message":"Producto eliminado correctamente"', $result->getJSON());
        // $this->assertStringContainsString('"csrf_token":', $result->getJSON());
    }
}
