<?php

namespace hahaha\tool\page\queue_viewer;

use App\Enums\db\hahaha as HahahaTable;
use App\Enums\db\hahaha\failed_jobs as HahahaFailedJobsColumn;
use App\Enums\db\hahaha\jobs as HahahaJobsColumn;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewType;
use Throwable;

class hahaha_controller_queue_viewer extends Controller
{
    public function Index(Request $request): ViewType
    {
        $page_config_ = hahaha_config_queue_viewer::Instance()
            ->Clear()
            ->Initial(
                (string) $request->query('connection', ''),
                (string) $request->query('db', ''),
                (string) $request->query('tab', 'setting'),
                $this->Request_Queue_List_Resolve($request)
            );
        $queue_snapshot_ = $this->Queue_Snapshot_Resolve($page_config_, $request);

        return View::file(base_path('tool/page/queue_viewer/view/hahaha_view_queue_viewer.blade.php'), [
            'page_config_' => $page_config_,
            'queue_snapshot_' => $queue_snapshot_,
            'status_message_' => (string) session('queue_viewer_status_', $queue_snapshot_['status_message']),
            'error_message_' => (string) session('queue_viewer_error_', ''),
        ]);
    }

    public function Queue_Delete(Request $request, string $job_id): RedirectResponse
    {
        $page_config_ = $this->Page_Config_From_Request_Resolve($request, 'queue');
        $queue_action_context_ = $this->Database_Queue_Action_Context_Resolve($page_config_);

        if ($queue_action_context_['is_supported'] === false) {
            return $this->Queue_Redirect_Response_Resolve($page_config_, [
                'queue_page' => (int) $request->input('queue_page', 1),
            ])->with('queue_viewer_error_', $queue_action_context_['message']);
        }

        $deleted_rows_count_ = DB::connection($page_config_->Selected_Database_)
            ->table($queue_action_context_['jobs_table_name'])
            ->where(HahahaJobsColumn::ID->value, (int) $job_id)
            ->delete();

        if ($deleted_rows_count_ === 0) {
            return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_error_', '刪除失敗：找不到指定的 queue job。');
        }

        return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_status_', 'Queue job 已刪除。');
    }

    public function Queue_Bulk_Delete(Request $request): RedirectResponse
    {
        $page_config_ = $this->Page_Config_From_Request_Resolve($request, 'queue');
        $queue_action_context_ = $this->Database_Queue_Action_Context_Resolve($page_config_);

        if ($queue_action_context_['is_supported'] === false) {
            return $this->Queue_Redirect_Response_Resolve($page_config_, [
                'queue_page' => (int) $request->input('queue_page', 1),
            ])->with('queue_viewer_error_', $queue_action_context_['message']);
        }

        $validated_data_ = $request->validate([
            'selected_job_ids' => ['required', 'array', 'min:1'],
            'selected_job_ids.*' => ['required', 'integer', 'min:1'],
        ], [], [
            'selected_job_ids' => '多選 jobs',
            'selected_job_ids.*' => 'job id',
        ]);

        $selected_job_ids_ = array_values(array_unique(array_map('intval', $validated_data_['selected_job_ids'])));
        $deleted_rows_count_ = DB::connection($page_config_->Selected_Database_)
            ->table($queue_action_context_['jobs_table_name'])
            ->whereIn(HahahaJobsColumn::ID->value, $selected_job_ids_)
            ->delete();

        return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_status_', '已刪除 '.$deleted_rows_count_.' 筆 queue jobs。');
    }

