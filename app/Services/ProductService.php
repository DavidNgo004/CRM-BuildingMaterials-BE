<?php

namespace App\Services;

use App\Repositories\ProductRepository;

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


    public function create($request)
    {
        return $this->productRepository->create($request->validated());
    }

    public function update($id, $request)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return null;
        }

        return $this->productRepository->update($product, $request->validated());
    }

    public function delete($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return false;
        }

        return $this->productRepository->delete($product);
    }
}