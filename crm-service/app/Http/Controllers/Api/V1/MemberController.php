<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Display a listing of the members.
     *
     * GET /api/v1/members
     */
    public function index(): JsonResponse
    {
        $members = Member::all();

        return response()->json([
            'status' => 'success',
            'data' => $members,
        ]);
    }

    /**
     * Store a newly created member in storage.
     *
     * POST /api/v1/members
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:members',
            'phone'             => 'required|string|max:50',
            'address'           => 'required|string',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone'   => 'required|string|max:50',
            'health_notes'      => 'nullable|string',
            'status'            => 'sometimes|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $member = Member::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'address'           => $request->address,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone'   => $request->emergency_phone,
            'health_notes'      => $request->health_notes,
            'status'            => $request->status ?? 'active',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Member created successfully',
            'data'    => $member,
        ], 201);
    }

    /**
     * Display the specified member.
     *
     * GET /api/v1/members/{id}
     */
    public function show(string $id): JsonResponse
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Member not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $member,
        ]);
    }

    /**
     * Update the specified member in storage.
     *
     * PUT /api/v1/members/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Member not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'              => 'sometimes|required|string|max:255',
            'email'             => 'sometimes|required|string|email|max:255|unique:members,email,' . $id,
            'phone'             => 'sometimes|required|string|max:50',
            'address'           => 'sometimes|required|string',
            'emergency_contact' => 'sometimes|required|string|max:255',
            'emergency_phone'   => 'sometimes|required|string|max:50',
            'health_notes'      => 'nullable|string',
            'status'            => 'sometimes|required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $member->update($request->only([
            'name',
            'email',
            'phone',
            'address',
            'emergency_contact',
            'emergency_phone',
            'health_notes',
            'status',
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'Member updated successfully',
            'data'    => $member,
        ]);
    }

    /**
     * Remove the specified member from storage.
     *
     * DELETE /api/v1/members/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Member not found',
            ], 404);
        }

        $member->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Member deleted successfully',
        ]);
    }
}