    public function Queue_Clear_Selected(Request $request): RedirectResponse
    {
        $page_config_ = $this->Page_Config_From_Request_Resolve($request, 'queue');
        $queue_action_context_ = $this->Database_Queue_Action_Context_Resolve($page_config_);

        if ($queue_action_context_['is_supported'] === false) {
            return $this->Queue_Redirect_Response_Resolve($page_config_, [
                'queue_page' => (int) $request->input('queue_page', 1),
            ])->with('queue_viewer_error_', $queue_action_context_['message']);
        }

        if ($page_config_->Selected_Queue_List_ === []) {
            return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_error_', '請先指定至少一個 queue，再清空 jobs。');
        }

        $deleted_rows_count_ = DB::connection($page_config_->Selected_Database_)
            ->table($queue_action_context_['jobs_table_name'])
            ->whereIn(HahahaJobsColumn::QUEUE->value, $page_config_->Selected_Queue_List_)
            ->delete();

        return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_status_', '已清空指定 queue 的 '.$deleted_rows_count_.' 筆 jobs。');
    }

    public function Queue_Clear_All(Request $request): RedirectResponse
    {
        $page_config_ = $this->Page_Config_From_Request_Resolve($request, 'queue');
        $queue_action_context_ = $this->Database_Queue_Action_Context_Resolve($page_config_);

        if ($queue_action_context_['is_supported'] === false) {
            return $this->Queue_Redirect_Response_Resolve($page_config_, [
                'queue_page' => (int) $request->input('queue_page', 1),
            ])->with('queue_viewer_error_', $queue_action_context_['message']);
        }

        $deleted_rows_count_ = DB::connection($page_config_->Selected_Database_)
            ->table($queue_action_context_['jobs_table_name'])
            ->delete();

        return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_status_', '已清空全部 queue 的 '.$deleted_rows_count_.' 筆 jobs。');
    }

    public function Fail_Queue_Delete(Request $request, string $job_id): RedirectResponse
    {
        $page_config_ = $this->Page_Config_From_Request_Resolve($request, 'fail_queue');
        $failed_queue_action_context_ = $this->Database_Failed_Queue_Action_Context_Resolve($page_config_);

        if ($failed_queue_action_context_['is_supported'] === false) {
            return $this->Queue_Redirect_Response_Resolve($page_config_, [
                'fail_queue_page' => (int) $request->input('fail_queue_page', 1),
            ])->with('queue_viewer_error_', $failed_queue_action_context_['message']);
        }

        $deleted_rows_count_ = DB::connection($page_config_->Selected_Database_)
            ->table($failed_queue_action_context_['failed_jobs_table_name'])
            ->where(HahahaFailedJobsColumn::ID->value, (int) $job_id)
            ->delete();

        if ($deleted_rows_count_ === 0) {
            return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_error_', '刪除失敗：找不到指定的 failed queue job。');
        }

        return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_status_', 'Failed queue job 已刪除。');
    }

    public function Fail_Queue_Bulk_Delete(Request $request): RedirectResponse
    {
        $page_config_ = $this->Page_Config_From_Request_Resolve($request, 'fail_queue');
        $failed_queue_action_context_ = $this->Database_Failed_Queue_Action_Context_Resolve($page_config_);

        if ($failed_queue_action_context_['is_supported'] === false) {
            return $this->Queue_Redirect_Response_Resolve($page_config_, [
                'fail_queue_page' => (int) $request->input('fail_queue_page', 1),
            ])->with('queue_viewer_error_', $failed_queue_action_context_['message']);
        }

        $validated_data_ = $request->validate([
            'selected_failed_job_ids' => ['required', 'array', 'min:1'],
            'selected_failed_job_ids.*' => ['required', 'integer', 'min:1'],
        ], [], [
            'selected_failed_job_ids' => '多選 failed jobs',
            'selected_failed_job_ids.*' => 'failed job id',
        ]);

        $selected_failed_job_ids_ = array_values(array_unique(array_map('intval', $validated_data_['selected_failed_job_ids'])));
        $deleted_rows_count_ = DB::connection($page_config_->Selected_Database_)
            ->table($failed_queue_action_context_['failed_jobs_table_name'])
            ->whereIn(HahahaFailedJobsColumn::ID->value, $selected_failed_job_ids_)
            ->delete();

        return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_status_', '已刪除 '.$deleted_rows_count_.' 筆 failed queue jobs。');
    }

