<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Enterprise\EnterpriseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

// Route::get('/', [HomeController::class, 'landing'])->middleware('guest')->name('landing');
Route::get('/', function (Request $request) {

    return redirect()->route('login', $request->query());
})->middleware('guest')->name('landing');

Route::get('/webhook/ready/{id}', [PhotoController::class, 'ready']);
Route::get('/locale/{locale}', [LanguageController::class, 'setLocale']);
Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('/home', [HomeController::class, 'index'])->name('home');
    // Route::post('/home', [PhotoController::class, 'store'])->name('lyrics.result');
    Route::get('/home', [PhotoController::class, 'index'])->middleware('plan:home')->name('home');
    Route::get('/referrals', [PhotoController::class, 'referrals'])->name('referrals');
    Route::post('/referrals', [PhotoController::class, 'requestReferralPay'])->name('referrals.request');
    Route::get('/home/{en_id}', [PhotoController::class, 'index'])->middleware('plan:enterprise')->name('user.enterprises.upload');
    Route::get('/enterprise', [HomeController::class, 'enterprise'])->name('user.enterprises');
    Route::get('/enterprise/invite/{token}', [HomeController::class, 'invite'])->name('user.enterprises.invite');
    Route::post('/enterprise/accept/{token}', [HomeController::class, 'accept'])->name('user.enterprises.accept');
    Route::post('/photo/upload', [PhotoController::class, 'upload'])->name('photo.upload');
    Route::post('/background/upload', [PhotoController::class, 'bg_upload'])->name('photo.bg.upload');
    Route::put('/background/status', [PhotoController::class, 'bg_status'])->name('photo.bg.status');
    Route::get('/photo/edit', [PhotoController::class, 'edit_photo'])->name('photos.edit');
    Route::put('/photo/edit/save', [PhotoController::class, 'save_edit'])->name('photo.edit.save');
    Route::post('/photo/delete', [PhotoController::class, 'delete'])->name('photo.delete');
    Route::post('/home', [PhotoController::class, 'create'])->middleware('plan:home')->name('photo.generate');
    Route::post('/home/{en_id}', [PhotoController::class, 'create'])->middleware('plan:enterprise')->name('photo.enterprise.generate');
    Route::get('/loading/{id}', [PhotoController::class, 'processing'])->name('photo.processing');
    Route::get('/loading/{id}/{en_id}', [PhotoController::class, 'processing'])->name('photo.enterprise.processing');
    Route::get('/gallery', [PhotoController::class, 'allPhotos'])->name('photos');
    Route::put('/gallery/downloaded', [PhotoController::class, 'downloaded'])->name('photos.downloaded');
    Route::get('/gallery/{id}/{type}', [PhotoController::class, 'show'])->name('photo.gallery');
    Route::get('/photo/edit/{id}/{index}', [PhotoController::class, 'edit'])->name('photo.edit');
    Route::post('/photo/removebg', [PhotoController::class, 'removeBg'])->middleware('credit')->name('photo.removeBg');
    Route::put('/photo/highRes/{id}', [PhotoController::class, 'setHighRes'])->name('photo.setHighRes');
    Route::get('/refund', [PlanController::class, 'userRefund'])->name('refund');
    Route::post('/refund', [PlanController::class, 'processRefund'])->name('refund.process');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile', [ProfileController::class, 'updatePromo'])->name('profile.promo.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(["auth", "verified"])->group(function () {
    Route::get('pricing', [PlanController::class, 'index'])->name('profile.plans');
    Route::get('pricing/{plan}', [PlanController::class, 'show'])->name("plans.show");
    Route::post('charge', [PlanController::class, 'charge'])->name("charge.create");
    Route::get('buy/credit', [PlanController::class, 'buyCredit'])->name("charge.buyCredit");
    Route::post('pay/credit', [PlanController::class, 'payCredit'])->name("charge.payCredit");
    Route::get('buy/headshot/{id}', [PlanController::class, 'buyMore'])->name("charge.buyMore");
    Route::post('pay/headshot/{id}', [PlanController::class, 'payMore'])->name("charge.payMore");
});

Route::middleware(["auth", "verified", "admin"])->prefix('admin')->group(function () {
    Route::get('users', [AdminController::class, 'index'])->name('admin.users');
    Route::put('users', [AdminController::class, 'update'])->name('admin.users.credit');
    Route::post('users/{id}', [AdminController::class, 'sendPromo'])->name('admin.users.promo');
    Route::get('users/{id}/photos', [PhotoController::class, 'allUserPhotos'])->name('admin.user.photos');
    Route::get('users/{id}/downloads', [PhotoController::class, 'showDownloaded'])->name('admin.user.photos.downloads');
    Route::get('/request/review/{id}', [AdminController::class, 'review_payment_request'])->name('admin.review.request');
    Route::post('/request/review/{id}', [AdminController::class, 'confirm_payment_request'])->name('admin.referrals.request.confirm');
    // Analizing utm params;
    Route::get('analyze', [AdminController::class, 'analyze'])->name('admin.analyze');
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('users/{id}/refund', [PlanController::class, 'refund'])->name('admin.refund');
});

Route::middleware(["auth", "verified", "enterprise"])->prefix('enterprise')->group(function () {
    Route::get('employees', [EnterpriseController::class, 'index'])->name('enterprise.employees');
    Route::post('employees/invite', [EnterpriseController::class, 'invite'])->name('enterprise.invite');
    Route::get('employees/{id}/photos', [PhotoController::class, 'allEmployeePhotos'])->name('enterprise.employee.photos');
});

require __DIR__.'/auth.php';