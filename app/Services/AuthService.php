<?php

namespace App\Services;

use App\Mail\StaffAccountCreatedMail;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login($request)
    {
        $credentials = $request->only('email', 'password');

        $token = JWTAuth::attempt($credentials);

        if (!$token) {

            return [
                'status' => false,
                'message' => 'Invalid credentials'
            ];
        }

        return [
            'status' => true,
            'data' => [
                'token' => $token,
                'user' => Auth::user()
            ]
        ];
    }

    // Get profile of authenticated user
    public function profile()
    {
        return Auth::user();
    }

    public function createWarehouseStaff($request)
    {
        if (Auth::user()->role !== 'admin') { // Only admin can create warehouse staff

            return [
                'status' => false,
                'message' => 'Unauthorized'
            ];
        }

        $plainPassword = $request->password;

        $user = $this->userRepository->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($plainPassword),
            'role' => 'warehouse_staff'
        ]);

        // Gửi email thông tin tài khoản cho nhân viên mới
        try {
            Mail::to($user->email)
                ->send(new StaffAccountCreatedMail($user->name, $user->email, $plainPassword));
        } catch (\Exception $e) {
            // Không dừng luồng chính nếu gửi mail thất bại
            \Log::error('StaffAccountCreatedMail failed: ' . $e->getMessage());
        }

        return [
            'status' => true,
            'data' => $user
        ];
    }

    public function updateStaff($request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return [
                'status' => false,
                'message' => 'Unauthorized'
            ];
        }

        $user = $this->userRepository->find($id);

        if (!$user || $user->role !== 'warehouse_staff') {
            return [
                'status' => false,
                'message' => 'Resource not found or invalid type'
            ];
        }

        $user = $this->userRepository->update($id, $request->validated());

        return [
            'status' => true,
            'data' => $user
        ];
    }

    public function deleteStaff($id)
    {
        if (Auth::user()->role !== 'admin') {
            return [
                'status' => false,
                'message' => 'Unauthorized'
            ];
        }

        $user = $this->userRepository->find($id);

        if (!$user || $user->role !== 'warehouse_staff') {
            return [
                'status' => false,
                'message' => 'Resource not found or invalid type'
            ];
        }

        $this->userRepository->delete($user);

        return [
            'status' => true,
        ];
    }

    // Get List Staffs
    public function listStaff()
    {

        if (Auth::user()->role !== 'admin') {

            return [
                'status' => false,
                'message' => 'Unauthorized'
            ];
        }

        $staffs = $this->userRepository->getStaffs();

        return [
            'status' => true,
            'data' => $staffs
        ];
    }

    // Change password
    public function changePassword($request)
    {

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {

            return [
                'status' => false,
                'message' => 'Current password incorrect'
            ];

        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($request->password)
        ]);

        return [
            'status' => true,
            'message' => 'Password updated'
        ];

    }

}