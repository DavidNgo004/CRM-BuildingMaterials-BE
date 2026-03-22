<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function paginate($perPage = 15, $search = null)
    {
        $query = Product::with('supplier');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query->latest()->paginate($perPage);
    }

    public function getAll()
    {
        return Product::with('supplier')->latest()->get();
    }

    public function find($id){
        return Product::find($id);
    }

    public function create($data)
    {
        return Product::create($data);
    }

    public function update($product, $data)
    {
        $product->update($data);
        return $product;
    }

    public function delete($product)
    {
        return $product->delete();
    }

}