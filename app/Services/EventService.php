<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Log;

class EventService
{
    public function createEvent(array $data)
    {
        try {
            return Event::create($data);
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());
            throw new \Exception('Failed to create event');
        }
    }

    public function getAllEvents(array $filters)
    {
        $query = Event::query();

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('start_time', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('end_time', '<=', $filters['end_date']);
        }

        return $query->paginate(10);
    }
}
