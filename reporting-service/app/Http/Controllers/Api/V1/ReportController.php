<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    /**
     * Get reports summary.
     *
     * GET /api/v1/reports/summary
     */
    public function summary(Request $request): JsonResponse
    {
        $authHeader = $request->header('Authorization');

        // Fetch data from CRM Service (port 8002)
        try {
            $crmResponse = Http::withHeaders([
                'Authorization' => $authHeader,
                'Accept'        => 'application/json',
            ])->timeout(30)->get(env('CRM_SERVICE_URL', 'https://localhost:8002') . '/api/v1/members');

            if (!$crmResponse->successful()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to retrieve data from CRM Service: Status ' . $crmResponse->status(),
                ], 502);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'CRM Service is unreachable: ' . $e->getMessage(),
            ], 502);
        }

        // Fetch memberships from Membership Service (port 8003)
        try {
            $membershipsResponse = Http::withHeaders([
                'Authorization' => $authHeader,
                'Accept'        => 'application/json',
            ])->timeout(30)->get(env('MEMBERSHIP_SERVICE_URL', 'https://localhost:8003') . '/api/v1/memberships');

            if (!$membershipsResponse->successful()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to retrieve memberships: Status ' . $membershipsResponse->status(),
                ], 502);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Membership Service memberships endpoint is unreachable: ' . $e->getMessage(),
            ], 502);
        }

        // Fetch expiring memberships from Membership Service (port 8003)
        try {
            $expiringResponse = Http::withHeaders([
                'Authorization' => $authHeader,
                'Accept'        => 'application/json',
            ])->timeout(30)->get(env('MEMBERSHIP_SERVICE_URL', 'https://localhost:8003') . '/api/v1/memberships/expiring');

            if (!$expiringResponse->successful()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to retrieve expiring memberships: Status ' . $expiringResponse->status(),
                ], 502);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Membership Service expiring endpoint is unreachable: ' . $e->getMessage(),
            ], 502);
        }

        // Process CRM data
        $members = $crmResponse->json('data') ?? [];
        $totalActiveMembers = 0;
        $newMembersThisMonth = 0;
        $currentMonth = Carbon::now()->format('Y-m');

        foreach ($members as $member) {
            if (isset($member['status']) && $member['status'] === 'active') {
                $totalActiveMembers++;
            }
            if (isset($member['created_at'])) {
                $createdAt = Carbon::parse($member['created_at']);
                if ($createdAt->format('Y-m') === $currentMonth) {
                    $newMembersThisMonth++;
                }
            }
        }

        // Process Membership data
        $memberships = $membershipsResponse->json('data') ?? [];
        $renewalsThisMonth = 0;
        $totalRevenueThisMonth = 0.0;

        foreach ($memberships as $membership) {
            $createdAt = Carbon::parse($membership['created_at']);
            $updatedAt = Carbon::parse($membership['updated_at']);
            
            $isCreatedThisMonth = $createdAt->format('Y-m') === $currentMonth;
            $isUpdatedThisMonth = $updatedAt->format('Y-m') === $currentMonth;
            
            // Check if it's a renewal (updated_at is strictly greater than created_at)
            $isRenewed = $updatedAt->gt($createdAt);

            if ($isRenewed && $isUpdatedThisMonth) {
                $renewalsThisMonth++;
            }

            // Sum amount_paid if membership was created or renewed this month
            if ($isCreatedThisMonth || ($isRenewed && $isUpdatedThisMonth)) {
                $totalRevenueThisMonth += floatval($membership['amount_paid']);
            }
        }

        $expiringMemberships = $expiringResponse->json('data') ?? [];
        $expiringThisWeek = count($expiringMemberships);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_active_members'      => $totalActiveMembers,
                'new_members_this_month'    => $newMembersThisMonth,
                'renewals_this_month'       => $renewalsThisMonth,
                'expiring_this_week'        => $expiringThisWeek,
                'total_revenue_this_month'  => round($totalRevenueThisMonth, 2),
            ]
        ]);
    }
}
