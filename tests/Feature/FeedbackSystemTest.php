<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Feedback;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackMail;

class FeedbackSystemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cook_can_submit_feedback()
    {
        Mail::fake();
        $cook = User::factory()->create(['role' => 'cook']);
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($cook)->post(route('api.feedback.store'), [
            'type' => 'suggestion',
            'message' => 'This is a test suggestion'
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('feedback', [
            'user_id' => $cook->id,
            'type' => 'suggestion',
            'message' => 'This is a test suggestion',
            'status' => 'new'
        ]);

        Mail::assertSent(FeedbackMail::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }

    /** @test */
    public function admin_can_view_and_manage_feedback()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cook = User::factory()->create(['role' => 'cook']);
        $feedback = Feedback::create([
            'user_id' => $cook->id,
            'type' => 'error',
            'message' => 'Test error message',
            'status' => 'new'
        ]);

        // Admin views the list
        $response = $this->actingAs($admin)->get(route('admin.feedback.index'));
        $response->assertStatus(200);
        $response->assertSee($cook->name);

        // Admin views the detail
        $response = $this->actingAs($admin)->get(route('admin.feedback.show', $feedback->id));
        $response->assertStatus(200);
        $response->assertSee('Test error message');
        $this->assertEquals('read', $feedback->fresh()->status);

        // Admin archives the feedback
        $response = $this->actingAs($admin)->post(route('admin.feedback.archive', $feedback->id));
        $response->assertStatus(302);
        $this->assertEquals('archived', $feedback->fresh()->status);
    }

    /** @test */
    public function guest_cannot_submit_feedback()
    {
        $response = $this->post(route('api.feedback.store'), [
            'type' => 'suggestion',
            'message' => 'Hack attempt'
        ]);

        $response->assertRedirect(route('login'));
    }
}
