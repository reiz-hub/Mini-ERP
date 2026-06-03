<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     *
     * GET /api/v1/employees
     */
    public function index(): JsonResponse
    {
        $employees = Employee::all();

        return response()->json([
            'status' => 'success',
            'data' => $employees,
        ]);
    }

    /**
     * Store a newly created employee.
     *
     * POST /api/v1/employees
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:employees',
            'phone'    => 'required|string|max:50',
            'role'     => 'required|string|in:manager,trainer,front_desk,maintenance',
            'branch'   => 'required|string|max:255',
            'schedule' => 'required|string|max:255',
            'status'   => 'sometimes|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $employee = Employee::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'branch'   => $request->branch,
            'schedule' => $request->schedule,
            'status'   => $request->status ?? 'active',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Employee created successfully',
            'data'    => $employee,
        ], 201);
    }

    /**
     * Display the specified employee.
     *
     * GET /api/v1/employees/{id}
     */
    public function show(string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $employee,
        ]);
    }

    /**
     * Update the specified employee.
     *
     * PUT /api/v1/employees/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Employee not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|string|email|max:255|unique:employees,email,' . $id,
            'phone'    => 'sometimes|required|string|max:50',
            'role'     => 'sometimes|required|string|in:manager,trainer,front_desk,maintenance',
            'branch'   => 'sometimes|required|string|max:255',
            'schedule' => 'sometimes|required|string|max:255',
            'status'   => 'sometimes|required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $employee->update($request->only([
            'name',
            'email',
            'phone',
            'role',
            'branch',
            'schedule',
            'status',
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'Employee updated successfully',
            'data'    => $employee,
        ]);
    }

    /**
     * Remove the specified employee.
     *
     * DELETE /api/v1/employees/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Employee not found',
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Employee deleted successfully',
        ]);
    }
}
