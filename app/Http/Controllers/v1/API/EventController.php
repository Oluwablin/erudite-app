<?php

namespace App\Http\Controllers\v1\API;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListEventsRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Resources\EventResource;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function createEvent(StoreEventRequest $request): JsonResponse
    {
        try {
            $event = $this->eventService->createEvent($request->validated());
            return $this->success(
                message: "Event created successfully",
                data: new EventResource($event),
                code: HttpStatusCode::CREATED->value
            );
        } catch (\Exception $e) {
            Log::error('Error storing event: ' . $e->getMessage());
            return $this->error(
                message: 'Error storing event: ' . $e->getMessage(),
                code: HttpStatusCode::BAD_REQUEST->value,
            );
        }
    }

    public function fetchEvents(ListEventsRequest $request): JsonResponse
    {
        try {
            $events = $this->eventService->getAllEvents($request->validated());
            return $this->success(
                message: "Events returned successfully",
                data: EventResource::collection($events),
                code: HttpStatusCode::SUCCESSFUL->value
            );
        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());
            return $this->error(
                message: 'Failed to retrieve events.',
                code: HttpStatusCode::BAD_REQUEST->value,
            );
        }
    }
}
