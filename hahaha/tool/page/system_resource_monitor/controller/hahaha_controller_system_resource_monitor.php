<?php

namespace hahaha\tool\page\system_resource_monitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewType;
use Throwable;

class hahaha_controller_system_resource_monitor extends Controller
{
    private const CPU_SAMPLER_STALE_SECONDS_ = 100;

    private const HARDWARE_INFO_JSON_RELATIVE_PATH_ = 'tool_c#/hardware-info.json';

    public function Index(Request $request): ViewType
    {
        $page_config_ = $this->Page_Config_Resolve($request);
        $initial_snapshot_ = $this->System_Snapshot_Resolve($page_config_);
        $frontend_bootstrap_ = json_encode([
            'page' => [
                'refreshIntervalMilliseconds' => $page_config_->Refresh_Interval_Milliseconds_,
                'chartMaxPoints' => $page_config_->Chart_Max_Points_,
                'metricOptions' => $page_config_->Metric_Options_,
                'endpoints' => [
                    'page' => route($page_config_->Route_Name_),
                    'metrics' => route($page_config_->Metrics_Route_Name_),
                ],
            ],
            'snapshot' => $initial_snapshot_,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return View::file(base_path('tool/page/system_resource_monitor/view/hahaha_view_system_resource_monitor.blade.php'), [
            'page_config_' => $page_config_,
            'initial_snapshot_' => $initial_snapshot_,
            'frontend_bootstrap_' => $frontend_bootstrap_,
        ]);
    }

    public function Metrics(Request $request): JsonResponse
    {
        $page_config_ = $this->Page_Config_Resolve($request);

        return response()->json($this->System_Snapshot_Resolve($page_config_));
    }

    public function Page_Config_Resolve(Request $request): hahaha_config_system_resource_monitor
    {
        $refresh_interval_milliseconds_ = (int) $request->query('refresh_ms', 0);
        if ($refresh_interval_milliseconds_ <= 0) {
            $refresh_interval_seconds_ = (int) $request->query('refresh', 5);
            $refresh_interval_milliseconds_ = $refresh_interval_seconds_ * 1000;
        }

        return hahaha_config_system_resource_monitor::Instance()
            ->Clear()
            ->Initial(
                $refresh_interval_milliseconds_,
                (int) $request->query('history', 24),
            );
    }

    public function System_Snapshot_Resolve(hahaha_config_system_resource_monitor $page_config_): array
    {
        $hardware_snapshot_ = $this->Hardware_Snapshot_Resolve();
        $cpu_snapshot_ = $this->Cpu_Snapshot_Resolve($hardware_snapshot_);
        $memory_snapshot_ = $this->Memory_Snapshot_Resolve($hardware_snapshot_);
        $disk_snapshot_ = $this->Disk_Snapshot_Resolve($hardware_snapshot_);
        $timestamp_ = $this->Snapshot_Timestamp_Resolve($hardware_snapshot_);

        return [
            'generated_at' => $timestamp_->format('Y-m-d H:i:s'),
            'sample_label' => $timestamp_->format('H:i:s'),
            'system' => [
                'host_name' => gethostname() ?: php_uname('n'),
                'os' => php_uname('s').' '.php_uname('r'),
                'php_version' => PHP_VERSION,
                'laravel_env' => (string) app()->environment(),
                'app_debug' => config('app.debug') ? 'true' : 'false',
                'cache_driver' => (string) config('cache.default', ''),
                'queue_driver' => (string) config('queue.default', ''),
                'timezone' => (string) config('app.timezone', ''),
                'hardware_source' => (string) ($hardware_snapshot_['source'] ?? 'server-runtime'),
                'hardware_updated_at' => (string) ($hardware_snapshot_['updated_at_display'] ?? $timestamp_->format('Y-m-d H:i:s')),
            ],
            'metrics' => [
                'cpu' => [
                    'label' => 'CPU',
                    'unit' => '%',
                    'value' => $cpu_snapshot_['usage_percent'],
                    'display' => $this->Percent_Display_Resolve($cpu_snapshot_['usage_percent']),
                    'status' => $this->Status_Resolve($cpu_snapshot_['usage_percent']),
                    'description' => $cpu_snapshot_['summary'],
                    'meta' => [
                        'source' => $cpu_snapshot_['source'],
                        'cores' => $cpu_snapshot_['cores'],
                    ],
                ],
                'memory' => [
                    'label' => 'Memory',
                    'unit' => '%',
                    'value' => $memory_snapshot_['usage_percent'],
                    'display' => $this->Percent_Display_Resolve($memory_snapshot_['usage_percent']),
                    'status' => $this->Status_Resolve($memory_snapshot_['usage_percent']),
                    'description' => $this->Bytes_Display_Resolve($memory_snapshot_['used_bytes']).' / '.$this->Bytes_Display_Resolve($memory_snapshot_['total_bytes']),
                    'meta' => [
                        'available' => $this->Bytes_Display_Resolve($memory_snapshot_['free_bytes']),
                        'source' => $memory_snapshot_['source'],
                    ],
                ],
                'disk' => [
                    'label' => 'Disk',
                    'unit' => '%',
                    'value' => $disk_snapshot_['usage_percent'],
                    'display' => $this->Percent_Display_Resolve($disk_snapshot_['usage_percent']),
                    'status' => $this->Status_Resolve($disk_snapshot_['usage_percent']),
                    'description' => $this->Bytes_Display_Resolve($disk_snapshot_['used_bytes']).' / '.$this->Bytes_Display_Resolve($disk_snapshot_['total_bytes']),
                    'meta' => [
                        'mount' => $disk_snapshot_['mount_path'],
                        'free' => $this->Bytes_Display_Resolve($disk_snapshot_['free_bytes']),
                    ],
                ],
            ],
            'details' => [
                'cpu' => $cpu_snapshot_,
                'memory' => $memory_snapshot_,
                'disk' => $disk_snapshot_,
                'hardware' => $hardware_snapshot_,
                'app' => [
                    'memory_limit' => (string) ini_get('memory_limit'),
                    'current_request_memory' => $this->Bytes_Display_Resolve(memory_get_usage(true)),
                    'peak_request_memory' => $this->Bytes_Display_Resolve(memory_get_peak_usage(true)),
                    'base_path' => base_path(),
                    'hardware_info_path' => $this->Hardware_Snapshot_File_Path_Resolve(),
                ],
            ],
            'chart' => [
                'refresh_interval_milliseconds' => $page_config_->Refresh_Interval_Milliseconds_,
                'max_points' => $page_config_->Chart_Max_Points_,
            ],
        ];
    }

    public function Cpu_Snapshot_Resolve(?array $hardware_snapshot_ = null): array
    {
        if (is_array($hardware_snapshot_)) {
            $resolved_snapshot_ = $this->Cpu_Snapshot_From_Hardware_Resolve($hardware_snapshot_);
            if ($resolved_snapshot_ !== null) {
                return $resolved_snapshot_;
            }
        }

        $usage_percent_ = null;
        $source_ = 'unknown';
        $cores_ = $this->Cpu_Core_Count_Resolve();
        $per_core_percentages_ = [];

        if (DIRECTORY_SEPARATOR === '\\') {
            $sampler_snapshot_ = $this->Cpu_Sampler_Snapshot_Resolve();
            if (is_array($sampler_snapshot_)) {
                $per_core_percentages_ = $this->Float_List_Resolve($sampler_snapshot_['per_core_percentages'] ?? []);
                $usage_percent_ = $this->Float_Value_Resolve((string) ($sampler_snapshot_['usage_percent'] ?? ''));
                $cores_ = max((int) ($sampler_snapshot_['cores'] ?? 0), count($per_core_percentages_), $cores_);
                $source_ = (string) ($sampler_snapshot_['source'] ?? 'laravel-command-sampler');
            } else {
                $usage_percent_ = 0.0;
                $source_ = 'sampler-not-running';
            }
        }

        if ($usage_percent_ === null) {
            $load_average_list_ = sys_getloadavg();
            $one_minute_load_ = is_array($load_average_list_) ? ($load_average_list_[0] ?? null) : null;

            if (is_numeric($one_minute_load_) && $cores_ > 0) {
                $usage_percent_ = min(100.0, max(0.0, ((float) $one_minute_load_ / $cores_) * 100));
                $source_ = 'sys_getloadavg';
            }
        }

        if ($usage_percent_ === null) {
            $usage_percent_ = 0.0;
        }

        $usage_percent_ = round($usage_percent_, 1);

        return [
            'usage_percent' => $usage_percent_,
            'source' => $source_,
            'cores' => $cores_,
            'per_core_percentages' => $per_core_percentages_,
            'summary' => '來源 '.$source_.' / '.$cores_.' cores',
        ];
    }

    public function Memory_Snapshot_Resolve(?array $hardware_snapshot_ = null): array
    {
        if (is_array($hardware_snapshot_)) {
            $resolved_snapshot_ = $this->Memory_Snapshot_From_Hardware_Resolve($hardware_snapshot_);
            if ($resolved_snapshot_ !== null) {
                return $resolved_snapshot_;
            }
        }

        $total_bytes_ = null;
        $free_bytes_ = null;
        $source_ = 'unknown';

        if (DIRECTORY_SEPARATOR === '\\') {
            $command_output_ = $this->Command_Output_Resolve('powershell -NoProfile -Command "Get-CimInstance Win32_OperatingSystem | Select-Object TotalVisibleMemorySize,FreePhysicalMemory | ConvertTo-Json -Compress"');
            $decoded_output_ = json_decode($command_output_, true);

            if (is_array($decoded_output_)) {
                $total_bytes_ = ((float) ($decoded_output_['TotalVisibleMemorySize'] ?? 0)) * 1024;
                $free_bytes_ = ((float) ($decoded_output_['FreePhysicalMemory'] ?? 0)) * 1024;
                $source_ = 'powershell:Get-CimInstance Win32_OperatingSystem';
            }
        }

        if (($total_bytes_ === null || $total_bytes_ <= 0) && is_file('/proc/meminfo')) {
            $meminfo_map_ = [];
            foreach ((array) file('/proc/meminfo', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line_) {
                if (! is_string($line_) || ! str_contains($line_, ':')) {
                    continue;
                }

                [$key_, $value_] = explode(':', $line_, 2);
                $meminfo_map_[trim($key_)] = trim($value_);
            }

            $total_kb_ = $this->Kilobytes_Value_Resolve((string) ($meminfo_map_['MemTotal'] ?? '0'));
            $available_kb_ = $this->Kilobytes_Value_Resolve((string) ($meminfo_map_['MemAvailable'] ?? ($meminfo_map_['MemFree'] ?? '0')));

            if ($total_kb_ > 0) {
                $total_bytes_ = $total_kb_ * 1024;
                $free_bytes_ = $available_kb_ * 1024;
                $source_ = '/proc/meminfo';
            }
        }

        if ($total_bytes_ === null || $total_bytes_ <= 0) {
            $total_bytes_ = max(memory_get_peak_usage(true), memory_get_usage(true));
            $free_bytes_ = max($total_bytes_ - memory_get_usage(true), 0);
            $source_ = 'php-memory-fallback';
        }

        $used_bytes_ = max($total_bytes_ - (float) $free_bytes_, 0);
        $usage_percent_ = $total_bytes_ > 0 ? round(($used_bytes_ / $total_bytes_) * 100, 1) : 0.0;

        return [
            'total_bytes' => (float) $total_bytes_,
            'free_bytes' => (float) $free_bytes_,
            'used_bytes' => (float) $used_bytes_,
            'usage_percent' => $usage_percent_,
            'source' => $source_,
        ];
    }

    public function Disk_Snapshot_Resolve(?array $hardware_snapshot_ = null): array
    {
        if (is_array($hardware_snapshot_)) {
            $resolved_snapshot_ = $this->Disk_Snapshot_From_Hardware_Resolve($hardware_snapshot_);
            if ($resolved_snapshot_ !== null) {
                return $resolved_snapshot_;
            }
        }

        $mount_path_ = base_path();
        $total_bytes_ = disk_total_space($mount_path_);
        $free_bytes_ = disk_free_space($mount_path_);

        $total_bytes_ = $total_bytes_ === false ? 0.0 : (float) $total_bytes_;
        $free_bytes_ = $free_bytes_ === false ? 0.0 : (float) $free_bytes_;
        $used_bytes_ = max($total_bytes_ - $free_bytes_, 0);
        $usage_percent_ = $total_bytes_ > 0 ? round(($used_bytes_ / $total_bytes_) * 100, 1) : 0.0;

        return [
            'mount_path' => $mount_path_,
            'total_bytes' => $total_bytes_,
            'free_bytes' => $free_bytes_,
            'used_bytes' => $used_bytes_,
            'usage_percent' => $usage_percent_,
            'source' => 'php-disk-space',
        ];
    }

    public function Cpu_Core_Count_Resolve(): int
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $processor_count_ = (int) getenv('NUMBER_OF_PROCESSORS');
            if ($processor_count_ > 0) {
                return $processor_count_;
            }

            $command_output_ = $this->Command_Output_Resolve('powershell -NoProfile -Command "(Get-CimInstance Win32_ComputerSystem).NumberOfLogicalProcessors"');
            $parsed_value_ = $this->Float_Value_Resolve($command_output_);

            if ($parsed_value_ !== null && $parsed_value_ > 0) {
                return (int) round($parsed_value_);
            }
        }

        $nproc_output_ = $this->Command_Output_Resolve('nproc 2>/dev/null');
        $parsed_nproc_ = $this->Float_Value_Resolve($nproc_output_);
        if ($parsed_nproc_ !== null && $parsed_nproc_ > 0) {
            return (int) round($parsed_nproc_);
        }

        return 1;
    }

    public function Command_Output_Resolve(string $command_): string
    {
        try {
            $output_ = shell_exec($command_);
        } catch (Throwable) {
            $output_ = null;
        }

        if (! is_string($output_)) {
            return '';
        }

        return trim($output_);
    }

    public function Float_Value_Resolve(string $value_): ?float
    {
        if ($value_ === '') {
            return null;
        }

        preg_match('/-?\d+(?:\.\d+)?/', $value_, $matches_);

        if (($matches_[0] ?? '') === '') {
            return null;
        }

        return (float) $matches_[0];
    }

    public function Kilobytes_Value_Resolve(string $value_): float
    {
        $parsed_value_ = $this->Float_Value_Resolve($value_);

        return $parsed_value_ ?? 0.0;
    }

    public function Cpu_Sampler_Snapshot_Resolve(): ?array
    {
        $snapshot_path_ = $this->Cpu_Sampler_Snapshot_Path_Resolve();
        if (! is_file($snapshot_path_)) {
            return null;
        }

        $snapshot_file_modified_at_ = filemtime($snapshot_path_);
        if ($snapshot_file_modified_at_ === false || (time() - $snapshot_file_modified_at_) > self::CPU_SAMPLER_STALE_SECONDS_) {
            return null;
        }

        $snapshot_content_ = @file_get_contents($snapshot_path_);
        if (! is_string($snapshot_content_) || trim($snapshot_content_) === '') {
            return null;
        }

        $decoded_snapshot_ = json_decode($snapshot_content_, true);
        if (! is_array($decoded_snapshot_)) {
            return null;
        }

        $expected_pid_ = $this->Cpu_Sampler_Pid_Resolve();
        $snapshot_pid_ = (int) ($decoded_snapshot_['pid'] ?? 0);
        if ($expected_pid_ === null || $expected_pid_ <= 0 || $snapshot_pid_ !== $expected_pid_) {
            return null;
        }

        return $decoded_snapshot_;
    }

    public function Cpu_Sampler_Snapshot_Path_Resolve(): string
    {
        return storage_path('app/ai-context/system-resource-monitor/cpu-snapshot-foreground.json');
    }

    public function Cpu_Sampler_Pid_Resolve(): ?int
    {
        $pid_path_ = storage_path('app/ai-context/system-resource-monitor/cpu-sampler-foreground.pid');
        if (! is_file($pid_path_)) {
            return null;
        }

        $pid_value_ = trim((string) @file_get_contents($pid_path_));
        $pid_ = $this->Float_Value_Resolve($pid_value_);

        return $pid_ !== null && $pid_ > 0 ? (int) round($pid_) : null;
    }

    public function Hardware_Snapshot_Resolve(): ?array
    {
        $hardware_info_path_ = $this->Hardware_Snapshot_File_Path_Resolve();
        if (! is_file($hardware_info_path_)) {
            return null;
        }

        $snapshot_content_ = @file_get_contents($hardware_info_path_);
        if (! is_string($snapshot_content_) || trim($snapshot_content_) === '') {
            return null;
        }

        $decoded_snapshot_ = json_decode($snapshot_content_, true);
        if (! is_array($decoded_snapshot_)) {
            return null;
        }

        $updated_at_value_ = isset($decoded_snapshot_['UpdatedAt']) ? trim((string) $decoded_snapshot_['UpdatedAt']) : '';
        $updated_at_ = null;

        if ($updated_at_value_ !== '') {
            try {
                $updated_at_ = Carbon::parse($updated_at_value_);
            } catch (Throwable) {
                $updated_at_ = null;
            }
        }

        return [
            'source' => 'hardware-info.json',
            'path' => $hardware_info_path_,
            'updated_at' => $updated_at_,
            'updated_at_display' => $updated_at_?->format('Y-m-d H:i:s') ?? $updated_at_value_,
            'total_cpu_usage_percent' => $this->Bounded_Percent_Resolve($decoded_snapshot_['TotalCpuUsagePercent'] ?? null),
            'core_usages' => $this->Hardware_Core_Usages_Resolve($decoded_snapshot_['CoreUsages'] ?? []),
            'used_memory_bytes' => $this->Positive_Float_Resolve($decoded_snapshot_['UsedMemoryBytes'] ?? null),
            'total_memory_bytes' => $this->Positive_Float_Resolve($decoded_snapshot_['TotalMemoryBytes'] ?? null),
            'used_disk_bytes' => $this->Positive_Float_Resolve($decoded_snapshot_['UsedDiskBytes'] ?? null),
            'total_disk_bytes' => $this->Positive_Float_Resolve($decoded_snapshot_['TotalDiskBytes'] ?? null),
        ];
    }

    public function Hardware_Snapshot_File_Path_Resolve(): string
    {
        return base_path(self::HARDWARE_INFO_JSON_RELATIVE_PATH_);
    }

    public function Snapshot_Timestamp_Resolve(?array $hardware_snapshot_ = null): Carbon
    {
        if ($hardware_snapshot_ !== null && ($hardware_snapshot_['updated_at'] ?? null) instanceof Carbon) {
            return $hardware_snapshot_['updated_at'];
        }

        return now();
    }

    public function Cpu_Snapshot_From_Hardware_Resolve(array $hardware_snapshot_): ?array
    {
        $usage_percent_ = $hardware_snapshot_['total_cpu_usage_percent'] ?? null;
        if (! is_float($usage_percent_) && ! is_int($usage_percent_)) {
            return null;
        }

        $per_core_percentages_ = [];
        foreach ((array) ($hardware_snapshot_['core_usages'] ?? []) as $core_usage_) {
            if (! is_array($core_usage_)) {
                continue;
            }

            $usage_value_ = $core_usage_['usage_percent'] ?? null;
            if (! is_float($usage_value_) && ! is_int($usage_value_)) {
                continue;
            }

            $per_core_percentages_[] = round((float) $usage_value_, 1);
        }

        return [
            'usage_percent' => round((float) $usage_percent_, 1),
            'source' => (string) ($hardware_snapshot_['source'] ?? 'hardware-info.json'),
            'cores' => max(count($per_core_percentages_), $this->Cpu_Core_Count_Resolve()),
            'per_core_percentages' => $per_core_percentages_,
            'summary' => '來源 hardware-info.json / '.count($per_core_percentages_).' cores',
        ];
    }

    public function Memory_Snapshot_From_Hardware_Resolve(array $hardware_snapshot_): ?array
    {
        $total_bytes_ = $hardware_snapshot_['total_memory_bytes'] ?? null;
        $used_bytes_ = $hardware_snapshot_['used_memory_bytes'] ?? null;
        if (! is_float($total_bytes_) || ! is_float($used_bytes_) || $total_bytes_ <= 0) {
            return null;
        }

        $free_bytes_ = max($total_bytes_ - $used_bytes_, 0);
        $usage_percent_ = round(($used_bytes_ / $total_bytes_) * 100, 1);

        return [
            'total_bytes' => $total_bytes_,
            'free_bytes' => $free_bytes_,
            'used_bytes' => $used_bytes_,
            'usage_percent' => $usage_percent_,
            'source' => (string) ($hardware_snapshot_['source'] ?? 'hardware-info.json'),
        ];
    }

    public function Disk_Snapshot_From_Hardware_Resolve(array $hardware_snapshot_): ?array
    {
        $total_bytes_ = $hardware_snapshot_['total_disk_bytes'] ?? null;
        $used_bytes_ = $hardware_snapshot_['used_disk_bytes'] ?? null;
        if (! is_float($total_bytes_) || ! is_float($used_bytes_) || $total_bytes_ <= 0) {
            return null;
        }

        $free_bytes_ = max($total_bytes_ - $used_bytes_, 0);
        $usage_percent_ = round(($used_bytes_ / $total_bytes_) * 100, 1);

        return [
            'mount_path' => 'fixed-drives',
            'total_bytes' => $total_bytes_,
            'free_bytes' => $free_bytes_,
            'used_bytes' => $used_bytes_,
            'usage_percent' => $usage_percent_,
            'source' => (string) ($hardware_snapshot_['source'] ?? 'hardware-info.json'),
        ];
    }

    public function Hardware_Core_Usages_Resolve(mixed $core_usage_list_): array
    {
        if (! is_array($core_usage_list_)) {
            return [];
        }

        $resolved_core_usage_list_ = [];
        foreach ($core_usage_list_ as $core_usage_) {
            if (! is_array($core_usage_)) {
                continue;
            }

            $core_name_ = trim((string) ($core_usage_['CoreName'] ?? ''));
            $usage_percent_ = $this->Bounded_Percent_Resolve($core_usage_['UsagePercent'] ?? null);
            if ($core_name_ === '' || $usage_percent_ === null) {
                continue;
            }

            $resolved_core_usage_list_[] = [
                'core_name' => $core_name_,
                'usage_percent' => $usage_percent_,
            ];
        }

        return $resolved_core_usage_list_;
    }

    public function Cpu_Sampler_Heartbeat_Path_Resolve(): string
    {
        return storage_path('app/ai-context/system-resource-monitor/cpu-heartbeat-foreground.txt');
    }

    public function Cpu_Per_Core_Percentages_Resolve(mixed $decoded_output_): array
    {
        if (! is_array($decoded_output_)) {
            return [];
        }

        $processor_list_ = array_is_list($decoded_output_) ? $decoded_output_ : [$decoded_output_];
        $per_core_percentages_ = [];

        foreach ($processor_list_ as $processor_) {
            if (! is_array($processor_)) {
                continue;
            }

            $core_name_ = isset($processor_['Name']) ? trim((string) $processor_['Name']) : '';
            if ($core_name_ === '' || ! preg_match('/^\d+$/', $core_name_)) {
                continue;
            }

            $core_percent_ = $this->Float_Value_Resolve((string) ($processor_['PercentProcessorTime'] ?? ''));
            if ($core_percent_ === null) {
                continue;
            }

            $per_core_percentages_[] = round(max(0.0, min(100.0, $core_percent_)), 1);
        }

        return $per_core_percentages_;
    }

    public function Float_List_Resolve(mixed $value_list_): array
    {
        if (! is_array($value_list_)) {
            return [];
        }

        $resolved_value_list_ = [];
        foreach ($value_list_ as $value_) {
            $parsed_value_ = is_numeric($value_) ? (float) $value_ : $this->Float_Value_Resolve((string) $value_);
            if ($parsed_value_ === null) {
                continue;
            }

            $resolved_value_list_[] = round(max(0.0, min(100.0, $parsed_value_)), 1);
        }

        return $resolved_value_list_;
    }

    public function Bounded_Percent_Resolve(mixed $value_): ?float
    {
        $parsed_value_ = is_numeric($value_) ? (float) $value_ : $this->Float_Value_Resolve((string) $value_);
        if ($parsed_value_ === null) {
            return null;
        }

        return round(max(0.0, min(100.0, $parsed_value_)), 1);
    }

    public function Positive_Float_Resolve(mixed $value_): ?float
    {
        $parsed_value_ = is_numeric($value_) ? (float) $value_ : $this->Float_Value_Resolve((string) $value_);
        if ($parsed_value_ === null || $parsed_value_ < 0) {
            return null;
        }

        return (float) $parsed_value_;
    }

    public function Percent_Display_Resolve(float $value_): string
    {
        return number_format($value_, 1).'%';
    }

    public function Bytes_Display_Resolve(float $bytes_): string
    {
        $unit_list_ = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $unit_index_ = 0;

        while ($bytes_ >= 1024 && $unit_index_ < count($unit_list_) - 1) {
            $bytes_ /= 1024;
            $unit_index_++;
        }

        return number_format($bytes_, $unit_index_ === 0 ? 0 : 1).' '.$unit_list_[$unit_index_];
    }

    public function Status_Resolve(float $value_): string
    {
        if ($value_ >= 85) {
            return 'critical';
        }

        if ($value_ >= 65) {
            return 'warning';
        }

        return 'healthy';
    }
}
