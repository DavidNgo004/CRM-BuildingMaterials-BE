<?php

namespace App\Http\Controllers;

use App\Services\ImportExcelService;
use App\Http\Requests\ImportExcelRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ImportTemplateExport;

class ImportExcelController extends Controller
{
    public function __construct(
        protected ImportExcelService $service
    ) {}

    public function import(ImportExcelRequest $request)
    {
        try {
            $this->service->import(
                $request->file('file')
            );

            return response()->json([
                'message' => 'Import Excel thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new ImportTemplateExport(), 'mau_nhap_kho.xlsx');
    }
}