<?php

namespace App\Http\Controllers\v1\API;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkRegisterParticipantsRequest;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterParticipantRequest;
use App\Http\Resources\ParticipantResource;
use App\Models\Event;
use App\Services\ParticipantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ParticipantController extends Controller
{
    protected $participantService;

    public function __construct(ParticipantService $participantService)
    {
        $this->participantService = $participantService;
    }

    public function register(RegisterParticipantRequest $request, Event $event): JsonResponse
    {
        try {
            $participant = $this->participantService->registerParticipant($event, $request->validated());
            return $this->success(
                message: "Participant registered successfully",
                data: new ParticipantResource($participant),
                code: HttpStatusCode::CREATED->value
            );
        } catch (\Exception $e) {
            Log::error('Error registering participant: ' . $e->getMessage());
            return $this->error(
                message: 'Error registering participant: ' . $e->getMessage(),
                code: HttpStatusCode::BAD_REQUEST->value,
            );
        }
    }

    public function bulkRegister(BulkRegisterParticipantsRequest $request, Event $event): JsonResponse
    {
        try {
            $result = $this->participantService->bulkRegisterParticipants($event, $request->validated()['emails']);
            return $this->success(
                message: "Bulk registration successful",
                data: $result,
                code: HttpStatusCode::CREATED->value
            );
        } catch (\Exception $e) {
            Log::error('Error in bulk registration: ' . $e->getMessage());
            return $this->error(
                message: 'Error in bulk registration: ' . $e->getMessage(),
                code: HttpStatusCode::BAD_REQUEST->value,
            );
        }
    }
}
