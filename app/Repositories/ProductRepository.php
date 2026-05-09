<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function paginate($perPage = 15, $search = null, $status = null)
    {
        $query = Product::with(['supplier', 'updatedBy']);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($status !== null) {
            $query->where('status', $status);
            if ($status == 1) {
                $query->whereHas('supplier', function($q) {
                    $q->where('status', 1);
                });
            }
        }

        return $query->latest()->paginate($perPage);
    }

    public function getAll($status = null)
    {
        $query = Product::with(['supplier', 'updatedBy']);
        
        if ($status !== null) {
            $query->where('status', $status);
            if ($status == 1) {
                $query->whereHas('supplier', function($q) {
                    $q->where('status', 1);
                });
            }
        }

        return $query->latest()->get();
    }

    public function find($id){
        return Product::find($id);
    }

    public function findByName($name)
    {
        return Product::where('name', $name)->first();
    }

    public function increaseStock($product, $quantity)
    {
        return $product->increment('stock', $quantity);
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