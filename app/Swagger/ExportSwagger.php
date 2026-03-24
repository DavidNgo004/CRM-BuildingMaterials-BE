<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Export",
 *     description="Quản lý Xuất Kho (Phiếu Xuất / Bán Hàng)"
 * )
 */
class ExportSwagger
{
    /**
     * @OA\Get(
     *     path="/api/exports",
     *     tags={"Export"},
     *     security={{"bearerAuth":{}}},
     *     summary="Lấy danh sách phiếu xuất kho (có phân trang)",
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Số lượng bản ghi trên trang",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Tìm kiếm theo mã phiếu",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Thành công"
     *     )
     * )
     */
    public function index()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/exports",
     *     summary="Tạo phiếu xuất mới (Bán hàng/Xuất kho)",
     *     tags={"Export"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"details"},
     *             @OA\Property(property="customer_id", type="integer", example=1, description="Nếu truyền customer_id thì sẽ dùng KH có sẵn"),
     *             @OA\Property(property="customer_name", type="string", example="Trần Văn Mới", description="Tạo KH mới nếu không có customer_id"),
     *             @OA\Property(property="customer_phone", type="string", example="0998887776", description="Tạo KH mới nếu không có customer_id"),
     *             @OA\Property(property="customer_email", type="string", example="[EMAIL_ADDRESS]", description="Tạo KH mới nếu không có customer_id"),
     *             @OA\Property(property="customer_address", type="string", example="123 Nguyễn Văn A", description="Tạo KH mới nếu không có customer_id"),
     *             @OA\Property(property="discount_amount", type="number", format="float", example=0),
     *             @OA\Property(property="note", type="string", example="Bán hàng cho khách A"),
     *             @OA\Property(
     *                 property="details", 
     *                 type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     required={"product_id", "quantity"},
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=10)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Thành công"),
     *     @OA\Response(response=422, description="Lỗi validate")
     * )
     */
    public function store()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/exports/{id}",
     *     summary="Xem chi tiết phiếu xuất",
     *     tags={"Export"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function show()
    {
    }

    /**
     * @OA\Put(
     *     path="/api/exports/{id}/status",
     *     summary="Cập nhật trạng thái phiếu xuất",
     *     tags={"Export"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending", "approved", "completed", "cancelled"}, example="approved")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cập nhật thành công"),
     *     @OA\Response(response=400, description="Lỗi trạng thái")
     * )
     */
    public function changeStatus()
    {
    }

    /**
     * @OA\Delete(
     *     path="/api/exports/{id}",
     *     summary="Xóa phiếu xuất (Chỉ dành cho Admin)",
     *     tags={"Export"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=400, description="Không thể xóa")
     * )
     */
    public function destroy()
    {
    }
}
