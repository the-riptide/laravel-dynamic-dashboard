<?php

use Illuminate\Support\Facades\Route;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardIndex;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardManage;

Route::get('/dashboard/create/{type}', DashboardManage::class)->name('dyndash.create');
Route::get('/dashboard/edit/{type}/{id}', DashboardManage::class)->name('dyndash.edit');
Route::get('/dashboard/{type}', DashboardIndex::class)->name('dyndash.index');
