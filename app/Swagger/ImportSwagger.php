<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Import",
 *     description="Quản lý Nhập Kho (Phiếu Nhập) Đa Nhà Cung Cấp"
 * )
 */
class ImportSwagger
{
    /**
     * @OA\Get(
     *     path="/api/imports",
     *     tags={"Import"},
     *     security={{"bearerAuth":{}}},
     *     summary="Lấy danh sách phiếu nhập kho (có phân trang)",
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
     *         description="Tìm kiếm theo mã phiếu hoặc tên nhà cung cấp",
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
    public function index(){}


    /**
     * @OA\Post(
     *     path="/api/imports",
     *     tags={"Import"},
     *     security={{"bearerAuth":{}}},
     *     summary="Tạo phiếu nhập kho mới (Gom sản phẩm của nhiều nhà cung cấp)",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"details"},
     *
     *             @OA\Property(
     *                 property="note",
     *                 type="string",
     *                 example="Nhập đợt 1 tổng hợp"
     *             ),
     *
     *             @OA\Property(
     *                 property="discount_amount",
     *                 type="number",
     *                 example=100000
     *             ),
     *
     *             @OA\Property(
     *                 property="details",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *                     required={"product_id","quantity","unit_price"},
     *
     *                     @OA\Property(
     *                         property="product_id",
     *                         type="integer",
     *                         example=2
     *                     ),
     *
     *                     @OA\Property(
     *                         property="quantity",
     *                         type="integer",
     *                         example=100
     *                     ),
     *
     *                     @OA\Property(
     *                         property="unit_price",
     *                         type="number",
     *                         example=50000
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Thành công")
     * )
     */
    public function store(){}


    /**
     * @OA\Post(
     *     path="/api/imports/excel",
     *     tags={"Import"},
     *     security={{"bearerAuth":{}}},
     *     summary="Import phiếu nhập kho bằng file Excel",
     *     description="Upload file Excel format: product_name | quantity | unit_price",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"file"},
     *
     *                 @OA\Property(
     *                     property="file",
     *                     description="File Excel (.xlsx hoặc .xls)",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Import Excel thành công"
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="File không hợp lệ"
     *     )
     * )
     */
    public function importExcel(){}


    /**
     * @OA\Get(
     *     path="/api/imports/{id}",
     *     tags={"Import"},
     *     security={{"bearerAuth":{}}},
     *     summary="Xem chi tiết phiếu nhập (kèm mảng chi tiết sản phẩm)",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID phiếu nhập",
     *
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function show(){}


    /**
     * @OA\Put(
     *     path="/api/imports/{id}/status",
     *     tags={"Import"},
     *     security={{"bearerAuth":{}}},
     *     summary="Cập nhật trạng thái phiếu nhập (pending → approved → completed)",
     *     description="approved: gửi báo giá supplier. completed: cộng tồn kho",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID phiếu nhập",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"status"},
     *
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"pending","approved","completed","cancelled"},
     *                 example="approved"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=400, description="Sai logic trạng thái"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function changeStatus(){}


    /**
     * @OA\Delete(
     *     path="/api/imports/{id}",
     *     tags={"Import"},
     *     security={{"bearerAuth":{}}},
     *     summary="Xóa phiếu nhập",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID phiếu nhập",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function destroy(){}
}