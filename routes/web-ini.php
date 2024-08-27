<?php

use App\Http\Controllers\ResellerAdmin\DashboardController;
use App\Http\Controllers\ResellerAdmin\LoginController;
use App\Http\Controllers\ResellerAdmin\UserController;
use App\Models\Tenant;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
// echo $request->route()->getPrefix();
// Route::get('/', function () {
//     return view('welcome');
// });
$current_uri = request()->segments();
// dd(count($current_uri));
$tenantId = "";
if (count($current_uri) > 0 && $current_uri[0] != 'api') {
    $subdomain = $current_uri[0];
    $tenant = Tenant::where('subdomain', '=', $subdomain)->select('id')->first();
    if (!empty($tenant)) {
        $tenantId = $tenant->id;
    }
}
if ($tenantId == "") {
    Route::get('/{page?}', [HomeController::class, 'index'])->name('front_index');
    Route::any('/about', ['as' => 'page', 'uses' => '\App\Http\Controllers\HomeController@page']);
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants/store', [TenantController::class, 'store'])->name('tenants.store');
    // Your other routes...
}

// Your reseller-specific routes go here
//if (count($current_uri) > 0 && $current_uri[0] != 'api') {
// dd($current_uri[0]);

//if ($current_uri[0] == 'reseller-admin') {
// dd("admin==" . count($current_uri));
Route::group(['prefix' => 'reseller-admin', 'namespace' => 'ResellerAdmin'], function () {

    Route::get('/', [LoginController::class, 'index'])->name('admin_login');
    Route::post('/dologin', [LoginController::class, 'dologin'])->name('do_admin_login');

    // Route::any('/forgot-password', array('as' => 'admin_forgot_password', 'uses' => 'LoginController@forgot_password'));
    // Route::get('/reset-password/{token}', array('as' => 'admin_reset_newpassword', 'uses' => 'LoginController@resetPassword'));
    // Route::post('/reset-password/{token}', array('as' => 'admin_password_update', 'uses' => 'LoginController@updatePassword'));

});

Route::group(['prefix' => 'reseller-admin', 'namespace' => 'ResellerAdmin', 'middleware' => 'admin'], function () {
    Route::any('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout', [LoginController::class, 'logout'])->name('admin_logout');
    Route::any('/change-password', [DashboardController::class, 'change_password'])->name('admin_change_password');
    Route::any('/update-profile', [UserController::class, 'updateAdminProfile'])->name('admin_update_profile');
    // Route::any('/update-profile', array('as' => 'admin_update_profile', 'uses' => 'UserController@updateAdminProfile'));
});
return false;
//}

Route::group(['prefix' => '{param}', 'middleware' => 'tenant'], function () {
    Route::get('/', [BlogController::class, 'index'])->name('tenant_index');
    Route::get('/details', [BlogController::class, 'details'])->name('tenant_details');
});
return false;
// }

// Route::middleware(['tenant'])->group(function () use ($current_uri) {
//     // dd($domainSplice);
//     // Your tenant-specific routes go here
//     if (count($current_uri) > 1 && $current_uri[0] != 'api') {
//         Route::get('/', [BlogController::class, 'index']);

//     }
// });
