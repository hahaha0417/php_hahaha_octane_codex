<?php

namespace hahaha\tool\page\system_resource_monitor;

use hahaha\hahaha_instance_clear;

class hahaha_config_system_resource_monitor
{
    use hahaha_instance_clear;

    public $Page_Title_ = '';

    public $Page_Subtitle_ = '';

    public $Route_Name_ = 'tool.page.system_resource_monitor';

    public $Metrics_Route_Name_ = 'tool.page.system_resource_monitor.metrics';

    public $Refresh_Interval_Milliseconds_ = 5000;

    public $Chart_Max_Points_ = 24;

    public $Metric_Options_ = [];

    public function Initial(int $refresh_interval_milliseconds_ = 5000, int $chart_max_points_ = 24): self
    {
        $this->Route_Name_ = 'tool.page.system_resource_monitor';
        $this->Metrics_Route_Name_ = 'tool.page.system_resource_monitor.metrics';
        $this->Refresh_Interval_Milliseconds_ = $this->Refresh_Interval_Milliseconds_Resolve($refresh_interval_milliseconds_);
        $this->Chart_Max_Points_ = $this->Chart_Max_Points_Resolve($chart_max_points_);
        $this->Page_Title_ = 'System Resource Monitor';
        $this->Page_Subtitle_ = '使用 multiple node 規則建立的系統資源監控頁，集中顯示 CPU、記憶體與磁碟使用率，並提供即時圖表追蹤變化。';
        $this->Metric_Options_ = [
            'cpu' => [
                'label' => 'CPU',
                'color' => '#fb7185',
                'description' => '目前主機 CPU 使用率',
            ],
            'memory' => [
                'label' => 'Memory',
                'color' => '#38bdf8',
                'description' => '目前實體記憶體使用率',
            ],
            'disk' => [
                'label' => 'Disk',
                'color' => '#f59e0b',
                'description' => 'Laravel 專案所在磁碟使用率',
            ],
        ];

        return $this;
    }

    public function Refresh_Interval_Milliseconds_Resolve(int $refresh_interval_milliseconds): int
    {
        return max(30, min($refresh_interval_milliseconds, 60000));
    }

    public function Chart_Max_Points_Resolve(int $chart_max_points): int
    {
        return max(10, min($chart_max_points, 120));
    }
}
