<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\CreateStaffRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    // login user and return token
    public function login(LoginRequest $request)
    {

        $result = $this->authService->login($request);

        if(!$result['status']){

            return response()->json([
                'message' => $result['message']
            ],401);
        }

        return response()->json($result['data']);
    }
    // Get user profile
    public function profile()
    {
        $result = $this->authService->profile();

        return response()->json([
            'name' => $result->name,
            'email' => $result->email,
        ]);
    }
    // Create warehouse staff (only admin can create)
    public function createStaff(CreateStaffRequest $request)
    {

        $result = $this->authService->createWarehouseStaff($request);

        if(!$result['status']){

            return response()->json([
                'message' => $result['message']
            ],403);
        }

        return response()->json([
            'message' => 'Staff created',
            'data' => $result['data']
        ]);

    }

    public function getStaffs()
    {

        $result = $this->authService->listStaff();

        if(!$result['status']){

            return response()->json([
                'message'=>$result['message']
            ],403);

        }

        return response()->json($result['data']);

    }

    public function changePassword(ChangePasswordRequest $request)
    {

        $result = $this->authService->changePassword($request);

        if(!$result['status']){

            return response()->json([
                'message'=>$result['message']
            ],400);

        }

        return response()->json([
            'message'=>$result['message']
        ]);

    }

}