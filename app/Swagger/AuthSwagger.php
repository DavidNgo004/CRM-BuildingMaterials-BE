<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="API xác thực người dùng"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthSwagger
{

    /**
     * Login hệ thống
     *
     * @OA\Post(
     *     path="/api/login",
     *     summary="Đăng nhập hệ thống",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="nguyenvana@gmail.com"),
     *             @OA\Property(property="password", type="string", example="nguyenvana123!"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Đăng nhập thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Sai email hoặc mật khẩu"
     *     )
     * )
     */
    public function login() {}



    /**
     * Admin tạo tài khoản nhân viên kho
     *
     * @OA\Post(
     *     path="/api/create-staff",
     *     summary="Admin tạo tài khoản nhân viên kho",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password", "password_confirmation"},
     *
     *             @OA\Property(property="name", type="string", example="Nhân viên kho 1"),
     *             @OA\Property(property="email", type="string", example="staff@gmail.com"),
     *             @OA\Property(property="password", type="string", example="abc123!@#"),
     *             @OA\Property(property="password_confirmation", type="string", example="abc123!@#")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tạo tài khoản thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Staff created"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Nhân viên kho 1"),
     *                 @OA\Property(property="email", type="string", example="staff@gmail.com"),
     *                 @OA\Property(property="role", type="string", example="warehouse_staff")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Không có quyền"
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function createStaff() {}

    /**
     * Lấy thông tin profile của người dùng đã xác thực
     * @OA\Get(
     *    path="/api/profile",
     *    summary="Lấy thông tin profile",
     *    tags={"Auth"},
     *    security={{"bearerAuth":{}}},
     * )
     * 
     * 
     *   @OA\Response(
     *       response=200,
     *      description="Thông tin profile",
     *      @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *       @OA\Property(property="email", type="string", example="nguyenvana@gmail.com")
     *       )
     *   )
     * )
     */

    /**
     * Lấy danh sách nhân viên kho
     *
     * @OA\Get(
     *     path="/api/staffs",
     *     summary="Lấy danh sách nhân viên kho",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách nhân viên kho",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Nhân viên kho 1"),
     *                 @OA\Property(property="email", type="string", example="staff@gmail.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Không có quyền"
     *     )
     * )
     */
    public function getStaffs() {}

    /**
     * Đổi mật khẩu
     *
     * @OA\Post(
     *     path="/api/change-password",
     *     summary="Đổi mật khẩu",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password","password", "password_confirmation"},
     *
     *             @OA\Property(property="current_password", type="string", example="abc123!@#"),
     *             @OA\Property(property="password", type="string", example="newpassword123!"),
     *             @OA\Property(property="password_confirmation", type="string", example="newpassword123!")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Đổi mật khẩu thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password changed")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Mật khẩu hiện tại không đúng"
     *     )
     * )
     */
    public function changePassword() {}

}