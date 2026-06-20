<?php

namespace hahaha\tool\page\system_resource_monitor;

use hahaha\hahaha_instance_clear;
use Illuminate\Support\Facades\Route;

class hahaha_route_system_resource_monitor
{
    use hahaha_instance_clear;

    public function Register(): void
    {
        Route::name('tool.page.')->group(function (): void {
            Route::get('/tool-page/system_resource_monitor', [hahaha_controller_system_resource_monitor::class, 'Index'])->name('system_resource_monitor');
            Route::get('/tool-page/system_resource_monitor/metrics', [hahaha_controller_system_resource_monitor::class, 'Metrics'])->name('system_resource_monitor.metrics');
        });
    }
}
