<?php

namespace App\Http\Controllers;

use App\Services\ImportService;
use App\Http\Requests\StoreImportRequest;
use App\Http\Requests\UpdateImportRequest;
use App\Http\Requests\ChangeImportStatusRequest;
use Illuminate\Http\Request;
use Exception;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', null);

        $imports = $this->importService->paginate($perPage, $search);
        return response()->json($imports);
    }

    public function store(StoreImportRequest $request)
    {
        try {
            $import = $this->importService->create($request->validated());
            return response()->json($import, 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show($id)
    {
        $import = $this->importService->find($id);

        if (!$import) {
            return response()->json(['message' => 'Không tìm thấy phiếu nhập'], 404);
        }

        return response()->json($import);
    }

    public function update($id, UpdateImportRequest $request)
    {
        try {
            $import = $this->importService->update($id, $request->validated());
            return response()->json($import);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function changeStatus($id, ChangeImportStatusRequest $request)
    {
        try {
            $import = $this->importService->changeStatus($id, $request->status);
            return response()->json($import);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->importService->delete($id);
            return response()->json(['message' => 'Phiếu nhập đã được xóa']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
