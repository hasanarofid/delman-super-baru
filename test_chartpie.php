<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UmpanbalikT;
use App\User;
use Illuminate\Support\Facades\Auth;

$user = User::where('email', 'bidangsma@delmansuper.com')->first();
if(!$user) die("User not found");
Auth::login($user);

$request = request();
$request->merge(['pengawas' => 'all', 'tahun' => '2026', 'kabupaten' => 'all', 'jenjang' => 'SMA']);

$controller = app(\App\Http\Controllers\AdminController::class);
$response = $controller->chartpie($request);

echo $response->getContent();
