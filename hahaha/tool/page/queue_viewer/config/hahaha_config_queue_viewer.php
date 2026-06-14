<?php

namespace hahaha\tool\page\queue_viewer;

use hahaha\hahaha_instance_clear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class hahaha_config_queue_viewer
{
    use hahaha_instance_clear;

    public $Page_Title_ = '';

    public $Page_Subtitle_ = '';

    public $Route_Name_ = 'tool.page.queue_viewer';

    public $Connection_Options_ = [];

    public $Database_Options_ = [];

    public $Selected_Connection_ = '';

    public $Selected_Database_ = '';

    public $Queue_Options_ = [];

    public $Selected_Queue_ = '';

    public $Selected_Queue_List_ = [];

    public $Preview_Limit_ = 10;

    public $Queue_Per_Page_ = 10;

    public $Tab_Options_ = [];

    public $Selected_Tab_ = 'setting';

    public function Initial($connection = '', $database = '', $tab = 'setting', $queue = [])
    {
        $this->Route_Name_ = 'tool.page.queue_viewer';
        $this->Preview_Limit_ = 10;
        $this->Queue_Per_Page_ = 10;
        $this->Tab_Options_ = $this->Tab_Options_Resolve();
        $this->Connection_Options_ = $this->Connection_Options_Resolve();
        $this->Selected_Connection_ = $this->Selected_Connection_Resolve((string) $connection);
        $this->Database_Options_ = $this->Database_Options_Resolve($this->Selected_Connection_);
        $this->Selected_Database_ = $this->Selected_Database_Resolve($this->Selected_Connection_, (string) $database);
        $this->Queue_Options_ = $this->Queue_Options_Resolve($this->Selected_Connection_, $this->Selected_Database_);
        $this->Selected_Queue_List_ = $this->Selected_Queue_List_Resolve($queue);
        foreach ($this->Selected_Queue_List_ as $queue_name_) {
            if (! array_key_exists($queue_name_, $this->Queue_Options_)) {
                $this->Queue_Options_[$queue_name_] = $queue_name_;
            }
        }
        $this->Selected_Queue_ = implode(', ', $this->Selected_Queue_List_);
        $this->Selected_Tab_ = $this->Selected_Tab_Resolve((string) $tab);
        $this->Page_Title_ = 'Queue Viewer';
        $this->Page_Subtitle_ = '使用 multiple node 規則建立的 queue 檢視頁，可切換 queue connection 與 db，快速查看目前 queue 狀態。';

        return $this;
    }

    public function Tab_Options_Resolve(): array
    {
        return [
            'setting' => '設定',
            'info' => '資訊',
            'queue' => 'queue',
            'fail_queue' => 'fail_queue',
        ];
    }

    public function Connection_Options_Resolve(): array
    {
        $connection_options_ = [];

        if (is_array(config('queue.connections.database'))) {
            $connection_options_['database'] = 'Database';
        }

        if (is_array(config('queue.connections.redis'))) {
            $connection_options_['redis'] = 'Redis';
        }

        if ($connection_options_ === []) {
            $connection_options_['database'] = 'Database';
        }

        return $connection_options_;
    }

    public function Selected_Connection_Resolve(string $connection_): string
    {
        if (array_key_exists($connection_, $this->Connection_Options_)) {
            return $connection_;
        }

        $default_connection_ = (string) config('queue.default', 'database');

        if (array_key_exists($default_connection_, $this->Connection_Options_)) {
            return $default_connection_;
        }

        return (string) array_key_first($this->Connection_Options_);
    }

    public function Database_Options_Resolve(string $connection_): array
    {
        if ($connection_ === 'redis') {
            $redis_connections_ = config('database.redis', []);
            $database_options_ = [];

            foreach ($redis_connections_ as $redis_connection_key_ => $redis_connection_config_) {
                if (! is_array($redis_connection_config_)) {
                    continue;
                }

                $database_options_[(string) $redis_connection_key_] = 'redis: '.(string) $redis_connection_key_;
            }

            if ($database_options_ === []) {
                $database_options_['default'] = 'redis: default';
            }

            return $database_options_;
        }

        $database_connections_ = config('database.connections', []);
        $database_options_ = [];

        foreach ($database_connections_ as $database_connection_key_ => $database_connection_config_) {
            if (! is_array($database_connection_config_)) {
                continue;
            }

            $database_options_[(string) $database_connection_key_] = (string) $database_connection_key_;
        }

        return $database_options_;
    }

    public function Selected_Database_Resolve(string $connection_, string $database_): string
    {
        if (array_key_exists($database_, $this->Database_Options_)) {
            return $database_;
        }

        if ($connection_ === 'redis') {
            $default_redis_connection_ = (string) config('queue.connections.redis.connection', 'default');

            if (array_key_exists($default_redis_connection_, $this->Database_Options_)) {
                return $default_redis_connection_;
            }

            return (string) array_key_first($this->Database_Options_);
        }

        $default_database_connection_ = (string) config('queue.connections.database.connection', config('database.default'));

        if (array_key_exists($default_database_connection_, $this->Database_Options_)) {
            return $default_database_connection_;
        }

        return (string) array_key_first($this->Database_Options_);
    }

    public function Queue_Options_Resolve(string $connection_, string $database_): array
    {
        $queue_options_ = [
            '' => '全部 queue',
            'default' => 'default',
            'hahaha' => 'hahaha',
        ];

        if ($connection_ === 'redis') {
            $default_queue_name_ = (string) config('queue.connections.redis.queue', 'default');
            $queue_options_[$default_queue_name_] = $default_queue_name_;

            return $queue_options_;
        }

        $queue_names_ = [];

        if (Schema::connection($database_)->hasTable('jobs')) {
            $queue_names_ = array_merge($queue_names_, DB::connection($database_)
                ->table('jobs')
                ->select('queue')
                ->distinct()
                ->orderBy('queue')
                ->pluck('queue')
                ->map(fn ($queue_name_) => trim((string) $queue_name_))
                ->filter(fn (string $queue_name_) => $queue_name_ !== '')
                ->values()
                ->all());
        }

        if (Schema::connection($database_)->hasTable('failed_jobs')) {
            $queue_names_ = array_merge($queue_names_, DB::connection($database_)
                ->table('failed_jobs')
                ->select('queue')
                ->distinct()
                ->orderBy('queue')
                ->pluck('queue')
                ->map(fn ($queue_name_) => trim((string) $queue_name_))
                ->filter(fn (string $queue_name_) => $queue_name_ !== '')
                ->values()
                ->all());
        }

        $queue_names_ = array_values(array_unique($queue_names_));

        foreach ($queue_names_ as $queue_name_) {
            $queue_options_[$queue_name_] = $queue_name_;
        }

        $default_queue_name_ = (string) config('queue.connections.database.queue', 'default');

        if ($default_queue_name_ !== '' && ! array_key_exists($default_queue_name_, $queue_options_)) {
            $queue_options_[$default_queue_name_] = $default_queue_name_;
        }

        return $queue_options_;
    }

    public function Selected_Queue_List_Resolve(array|string $queue_): array
    {
        $queue_list_ = is_array($queue_) ? $queue_ : [$queue_];
        $selected_queue_list_ = [];

        foreach ($queue_list_ as $queue_name_) {
            $queue_name_ = trim((string) $queue_name_);

            if ($queue_name_ === '') {
                continue;
            }

            $selected_queue_list_[] = $queue_name_;
        }

        return array_values(array_unique($selected_queue_list_));
    }

    public function Selected_Tab_Resolve(string $tab_): string
    {
        if (array_key_exists($tab_, $this->Tab_Options_)) {
            return $tab_;
        }

        return 'setting';
    }
}
