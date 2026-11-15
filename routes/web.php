<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->name('password.confirm');

    Route::put('/password', [PasswordController::class, 'update'])
        ->name('password.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/stats', [DashboardController::class, 'stats'])->name('api.dashboard.stats');
    Route::get('/api/products/low-stock', [DashboardController::class, 'lowStockApi'])->name('api.products.low-stock');
    Route::get('/api/transactions/recent', [DashboardController::class, 'recentTransactionsApi'])->name('api.transactions.recent');
    Route::get('/api/dashboard/pending-orders', [DashboardController::class, 'pendingOrdersApi'])->name('api.dashboard.pending-orders');
    Route::get('/api/dashboard/recent-orders', [DashboardController::class, 'recentOrdersApi'])->name('api.dashboard.recent-orders');
    Route::get('/api/dashboard/supplier-performance', [DashboardController::class, 'supplierPerformanceApi'])->name('api.dashboard.supplier-performance');
    Route::get('/api/dashboard/reorder-alerts', [DashboardController::class, 'reorderAlertsApi'])->name('api.dashboard.reorder-alerts');

    // Complete CRUD Routes para sa Products
    Route::resource('products', ProductController::class);
    Route::resource('transactions', InventoryTransactionController::class)->except(['edit', 'update']);
    Route::get('/supplier-performance', [SupplierController::class, 'performance'])->name('supplier-performance.index');
    Route::post('/suppliers/{supplier}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');
    Route::resource('suppliers', SupplierController::class);
    Route::post('/purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('/purchase-orders/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])->name('purchase-orders.reject');
    Route::post('/purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])->name('purchase-orders.send');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::get('/exports/{type}/{format}', [ExportController::class, 'download'])
        ->whereIn('type', ['products', 'purchase-orders', 'transactions', 'suppliers', 'supplier-performance'])
        ->whereIn('format', ['csv', 'pdf'])
        ->name('exports.download');
    Route::get('/adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
    Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Brands are managed separately and retain their existing product assignments.
    Route::get('/brands', function () {
        $brands = Brand::query()->with('products')->orderBy('name')->get();

        return view('brands.index', compact('brands'));
    })->name('brands.index');

    Route::post('/brands', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:brands,name'],
        ]);

        Brand::create($validated);

        return redirect()->route('brands.index')->with('success', 'Brand added successfully.');
    })->name('brands.store');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

    // Categories are managed separately while existing products retain their category names.
    Route::get('/categories', function () {
        $categories = Category::query()->with('products')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    })->name('categories.index');

    Route::post('/categories', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category added successfully.');
    })->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Display registered users without exposing sensitive account fields.
    Route::get('/users', function () {
        abort_unless(request()->user()->hasRole('admin'), 403);
        $users = User::query()->orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    })->name('users.index');

    Route::post('/users', function (Request $request) {
        abort_unless($request->user()->hasRole('admin'), 403);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', \Illuminate\Validation\Rule::in(['admin', 'manager', 'warehouse', 'staff'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'User added successfully.');
    })->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
