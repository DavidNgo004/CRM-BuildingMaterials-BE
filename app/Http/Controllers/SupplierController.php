<?php

namespace App\Http\Controllers;

use App\Services\SupplierService;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', null);
        
        $suppliers = $this->supplierService->paginate($perPage, $search);
        return response()->json($suppliers);
    }

    public function store(StoreSupplierRequest $request)
    {
        $supplier = $this->supplierService->create($request);
        return response()->json($supplier, 201);
    }

    public function show($id)
    {
        $supplier = $this->supplierService->find($id);
        
        if (!$supplier) {
            return response()->json(['message' => 'Không tìm thấy nhà cung cấp'], 404);
        }

        return response()->json($supplier);
    }

    public function update($id, UpdateSupplierRequest $request)
    {
        $supplier = $this->supplierService->update($id, $request);

        if (!$supplier) {
            return response()->json(['message' => 'Không tìm thấy nhà cung cấp'], 404);
        }

        return response()->json($supplier);
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->supplierService->delete($id);

            if (!$deleted) {
                return response()->json(['message' => 'Không tìm thấy nhà cung cấp'], 404);
            }

            return response()->json(['message' => 'Nhà cung cấp đã được xóa']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
