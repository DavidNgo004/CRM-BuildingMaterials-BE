<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;

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
        // Assuming find method in service calls repository->find
        // For brevity, skipping if not heavily used, but good to add if needed.
        // I will add a simple return if they need it.
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

}
