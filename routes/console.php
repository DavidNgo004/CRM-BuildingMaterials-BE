<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use App\Models\Export;
use App\Services\ExportService;
use Illuminate\Support\Facades\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $exportService = app(ExportService::class);
    $exports = Export::where('status', 'pending')
        ->where('updated_at', '<=', Carbon::now()->subMinutes(10))
        ->get();

    foreach ($exports as $export) {
        try {
            // Re-use logic in ExportService::changeStatus
            // Note: changeStatus sets status = 'approved' and deducts stock
            $exportService->changeStatus($export->id, 'approved');
            Log::info("Auto-approved export ID: {$export->id}, Code: {$export->code}");
        } catch (\Exception $e) {
            Log::error("Auto-approval failed for export {$export->code}: " . $e->getMessage());
        }
    }
})->everyMinute();
