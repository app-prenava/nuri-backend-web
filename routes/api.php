<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeteksiController;
use App\Http\Controllers\AirQualityController;
use App\Http\Controllers\CatatanController;
use App\Http\Controllers\KomunitasController;
use App\Http\Controllers\PrediksiDepresiController;
use App\Http\Controllers\RekomendasiMakananController;
use App\Http\Controllers\KickCounterController;
use App\Http\Controllers\SkorEpdsController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\WaterIntakeController;
use App\Http\Controllers\PregnancyCalculatorController;
use App\Http\Controllers\PostpartumArticleController;
use App\Http\Controllers\PostpartumController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IconsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddProfileController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminUserStatusController;
use App\Http\Controllers\RecomendationSportController;
use App\Http\Controllers\PregnancyController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ShopController;



use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('jwt.auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
    
    Route::put('change-password', [AuthController::class, 'change']);
});

Route::post('/profile/create', [AddProfileController::class, 'create']);
Route::post('/profile/update',  [AddProfileController::class, 'update']);
Route::get('/profile', action: [AddProfileController::class, 'show']);


Route::get('/admin/users', [AdminAccountController::class, 'allUser']);
Route::post('/admin/create/account/bidan',  [AdminAccountController::class, 'createBidan']);
Route::post('/admin/create/account/dinkes', [AdminAccountController::class, 'createDinkes']);
Route::post('/admin/users/{userId}/reset-password', [AdminAccountController::class, 'reset']);

Route::get('/admin/shop/logs', [ShopController::class, 'getShopLogs']);
Route::get('/shop', [ShopController::class, 'getByUser']);
Route::get('/shop/all', [ShopController::class, 'getAll']);
Route::post('/shop/create',  [ShopController::class, 'create']);
Route::post('/shop/update/{id}',  [ShopController::class, 'update']);
Route::post('/shop/delete/{id}',  [ShopController::class, 'delete']);

Route::post('/pregnancies/create', [PregnancyController::class, 'create']);
Route::post('/recomendation/sports', [RecomendationSportController::class, 'create']);

Route::post('/admin/users/{userId}/deactivate', [AdminUserStatusController::class, 'deactivate']);
Route::post('/admin/users/{userId}/activate',   [AdminUserStatusController::class, 'activate']);

Route::post('/banner/create', [BannerController::class, 'create']);
Route::post('/banner/update/{id}', [BannerController::class, 'update']);
Route::delete('/banner/delete/{id}', [BannerController::class, 'delete']);

Route::get('/banner/show/production', [BannerController::class, 'ShowOnProd']);
Route::get('/banner/show/all', [BannerController::class, 'ShowAll']);



