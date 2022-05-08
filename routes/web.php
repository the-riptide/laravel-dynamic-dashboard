<?php

use Illuminate\Support\Facades\Route;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardIndex;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardManage;
use TheRiptide\LaravelDynamicDashboard\Middleware\AuthorizeDashboardMiddleware;

Route::get('/dashboard/create/{type}', DashboardManage::class)->name('dyndash.create')->middleware(AuthorizeDashboardMiddleware::class);
Route::get('/dashboard/edit/{type}/{id}', DashboardManage::class)->name('dyndash.edit')->middleware(AuthorizeDashboardMiddleware::class);
Route::get('/dashboard/{type}', DashboardIndex::class)->name('dyndash.index')->middleware(['web', AuthorizeDashboardMiddleware::class]);
