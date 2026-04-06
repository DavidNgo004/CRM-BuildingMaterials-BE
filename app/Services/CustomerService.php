<?php

namespace App\Services;

use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Auth;

class CustomerService
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function paginate($perPage = 15, $search = null)
    {
        return $this->customerRepository->paginate($perPage, $search);
    }

    public function getAll()
    {
        return $this->customerRepository->getAll();
    }

    public function find($id)
    {
        return $this->customerRepository->find($id);
    }

    public function create($request)
    {
        return $this->customerRepository->create($request->validated());
    }

    public function update($id, $request)
    {
        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            return null;
        }

        return $this->customerRepository->update($customer, $request->validated());
    }

    public function delete($id)
    {
        if (Auth::user()->role != 'admin') {
            return [
                'status' => false,
                'message' => 'Unauthorized'
            ];
        }

        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            return false;
        }

        // Logic check: có thể chặn xóa nếu đã có Order liên kết (sắp tới sẽ triển khai)
        if ($customer->exportOrders->count() > 0) {
            return [
                'status' => false,
                'message' => 'Khách hàng đã có đơn xuất hàng'
            ];
        }
        // Hiện tại chưa có module Order, chúng ta để tạm logic delete
        return $this->customerRepository->delete($customer);
    }
}
