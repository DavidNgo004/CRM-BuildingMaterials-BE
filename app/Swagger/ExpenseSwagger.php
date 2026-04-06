<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Expense",
 *     description="Quản lý chi phí vận hành (Lương, Điện, Vận chuyển...)"
 * )
 */
class ExpenseSwagger
{
    /**
     * @OA\Get(
     *     path="/api/expenses",
     *     tags={"Expense"},
     *     security={{"bearerAuth":{}}},
     *     summary="Lấy danh sách khoản chi (có phân trang)",
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
     *         description="Tìm kiếm theo tên khoản chi",
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
     *     path="/api/expenses",
     *     summary="Tạo khoản chi mới",
     *     tags={"Expense"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "category", "amount", "expense_date"},
     *             @OA\Property(property="title", type="string", example="Tiền điện tháng 3"),
     *             @OA\Property(property="category", type="string", enum={"salary", "electricity_water", "transport", "other"}, example="electricity_water"),
     *             @OA\Property(property="amount", type="number", format="float", example=5000000),
     *             @OA\Property(property="expense_date", type="string", format="date", example="2026-03-24"),
     *             @OA\Property(property="note", type="string", example="Thanh toán qua chuyển khoản")
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
     *     path="/api/expenses/{id}",
     *     summary="Xem chi tiết khoản chi",
     *     tags={"Expense"},
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
     *     path="/api/expenses/{id}",
     *     summary="Cập nhật khoản chi",
     *     tags={"Expense"},
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
     *             required={"title", "amount", "expense_date"},
     *             @OA\Property(property="title", type="string", example="Tiền điện tháng 3 (Đã sửa)"),
     *             @OA\Property(property="amount", type="number", format="float", example=5500000),
     *             @OA\Property(property="expense_date", type="string", format="date", example="2026-03-24"),
     *             @OA\Property(property="note", type="string", example="Có tính thêm phí VAT")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function update()
    {
    }

    /**
     * @OA\Delete(
     *     path="/api/expenses/{id}",
     *     summary="Xóa khoản chi",
     *     tags={"Expense"},
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
    public function destroy()
    {
    }
}