    public function Fail_Queue_Clear_Selected(Request $request): RedirectResponse
    {
        $page_config_ = $this->Page_Config_From_Request_Resolve($request, 'fail_queue');
        $failed_queue_action_context_ = $this->Database_Failed_Queue_Action_Context_Resolve($page_config_);

        if ($failed_queue_action_context_['is_supported'] === false) {
            return $this->Queue_Redirect_Response_Resolve($page_config_, [
                'fail_queue_page' => (int) $request->input('fail_queue_page', 1),
            ])->with('queue_viewer_error_', $failed_queue_action_context_['message']);
        }

        if ($page_config_->Selected_Queue_List_ === []) {
            return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_error_', '請先指定至少一個 queue，再清空 failed jobs。');
        }

        $deleted_rows_count_ = DB::connection($page_config_->Selected_Database_)
            ->table($failed_queue_action_context_['failed_jobs_table_name'])
            ->whereIn(HahahaFailedJobsColumn::QUEUE->value, $page_config_->Selected_Queue_List_)
            ->delete();

        return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_status_', '已清空指定 queue 的 '.$deleted_rows_count_.' 筆 failed jobs。');
    }

    public function Fail_Queue_Clear_All(Request $request): RedirectResponse
    {
        $page_config_ = $this->Page_Config_From_Request_Resolve($request, 'fail_queue');
        $failed_queue_action_context_ = $this->Database_Failed_Queue_Action_Context_Resolve($page_config_);

        if ($failed_queue_action_context_['is_supported'] === false) {
            return $this->Queue_Redirect_Response_Resolve($page_config_, [
                'fail_queue_page' => (int) $request->input('fail_queue_page', 1),
            ])->with('queue_viewer_error_', $failed_queue_action_context_['message']);
        }

        $deleted_rows_count_ = DB::connection($page_config_->Selected_Database_)
            ->table($failed_queue_action_context_['failed_jobs_table_name'])
            ->delete();

        return $this->Queue_Redirect_Response_Resolve($page_config_)->with('queue_viewer_status_', '已清空全部 failed queue 的 '.$deleted_rows_count_.' 筆 jobs。');
    }

    public function Queue_Snapshot_Resolve(hahaha_config_queue_viewer $page_config_, Request $request): array
    {
        if ($page_config_->Selected_Connection_ === 'redis') {
            return $this->Redis_Snapshot_Resolve($page_config_, $request);
        }

        return $this->Database_Snapshot_Resolve($page_config_, $request);
    }

