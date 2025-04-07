<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InfluencerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewReportController;
use App\Models\Review;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
         'reviews' => Review::with(['platform', 'user'])
                ->latest()
                ->get()
                ->map(function ($review) {
                    // Extract platform name and link
                    $review->social_media = $review->platform->map(function ($platform) {
                        return [
                            'platform' => $platform->platform, // e.g., 'tiktok', 'facebook'
                            'link' => generatePlatformLink($platform->platform, $platform->name) // e.g., 'https://tiktok.com/@user'
                        ];
                    })->toArray();

                    // Add verification status if needed
                    $review->is_verified = optional($review->user)->status;

                    unset($review->platform, $review->user); // Remove unnecessary relations
                    return $review;
                }),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';




Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');
});


Route::get('/customer-reviews', [ReviewController::class, 'showReviews'])->name('customer.reviews');
Route::get('/all-influencer', [ReviewController::class, 'allInfluencer'])->name('all-influencer');


Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', function () {
        return Inertia::render('Customer/Dashboard');
    })->name('customer.dashboard');

    Route::get('/customer/add-influencer', function () {
        return Inertia::render('Customer/AddInfluencer');
    })->name('customer.add-influencer');

    Route::post('/insert-reviews', [ReviewController::class, 'store'])->middleware('auth')->name('customer-review');
    Route::get('/customer/customer-reviews', [ReviewController::class, 'showReviews'])->name('customer.reviews');
    
    Route::get('/customer/reviews/{id}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::post('/reviews', [ReviewController::class, 'update'])->middleware('auth')->name('customer-review-update');
});

Route::middleware(['auth', 'role:influencer'])->group(function () {
    Route::get('/influencer/dashboard', function () {
        return Inertia::render('Influencer/Dashboard');
    })->name('influencer.dashboard');

    Route::post('/influencers/store', [InfluencerController::class, 'store'])->name('influencers.store');
    Route::get('/influencer/edit-detail', [InfluencerController::class, 'index'])->name('influencer.edi-detail');
    Route::get('/influencer/my-reviews',[InfluencerController::class, 'myReviews'])->name('influencer.my-reviews');

    Route::get('/influencer/report-review', function () {
        return Inertia::render('Influencer/Reportreview');
    })->name('influencer.report.review');

    Route::post('/influencer/reports', [ReviewReportController::class, 'store'])->name('influencer.reports.store');
});


/** admin **/
 Route::get('/admin', function () {
        return view('admin/dashboard');
    })->name('admin.dashboard');

Route::get('password/change', [ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change');

Route::post('password/change', [ChangePasswordController::class, 'changePassword'])->name('password.change.submit');

Route::get('admin/users', [UserController::class, 'index'])->name('admin-user');
Route::get('admin/user/{id}', [UserController::class, 'view'])->name('view-user');
Route::get('admin/user/delete/{id}', [UserController::class, 'deleteUser'])->name('admin-delete-user');


Route::get('admin/categories', [CategoryController::class, 'index'])->name('category');
Route::post('admin/categoy/new-skill', [CategoryController::class, 'newCategoyCreate'])->name('admin-new-category');
Route::get('admin/categoy/edit/{id}', [CategoryController::class, 'edit'])->name('admin-categoy-edit');
Route::get('admin/categoy/delete/{id}', [CategoryController::class, 'Delete'])->name('admin-categoy-delete');


Route::get('admin/reported-review', [ReviewReportController::class, 'index'])->name('review.report');
//Route::get('admin/reported-review/status/{id}', [ReviewReportController::class, 'AdminUpdateStatus'])->name('review.status');

Route::get('admin/reviews', [ReviewReportController::class, 'reviews'])->name('admin.reviews');

Route::post('/admin/reviews/{id}/approve', [ReviewReportController::class, 'approve']);
Route::post('/admin/reviews/{id}/reject', [ReviewReportController::class, 'reject']);

