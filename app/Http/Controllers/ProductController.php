<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use App\Exports\ProductTemplateExport;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request){
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', null);
        
        $products = $this->productService->paginate($perPage, $search);
        return response()->json($products);
    }

    public function show($id){
        $product = $this->productService->find($id);

        if (!$product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        return response()->json($product);
    }

    public function store(StoreProductRequest $request){
        $product = $this->productService->create($request);
        return response()->json($product, 201);
    }

    public function update($id, UpdateProductRequest $request){
        $product = $this->productService->update($id, $request);

        if(!$product){
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        return response()->json($product);
    }

    public function destroy($id){
        $deleted = $this->productService->delete($id);

        if(!$deleted){
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        return response()->json(['message' => 'Sản phẩm đã được xóa']);
    }

    public function exportExcel()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function downloadTemplateExcel()
    {
        return Excel::download(new ProductTemplateExport, 'mau_nhap_san_pham.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new ProductImport, $request->file('file'));
            return response()->json(['message' => 'Nhập Excel thành công!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi nhập Excel: ' . $e->getMessage()], 400);
        }
    }

}
