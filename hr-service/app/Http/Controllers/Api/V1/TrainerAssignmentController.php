<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\TrainerAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainerAssignmentController extends Controller
{
    /**
     * Display a listing of assignments.
     *
     * GET /api/v1/assignments
     */
    public function index(): JsonResponse
    {
        $assignments = TrainerAssignment::with('trainer')->get();

        return response()->json([
            'status' => 'success',
            'data' => $assignments,
        ]);
    }

    /**
     * Store a newly created assignment.
     *
     * POST /api/v1/assignments
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'trainer_id' => 'required|exists:employees,id',
            'member_id'  => 'required|integer',
            'schedule'   => 'required|string|max:255',
            'notes'      => 'nullable|string',
            'status'     => 'sometimes|string|in:active,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $trainer = Employee::find($request->trainer_id);

        if ($trainer->role !== 'trainer') {
            return response()->json([
                'status'  => 'error',
                'message' => 'The selected employee is not a trainer. Employee role: ' . $trainer->role,
            ], 400);
        }

        if ($trainer->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'The selected trainer is currently inactive',
            ], 400);
        }

        $assignment = TrainerAssignment::create([
            'trainer_id' => $request->trainer_id,
            'member_id'  => $request->member_id,
            'schedule'   => $request->schedule,
            'notes'      => $request->notes,
            'status'     => $request->status ?? 'active',
        ]);

        $assignment->load('trainer');

        return response()->json([
            'status'  => 'success',
            'message' => 'Trainer assigned successfully',
            'data'    => $assignment,
        ], 201);
    }

    /**
     * Display the specified assignment.
     *
     * GET /api/v1/assignments/{id}
     */
    public function show(string $id): JsonResponse
    {
        $assignment = TrainerAssignment::with('trainer')->find($id);

        if (!$assignment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Assignment not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $assignment,
        ]);
    }

    /**
     * Update the specified assignment.
     *
     * PUT /api/v1/assignments/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $assignment = TrainerAssignment::find($id);

        if (!$assignment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Assignment not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'trainer_id' => 'sometimes|required|exists:employees,id',
            'member_id'  => 'sometimes|required|integer',
            'schedule'   => 'sometimes|required|string|max:255',
            'notes'      => 'nullable|string',
            'status'     => 'sometimes|required|string|in:active,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->has('trainer_id')) {
            $trainer = Employee::find($request->trainer_id);
            if ($trainer->role !== 'trainer') {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'The selected employee is not a trainer. Employee role: ' . $trainer->role,
                ], 400);
            }
            if ($trainer->status !== 'active') {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'The selected trainer is currently inactive',
                ], 400);
            }
        }

        $assignment->update($request->only([
            'trainer_id',
            'member_id',
            'schedule',
            'notes',
            'status',
        ]));

        $assignment->load('trainer');

        return response()->json([
            'status'  => 'success',
            'message' => 'Assignment updated successfully',
            'data'    => $assignment,
        ]);
    }

    /**
     * Remove the specified assignment.
     *
     * DELETE /api/v1/assignments/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $assignment = TrainerAssignment::find($id);

        if (!$assignment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Assignment not found',
            ], 404);
        }

        $assignment->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Assignment deleted successfully',
        ]);
    }
}