    public function Database_Snapshot_Resolve(hahaha_config_queue_viewer $page_config_, Request $request): array
    {
        $database_connection_name_ = $page_config_->Selected_Database_;
        $jobs_table_name_ = HahahaTable::JOBS->value;
        $failed_jobs_table_name_ = HahahaTable::FAILED_JOBS->value;
        $selected_queue_list_ = $page_config_->Selected_Queue_List_;
        $queue_page_ = max((int) $request->query('queue_page', 1), 1);
        $fail_queue_page_ = max((int) $request->query('fail_queue_page', 1), 1);
        $snapshot_ = [
            'mode' => 'database',
            'headline' => 'Database Preview',
            'selected_connection' => $database_connection_name_,
            'selected_database' => $database_connection_name_,
            'selected_queue' => implode(', ', $selected_queue_list_),
            'selected_queue_list' => $selected_queue_list_,
            'queue_driver' => (string) config('queue.connections.database.driver', 'database'),
            'queue_name' => (string) config('queue.connections.database.queue', 'default'),
            'jobs_table' => $jobs_table_name_,
            'failed_jobs_table' => $failed_jobs_table_name_,
            'jobs_table_exists' => false,
            'failed_jobs_table_exists' => false,
            'jobs_count' => null,
            'failed_jobs_count' => null,
            'recent_jobs' => [],
            'recent_failed_jobs' => [],
            'queue_pagination' => null,
            'fail_queue_pagination' => null,
            'queue_actions_supported' => true,
            'status_message' => '',
        ];

        try {
            $snapshot_['jobs_table_exists'] = Schema::connection($database_connection_name_)->hasTable($jobs_table_name_);
            $snapshot_['failed_jobs_table_exists'] = Schema::connection($database_connection_name_)->hasTable($failed_jobs_table_name_);

            if ($snapshot_['jobs_table_exists']) {
                $snapshot_['jobs_count'] = DB::connection($database_connection_name_)
                    ->table($jobs_table_name_)
                    ->when($selected_queue_list_ !== [], function ($query_) use ($selected_queue_list_): void {
                        $query_->whereIn(HahahaJobsColumn::QUEUE->value, $selected_queue_list_);
                    })
                    ->count();

                $jobs_paginator_ = DB::connection($database_connection_name_)
                    ->table($jobs_table_name_)
                    ->select([
                        HahahaJobsColumn::ID->value,
                        HahahaJobsColumn::QUEUE->value,
                        HahahaJobsColumn::PAYLOAD->value,
                        HahahaJobsColumn::ATTEMPTS->value,
                        HahahaJobsColumn::RESERVED_AT->value,
                        HahahaJobsColumn::AVAILABLE_AT->value,
                        HahahaJobsColumn::CREATED_AT->value,
                    ])
                    ->when($selected_queue_list_ !== [], function ($query_) use ($selected_queue_list_): void {
                        $query_->whereIn(HahahaJobsColumn::QUEUE->value, $selected_queue_list_);
                    })
                    ->orderBy(HahahaJobsColumn::ID->value)
                    ->paginate($page_config_->Queue_Per_Page_, ['*'], 'queue_page', $queue_page_);

                $snapshot_['recent_jobs'] = $jobs_paginator_
                    ->map(function (object $job_row_): array {
                        $payload_ = (string) $job_row_->payload;

                        return [
                            'id' => (string) $job_row_->id,
                            'queue' => (string) $job_row_->queue,
                            'display_name' => $this->Payload_Display_Name_Resolve($payload_),
                            'payload' => $payload_,
                            'attempts' => (string) $job_row_->attempts,
                            'reserved_at' => $job_row_->reserved_at === null ? '-' : (string) $job_row_->reserved_at,
                            'available_at' => Carbon::createFromTimestamp((int) $job_row_->available_at, config('app.timezone'))->format('Y-m-d H:i:s'),
                            'created_at' => Carbon::createFromTimestamp((int) $job_row_->created_at, config('app.timezone'))->format('Y-m-d H:i:s'),
                        ];
                    })
                    ->all();

                $snapshot_['queue_pagination'] = $this->Pagination_Data_Resolve($jobs_paginator_, 'queue_page');
            }

            if ($snapshot_['failed_jobs_table_exists']) {
                $snapshot_['failed_jobs_count'] = DB::connection($database_connection_name_)
                    ->table($failed_jobs_table_name_)
                    ->when($selected_queue_list_ !== [], function ($query_) use ($selected_queue_list_): void {
                        $query_->whereIn(HahahaFailedJobsColumn::QUEUE->value, $selected_queue_list_);
                    })
                    ->count();

                $failed_jobs_paginator_ = DB::connection($database_connection_name_)
                    ->table($failed_jobs_table_name_)
                    ->select([
                        HahahaFailedJobsColumn::ID->value,
                        HahahaFailedJobsColumn::UUID->value,
                        HahahaFailedJobsColumn::CONNECTION->value,
                        HahahaFailedJobsColumn::QUEUE->value,
                        HahahaFailedJobsColumn::PAYLOAD->value,
                        HahahaFailedJobsColumn::FAILED_AT->value,
                    ])
                    ->when($selected_queue_list_ !== [], function ($query_) use ($selected_queue_list_): void {
                        $query_->whereIn(HahahaFailedJobsColumn::QUEUE->value, $selected_queue_list_);
                    })
                    ->orderBy(HahahaFailedJobsColumn::ID->value)
                    ->paginate($page_config_->Queue_Per_Page_, ['*'], 'fail_queue_page', $fail_queue_page_);

                $snapshot_['recent_failed_jobs'] = $failed_jobs_paginator_
                    ->map(function (object $failed_job_row_): array {
                        $payload_ = (string) $failed_job_row_->payload;

                        return [
                            'id' => (string) $failed_job_row_->id,
                            'uuid' => (string) $failed_job_row_->uuid,
                            'connection' => (string) $failed_job_row_->connection,
                            'queue' => (string) $failed_job_row_->queue,
                            'display_name' => $this->Payload_Display_Name_Resolve($payload_),
                            'payload' => $payload_,
                            'failed_at' => (string) $failed_job_row_->failed_at,
                        ];
                    })
                    ->all();

                $snapshot_['fail_queue_pagination'] = $this->Pagination_Data_Resolve($failed_jobs_paginator_, 'fail_queue_page');
            }

            $snapshot_['status_message'] = '已讀取 database queue 狀態。';
        } catch (Throwable $throwable_) {
            $snapshot_['status_message'] = '讀取失敗：'.$throwable_->getMessage();
        }

        return $snapshot_;
    }

