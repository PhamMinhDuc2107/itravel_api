<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\SupportTeamController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\HotelReviewController;
use App\Http\Controllers\Admin\BookingController;
use Illuminate\Support\Facades\Route;

// AUTH
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name("ADMIN_AUTH_LOGIN");
    Route::middleware(['admin.auth'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name("ADMIN_AUTH_LOGOUT");
    });
});

Route::middleware(['admin.auth'])->group(function () {
    // ADMIN
    Route::prefix('admins')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.admins.index');
        Route::post('/', [AdminController::class, 'store'])->name('admin.admins.store');
        Route::get('/{id}', [AdminController::class, 'show'])->name('admin.admins.show');
        Route::put('/{id}', [AdminController::class, 'update'])->name('admin.admins.update');
        Route::delete('/{id}', [AdminController::class, 'destroy'])->name('admin.admins.destroy');
    });
    // CATEGORIES
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/', [CategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('admin.categories.show');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    });
    // BANNERS
    Route::prefix('banners')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('admin.banners.index');
        Route::post('/', [BannerController::class, 'store'])->name('admin.banners.store');
        Route::get('/{id}', [BannerController::class, 'show'])->name('admin.banners.show');
        Route::put('/{id}', [BannerController::class, 'update'])->name('admin.banners.update');
        Route::delete('/{id}', [BannerController::class, 'destroy'])->name('admin.banners.destroy');
    });
    // LOCATIONS
    Route::prefix('locations')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('admin.locations.index');
        Route::post('/', [LocationController::class, 'store'])->name('admin.locations.store');
        Route::get('/{id}', [LocationController::class, 'show'])->name('admin.locations.show');
        Route::put('/{id}', [LocationController::class, 'update'])->name('admin.locations.update');
        Route::delete('/{id}', [LocationController::class, 'destroy'])->name('admin.locations.destroy');
    });
    // BLOG_CATEGORIES
    Route::prefix('blog-categories')->group(function () {
        Route::get('/', [BlogCategoryController::class, 'index'])->name('admin.blog-categories.index');
        Route::post('/', [BlogCategoryController::class, 'store'])->name('admin.blog-categories.store');
        Route::get('/{id}', [BlogCategoryController::class, 'show'])->name('admin.blog-categories.show');
        Route::put('/{id}', [BlogCategoryController::class, 'update'])->name('admin.blog-categories.update');
        Route::delete('/{id}', [BlogCategoryController::class, 'destroy'])->name('admin.blog-categories.destroy');
    });
    // BLOGS
    Route::prefix('blogs')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('admin.blogs.index');
        Route::post('/', [BlogController::class, 'store'])->name('admin.blogs.store');
        Route::get('/{id}', [BlogController::class, 'show'])->name('admin.blogs.show');
        Route::put('/{id}', [BlogController::class, 'update'])->name('admin.blogs.update');
        Route::delete('/{id}', [BlogController::class, 'destroy'])->name('admin.blogs.destroy');
    });
    //SUPPORT_TEAM
    Route::prefix('support-team')->group(function () {
        Route::get('/', [SupportTeamController::class, 'index'])->name('admin.support-team.index');
        Route::post('/', [SupportTeamController::class, 'store'])->name('admin.support-team.store');
        Route::get('/{id}', [SupportTeamController::class, 'show'])->name('admin.support-team.show');
        Route::put('/{id}', [SupportTeamController::class, 'update'])->name('admin.support-team.update');
        Route::delete('/{id}', [SupportTeamController::class, 'destroy'])->name('admin.support-team.destroy');
    });
    // BANK_ACCOUNT
    Route::prefix('bank-accounts')->group(function () {
        Route::get('/', [BankAccountController::class, 'index'])->name('admin.bank-accounts.index');
        Route::post('/', [BankAccountController::class, 'store'])->name('admin.bank-accounts.store');
        Route::get('/{id}', [BankAccountController::class, 'show'])->name('admin.bank-accounts.show');
        Route::put('/{id}', [BankAccountController::class, 'update'])->name('admin.bank-accounts.update');
        Route::delete('/{id}', [BankAccountController::class, 'destroy'])->name('admin.bank-accounts.destroy');
    });
    // CONSULTATIONS
    Route::prefix('consultations')->group(function () {
        Route::get('/', [ConsultationController::class, 'index'])->name('admin.consultations.index');
        Route::post('/', [ConsultationController::class, 'store'])->name('admin.consultations.store');
        Route::get('/{id}', [ConsultationController::class, 'show'])->name('admin.consultations.show');
        Route::put('/{id}', [ConsultationController::class, 'update'])->name('admin.consultations.update');
        Route::delete('/{id}', [ConsultationController::class, 'destroy'])->name('admin.consultations.destroy');
    });
    // TOURS
    Route::prefix('tours')->group(function () {
        Route::get('/', [TourController::class, 'index'])->name('admin.tours.index');
        Route::post('/', [TourController::class, 'store'])->name('admin.tours.store');
        Route::get('/{id}', [TourController::class, 'show'])->name('admin.tours.show');
        Route::put('/{id}', [TourController::class, 'update'])->name('admin.tours.update');
        Route::delete('/{id}', [TourController::class, 'destroy'])->name('admin.tours.destroy');
    });
    // AMENITIES
    Route::prefix('amenities')->group(function () {
        Route::get('/', [AmenityController::class, 'index'])->name('admin.amenities.index');
        Route::post('/', [AmenityController::class, 'store'])->name('admin.amenities.store');
        Route::get('/{id}', [AmenityController::class, 'show'])->name('admin.amenities.show');
        Route::put('/{id}', [AmenityController::class, 'update'])->name('admin.amenities.update');
        Route::delete('/{id}', [AmenityController::class, 'destroy'])->name('admin.amenities.destroy');
    });
    // HOTELS
    Route::prefix('hotels')->group(function () {
        Route::get('/', [HotelController::class, 'index'])->name('admin.hotels.index');
        Route::post('/', [HotelController::class, 'store'])->name('admin.hotels.store');
        Route::get('/{id}', [HotelController::class, 'show'])->name('admin.hotels.show');
        Route::put('/{id}', [HotelController::class, 'update'])->name('admin.hotels.update');
        Route::delete('/{id}', [HotelController::class, 'destroy'])->name('admin.hotels.destroy');
    });
    // HOTEL REVIEWS
    Route::prefix('hotel-reviews')->group(function () {
        Route::get('/', [HotelReviewController::class, 'index'])->name('admin.hotel-reviews.index');
        Route::post('/', [HotelReviewController::class, 'store'])->name('admin.hotel-reviews.store');
        Route::get('/{id}', [HotelReviewController::class, 'show'])->name('admin.hotel-reviews.show');
        Route::put('/{id}', [HotelReviewController::class, 'update'])->name('admin.hotel-reviews.update');
        Route::delete('/{id}', [HotelReviewController::class, 'destroy'])->name('admin.hotel-reviews.destroy');
    });
    // BOOKINGS
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('admin.bookings.index');
        Route::post('/', [BookingController::class, 'store'])->name('admin.bookings.store');
        Route::get('/{id}', [BookingController::class, 'show'])->name('admin.bookings.show');
        Route::put('/{id}', [BookingController::class, 'update'])->name('admin.bookings.update');
        Route::delete('/{id}', [BookingController::class, 'destroy'])->name('admin.bookings.destroy');
    });

});
