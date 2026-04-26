<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Cache::clear(); // Xóa cache để đo lúc chưa có cache

$c = app(App\Http\Controllers\DashboardController::class);
$req = request();
$req->merge(['period' => 'this_month']);

$start = microtime(true);
$c->kpiCards($req);
echo "KPI Cards (No Cache): " . (microtime(true) - $start) . "s\n";

$start = microtime(true);
$c->kpiCards($req);
echo "KPI Cards (Cached): " . (microtime(true) - $start) . "s\n";

$start = microtime(true);
$c->charts($req);
echo "Charts (No Cache): " . (microtime(true) - $start) . "s\n";

$start = microtime(true);
$c->charts($req);
$start = microtime(true);
$c->alerts($req);
echo "Alerts (No Cache): " . (microtime(true) - $start) . "s\n";

$start = microtime(true);
$c->alerts($req);
echo "Alerts (Cached): " . (microtime(true) - $start) . "s\n";

$start = microtime(true);
$c->recentActivities($req);
echo "Activities (No Cache): " . (microtime(true) - $start) . "s\n";

$start = microtime(true);
$c->recentActivities($req);
echo "Activities (Cached): " . (microtime(true) - $start) . "s\n";

$start = microtime(true);
$c->miniReports($req);
echo "Mini Reports (No Cache): " . (microtime(true) - $start) . "s\n";

$start = microtime(true);
$c->miniReports($req);
echo "Mini Reports (Cached): " . (microtime(true) - $start) . "s\n";
