<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use App\Http\Requests\StoreExportRequest;
use App\Http\Requests\UpdateExportStatusRequest;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function index(Request $request)
    {
        $exports = $this->exportService->paginate($request->get('limit', 15), $request->get('search'));
        return response()->json($exports);
    }

    public function store(StoreExportRequest $request)
    {
        try {
            $export = $this->exportService->create($request->validated());
            return response()->json([
                'message' => 'Tạo phiếu xuất thành công',
                'data' => $export
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi: ' . $e->getMessage()], 400);
        }
    }

    public function show($id)
    {
        $export = $this->exportService->find($id);
        return response()->json($export);
    }

    public function changeStatus(UpdateExportStatusRequest $request, $id)
    {
        try {
            $export = $this->exportService->changeStatus($id, $request->status);
            return response()->json([
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $export
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi: ' . $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->exportService->delete($id);
            return response()->json(['message' => 'Xóa phiếu xuất thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi: ' . $e->getMessage()], 400);
        }
    }
}
