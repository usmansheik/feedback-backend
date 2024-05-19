<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching all products.
     *
     * @return void
     */
    public function testFetchAllProducts(): void
    {
        // Arrange: Create some products
        $products = Product::factory()->count(3)->create();

        // Act: Make a GET request to the index method of ProductController
        $response = $this->get('/api/product');

        // Assert: Check if the response is successful
        $response->assertStatus(200);

        // Assert: Check if the response contains the products
        $response->assertJson([
            'products' => $products->toArray(),
            'msg' => 'fetch all products'
        ]);
    }

    /**
     * Test fetching no products when there are none in the database.
     *
     * @return void
     */
    public function testFetchNoProductsWhenNoneExist(): void
    {
        // Act: Make a GET request to the index method of ProductController
        $response = $this->get('/api/product');

        // Assert: Check if the response is successful
        $response->assertStatus(200);

        // Assert: Check if the response contains the appropriate message
        $response->assertJson([
            'msg' => 'no product found'
        ]);
    }
}
