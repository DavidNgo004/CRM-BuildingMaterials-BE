<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255|required_without:customer_id',
            'customer_phone' => 'nullable|digits:10|required_without:customer_id',
            'customer_email' => 'nullable|email',
            'discount_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.exists' => 'Khách hàng không tồn tại.',
            'customer_name.required_without' => 'Vui lòng chọn khách hàng có sẵn hoặc nhập tên khách hàng mới.',
            'customer_phone.required_without' => 'Vui lòng chọn khách hàng có sẵn hoặc nhập SĐT khách hàng mới.',
            'details.required' => 'Phiếu xuất phải có ít nhất 1 sản phẩm.',
            'details.min' => 'Phiếu xuất phải có ít nhất 1 sản phẩm.',
            'details.*.product_id.required' => 'Sản phẩm không được để trống.',
            'details.*.product_id.exists' => 'Sản phẩm không tồn tại trong hệ thống.',
            'details.*.quantity.required' => 'Số lượng không được để trống.',
            'details.*.quantity.min' => 'Số lượng phải lớn hơn 0.',
        ];
    }
}
