<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Services\ExpenseService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $expenses = $this->expenseService->paginate($perPage, $search);
        return response()->json($expenses);
    }

    public function store(StoreExpenseRequest $request)
    {
        $expense = $this->expenseService->create($request->validated());
        return response()->json($expense, 201);
    }

    public function show($id)
    {
        $expense = $this->expenseService->find($id);
        return response()->json($expense);
    }

    public function update(UpdateExpenseRequest $request, $id)
    {
        $expense = $this->expenseService->update($id, $request->validated());
        return response()->json($expense);
    }

    public function destroy($id)
    {
        $this->expenseService->delete($id);
        return response()->json(['message' => 'Xóa khoản chi thành công']);
    }
}
