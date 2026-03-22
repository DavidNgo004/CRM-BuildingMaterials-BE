<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Product",
 *     description="Quản lý sản phẩm"
 * )
 */
class ProductSwagger
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Product"},
     *     security={{"bearerAuth":{}}},
     *     summary="Lấy danh sách sản phẩm",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách sản phẩm",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="supplier_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Xi măng"),
     *                 @OA\Property(property="unit", type="string", example="bao"),
     *                 @OA\Property(property="import_price", type="number", example=50000),
     *                 @OA\Property(property="sell_price", type="number", example=60000)
     *             )
     *         )
     *     )
     * )
     */
    public function index(){}

    /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Product"},
     *     security={{"bearerAuth":{}}},
     *     summary="Tạo mới sản phẩm",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"supplier_id","name","unit","import_price","sell_price"},
     *             @OA\Property(property="supplier_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Xi măng"),
     *             @OA\Property(property="unit", type="string", example="bao"),
     *             @OA\Property(property="import_price", type="number", example=50000),
     *             @OA\Property(property="sell_price", type="number", example=60000)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tạo sản phẩm thành công"
     *     )
     * )
     */
    public function store(){}

    /**
 * @OA\Put(
 *     path="/api/products/{id}",
 *     tags={"Product"},
 *     security={{"bearerAuth":{}}},
 *     summary="Cập nhật sản phẩm",
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID sản phẩm",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"supplier_id"},
 *             @OA\Property(property="supplier_id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Xi mang updated"),
 *             @OA\Property(property="unit", type="string", example="bao"),
 *             @OA\Property(property="import_price", type="number", example=55000),
 *             @OA\Property(property="sell_price", type="number", example=65000)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Cập nhật thành công",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="supplier_id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Xi mang updated"),
 *             @OA\Property(property="unit", type="string", example="bao"),
 *             @OA\Property(property="import_price", type="number", example=55000),
 *             @OA\Property(property="sell_price", type="number", example=65000)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Không tìm thấy sản phẩm"
 *     )
 * )
 */
public function update(){}

/**
 * @OA\Delete(
 *     path="/api/products/{id}",
 *     tags={"Product"},
 *     security={{"bearerAuth":{}}},
 *     summary="Xóa sản phẩm",
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID sản phẩm",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Xóa thành công",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Deleted")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Không tìm thấy sản phẩm"
 *     )
 * )
 */
public function destroy(){}
}