<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CommunicationRequest;
use App\Http\Resources\CommunicationResource;
use App\Models\Communication;
use App\Services\CommunicationService;
use App\OpenApi\Controllers\CommunicationControllerDoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

/**
 * @see CommunicationControllerDoc for API documentation
 */
class CommunicationController extends Controller
{
    protected $communicationService;
    
    public function __construct(CommunicationService $communicationService)
    {
        $this->communicationService = $communicationService;
    }
    
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Communication::query();
        
        if ($request->has('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->has('from_date')) {
            $query->whereDate('send_date', '>=', $request->input('from_date'));
        }
        
        if ($request->has('to_date')) {
            $query->whereDate('send_date', '<=', $request->input('to_date'));
        }
        
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('message', 'like', "%{$searchTerm}%");
            });
        }
        
        $with = [];
        if ($request->boolean('with_course')) {
            $with[] = 'course';
        }
        
        if ($request->boolean('with_guardians')) {
            $with[] = 'guardians';
        }
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        if ($request->boolean('with_counts')) {
            $query->withCount('guardians');
        }
        
        $communications = $query->latest()->paginate($request->input('per_page', 15));
        
        return CommunicationResource::collection($communications);
    }
    
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validate([
                'course_id' => 'required|integer|exists:courses,id',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'send_date' => 'required|date',
                'status' => 'required|string|in:draft,sent,scheduled',
                'send_now' => 'sometimes|boolean'
            ]);
            
            $communication = Communication::create($validated);
            
            if (isset($validated['send_now']) && $validated['send_now']) {
                $result = $this->communicationService->sendCommunication($communication);
                
                if (!$result['success']) {
                    throw new \Exception($result['message']);
                }
                
                $communication->status = 'sent';
                $communication->save();
            }
            
            DB::commit();
            
            $communication->load(['course']);
            
            return (new CommunicationResource($communication))
                ->response()
                ->setStatusCode(201);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Error al crear el comunicado: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Request $request, Communication $communication): CommunicationResource
    {
        $with = [];
        if ($request->boolean('with_course')) {
            $with[] = 'course';
        }
        
        if ($request->boolean('with_guardians')) {
            $with[] = 'guardians';
        }
        
        if (!empty($with)) {
            $communication->load($with);
        }
        
        if ($request->boolean('with_counts')) {
            $communication->loadCount('guardians');
        }
        
        return new CommunicationResource($communication);
    }
    
    public function update(Request $request, Communication $communication): CommunicationResource
    {
        if ($communication->status === 'sent') {
            abort(422, 'No se puede actualizar un comunicado que ya ha sido enviado');
        }
        
        $validated = $request->validate([
            'course_id' => 'sometimes|integer|exists:courses,id',
            'title' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'send_date' => 'sometimes|date',
            'status' => 'sometimes|string|in:draft,scheduled'
        ]);
        
        $communication->update($validated);
        
        return new CommunicationResource($communication);
    }
    
    public function destroy(Communication $communication): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $communication->guardians()->detach();
            
            $communication->delete();
            
            DB::commit();
            
            return response()->json(null, 204);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Error al eliminar el comunicado: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function send(Communication $communication): JsonResponse
    {
        if ($communication->status === 'sent') {
            return response()->json([
                'message' => 'El comunicado ya ha sido enviado'
            ], 400);
        }
        
        $result = $this->communicationService->sendCommunication($communication);
        
        if ($result['success']) {
            $communication->status = 'sent';
            $communication->save();
            
            return response()->json([
                'message' => $result['message'],
                'sent' => $result['sent'],
                'errors' => $result['errors']
            ]);
        } else {
            return response()->json([
                'message' => $result['message']
            ], 500);
        }
    }
}