    public function Payload_Display_Name_Resolve(string $payload_): string
    {
        $decoded_payload_ = json_decode($payload_, true);
        if (! is_array($decoded_payload_)) {
            return '-';
        }

        $display_name_ = $decoded_payload_['displayName'] ?? $decoded_payload_['data']['commandName'] ?? $decoded_payload_['job'] ?? null;
        if (! is_string($display_name_)) {
            return '-';
        }

        $display_name_ = trim($display_name_);

        return $display_name_ === '' ? '-' : $display_name_;
    }

    public function Redis_Snapshot_Resolve(hahaha_config_queue_viewer $page_config_, Request $request): array
    {
        $redis_connection_name_ = $page_config_->Selected_Database_;
        $queue_name_ = (string) config('queue.connections.redis.queue', 'default');
        $selected_queue_ = $page_config_->Selected_Queue_List_[0] ?? $queue_name_;
        $queue_prefix_ = 'queues:'.$selected_queue_;
        $snapshot_ = [
            'mode' => 'redis',
            'headline' => 'Redis Snapshot',
            'selected_connection' => 'redis',
            'selected_database' => $redis_connection_name_,
            'selected_queue' => $selected_queue_,
            'queue_driver' => (string) config('queue.connections.redis.driver', 'redis'),
            'queue_name' => $selected_queue_,
            'jobs_table' => '-',
            'failed_jobs_table' => (string) config('queue.failed.table', HahahaTable::FAILED_JOBS->value),
            'jobs_table_exists' => false,
            'failed_jobs_table_exists' => false,
            'jobs_count' => null,
            'failed_jobs_count' => null,
            'recent_jobs' => [],
            'recent_failed_jobs' => [],
            'queue_pagination' => null,
            'fail_queue_pagination' => null,
            'queue_actions_supported' => false,
            'redis_keys' => [
                'pending' => $queue_prefix_,
                'delayed' => $queue_prefix_.':delayed',
                'reserved' => $queue_prefix_.':reserved',
            ],
            'redis_counts' => [
                'pending' => null,
                'delayed' => null,
                'reserved' => null,
            ],
            'status_message' => '',
        ];

        try {
            $redis_connection_ = app('redis')->connection($redis_connection_name_);

            $snapshot_['redis_counts']['pending'] = (int) $redis_connection_->llen($snapshot_['redis_keys']['pending']);
            $snapshot_['redis_counts']['delayed'] = (int) $redis_connection_->zcard($snapshot_['redis_keys']['delayed']);
            $snapshot_['redis_counts']['reserved'] = (int) $redis_connection_->zcard($snapshot_['redis_keys']['reserved']);
            $snapshot_['status_message'] = '已讀取 redis queue 狀態。';
        } catch (Throwable $throwable_) {
            $snapshot_['status_message'] = '讀取失敗：'.$throwable_->getMessage();
        }

        return $snapshot_;
    }

