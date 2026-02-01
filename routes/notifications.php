<?php

use App\Http\Controllers\Notifications\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('notifications', '/notifications/index');

    Route::get('notifications/index', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('notifications/archive', [NotificationController::class, 'archive'])
        ->name('notifications.archive');

    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::patch('notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read_all');

    Route::patch('notifications/{id}/unread', [NotificationController::class, 'markAsUnread'])
        ->name('notifications.unread');

    Route::patch('notifications/{id}/archive', [NotificationController::class, 'archiveNotification'])
        ->name('notifications.update_archive');

    Route::patch('notifications/{id}/unarchive', [NotificationController::class, 'unarchiveNotification'])
        ->name('notifications.unarchive');
});
