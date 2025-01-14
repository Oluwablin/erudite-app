<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Event;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testEventCreation()
    {
        $data = [
            'name' => 'Test Event',
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'), // Start time: tomorrow
            'end_time' => now()->addDays(2)->format('Y-m-d H:i:s'), // End time: day after tomorrow
            'max_participants' => 50,
        ];

        $response = $this->postJson('/api/v1/events', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => $data['name'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'max_participants' => $data['max_participants'],
            ]);

        $this->assertDatabaseHas('events', $data);
    }

    public function testGetAllEventsWithFilters()
    {
        Event::factory()->create([
            'name' => 'Event 1',
            'start_time' => now()->addDays(1),
            'end_time' => now()->addDays(2),
        ]);

        Event::factory()->create([
            'name' => 'Event 2',
            'start_time' => now()->addDays(3),
            'end_time' => now()->addDays(4),
        ]);

        $filters = [
            'name' => 'Event 1',
            'start_date' => now()->addDay()->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
        ];

        $response = $this->getJson('/api/v1/events?' . http_build_query($filters));

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Event 1']);
        $response->assertJsonMissing(['name' => 'Event 2']);
    }

    public function testParticipantRegistration()
    {
        $event = Event::factory()->create();

        $data = ['email' => 'participant@example.com'];

        $response = $this->postJson("/api/v1/events/{$event->id}/register", $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('participants', [
            'event_id' => $event->id,
            'email' => 'participant@example.com',
        ]);
    }

    public function testBulkRegistrationWithInvalidEmails()
    {
        $event = Event::factory()->create(['max_participants' => 5]);

        $data = [
            'emails' => [
                'invalid-email',
                'duplicate@example.com',
                'duplicate@example.com',
            ],
        ];

        $response = $this->postJson("/api/v1/events/{$event->id}/bulk-register", $data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'emails.0' => ['Each email address must be valid.'],
            'emails.1' => ['Duplicate email addresses are not allowed.'],
            'emails.2' => ['Duplicate email addresses are not allowed.'],
        ]);
    }

    public function testBulkRegistration()
    {
        $event = Event::factory()->create(['max_participants' => 3]);

        $data = [
            'emails' => [
                'participant1@example.com',
                'participant2@example.com',
                'participant3@example.com',
            ],
        ];

        $response = $this->postJson("/api/v1/events/{$event->id}/bulk-register", $data);

        $response->assertStatus(201);
        $this->assertCount(3, $event->participants);
    }
}