    public function Page_Config_From_Request_Resolve(Request $request, string $default_tab_ = 'queue'): hahaha_config_queue_viewer
    {
        return hahaha_config_queue_viewer::Instance()
            ->Clear()
            ->Initial(
                (string) $request->input('connection', ''),
                (string) $request->input('db', ''),
                (string) $request->input('tab', $default_tab_),
                $this->Request_Queue_List_Resolve($request)
            );
    }

    public function Request_Queue_List_Resolve(Request $request): array
    {
        $selected_queue_list_ = $request->array('selected_queue');

        if ($selected_queue_list_ !== []) {
            return $selected_queue_list_;
        }

        return $request->array('queue');
    }

    /**
     * @return array{is_supported: bool, message: string, jobs_table_name: string}
     */
    public function Database_Queue_Action_Context_Resolve(hahaha_config_queue_viewer $page_config_): array
    {
        $jobs_table_name_ = HahahaTable::JOBS->value;

        if ($page_config_->Selected_Connection_ !== 'database') {
            return [
                'is_supported' => false,
                'message' => '只有 database connection 支援 queue CRUD，redis 目前為唯讀檢視。',
                'jobs_table_name' => $jobs_table_name_,
            ];
        }

        if (! Schema::connection($page_config_->Selected_Database_)->hasTable($jobs_table_name_)) {
            return [
                'is_supported' => false,
                'message' => '指定 DB 找不到 jobs 資料表，無法進行 queue CRUD。',
                'jobs_table_name' => $jobs_table_name_,
            ];
        }

        return [
            'is_supported' => true,
            'message' => '',
            'jobs_table_name' => $jobs_table_name_,
        ];
    }

    /**
     * @return array{is_supported: bool, message: string, failed_jobs_table_name: string}
     */
    public function Database_Failed_Queue_Action_Context_Resolve(hahaha_config_queue_viewer $page_config_): array
    {
        $failed_jobs_table_name_ = HahahaTable::FAILED_JOBS->value;

        if ($page_config_->Selected_Connection_ !== 'database') {
            return [
                'is_supported' => false,
                'message' => '只有 database connection 支援 failed queue CRUD，redis 目前為唯讀檢視。',
                'failed_jobs_table_name' => $failed_jobs_table_name_,
            ];
        }

        if (! Schema::connection($page_config_->Selected_Database_)->hasTable($failed_jobs_table_name_)) {
            return [
                'is_supported' => false,
                'message' => '指定 DB 找不到 failed_jobs 資料表，無法進行 failed queue CRUD。',
                'failed_jobs_table_name' => $failed_jobs_table_name_,
            ];
        }

        return [
            'is_supported' => true,
            'message' => '',
            'failed_jobs_table_name' => $failed_jobs_table_name_,
        ];
    }

    public function Queue_Redirect_Response_Resolve(hahaha_config_queue_viewer $page_config_, array $extra_query_ = []): RedirectResponse
    {
        return redirect()->route($page_config_->Route_Name_, array_filter([
            'tab' => $page_config_->Selected_Tab_,
            'connection' => $page_config_->Selected_Connection_,
            'db' => $page_config_->Selected_Database_,
            'queue' => $page_config_->Selected_Queue_List_,
            ...$extra_query_,
        ], function ($value_): bool {
            if (is_array($value_)) {
                return $value_ !== [];
            }

            return $value_ !== null && $value_ !== '';
        }));
    }

    public function Pagination_Data_Resolve(object $paginator_, string $page_name_): array
    {
        return [
            'current_page' => $paginator_->currentPage(),
            'last_page' => $paginator_->lastPage(),
            'per_page' => $paginator_->perPage(),
            'total' => $paginator_->total(),
            'has_more_pages' => $paginator_->hasMorePages(),
            'has_previous_page' => $paginator_->currentPage() > 1,
            'previous_page' => max($paginator_->currentPage() - 1, 1),
            'next_page' => min($paginator_->currentPage() + 1, max($paginator_->lastPage(), 1)),
            'page_name' => $page_name_,
        ];
    }
}
