<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Customer",
 *     description="Quản lý khách hàng"
 * )
 */
class CustomerSwagger
{
    /**
     * @OA\Get(
     *     path="/api/customers",
     *     tags={"Customer"},
     *     security={{"bearerAuth":{}}},
     *     summary="Lấy danh sách khách hàng (có phân trang)",
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
     *         description="Từ khóa tìm kiếm (tên, sđt, mã...)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="code", type="string", example="KH-A1B2C3"),
     *                     @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *                     @OA\Property(property="email", type="string", example="nguyenvana@gmail.com", nullable=true),
     *                     @OA\Property(property="phone", type="string", example="0901234567"),
     *                     @OA\Property(property="address", type="string", example="Quận 1, TPHCM"),
     *                     @OA\Property(property="customer_type", type="string", example="wholesale"),
     *                     @OA\Property(property="status", type="boolean", example=true)
     *                 )
     *             ),
     *             @OA\Property(property="total", type="integer", example=20)
     *         )
     *     )
     * )
     */
    public function index(){}

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     tags={"Customer"},
     *     security={{"bearerAuth":{}}},
     *     summary="Tạo mới khách hàng",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "phone"},
     *             @OA\Property(property="name", type="string", example="Trần Thị B"),
     *             @OA\Property(property="email", type="string", example="tranthib@gmail.com"),
     *             @OA\Property(property="phone", type="string", example="0987654321", description="SĐT 10 số tĩnh"),
     *             @OA\Property(property="address", type="string", example="Quận 1, TPHCM"),
     *             @OA\Property(property="customer_type", type="string", example="retail"),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="notes", type="string", example="Khách mua lẻ thường xuyên")
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Thành công")
     * )
     */
    public function store(){}

    /**
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     tags={"Customer"},
     *     security={{"bearerAuth":{}}},
     *     summary="Xem chi tiết khách hàng",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy khách hàng")
     * )
     */
    public function show(){}

    /**
     * @OA\Put(
     *     path="/api/customers/{id}",
     *     tags={"Customer"},
     *     security={{"bearerAuth":{}}},
     *     summary="Cập nhật khách hàng",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Trần Thị Bình"),
     *             @OA\Property(property="email", type="string", example="binh@gmail.com"),
     *             @OA\Property(property="phone", type="string", example="0987654321"),
     *             @OA\Property(property="address", type="string", example="Quận 3, TP. HCM"),
     *             @OA\Property(property="customer_type", type="string", example="retail"),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="notes", type="string", example="Sửa tên bị sai")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy khách hàng")
     * )
     */
    public function update(){}

    /**
     * @OA\Delete(
     *     path="/api/customers/{id}",
     *     tags={"Customer"},
     *     security={{"bearerAuth":{}}},
     *     summary="Xóa khách hàng",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Khách hàng đã được xóa"))
     *     ),
     *     @OA\Response(response=404, description="Không tìm thấy khách hàng")
     * )
     */
    public function destroy(){}
}
