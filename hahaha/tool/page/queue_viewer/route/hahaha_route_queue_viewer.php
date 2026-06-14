<?php

namespace hahaha\tool\page\queue_viewer;

use hahaha\hahaha_instance_clear;
use Illuminate\Support\Facades\Route;

class hahaha_route_queue_viewer
{
    use hahaha_instance_clear;

    public function Register(): void
    {
        Route::prefix('tool/page')->name('tool.page.')->group(function (): void {
            Route::get('/queue/viewer', [hahaha_controller_queue_viewer::class, 'Index'])->name('queue_viewer');
            Route::post('/queue/viewer/queue/{job_id}/delete', [hahaha_controller_queue_viewer::class, 'Queue_Delete'])->name('queue_viewer.queue_delete');
            Route::post('/queue/viewer/queue/bulk-delete', [hahaha_controller_queue_viewer::class, 'Queue_Bulk_Delete'])->name('queue_viewer.queue_bulk_delete');
            Route::post('/queue/viewer/queue/clear-selected', [hahaha_controller_queue_viewer::class, 'Queue_Clear_Selected'])->name('queue_viewer.queue_clear_selected');
            Route::post('/queue/viewer/queue/clear-all', [hahaha_controller_queue_viewer::class, 'Queue_Clear_All'])->name('queue_viewer.queue_clear_all');
            Route::post('/queue/viewer/fail-queue/{job_id}/delete', [hahaha_controller_queue_viewer::class, 'Fail_Queue_Delete'])->name('queue_viewer.fail_queue_delete');
            Route::post('/queue/viewer/fail-queue/bulk-delete', [hahaha_controller_queue_viewer::class, 'Fail_Queue_Bulk_Delete'])->name('queue_viewer.fail_queue_bulk_delete');
            Route::post('/queue/viewer/fail-queue/clear-selected', [hahaha_controller_queue_viewer::class, 'Fail_Queue_Clear_Selected'])->name('queue_viewer.fail_queue_clear_selected');
            Route::post('/queue/viewer/fail-queue/clear-all', [hahaha_controller_queue_viewer::class, 'Fail_Queue_Clear_All'])->name('queue_viewer.fail_queue_clear_all');
        });
    }
}
