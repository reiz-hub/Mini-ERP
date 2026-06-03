<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MembershipController extends Controller
{
    /**
     * Display a listing of the memberships.
     *
     * GET /api/v1/memberships
     */
    public function index(): JsonResponse
    {
        $memberships = Membership::with('plan')->get();

        return response()->json([
            'status' => 'success',
            'data' => $memberships,
        ]);
    }

    /**
     * Enroll a member in a plan.
     *
     * POST /api/v1/memberships/enroll
     */
    public function enroll(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'member_id'   => 'required|integer',
            'plan_id'     => 'required|exists:plans,id',
            'start_date'  => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $plan = Plan::find($request->plan_id);

        if ($plan->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'The selected plan is currently inactive',
            ], 400);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addMonths($plan->duration_months);

        $membership = Membership::create([
            'member_id'   => $request->member_id,
            'plan_id'     => $request->plan_id,
            'start_date'  => $startDate->toDateString(),
            'end_date'    => $endDate->toDateString(),
            'status'      => 'active',
            'amount_paid' => $request->amount_paid,
        ]);

        // Sync with CRM Service (https://localhost:8002/api/v1/members/{member_id})
        $crmUpdated = false;
        $crmMessage = '';
        $authHeader = $request->header('Authorization');

        try {
            $crmResponse = Http::withHeaders([
                'Authorization' => $authHeader,
                'Accept'        => 'application/json',
            ])->timeout(5)->put(env('CRM_SERVICE_URL', 'https://localhost:8002') . "/api/v1/members/{$request->member_id}", [
                'status' => 'active',
            ]);

            if ($crmResponse->successful()) {
                $crmUpdated = true;
            } else {
                $crmMessage = 'CRM Service returned status ' . $crmResponse->status() . ': ' . $crmResponse->body();
            }
        } catch (\Exception $e) {
            $crmMessage = 'Could not contact CRM service: ' . $e->getMessage();
        }

        $membership->load('plan');

        return response()->json([
            'status'   => 'success',
            'message' => 'Member enrolled successfully',
            'data'     => $membership,
            'crm_sync' => [
                'status'  => $crmUpdated ? 'success' : 'failed',
                'message' => $crmMessage ?: 'Member status updated to active in CRM service',
            ]
        ], 201);
    }

    /**
     * Renew a membership.
     *
     * PUT /api/v1/memberships/{id}/renew
     */
    public function renew(Request $request, string $id): JsonResponse
    {
        $membership = Membership::find($id);

        if (!$membership) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Membership record not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'plan_id'     => 'sometimes|required|exists:plans,id',
            'start_date'  => 'nullable|date',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Determine plan
        $planId = $request->plan_id ?? $membership->plan_id;
        $plan = Plan::find($planId);

        if ($plan->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'The selected renewal plan is currently inactive',
            ], 400);
        }

        // Determine start date
        if ($request->has('start_date') && $request->start_date) {
            $startDate = Carbon::parse($request->start_date);
        } else {
            // Default: if current is active and not expired, start the day after current ends
            $currentEnd = Carbon::parse($membership->end_date);
            if ($membership->status === 'active' && $currentEnd->isFuture()) {
                $startDate = $currentEnd->copy()->addDay();
            } else {
                $startDate = Carbon::today();
            }
        }

        $endDate = $startDate->copy()->addMonths($plan->duration_months);
        $amountPaid = $request->amount_paid ?? $plan->price;

        $membership->update([
            'plan_id'     => $plan->id,
            'start_date'  => $startDate->toDateString(),
            'end_date'    => $endDate->toDateString(),
            'status'      => 'active',
            'amount_paid' => $amountPaid,
        ]);

        // Sync with CRM Service to ensure the member remains active
        $crmUpdated = false;
        $crmMessage = '';
        $authHeader = $request->header('Authorization');

        try {
            $crmResponse = Http::withHeaders([
                'Authorization' => $authHeader,
                'Accept'        => 'application/json',
            ])->timeout(5)->put(env('CRM_SERVICE_URL', 'http://localhost:8002') . "/api/v1/members/{$membership->member_id}", [
                'status' => 'active',
            ]);

            if ($crmResponse->successful()) {
                $crmUpdated = true;
            } else {
                $crmMessage = 'CRM Service returned status ' . $crmResponse->status();
            }
        } catch (\Exception $e) {
            $crmMessage = 'Could not contact CRM service: ' . $e->getMessage();
        }

        $membership->load('plan');

        return response()->json([
            'status'   => 'success',
            'message' => 'Membership renewed successfully',
            'data'     => $membership,
            'crm_sync' => [
                'status'  => $crmUpdated ? 'success' : 'failed',
                'message' => $crmMessage ?: 'Member status updated to active in CRM service',
            ]
        ]);
    }

    /**
     * Get memberships expiring within 7 days.
     *
     * GET /api/v1/memberships/expiring
     */
    public function expiring(): JsonResponse
    {
        $today = Carbon::today()->toDateString();
        $sevenDaysLater = Carbon::today()->addDays(7)->toDateString();

        $expiringMemberships = Membership::with('plan')
            ->where('status', 'active')
            ->whereBetween('end_date', [$today, $sevenDaysLater])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $expiringMemberships,
        ]);
    }
}
