<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(){
        $products = $this->productService->getAll();
        return response()->json($products);
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