// Semua route terproteksi
Route::group(['middleware' => 'auth:api'], function () {

    //Profil
    Route::get('/user-data', [UserController::class, 'getUserData']);
    Route::post('/isidata', [UserController::class, 'isidata']);
    Route::post('/update-data/{id}', [UserController::class, 'updateData']);
    Route::get('/icons', [IconsController::class, 'index']);
    Route::put('/user/select-icon', [UserController::class, 'updateSelectedIcon']);

    // Air Quality
    Route::get('/kualitasudara', [AirQualityController::class, 'getCityData']);

    // Komunitas
    Route::get('/komunitas', [KomunitasController::class, 'index']);
    Route::get('/komunitas/{id}', [KomunitasController::class, 'indexid']);
    Route::post('/komunitas/add', [KomunitasController::class, 'store']);
    Route::delete('/komunitas/history/deleteAll', [KomunitasController::class, 'deleteAll']);
    Route::delete('/komunitas/history/{id}', [KomunitasController::class, 'deleteById']);
    Route::post('/komunitas/komen/add/{id}', [KomunitasController::class, 'addComment']);
    Route::post('/komunitas/like/add/{id}', [KomunitasController::class, 'addLike']);
    Route::get('/komunitas/komen/{id}', [KomunitasController::class, 'getComments']);

    // Catatan Kunjungan
    Route::get('/catatan/history', [CatatanController::class, 'index']);
    Route::post('/catatan', [CatatanController::class, 'store']);
    Route::delete('/catatan/history/deleteAll', [CatatanController::class, 'deleteAll']);
    Route::delete('/catatan/history/{id}', [CatatanController::class, 'deleteById']);

    // Deteksi Penyakit
    Route::get('/deteksi/history', [DeteksiController::class, 'index']);
    Route::get('/deteksi/latest', [DeteksiController::class, 'indexlatest']);
    Route::post('/deteksi/store', [DeteksiController::class, 'store']);
    Route::delete('/deteksi/history/deleteAll', [DeteksiController::class, 'deleteAll']);
    Route::delete('/deteksi/history/{id}', [DeteksiController::class, 'deleteById']);

    // Postpartum Recovery Tracker
    Route::get('/Recovery', [PostpartumController::class, 'index']);
    Route::get('/Recovery/history', [PostpartumController::class, 'histindex']);

    // Home
    Route::get('/home', [HomeController::class, 'home']);

    // Prediksi Depresi
    Route::get('/prediksidepresi', [PrediksiDepresiController::class, 'index']);
    Route::post('/prediksidepresi/store', [PrediksiDepresiController::class, 'store']);
    Route::get('/prediksidepresi/{id}', [PrediksiDepresiController::class, 'show']);
    Route::delete('/prediksidepresi/delete/{id}', [PrediksiDepresiController::class, 'deletebyID']);

    // EPDS
    Route::post('/epds/store', [SkorEpdsController::class, 'store']);
    Route::get('/epds', [SkorEpdsController::class, 'index']);
    Route::get('/epds/{id}', [SkorEpdsController::class, 'show']);

    // Rekomendasi Makanan
    Route::get('/rekomendasimakanan', [RekomendasiMakananController::class, 'index']);
    Route::get('/rekomendasimakanan/{id}', [RekomendasiMakananController::class, 'show']);

    // Kick Counter
    Route::get('/kick-counter', [KickCounterController::class, 'index']);
    Route::post('/kick-counter/store', [KickCounterController::class, 'store']);

    // Prediksi Metode Persalinan
    Route::get('/predictions', [PredictionController::class, 'index']);
    Route::post('/predictions', [PredictionController::class, 'store']);
    Route::get('/predictions/{id}', [PredictionController::class, 'show']);
    Route::put('/predictions/{id}', [PredictionController::class, 'update']);
    Route::delete('/predictions/{id}', [PredictionController::class, 'destroy']);

    // Water Intake
    Route::post('/water-intake', [WaterIntakeController::class, 'store']);
    Route::get('/water-intake', [WaterIntakeController::class, 'index']);
    Route::get('/water-intake/{id}', [WaterIntakeController::class, 'show']);
    Route::put('/water-intake/{id}', [WaterIntakeController::class, 'update']);
    Route::delete('/water-intake/{id}', [WaterIntakeController::class, 'destroy']);

    // Kalkulator HPL
    Route::get('pregnancy-calculators', [PregnancyCalculatorController::class, 'index']);
    Route::post('pregnancy-calculators', [PregnancyCalculatorController::class, 'store']);
    Route::get('pregnancy-calculators/{id}', [PregnancyCalculatorController::class, 'show']);
    Route::put('pregnancy-calculators/{id}', [PregnancyCalculatorController::class, 'update']);
    Route::delete('pregnancy-calculators/{id}', [PregnancyCalculatorController::class, 'destroy']);
    Route::post('/pregnancy-calculators/manual', [PregnancyCalculatorController::class, 'storeManual']);


    // PostPartum Article
    Route::get('/postpartum', [PostpartumArticleController::class, 'index']);
    Route::get('/postpartum/{id}', [PostpartumArticleController::class, 'show']);

    // Protected Data
    Route::get('/protected-data', function () {
        return response()->json(['message' => 'Data protected by JWT']);
    });

    // Shop
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);


});


// Welcome
Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Prenava Backend',
    ], 200);
});

