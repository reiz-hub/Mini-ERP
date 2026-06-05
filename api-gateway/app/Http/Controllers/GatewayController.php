<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class GatewayController extends Controller
{
    private function getHeaders()
    {
        $token = Session::get('jwt_token');
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
    }

    /**
     * Display the Dashboard.
     */
    public function dashboard()
    {
        $reportingUrl = env('REPORTING_SERVICE_URL', 'https://fitlife-reporting-service.onrender.com');
        $crmUrl = env('CRM_SERVICE_URL', 'https://fitlife-crm-service.onrender.com');
        $hrUrl = env('HR_SERVICE_URL', 'https://fitlife-hr-service.onrender.com');
        $membershipUrl = env('MEMBERSHIP_SERVICE_URL', 'https://fitlife-membership-service.onrender.com');

        $stats = [
            'total_active_members'     => 0,
            'new_members_this_month'   => 0,
            'renewals_this_month'      => 0,
            'expiring_this_week'       => 0,
            'total_revenue_this_month' => 0.0,
            'total_employees'          => 0,
            'total_plans'              => 0,
        ];

        // Try to fetch reporting summary
        try {
            $repRes = Http::withHeaders($this->getHeaders())->timeout(30)->get("{$reportingUrl}/api/v1/reports/summary");
            if ($repRes->successful()) {
                $stats = array_merge($stats, $repRes->json('data') ?? []);
            }
        } catch (\Exception $e) {
            // Degrade gracefully
        }

        // Try to count employees
        try {
            $empRes = Http::withHeaders($this->getHeaders())->timeout(30)->get("{$hrUrl}/api/v1/employees");
            if ($empRes->successful()) {
                $stats['total_employees'] = count($empRes->json('data') ?? []);
            }
        } catch (\Exception $e) {
            // Degrade gracefully
        }

        // Try to count plans
        try {
            $planRes = Http::withHeaders($this->getHeaders())->timeout(30)->get("{$membershipUrl}/api/v1/plans");
            if ($planRes->successful()) {
                $stats['total_plans'] = count($planRes->json('data') ?? []);
            }
        } catch (\Exception $e) {
            // Degrade gracefully
        }

        return view('dashboard', compact('stats'));
    }

    /**
     * CRM Members View & CRUD
     */
    public function members(Request $request)
    {
        $crmUrl = env('CRM_SERVICE_URL', 'https://fitlife-crm-service.onrender.com');
        $members = [];
        $editMember = null;

        try {
            $res = Http::withHeaders($this->getHeaders())->get("{$crmUrl}/api/v1/members");
            if ($res->successful()) {
                $members = $res->json('data') ?? [];
            }
        } catch (\Exception $e) {
            return view('members', ['members' => [], 'error' => 'CRM Service is currently unreachable: ' . $e->getMessage()]);
        }

        if ($request->has('edit_id')) {
            try {
                $editRes = Http::withHeaders($this->getHeaders())->get("{$crmUrl}/api/v1/members/" . $request->edit_id);
                if ($editRes->successful()) {
                    $editMember = $editRes->json('data');
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        return view('members', compact('members', 'editMember'));
    }

    public function createMember(Request $request)
    {
        $crmUrl = env('CRM_SERVICE_URL', 'https://fitlife-crm-service.onrender.com');
        
        try {
            $response = Http::withHeaders($this->getHeaders())->post("{$crmUrl}/api/v1/members", $request->all());

            if ($response->successful()) {
                return redirect()->route('members')->with('success', 'Member added successfully.');
            }

            $errors = $response->json('errors') ?? ['api' => [$response->json('message') ?? 'Failed to create member.']];
            return back()->withErrors($errors)->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'CRM Service unreachable: ' . $e->getMessage()])->withInput();
        }
    }

    public function updateMember(Request $request, $id)
    {
        $crmUrl = env('CRM_SERVICE_URL', 'https://fitlife-crm-service.onrender.com');

        try {
            $response = Http::withHeaders($this->getHeaders())->put("{$crmUrl}/api/v1/members/{$id}", $request->all());

            if ($response->successful()) {
                return redirect()->route('members')->with('success', 'Member updated successfully.');
            }

            $errors = $response->json('errors') ?? ['api' => [$response->json('message') ?? 'Failed to update member.']];
            return back()->withErrors($errors)->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'CRM Service unreachable: ' . $e->getMessage()])->withInput();
        }
    }

    public function deleteMember($id)
    {
        $crmUrl = env('CRM_SERVICE_URL', 'https://fitlife-crm-service.onrender.com');

        try {
            $response = Http::withHeaders($this->getHeaders())->delete("{$crmUrl}/api/v1/members/{$id}");

            if ($response->successful()) {
                return redirect()->route('members')->with('success', 'Member deleted successfully.');
            }

            return redirect()->route('members')->with('error', $response->json('message') ?? 'Failed to delete member.');
        } catch (\Exception $e) {
            return redirect()->route('members')->with('error', 'CRM Service unreachable: ' . $e->getMessage());
        }
    }

    /**
     * Membership Services View & Operations
     */
    public function memberships()
    {
        $crmUrl = env('CRM_SERVICE_URL', 'https://fitlife-crm-service.onrender.com');
        $membershipUrl = env('MEMBERSHIP_SERVICE_URL', 'https://fitlife-membership-service.onrender.com');

        $members = [];
        $plans = [];
        $memberships = [];

        try {
            // Get plans
            $plansRes = Http::withHeaders($this->getHeaders())->get("{$membershipUrl}/api/v1/plans");
            if ($plansRes->successful()) {
                $plans = $plansRes->json('data') ?? [];
            }

            // Get memberships
            $mRes = Http::withHeaders($this->getHeaders())->get("{$membershipUrl}/api/v1/memberships");
            if ($mRes->successful()) {
                $memberships = $mRes->json('data') ?? [];
            }

            // Get members list for the dropdown select in enrollment form
            $crmRes = Http::withHeaders($this->getHeaders())->get("{$crmUrl}/api/v1/members");
            if ($crmRes->successful()) {
                $members = $crmRes->json('data') ?? [];
            }
        } catch (\Exception $e) {
            return view('memberships', [
                'members' => [], 'plans' => [], 'memberships' => [],
                'error' => 'Membership or CRM Service is unreachable: ' . $e->getMessage()
            ]);
        }

        return view('memberships', compact('members', 'plans', 'memberships'));
    }

    public function createPlan(Request $request)
    {
        $membershipUrl = env('MEMBERSHIP_SERVICE_URL', 'https://fitlife-membership-service.onrender.com');

        try {
            $response = Http::withHeaders($this->getHeaders())->post("{$membershipUrl}/api/v1/plans", $request->all());

            if ($response->successful()) {
                return redirect()->route('memberships')->with('success', 'Plan created successfully.');
            }

            $errors = $response->json('errors') ?? ['api' => [$response->json('message') ?? 'Failed to create plan.']];
            return back()->withErrors($errors)->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Membership Service unreachable: ' . $e->getMessage()])->withInput();
        }
    }

    public function enroll(Request $request)
    {
        $membershipUrl = env('MEMBERSHIP_SERVICE_URL', 'https://fitlife-membership-service.onrender.com');

        try {
            $response = Http::withHeaders($this->getHeaders())->post("{$membershipUrl}/api/v1/memberships/enroll", $request->all());

            if ($response->successful()) {
                return redirect()->route('memberships')->with('success', 'Member enrolled successfully.');
            }

            $errors = $response->json('errors') ?? ['api' => [$response->json('message') ?? 'Failed to enroll member.']];
            return back()->withErrors($errors)->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Membership Service unreachable: ' . $e->getMessage()])->withInput();
        }
    }

    public function renew(Request $request, $id)
    {
        $membershipUrl = env('MEMBERSHIP_SERVICE_URL', 'https://fitlife-membership-service.onrender.com');

        try {
            $response = Http::withHeaders($this->getHeaders())->put("{$membershipUrl}/api/v1/memberships/{$id}/renew", $request->all());

            if ($response->successful()) {
                return redirect()->route('memberships')->with('success', 'Membership renewed successfully.');
            }

            return redirect()->route('memberships')->with('error', $response->json('message') ?? 'Failed to renew membership.');
        } catch (\Exception $e) {
            return redirect()->route('memberships')->with('error', 'Membership Service unreachable: ' . $e->getMessage());
        }
    }

    /**
     * HR Services View & Operations
     */
    public function employees()
    {
        $hrUrl = env('HR_SERVICE_URL', 'https://fitlife-hr-service.onrender.com');
        $crmUrl = env('CRM_SERVICE_URL', 'https://fitlife-crm-service.onrender.com');

        $employees = [];
        $assignments = [];
        $members = [];

        try {
            // Get employees
            $empRes = Http::withHeaders($this->getHeaders())->get("{$hrUrl}/api/v1/employees");
            if ($empRes->successful()) {
                $employees = $empRes->json('data') ?? [];
            }

            // Get assignments
            $asgRes = Http::withHeaders($this->getHeaders())->get("{$hrUrl}/api/v1/assignments");
            if ($asgRes->successful()) {
                $assignments = $asgRes->json('data') ?? [];
            }

            // Get members list for trainer assignment dropdown
            $crmRes = Http::withHeaders($this->getHeaders())->get("{$crmUrl}/api/v1/members");
            if ($crmRes->successful()) {
                $members = $crmRes->json('data') ?? [];
            }
        } catch (\Exception $e) {
            return view('employees', [
                'employees' => [], 'assignments' => [], 'members' => [],
                'error' => 'HR or CRM Service is unreachable: ' . $e->getMessage()
            ]);
        }

        return view('employees', compact('employees', 'assignments', 'members'));
    }

    public function createEmployee(Request $request)
    {
        $hrUrl = env('HR_SERVICE_URL', 'https://fitlife-hr-service.onrender.com');

        try {
            $response = Http::withHeaders($this->getHeaders())->post("{$hrUrl}/api/v1/employees", $request->all());

            if ($response->successful()) {
                return redirect()->route('employees')->with('success', 'Employee added successfully.');
            }

            $errors = $response->json('errors') ?? ['api' => [$response->json('message') ?? 'Failed to create employee.']];
            return back()->withErrors($errors)->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'HR Service unreachable: ' . $e->getMessage()])->withInput();
        }
    }

    public function assignTrainer(Request $request)
    {
        $hrUrl = env('HR_SERVICE_URL', 'https://fitlife-hr-service.onrender.com');

        try {
            $response = Http::withHeaders($this->getHeaders())->post("{$hrUrl}/api/v1/assignments", $request->all());

            if ($response->successful()) {
                return redirect()->route('employees')->with('success', 'Trainer assigned successfully.');
            }

            $errors = $response->json('errors') ?? ['api' => [$response->json('message') ?? 'Failed to assign trainer.']];
            return back()->withErrors($errors)->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'HR Service unreachable: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Reports Summary Dashboard View
     */
    public function reports()
    {
        $reportingUrl = env('REPORTING_SERVICE_URL', 'https://fitlife-reporting-service.onrender.com');
        $reportData = null;

        try {
            $response = Http::withHeaders($this->getHeaders())->get("{$reportingUrl}/api/v1/reports/summary");
            if ($response->successful()) {
                $reportData = $response->json('data');
            } else {
                return view('reports', ['error' => 'Failed to fetch report summary: Status ' . $response->status()]);
            }
        } catch (\Exception $e) {
            return view('reports', ['error' => 'Reporting Service is unreachable: ' . $e->getMessage()]);
        }

        return view('reports', compact('reportData'));
    }
}
