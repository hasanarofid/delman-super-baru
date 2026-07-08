<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use Illuminate\Support\Facades\Auth;

$user = User::where('nip', '198311202010011009')->first();
if(!$user) die("User not found\n");
Auth::login($user);
echo "User ID: " . $user->id . "\n";

$request = request();
$request->merge(['pengawas' => 'all']);

$controller = app(\App\Http\Controllers\PengawasController::class);
$response = $controller->chartpie($request);

echo $response->getContent() . "\n";
