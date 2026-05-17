<?php

namespace App\Services;

use App\Mail\StaffAccountCreatedMail;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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

        $user = Auth::user();
        if ($user->is_locked) {
            JWTAuth::setToken($token)->invalidate();
            return [
                'status' => false,
                'message' => 'Tài khoản của bạn đang bị khoá vui lòng liên hệ admin'
            ];
        }

        return [
            'status' => true,
            'data' => [
                'token' => $token,
                'user' => $user
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
        } catch (\Throwable $e) {
            // Không dừng luồng chính nếu gửi mail thất bại
            Log::error('StaffAccountCreatedMail failed: ' . $e->getMessage());
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

    public function toggleLockStaff($id)
    {
        if (Auth::user()->role !== 'admin') {
            return [
                'status' => false,
                'message' => 'Unauthorized'
            ];
        }

        $user = $this->userRepository->find($id);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'Resource not found'
            ];
        }

        $user->is_locked = !$user->is_locked;
        $user->save();

        try {
            Mail::to($user->email)->send(new \App\Mail\StaffAccountStatusMail($user->name, $user->is_locked));
        } catch (\Throwable $e) {
            Log::error('StaffAccountStatusMail failed: ' . $e->getMessage());
        }

        return [
            'status' => true,
            'data' => $user,
            'message' => $user->is_locked ? 'Tài khoản đã bị khoá' : 'Tài khoản đã được mở khoá'
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

        $hasLinks = \Illuminate\Support\Facades\DB::table('imports')->where('user_id', $id)->exists()
            || \Illuminate\Support\Facades\DB::table('exports')->where('user_id', $id)->exists()
            || \Illuminate\Support\Facades\DB::table('inventory_logs')->where('created_by', $id)->exists()
            || \Illuminate\Support\Facades\DB::table('activity_logs')->where('user_id', $id)->exists();

        if ($hasLinks) {
            $user->delete(); // Soft delete
        } else {
            $user->forceDelete(); // Hard delete
        }

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

    public function forgotPassword($request)
    {
        $user = $this->userRepository->findByEmail($request->email);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'Không tìm thấy tài khoản với email này.'
            ];
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]
        );

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $resetUrl = $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($request->email);

        try {
            Mail::to($request->email)->send(new \App\Mail\ResetPasswordMail($resetUrl));
        } catch (\Throwable $e) {
            Log::error('ResetPasswordMail failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Lỗi gửi mail: ' . $e->getMessage() . ' (File: ' . $e->getFile() . ':' . $e->getLine() . ')'
            ];
        }

        return [
            'status' => true,
            'message' => 'Email chứa liên kết đặt lại mật khẩu đã được gửi.'
        ];
    }

    public function resetPassword($request)
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return [
                'status' => false,
                'message' => 'Token không hợp lệ hoặc đã hết hạn.'
            ];
        }

        // Check if token is older than 60 minutes
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->diffInMinutes(now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return [
                'status' => false,
                'message' => 'Token đã hết hạn. Vui lòng yêu cầu đặt lại mật khẩu mới.'
            ];
        }

        $user = $this->userRepository->findByEmail($request->email);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'Không tìm thấy tài khoản.'
            ];
        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return [
            'status' => true,
            'message' => 'Mật khẩu đã được đặt lại thành công.'
        ];
    }

}