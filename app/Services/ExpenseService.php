<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ActivityLog;
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
        $expense = Expense::create($data);

        // ── Activity Log ──────────────────────────────────────────────
        ActivityLogService::log(
            ActivityLog::CREATE_EXPENSE,
            'expense',
            $expense->id,
            null,
            [
                'title'  => $expense->title,
                'amount' => $expense->amount,
            ]
        );

        return $expense;
    }

    public function update($id, array $data)
    {
        $expense = Expense::findOrFail($id);

        // ── Snapshot trước khi update ──────────────────────────────────
        $oldData = [
            'title'  => $expense->title,
            'amount' => $expense->amount,
        ];

        $expense->update($data);

        // ── Activity Log ──────────────────────────────────────────────
        ActivityLogService::log(
            ActivityLog::UPDATE_EXPENSE,
            'expense',
            $id,
            $oldData,
            [
                'title'  => $expense->title,
                'amount' => $expense->amount,
            ]
        );

        return $expense;
    }

    public function delete($id)
    {
        $expense = Expense::findOrFail($id);

        // ── Activity Log (trước khi xóa) ────────────────────────────────
        ActivityLogService::log(
            ActivityLog::DELETE_EXPENSE,
            'expense',
            $id,
            [
                'title'  => $expense->title,
                'amount' => $expense->amount,
            ],
            null
        );

        return $expense->delete();
    }
}
