<?php

namespace App\Services;

use App\Repositories\SupplierRepository;

class SupplierService
{
    protected $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function paginate($perPage = 15, $search = null)
    {
        return $this->supplierRepository->paginate($perPage, $search);
    }

    public function getAll()
    {
        return $this->supplierRepository->getAll();
    }

    public function find($id)
    {
        return $this->supplierRepository->find($id);
    }

    public function create($request)
    {
        return $this->supplierRepository->create($request->validated());
    }

    public function update($id, $request)
    {
        $supplier = $this->supplierRepository->find($id);

        if (!$supplier) {
            return null;
        }

        return $this->supplierRepository->update($supplier, $request->validated());
    }

    public function delete($id)
    {
        $supplier = $this->supplierRepository->find($id);

        if (!$supplier) {
            return false;
        }

        // Logic check: có thể chặn xóa nếu đã có Product liên kết
        if ($supplier->products()->count() > 0) {
            throw new \Exception('Không thể xóa nhà cung cấp đã có sản phẩm.');
        }

        return $this->supplierRepository->delete($supplier);
    }
}
