<?php

namespace Tests\Feature;

use App\Jobs\SendEmailJob;
use App\Mail\MotivationEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmailSendingTest extends TestCase
{
    public function test_authenticated_user_can_send_multiple_emails_with_queues()
    {
        Mail::fake();
        Queue::fake();

        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;


        $emails = [
            [
                'email' => 'recipient1@example.com',
                'subject' => 'Test Email 1',
                'body' => 'This is a test email 1'
            ],
            [
                'email' => 'recipient2@example.com',
                'subject' => 'Test Email 2',
                'body' => 'This is a test email 2'
            ]
        ];
        Sanctum::actingAs($user);
        $response = $this->postJson("/api/{$user->id}/send",  [
            'emails' => $emails,
            'api_token' => $token
        ]);
        $response->assertStatus(200);

        foreach ($emails as $email) {
            Queue::assertPushedOn('send-email', function (SendEmailJob $job) use ($email) {
                return $job->email['email'] === $email['email'];
            });
        }
    }
}
