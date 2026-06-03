<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    /**
     * Display a listing of plans.
     *
     * GET /api/v1/plans
     */
    public function index(): JsonResponse
    {
        $plans = Plan::all();

        return response()->json([
            'status' => 'success',
            'data' => $plans,
        ]);
    }

    /**
     * Store a newly created plan.
     *
     * POST /api/v1/plans
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'duration_months' => 'required|integer|min:1',
            'price'           => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'status'          => 'sometimes|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $plan = Plan::create([
            'name'            => $request->name,
            'duration_months' => $request->duration_months,
            'price'           => $request->price,
            'description'     => $request->description,
            'status'          => $request->status ?? 'active',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Plan created successfully',
            'data'    => $plan,
        ], 201);
    }

    /**
     * Display the specified plan.
     *
     * GET /api/v1/plans/{id}
     */
    public function show(string $id): JsonResponse
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Plan not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $plan,
        ]);
    }

    /**
     * Update the specified plan.
     *
     * PUT /api/v1/plans/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Plan not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'            => 'sometimes|required|string|max:255',
            'duration_months' => 'sometimes|required|integer|min:1',
            'price'           => 'sometimes|required|numeric|min:0',
            'description'     => 'nullable|string',
            'status'          => 'sometimes|required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $plan->update($request->only([
            'name',
            'duration_months',
            'price',
            'description',
            'status',
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'Plan updated successfully',
            'data'    => $plan,
        ]);
    }

    /**
     * Remove the specified plan.
     *
     * DELETE /api/v1/plans/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Plan not found',
            ], 404);
        }

        $plan->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Plan deleted successfully',
        ]);
    }
}
