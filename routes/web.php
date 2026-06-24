<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::public-profile-browser')->name('home');

Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');
Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

Route::livewire('/browse', 'pages::browse-profiles')->middleware('auth')->name('browse');
Route::livewire('/messages', 'pages::messages')->middleware('auth')->name('messages');
Route::livewire('/chat/{user}', 'pages::chat')->middleware('auth')->name('chat.show');
Route::livewire('/profile/edit', 'pages::edit-profile')->middleware('auth')->name('profile.edit');

require __DIR__.'/auth.php';
