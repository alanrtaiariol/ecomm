<?php

namespace Tests\ProductController;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\ProductModel;

class ProductControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    protected $testFilePath;

    protected function setUp(): void
    {
        parent::setUp();
        
        $_SERVER['REQUEST_METHOD'] = 'POST';  
        $_POST[csrf_token()] = csrf_hash();   
        $productModel = new ProductModel();
    }
/*
    public function testIndex()
    {
        $result = $this->get('products');
        $result->assertStatus(200);
        $result->assertJSONFragment([
            'success' => true,
        ]);
    }

    public function testStore()
    {
        $result = $this->post('product/store', [
            'title' => 'Test Product',
            'price' => 100
        ]);
        $result->assertStatus(200);
        $result->assertJSONFragment([
            'success' => true,
            'message' => 'El producto se creo correctamente'
        ]);
    }*/

    /*public function testUpdate()
    {
        $result = $this->post('product/update/1', [
            'title' => 'Updated Product',
            'price' => 150
        ]);
        $result->assertStatus(200);
        $result->assertJSONFragment([
            'success' => true,
            'message' => 'El producto se modifico correctamente'
        ]);
    }*/

    public function testDelete()
    {
        $productModel = new ProductModel();

        $product = [
            'id' => 1,
            'title' => "test_product",
            'price' => '1000',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $productModel->store($product);

        $result = $this->post( 'product/delete', [
            'id' => 1
        ]);
        $this->assertTrue($result->isOK());
        $this->assertStringContainsString('"success":true', $result->getJSON());
        $this->assertStringContainsString('"message":"Producto eliminado correctamente"', $result->getJSON());
        $this->assertStringContainsString('"csrf_token":', $result->getJSON());
    }
}
