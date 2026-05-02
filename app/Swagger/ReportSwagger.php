<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Report",
 *     description="Quản lý báo cáo - Báo cáo tồn kho, sự cố, yêu cầu"
 * )
 */
class ReportSwagger
{
    /**
     * @OA\Get(
     *     path="/api/reports",
     *     tags={"Report"},
     *     security={{"bearerAuth":{}}},
     *     summary="Lấy danh sách báo cáo",
     *     description="Lấy danh sách báo cáo. Warehouse staff chỉ thấy báo cáo của mình.",
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Số lượng bản ghi mỗi trang",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Số trang",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách báo cáo",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Báo cáo tồn kho tháng 4"),
     *                 @OA\Property(property="content", type="string", example="Tồn kho cần bổ sung thêm xi măng"),
     *                 @OA\Property(property="type", type="string", example="inventory", enum={"inventory", "incident", "general", "request"}),
     *                 @OA\Property(property="status", type="string", example="pending", enum={"pending", "seen", "resolved"}),
     *                 @OA\Property(property="admin_reply", type="string", example=null),
     *                 @OA\Property(property="seen_at", type="string", example=null, format="datetime"),
     *                 @OA\Property(property="created_at", type="string", example="2026-04-29T10:00:00", format="datetime"),
     *                 @OA\Property(property="updated_at", type="string", example="2026-04-29T10:00:00", format="datetime"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *                     @OA\Property(property="email", type="string", example="user@example.com")
     *                 )
     *             )),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=1),
     *             @OA\Property(property="per_page", type="integer", example=20),
     *             @OA\Property(property="total", type="integer", example=10)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token không hợp lệ"
     *     )
     * )
     */
    public function index(){}

    /**
     * @OA\Post(
     *     path="/api/reports",
     *     tags={"Report"},
     *     security={{"bearerAuth":{}}},
     *     summary="Tạo mới báo cáo",
     *     description="Tạo mới một báo cáo",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content", "type"},
     *             @OA\Property(property="title", type="string", example="Báo cáo tồn kho tháng 4", description="Tiêu đề báo cáo"),
     *             @OA\Property(property="content", type="string", example="Tồn kho cần bổ sung thêm xi măng", description="Nội dung báo cáo"),
     *             @OA\Property(property="type", type="string", example="inventory", description="Loại báo cáo", enum={"inventory", "incident", "general", "request"})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Tạo báo cáo thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Báo cáo tồn kho tháng 4"),
     *             @OA\Property(property="content", type="string", example="Tồn kho cần bổ sung thêm xi măng"),
     *             @OA\Property(property="type", type="string", example="inventory"),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(property="admin_reply", type="string", example=null),
     *             @OA\Property(property="seen_at", type="string", example=null),
     *             @OA\Property(property="created_at", type="string", example="2026-04-29T10:00:00"),
     *             @OA\Property(property="updated_at", type="string", example="2026-04-29T10:00:00"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token không hợp lệ"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error - Dữ liệu không hợp lệ"
     *     )
     * )
     */
    public function store(){}

    /**
     * @OA\Put(
     *     path="/api/reports/{id}/seen",
     *     tags={"Report"},
     *     security={{"bearerAuth":{}}},
     *     summary="Đánh dấu báo cáo đã xem",
     *     description="Admin đánh dấu báo cáo đã xem (chỉ admin)",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID báo cáo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Báo cáo tồn kho tháng 4"),
     *             @OA\Property(property="content", type="string", example="Tồn kho cần bổ sung thêm xi măng"),
     *             @OA\Property(property="type", type="string", example="inventory"),
     *             @OA\Property(property="status", type="string", example="seen"),
     *             @OA\Property(property="admin_reply", type="string", example=null),
     *             @OA\Property(property="seen_at", type="string", example="2026-04-29T11:00:00", format="datetime"),
     *             @OA\Property(property="created_at", type="string", example="2026-04-29T10:00:00"),
     *             @OA\Property(property="updated_at", type="string", example="2026-04-29T11:00:00"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token không hợp lệ"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Chỉ admin mới có quyền"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - Báo cáo không tồn tại"
     *     )
     * )
     */
    public function markSeen(){}

    /**
     * @OA\Put(
     *     path="/api/reports/{id}/reply",
     *     tags={"Report"},
     *     security={{"bearerAuth":{}}},
     *     summary="Phản hồi báo cáo",
     *     description="Admin phản hồi và giải quyết báo cáo (chỉ admin)",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID báo cáo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"admin_reply"},
     *             @OA\Property(property="admin_reply", type="string", example="Đã tiếp nhận và xử lý", description="Nội dung phản hồi từ admin")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Phản hồi thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Báo cáo tồn kho tháng 4"),
     *             @OA\Property(property="content", type="string", example="Tồn kho cần bổ sung thêm xi măng"),
     *             @OA\Property(property="type", type="string", example="inventory"),
     *             @OA\Property(property="status", type="string", example="resolved"),
     *             @OA\Property(property="admin_reply", type="string", example="Đã tiếp nhận và xử lý"),
     *             @OA\Property(property="seen_at", type="string", example="2026-04-29T11:00:00", format="datetime"),
     *             @OA\Property(property="created_at", type="string", example="2026-04-29T10:00:00"),
     *             @OA\Property(property="updated_at", type="string", example="2026-04-29T11:30:00"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token không hợp lệ"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Chỉ admin mới có quyền"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - Báo cáo không tồn tại"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error - Dữ liệu không hợp lệ"
     *     )
     * )
     */
    public function reply(){}
}