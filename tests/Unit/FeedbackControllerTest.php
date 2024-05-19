<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Feedback;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Response;

class FeedbackControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    use DatabaseTransactions;

    public function testFeedbackStoredSuccessfully()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a customer and associate it with the user
        // Create a customer with name, email, and user_id attributes
        $customer = Customer::create([
            'name' => $this->faker->name, // Generate a random name
            'email' => $this->faker->unique()->safeEmail, // Generate a unique email
            'user_id' => $user->id,
            // Add other customer attributes here if needed
        ]);


        // Create a product
        $product = Product::factory()->create();

        // Simulate authentication of the user
        $this->actingAs($user);

        // Generate fake data for the feedback
        $payload = [
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence,
        ];

        // Send a POST request to the store method of FeedbackController
        $response = $this->postJson('/api/feedback', $payload);

        // Assert that the response has a successful status code
        $response->assertStatus(201);

        // Assert that the feedback is stored in the database
        $this->assertDatabaseHas('feedback', [
            'customer_id' => $customer->id,
            'product_id' => $payload['product_id'],
            'rating' => $payload['rating'],
            'comment' => $payload['comment'],
        ]);
    }
    public function testFeedbackIndexReturnsFeedbackWithProductInfo()
    {
        // Create a product
        $product = Product::factory()->create();

        // Create feedback associated with the product
        $feedback = Feedback::create([
            'product_id' => $product->id,
            'rating' => 4,
            'comment' => 'This is an awesome product',
            // Add other feedback attributes here if needed
        ]);

        // Call the index method of FeedbackController
        $response = $this->getJson('/api/admin/feedback');

        // Assert that the response has a successful status code
        $response->assertStatus(200);

        // Assert that the response contains the feedback data with associated product information
        $response->assertJson([
            [
                'id' => $feedback->id,
                'customer_id' => $feedback->customer_id,
                'product_id' => $feedback->product_id,
                'rating' => $feedback->rating,
                'comment' => $feedback->comment,
                'response' => $feedback->response,
                'status' => $feedback->status,
                'created_at' => $feedback->created_at->toISOString(), // Convert to ISO 8601 format
                'updated_at' => $feedback->updated_at->toISOString(), // Convert to ISO 8601 format
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'created_at' => $product->created_at->toISOString(), // Convert to ISO 8601 format
                    'updated_at' => $product->updated_at->toISOString(), // Convert to ISO 8601 format
                ],
            ]
        ]);
    }

    public function testPostResponseUpdatesFeedback()
    {
        # Create a product
        $product = Product::factory()->create();

        # Create feedback associated with the product
        $feedback = Feedback::create([
            'product_id' => $product->id,
            'rating' => 4,
            'comment' => 'This is an awesome product',
        ]);
        // New response data
        $newResponseData = [
            'response' => 'Thank you for your feedback!'
        ];

        $response = $this->postJson("/api/admin/feedback/{$feedback->id}/response", $newResponseData);

        // Assert that the response has a successful status code
        $response->assertStatus(Response::HTTP_OK);

        // Refresh the feedback from the database
        $feedback->refresh();

        // Assert that the feedback's response is updated
        $this->assertEquals($newResponseData['response'], $feedback->response);
    }

    public function testToggleFeedbackTogglesStatus()
    {
        # Create a product
        $product = Product::factory()->create();

        # Create a feedback with status = 0
        $feedback = Feedback::create([
            'product_id' => $product->id,
            'rating' => 4,
            'comment' => 'This is an awesome product',
            "status"=>0
        ]);

        // Call the toggleFeedback method of FeedbackController
        $response = $this->putJson("/api/admin/toggle-feedback/{$feedback->id}");

        // Assert that the response has a successful status code
        $response->assertStatus(Response::HTTP_OK);

        // Refresh the feedback from the database
        $feedback->refresh();

        // Assert that the feedback's status is toggled
        $this->assertEquals(1, $feedback->status);
    }


}
