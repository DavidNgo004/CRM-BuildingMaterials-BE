<?php

namespace App\Http\Controllers;

use App\Services\ImportExcelService;
use App\Http\Requests\ImportExcelRequest;

class ImportExcelController extends Controller
{
    public function __construct(
        protected ImportExcelService $service
    ) {}

    public function import(ImportExcelRequest $request)
    {
        $this->service->import(
            $request->file('file')
        );

        return response()->json([
            'message' => 'Import Excel thành công'
        ]);
    }
}