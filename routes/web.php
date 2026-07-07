<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ReportedContentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CircuitsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DriverRankingsController;
use App\Http\Controllers\DriversController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ModerationNotificationController;
use App\Http\Controllers\PitStopsController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RacesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SeasonsController;
use App\Http\Controllers\TeamRankingsController;
use App\Http\Controllers\TeamsController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');
Route::get('/projects', fn () => view('projects'))->name('projects');
Route::get('/posts', [PostsController::class, 'index'])->name('posts.index');
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('users.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/account', fn () => redirect()->route('profile.edit'))->name('account.edit');
    Route::put('/account', [ProfileController::class, 'accountUpdate'])->name('account.update');
    Route::get('/my-posts', [PostsController::class, 'mine'])->name('posts.mine');
    Route::get('/posts/create', [PostsController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostsController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post:slug}/edit', [PostsController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post:slug}', [PostsController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post:slug}', [PostsController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::post('/posts/{post:slug}/comments/{comment}/delete', [CommentController::class, 'destroy'])->name('posts.comments.destroy');
    Route::post('/posts/{post:slug}/like', [PostLikeController::class, 'toggle'])->name('posts.like.toggle');
    Route::post('/posts/{post:slug}/report', [ReportController::class, 'storePost'])->name('posts.report');
    Route::post('/posts/{post:slug}/comments/{comment}/report', [ReportController::class, 'storeComment'])->name('posts.comments.report');
    Route::post('/users/{user}/report', [ReportController::class, 'storeProfile'])->name('users.report');
    Route::post('/moderation-notifications/{notification}/read', [ModerationNotificationController::class, 'markRead'])->name('moderation-notifications.read');
});

Route::get('/posts/{post:slug}', [PostsController::class, 'show'])->name('posts.show');
Route::get('/services', fn () => view('services'))->name('services');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/teams', [TeamsController::class, 'index'])->name('teams.index');
Route::get('/drivers', [DriversController::class, 'index'])->name('drivers.index');
Route::get('/races', [RacesController::class, 'index'])->name('races.index');
Route::get('/circuits', [CircuitsController::class, 'index'])->name('circuits.index');
Route::get('/seasons', [SeasonsController::class, 'index'])->name('seasons.index');
Route::get('/TeamRankings', [TeamRankingsController::class, 'index'])->name('teamRankings.index');
Route::get('/DriverRankings', [DriverRankingsController::class, 'index'])->name('driverRankings.index');
Route::get('/PitStops', [PitStopsController::class, 'index'])->name('pitStops.index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store']);
    Route::get('/register', [AuthController::class, 'registerCreate'])->name('register');
    Route::post('/register', [AuthController::class, 'registerStore']);

    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::resource('posts', PostController::class)->except(['show']);
        Route::post('/posts/{post}/toggle-pin', [PostController::class, 'togglePin'])->name('posts.toggle-pin');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::get('/reported', [ReportedContentController::class, 'index'])->name('reported.index');
        Route::post('/reported/{report}/draft', [ReportedContentController::class, 'draft'])->name('reported.draft');
        Route::post('/reported/{report}/dismiss', [ReportedContentController::class, 'dismiss'])->name('reported.dismiss');
        Route::delete('/reported/{report}', [ReportedContentController::class, 'destroy'])->name('reported.destroy');
    });
