<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\ProductModel;

class ProductModelTest extends TestCase
{
    protected $testFilePath;
    protected $productModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testFilePath = './tests/test_data/products_test.json';

        $this->productModel = new ProductModel($this->testFilePath);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }

        parent::tearDown();
    }

    public function testStoreProduct()
    {
        $product = [
            'id' => 1,
            'title' => "test_product",
            'price' => '1000',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->productModel->store($product);

        $this->assertIsArray($result);

        $this->assertFileExists($this->testFilePath);

        $productsFromFile = json_decode(file_get_contents($this->testFilePath), true);
        $this->assertContains($product, $productsFromFile);
    }
}
