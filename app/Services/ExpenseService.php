<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function paginate($perPage = 15, $search = null)
    {
        $query = Expense::with('user')->orderBy('expense_date', 'desc');
        
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    public function find($id)
    {
        return Expense::with('user')->findOrFail($id);
    }

    public function create(array $data)
    {
        $data['user_id'] = auth()->id() ?? 1;
        return Expense::create($data);
    }

    public function update($id, array $data)
    {
        $expense = Expense::findOrFail($id);
        $expense->update($data);
        return $expense;
    }

    public function delete($id)
    {
        $expense = Expense::findOrFail($id);
        return $expense->delete();
    }
}
