<?php

namespace App\Services;

use App\Models\Participant;
use Illuminate\Support\Facades\Log;

class ParticipantService
{
    public function registerParticipant($event, array $data)
    {
        try {
            if ($event->participants()->count() >= $event->max_participants) {
                throw new \Exception('Event is full.');
            }

            $overlapping = Participant::where('email', $data['email'])
                ->whereHas('event', function ($query) use ($event) {
                    $query->where('start_time', '<', $event->end_time)
                        ->where('end_time', '>', $event->start_time);
                })
                ->exists();

            if ($overlapping) {
                throw new \Exception('Participant is already registered for an overlapping event.');
            }

            return $event->participants()->create($data);
        } catch (\Exception $e) {
            Log::error('Error registering participant: ' . $e->getMessage());
            throw $e;
        }
    }

    public function bulkRegisterParticipants($event, array $emails)
    {
        try {
            $errors = [];
            $registered = [];

            foreach ($emails as $email) {
                try {
                    $this->registerParticipant($event, ['email' => $email]);
                    $registered[] = $email;
                } catch (\Exception $e) {
                    $errors[$email] = $e->getMessage();
                }
            }

            return ['registered' => $registered, 'errors' => $errors];
        } catch (\Exception $e) {
            Log::error('Error during bulk registration: ' . $e->getMessage());
            throw $e;
        }
    }
}