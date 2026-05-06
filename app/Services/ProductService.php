<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function paginate($perPage = 15, $search = null)
    {
        return $this->productRepository->paginate($perPage, $search);
    }

    public function getAll()
    {
        return $this->productRepository->getAll();
    }

    public function find($id)
    {
        return $this->productRepository->find($id);
    }


    public function create($request)
    {
        if (Auth::user()->role != 'admin') {
            throw new \Exception('Unauthorized: Only admin can create products.');
        }
        $product = $this->productRepository->create($request->validated());

        // ── Activity Log ──────────────────────────────────────────────
        ActivityLogService::log(
            ActivityLog::CREATE_PRODUCT,
            'product',
            $product->id,
            null,
            [
                'name'         => $product->name,
                'stock'        => $product->stock,
                'sell_price'   => $product->sell_price,
                'import_price' => $product->import_price,
            ]
        );

        return $product;
    }

    public function update($id, $request)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return null;
        }

        // ── Snapshot trước khi update ──────────────────────────────────
        $oldData = [
            'name'         => $product->name,
            'stock'        => $product->stock,
            'sell_price'   => $product->sell_price,
            'import_price' => $product->import_price,
        ];

        $data = $request->validated();
        $data['updated_by'] = Auth::id();

        $updated = $this->productRepository->update($product, $data);

        // ── Activity Log ──────────────────────────────────────────────
        ActivityLogService::log(
            ActivityLog::UPDATE_PRODUCT,
            'product',
            $id,
            $oldData,
            [
                'name'         => $updated->name,
                'stock'        => $updated->stock,
                'sell_price'   => $updated->sell_price,
                'import_price' => $updated->import_price,
            ]
        );

        return $updated;
    }

    public function delete($id)
    {
        if (Auth::user()->role != 'admin') {
            return [
                'status' => false,
                'message' => 'Unauthorized'
            ];
        }
        $product = $this->productRepository->find($id);

        if (!$product) {
            return false;
        }
        //chặn xóa nếu đã có export order  hoặc import order liên kết
        if ($product->exportDetails()->count() > 0 || $product->importDetails()->count() > 0) {
            throw new \Exception('Không thể xóa sản phẩm đã có trong đơn xuất hoặc nhập.');
        }

        // ── Activity Log (trước khi xóa) ────────────────────────────────
        ActivityLogService::log(
            ActivityLog::DELETE_PRODUCT,
            'product',
            $id,
            [
                'name'  => $product->name,
                'stock' => $product->stock,
            ],
            null
        );

        return $this->productRepository->delete($product);
    }
}
