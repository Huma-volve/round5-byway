<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Category;
use App\Models\OrderItem;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InstructorRevenueTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_it_returns_correct_revenue_analytics_for_instructor()
    {

        $instructor = User::create(
            [
                'name' => 'Test Instructor',
                'email' => 'instructor@example.com',
                'password' => bcrypt('password'),
                'role' => 'instructor',
                'status' => 'Active',
            ],
        );

        $category = Category::create([
            'name' => 'Programming',
            'description' => 'Programming related courses',
        ]);


        $course = Course::create([
            'title' => 'Test Course',
            'description' => 'Some description',
            'price' => 100,
            'user_id' => $instructor->id,
            'category_id' => $category->id,
        ]);


        $order = Order::create([
            'user_id' => $instructor->id,
            'status' => 'completed',
            'total_amount' => 300,
        ]);


        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'price' => 200,
        ]);

        // Settings
        Setting::create(['key' => 'commission', 'value' => 15]); // 20%
        Setting::create(['key' => 'withdrawal', 'value' => 50]);

        // Withdrawals
        Payment::create([
            'user_id' => $instructor->id,
            'amount' => 100,
            'status' => 'succeeded',
        ]);

        Sanctum::actingAs($instructor, ['*']);

        $response = $this->getJson('/api/instructor/revenue-analytics');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'total_profits' => 170,
                    'available_balance' => 70,
                    'minimum_withdrawal' => 50,
                    'last_transaction' => 100
                ],
                'status' => 200,
                'message' => 'Revenue analytics retrieved successfully',
            ]);
    }

    public function test_it_returns_zero_if_no_sales_or_payments()
    {
        $instructor = User::create([
            'name' => 'Test Instructor',
            'email' => 'instructor@example.com',
            'password' => bcrypt('password'),
            'role' => 'instructor',
            'status' => 'Active',
        ],);

        Sanctum::actingAs($instructor, ['*']);
        $response = $this->getJson('/api/instructor/revenue-analytics');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'total_profits' => 0,
                'available_balance' => 0,
                'last_transaction' => 0,
            ]);
    }
}
