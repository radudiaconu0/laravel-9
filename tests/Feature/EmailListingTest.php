<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Mail;
use Tests\TestCase;

class EmailListingTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_can_list_emails(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $emails = [
            [
                'email' => 'johndoe@ceva.com',
                'subject' => 'Test Email 1',
                'body' => 'This is a test email 1',
            ],
            [
                'email' => 'johndoe@ceva.com',
                'subject' => 'Test Email 2',
                'body' => 'This is a test email 2',
            ],
        ];
        Sanctum::actingAs($user);
        $response = $this->postJson("/api/{$user->id}/send", [
            'emails' => $emails,
            'api_token' => $token,
        ]);

        $response->assertStatus(200);

        $response = $this->getJson("/api/list");

        $response->assertStatus(200)->assertJson([
            'message' => 'Emails are listed successfully',
        ]);
    }
}
