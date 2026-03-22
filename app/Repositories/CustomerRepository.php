<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function paginate($perPage = 15, $search = null)
    {
        $query = Customer::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('customer_type', 'like', '%' . $search . '%');
        }

        return $query->latest()->paginate($perPage);
    }

    public function getAll()
    {
        return Customer::latest()->get();
    }

    public function find($id)
    {
        return Customer::find($id);
    }

    public function create($data)
    {
        return Customer::create($data);
    }

    public function update($customer, $data)
    {
        $customer->update($data);
        return $customer;
    }

    public function delete($customer)
    {
        return $customer->delete();
    }
}
