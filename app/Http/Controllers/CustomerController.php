<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', null);
        
        $customers = $this->customerService->paginate($perPage, $search);
        return response()->json($customers);
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerService->create($request);
        return response()->json($customer, 201);
    }

    public function show($id)
    {
        $customer = $this->customerService->find($id);
        
        if (!$customer) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }

        return response()->json($customer);
    }

    public function update($id, UpdateCustomerRequest $request)
    {
        $customer = $this->customerService->update($id, $request);

        if (!$customer) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }

        return response()->json($customer);
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->customerService->delete($id);

            if (!$deleted) {
                return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
            }

            return response()->json(['message' => 'Khách hàng đã được xóa']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
