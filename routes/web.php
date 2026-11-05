<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmailTemplateController as AdminEmailTemplateController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SpecialistController as AdminSpecialistController;
use App\Http\Controllers\Admin\TimeSlotController as AdminTimeSlotController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

// Public Frontend
Route::get('/', [FrontendController::class, 'index'])->name('home');

// Customer & Mobile App REST API
Route::prefix('api')->group(function () {
    Route::get('/home', [ApiController::class, 'home'])->name('api.home');
    Route::get('/settings', [ApiController::class, 'getSettings'])->name('api.settings');
    Route::get('/services', [ApiController::class, 'getServices'])->name('api.services');
    Route::get('/services/{id}', [ApiController::class, 'getServiceDetail'])->name('api.services.show');
    Route::get('/specialists', [ApiController::class, 'getSpecialists'])->name('api.specialists');
    Route::get('/plans', [ApiController::class, 'getPlans'])->name('api.plans');
    Route::get('/devices', [ApiController::class, 'getDevices'])->name('api.devices');
    Route::get('/products', [ApiController::class, 'getProducts'])->name('api.products');
    Route::get('/concerns', [ApiController::class, 'getConcerns'])->name('api.concerns');
    Route::get('/reviews', [ApiController::class, 'getReviews'])->name('api.reviews');
    Route::get('/faqs', [ApiController::class, 'getFaqs'])->name('api.faqs');
    
    // Booking & Slots
    Route::get('/available-slots', [BookingController::class, 'getAvailableSlots'])->name('api.slots');
    Route::post('/bookings', [BookingController::class, 'store'])->name('api.bookings.store');
    Route::get('/bookings/track', [ApiController::class, 'getBookingsByPhone'])->name('api.bookings.by_phone');
    Route::get('/bookings/{identifier}', [ApiController::class, 'getBooking'])->name('api.bookings.show');
    Route::post('/bookings/{id}/cancel', [ApiController::class, 'cancelBooking'])->name('api.bookings.cancel');

    // Payments
    Route::post('/payment/create-order', [PaymentController::class, 'createOrder'])->name('api.payment.order');
    Route::post('/payment/verify', [PaymentController::class, 'verifyPayment'])->name('api.payment.verify');
});

// Admin Authentication
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Routes
    Route::middleware('auth')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Bookings
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
        Route::get('/bookings/{id}', [AdminBookingController::class, 'show'])->name('admin.bookings.show');
        Route::put('/bookings/{id}', [AdminBookingController::class, 'update'])->name('admin.bookings.update');
        Route::delete('/bookings/{id}', [AdminBookingController::class, 'destroy'])->name('admin.bookings.destroy');

        // Services
        Route::resource('services', AdminServiceController::class)->names('admin.services');

        // Specialists
        Route::resource('specialists', AdminSpecialistController::class)->names('admin.specialists');

        // Time Slots
        Route::get('/time-slots', [AdminTimeSlotController::class, 'index'])->name('admin.time-slots.index');
        Route::post('/time-slots', [AdminTimeSlotController::class, 'store'])->name('admin.time-slots.store');
        Route::put('/time-slots/{id}', [AdminTimeSlotController::class, 'update'])->name('admin.time-slots.update');
        Route::delete('/time-slots/{id}', [AdminTimeSlotController::class, 'destroy'])->name('admin.time-slots.destroy');

        // Email Templates
        Route::get('/email-templates', [AdminEmailTemplateController::class, 'index'])->name('admin.email-templates.index');
        Route::get('/email-templates/{id}/edit', [AdminEmailTemplateController::class, 'edit'])->name('admin.email-templates.edit');
        Route::put('/email-templates/{id}', [AdminEmailTemplateController::class, 'update'])->name('admin.email-templates.update');
        Route::post('/email-templates/{id}/test', [AdminEmailTemplateController::class, 'sendTest'])->name('admin.email-templates.test');

        // Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');

        // Content management (Plans, Devices, Products, Concerns, FAQs)
        Route::get('/content', [AdminContentController::class, 'index'])->name('admin.content.index');
        Route::post('/content/plans/{id?}', [AdminContentController::class, 'savePlan'])->name('admin.content.plan.save');
        Route::delete('/content/plans/{id}', [AdminContentController::class, 'deletePlan'])->name('admin.content.plan.delete');
        Route::post('/content/devices/{id?}', [AdminContentController::class, 'saveDevice'])->name('admin.content.device.save');
        Route::delete('/content/devices/{id}', [AdminContentController::class, 'deleteDevice'])->name('admin.content.device.delete');
        Route::post('/content/products/{id?}', [AdminContentController::class, 'saveProduct'])->name('admin.content.product.save');
        Route::delete('/content/products/{id}', [AdminContentController::class, 'deleteProduct'])->name('admin.content.product.delete');
        Route::post('/content/faqs/{id?}', [AdminContentController::class, 'saveFaq'])->name('admin.content.faq.save');
        Route::delete('/content/faqs/{id}', [AdminContentController::class, 'deleteFaq'])->name('admin.content.faq.delete');
    });
});
