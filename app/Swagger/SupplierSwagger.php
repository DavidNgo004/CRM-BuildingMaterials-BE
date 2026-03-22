<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Supplier",
 *     description="Quản lý nhà cung cấp"
 * )
 */
class SupplierSwagger
{
    /**
     * @OA\Get(
     *     path="/api/suppliers",
     *     tags={"Supplier"},
     *     security={{"bearerAuth":{}}},
     *     summary="Lấy danh sách nhà cung cấp (có phân trang)",
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Số lượng bản ghi trên một trang",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Từ khóa tìm kiếm (tên, mã, SĐT)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách nhà cung cấp",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="code", type="string", example="NCC001"),
     *                     @OA\Property(property="name", type="string", example="Công ty Xi măng Vicem Hà Tiên"),
     *                     @OA\Property(property="tax_code", type="string", example="0101234567"),
     *                     @OA\Property(property="email", type="string", example="contact@vicem.vn"),
     *                     @OA\Property(property="phone", type="string", example="02812345678"),
     *                     @OA\Property(property="address", type="string", example="Số 1, đường A, HCM"),
     *                     @OA\Property(property="status", type="boolean", example=true)
     *                 )
     *             ),
     *             @OA\Property(property="total", type="integer", example=50)
     *         )
     *     )
     * )
     */
    public function index(){}

    /**
     * @OA\Post(
     *     path="/api/suppliers",
     *     tags={"Supplier"},
     *     security={{"bearerAuth":{}}},
     *     summary="Tạo mới nhà cung cấp",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="code", type="string", example="NCC002"),
     *             @OA\Property(property="name", type="string", example="Công ty Cổ phần Thép Hòa Phát"),
     *             @OA\Property(property="tax_code", type="string", example="0901234567"),
     *             @OA\Property(property="email", type="string", example="contact@hoaphat.com.vn"),
     *             @OA\Property(property="phone", type="string", example="02412345678"),
     *             @OA\Property(property="address", type="string", example="KCN Phố Nối A, Hưng Yên"),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="notes", type="string", example="Nhà cung cấp thép chính")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Tạo nhà cung cấp thành công"
     *     )
     * )
     */
    public function store(){}

    /**
     * @OA\Get(
     *     path="/api/suppliers/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth":{}}},
     *     summary="Xem chi tiết nhà cung cấp",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID nhà cung cấp",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin chi tiết"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy nhà cung cấp"
     *     )
     * )
     */
    public function show(){}

    /**
     * @OA\Put(
     *     path="/api/suppliers/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth":{}}},
     *     summary="Cập nhật nhà cung cấp",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID nhà cung cấp",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", example="NCC002"),
     *             @OA\Property(property="name", type="string", example="Thép Hòa Phát (Updated)"),
     *             @OA\Property(property="tax_code", type="string", example="0901234567"),
     *             @OA\Property(property="email", type="string", example="contact@hoaphat.com.vn"),
     *             @OA\Property(property="phone", type="string", example="02412345678"),
     *             @OA\Property(property="address", type="string", example="KCN Phố Nối A, Hưng Yên"),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="notes", type="string", example="Tạm dừng nhập hàng")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật thành công"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy nhà cung cấp"
     *     )
     * )
     */
    public function update(){}

    /**
     * @OA\Delete(
     *     path="/api/suppliers/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth":{}}},
     *     summary="Xóa nhà cung cấp",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID nhà cung cấp",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Xóa thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Nhà cung cấp đã được xóa")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Không thể xóa do ràng buộc dữ liệu (đã có sản phẩm)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy nhà cung cấp"
     *     )
     * )
     */
    public function destroy(){}
}
