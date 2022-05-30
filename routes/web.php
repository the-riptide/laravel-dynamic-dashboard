<?php

use Illuminate\Support\Facades\Route;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardIndex;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardManage;
use TheRiptide\LaravelDynamicDashboard\Middleware\AuthorizeDashboardMiddleware;

Route::redirect('dashboard', config('dyndash.dash_home') ?? 'dashboard/article')->name('dyndash.home')->middleware(['web', AuthorizeDashboardMiddleware::class]);

Route::get('/dashboard/create/{type}', DashboardManage::class)->name('dyndash.create')->middleware(['web', AuthorizeDashboardMiddleware::class]);
Route::get('/dashboard/edit/{id}/{type}', DashboardManage::class)->name('dyndash.edit')->middleware(['web', AuthorizeDashboardMiddleware::class]);
Route::get('/dashboard/{type}', DashboardIndex::class)->name('dyndash.index')->middleware(['web', AuthorizeDashboardMiddleware::class]);
