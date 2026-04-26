<?php

namespace App\Services;

use App\Repositories\ProductRepository;
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
        return $this->productRepository->create($request->validated());
    }

    public function update($id, $request)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return null;
        }

        $data = $request->validated();
        $data['updated_by'] = Auth::id();

        return $this->productRepository->update($product, $data);
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
        return $this->productRepository->delete($product);
    }
}
