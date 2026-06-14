<?php

namespace hahaha\tool\page\queue_viewer;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class hahaha_test_queue_viewer extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::connection('sqlite')->hasTable('jobs')) {
            Schema::connection('sqlite')->create('jobs', function (Blueprint $table): void {
                $table->id();
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (! Schema::connection('sqlite')->hasTable('failed_jobs')) {
            Schema::connection('sqlite')->create('failed_jobs', function (Blueprint $table): void {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        DB::connection('sqlite')->table('jobs')->delete();
        DB::connection('sqlite')->table('failed_jobs')->delete();
    }

    public function test_queue_viewer_page_can_render(): void
    {
        $response_ = $this->get('/tool/page/queue/viewer');

        $response_->assertStatus(200);
        $response_->assertSee('Queue Viewer');
        $response_->assertSee('設定');
        $response_->assertSee('資訊');
        $response_->assertSee('fail_queue');
    }

    public function test_queue_viewer_page_can_switch_database_connection(): void
    {
        DB::connection('sqlite')->table('jobs')->insert([
            'queue' => 'emails',
            'payload' => '{"job":"seed"}',
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => 1,
            'created_at' => 1,
        ]);

        $response_ = $this->get('/tool/page/queue/viewer?tab=setting&connection=database&db=sqlite');

        $response_->assertStatus(200);
        $response_->assertSee('DB');
        $response_->assertSee('sqlite');
        $response_->assertDontSee('全部 queue');
    }

    public function test_queue_viewer_page_can_switch_redis_connection(): void
    {
        $response_ = $this->get('/tool/page/queue/viewer?tab=info&connection=redis&db=default');

        $response_->assertStatus(200);
        $response_->assertSee('Redis Snapshot');
        $response_->assertSee('Pending');
    }

    public function test_queue_viewer_page_can_delete_database_job(): void
    {
        DB::connection('sqlite')->table('jobs')->insert([
            'queue' => 'emails',
            'payload' => '{"displayName":"DeleteJob"}',
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => 100,
            'created_at' => 100,
        ]);

        $created_job_id_ = (int) DB::connection('sqlite')->table('jobs')->max('id');
        $this->assertGreaterThan(0, $created_job_id_);

        $delete_response_ = $this->post('/tool/page/queue/viewer/queue/'.$created_job_id_.'/delete', [
            'tab' => 'queue',
            'connection' => 'database',
            'db' => 'sqlite',
        ]);

        $delete_response_->assertRedirect();
        $this->assertNull(DB::connection('sqlite')->table('jobs')->where('id', $created_job_id_)->first());
    }

    public function test_queue_viewer_page_can_bulk_delete_and_paginate_database_jobs(): void
    {
        foreach (range(1, 12) as $job_index_) {
            DB::connection('sqlite')->table('jobs')->insert([
                'queue' => 'bulk',
                'payload' => '{"job":'.$job_index_.'}',
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => $job_index_,
                'created_at' => $job_index_,
            ]);
        }

        $page_response_ = $this->get('/tool/page/queue/viewer?tab=queue&connection=database&db=sqlite&queue_page=2');

        $page_response_->assertStatus(200);
        $page_response_->assertSee('queue page 2 / 2');
        $page_response_->assertSee('刪除多選');
        $page_response_->assertSee('清空指定 queue 所有 job');
        $page_response_->assertSee('清空 queue 所有 job');
        $page_response_->assertSee('總頁數 2');
        $page_response_->assertSee('快捷鍵 q(最前) w(前一頁) e(下一頁) r(最後)');
        $page_response_->assertSee('name="queue_page"', false);
        $page_response_->assertSee('>跳頁<', false);
        $page_response_->assertSee('form="queue_bulk_delete_form_"', false);
        $page_response_->assertSee('data-queue-select-all_', false);
        $page_response_->assertSee('data-queue-select-item_', false);
        $page_response_->assertSee('aria-label="全選 jobs"', false);
        $page_response_->assertSee('最前');
        $page_response_->assertSee('最後');
        $page_response_->assertSee('is_disabled_', false);
        $page_response_->assertSee('pagination_page_link_', false);

        $job_ids_ = DB::connection('sqlite')->table('jobs')->orderBy('id')->limit(2)->pluck('id')->map(fn ($value_) => (int) $value_)->all();
        $bulk_delete_response_ = $this->post('/tool/page/queue/viewer/queue/bulk-delete', [
            'tab' => 'queue',
            'connection' => 'database',
            'db' => 'sqlite',
            'selected_job_ids' => $job_ids_,
        ]);

        $bulk_delete_response_->assertRedirect();
        $this->assertSame(10, DB::connection('sqlite')->table('jobs')->count());
    }

    public function test_queue_viewer_page_can_clear_all_jobs_for_selected_queue(): void
    {
        DB::connection('sqlite')->table('jobs')->insert([
            [
                'queue' => 'emails',
                'payload' => '{"job":"mail-1"}',
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => 1,
                'created_at' => 1,
            ],
            [
                'queue' => 'emails',
                'payload' => '{"job":"mail-2"}',
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => 2,
                'created_at' => 2,
            ],
            [
                'queue' => 'reports',
                'payload' => '{"job":"report"}',
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => 3,
                'created_at' => 3,
            ],
        ]);

        $clear_response_ = $this->post('/tool/page/queue/viewer/queue/clear-selected', [
            'tab' => 'queue',
            'connection' => 'database',
            'db' => 'sqlite',
            'selected_queue' => ['emails'],
        ]);

        $clear_response_->assertRedirect();
        $this->assertSame(1, DB::connection('sqlite')->table('jobs')->count());
        $this->assertSame(1, DB::connection('sqlite')->table('jobs')->where('queue', 'reports')->count());
        $this->assertSame(0, DB::connection('sqlite')->table('jobs')->where('queue', 'emails')->count());
    }

    public function test_queue_viewer_page_can_clear_all_jobs_for_all_queues(): void
    {
        DB::connection('sqlite')->table('jobs')->insert([
            [
                'queue' => 'emails',
                'payload' => '{"job":"mail-1"}',
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => 1,
                'created_at' => 1,
            ],
            [
                'queue' => 'reports',
                'payload' => '{"job":"report-1"}',
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => 2,
                'created_at' => 2,
            ],
        ]);

        $clear_response_ = $this->post('/tool/page/queue/viewer/queue/clear-all', [
            'tab' => 'queue',
            'connection' => 'database',
            'db' => 'sqlite',
        ]);

        $clear_response_->assertRedirect();
        $this->assertSame(0, DB::connection('sqlite')->table('jobs')->count());
    }

    public function test_queue_viewer_page_can_switch_fail_queue_tab(): void
    {
        foreach (range(1, 12) as $job_index_) {
            DB::connection('sqlite')->table('failed_jobs')->insert([
                'uuid' => 'failed-'.$job_index_,
                'connection' => 'database',
                'queue' => $job_index_ <= 6 ? 'emails' : 'reports',
                'payload' => '{"displayName":"FailedJob'.$job_index_.'","job":"failed-'.$job_index_.'"}',
                'exception' => 'Example exception '.$job_index_,
                'failed_at' => now(),
            ]);
        }

        $response_ = $this->get('/tool/page/queue/viewer?tab=fail_queue&connection=database&db=sqlite&fail_queue_page=2');

        $response_->assertStatus(200);
        $response_->assertSee('Failed Queue Jobs');
        $response_->assertSee('fail_queue page 2 / 2');
        $response_->assertSee('刪除多選');
        $response_->assertSee('清空指定 queue 所有 job');
        $response_->assertSee('清空 queue 所有 job');
        $response_->assertSee('總頁數 2');
        $response_->assertSee('name="fail_queue_page"', false);
        $response_->assertSee('>跳頁<', false);
        $response_->assertSee('Display Name');
        $response_->assertSee('FailedJob11');
        $response_->assertSee('data-failed-queue-row_', false);
        $response_->assertSee('data-failed-payload-detail-row_', false);
        $response_->assertSee('data-failed-queue-select-all_', false);
        $response_->assertSee('data-failed-queue-select-item_', false);
        $response_->assertSee('form="fail_queue_bulk_delete_form_"', false);
        $response_->assertSee('最前');
        $response_->assertSee('最後');
        $response_->assertSee('is_disabled_', false);
        $response_->assertSee('pagination_page_link_', false);
    }

    public function test_queue_viewer_page_can_bulk_delete_failed_jobs(): void
    {
        foreach (range(1, 3) as $job_index_) {
            DB::connection('sqlite')->table('failed_jobs')->insert([
                'uuid' => 'bulk-failed-'.$job_index_,
                'connection' => 'database',
                'queue' => 'emails',
                'payload' => '{"displayName":"BulkFailed'.$job_index_.'"}',
                'exception' => 'Example exception '.$job_index_,
                'failed_at' => now(),
            ]);
        }

        $job_ids_ = DB::connection('sqlite')->table('failed_jobs')->orderBy('id')->limit(2)->pluck('id')->map(fn ($value_) => (int) $value_)->all();
        $delete_response_ = $this->post('/tool/page/queue/viewer/fail-queue/bulk-delete', [
            'tab' => 'fail_queue',
            'connection' => 'database',
            'db' => 'sqlite',
            'selected_failed_job_ids' => $job_ids_,
        ]);

        $delete_response_->assertRedirect();
        $this->assertSame(1, DB::connection('sqlite')->table('failed_jobs')->count());
    }

    public function test_queue_viewer_page_can_clear_selected_failed_queue_jobs(): void
    {
        DB::connection('sqlite')->table('failed_jobs')->insert([
            [
                'uuid' => 'failed-emails-1',
                'connection' => 'database',
                'queue' => 'emails',
                'payload' => '{"displayName":"FailedEmails1"}',
                'exception' => 'Example exception 1',
                'failed_at' => now(),
            ],
            [
                'uuid' => 'failed-emails-2',
                'connection' => 'database',
                'queue' => 'emails',
                'payload' => '{"displayName":"FailedEmails2"}',
                'exception' => 'Example exception 2',
                'failed_at' => now(),
            ],
            [
                'uuid' => 'failed-reports-1',
                'connection' => 'database',
                'queue' => 'reports',
                'payload' => '{"displayName":"FailedReports1"}',
                'exception' => 'Example exception 3',
                'failed_at' => now(),
            ],
        ]);

        $clear_response_ = $this->post('/tool/page/queue/viewer/fail-queue/clear-selected', [
            'tab' => 'fail_queue',
            'connection' => 'database',
            'db' => 'sqlite',
            'selected_queue' => ['emails'],
        ]);

        $clear_response_->assertRedirect();
        $this->assertSame(1, DB::connection('sqlite')->table('failed_jobs')->count());
        $this->assertSame(1, DB::connection('sqlite')->table('failed_jobs')->where('queue', 'reports')->count());
        $this->assertSame(0, DB::connection('sqlite')->table('failed_jobs')->where('queue', 'emails')->count());
    }

    public function test_queue_viewer_page_can_delete_single_failed_job_and_clear_all_failed_jobs(): void
    {
        DB::connection('sqlite')->table('failed_jobs')->insert([
            [
                'uuid' => 'single-failed-1',
                'connection' => 'database',
                'queue' => 'emails',
                'payload' => '{"displayName":"SingleFailed1"}',
                'exception' => 'Example exception 1',
                'failed_at' => now(),
            ],
            [
                'uuid' => 'single-failed-2',
                'connection' => 'database',
                'queue' => 'reports',
                'payload' => '{"displayName":"SingleFailed2"}',
                'exception' => 'Example exception 2',
                'failed_at' => now(),
            ],
        ]);

        $failed_job_id_ = (int) DB::connection('sqlite')->table('failed_jobs')->where('uuid', 'single-failed-1')->value('id');
        $delete_response_ = $this->post('/tool/page/queue/viewer/fail-queue/'.$failed_job_id_.'/delete', [
            'tab' => 'fail_queue',
            'connection' => 'database',
            'db' => 'sqlite',
        ]);

        $delete_response_->assertRedirect();
        $this->assertSame(1, DB::connection('sqlite')->table('failed_jobs')->count());

        $clear_response_ = $this->post('/tool/page/queue/viewer/fail-queue/clear-all', [
            'tab' => 'fail_queue',
            'connection' => 'database',
            'db' => 'sqlite',
        ]);

        $clear_response_->assertRedirect();
        $this->assertSame(0, DB::connection('sqlite')->table('failed_jobs')->count());
    }

    public function test_queue_viewer_page_can_filter_by_multiple_queue(): void
    {
        DB::connection('sqlite')->table('jobs')->insert([
            [
                'queue' => 'emails',
                'payload' => '{"displayName":"MailJob","job":"mail"}',
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => 1,
                'created_at' => 1,
            ],
            [
                'queue' => 'reports',
                'payload' => '{"displayName":"ReportJob","job":"report"}',
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => 2,
                'created_at' => 2,
            ],
            [
                'queue' => 'sms',
                'payload' => '{"displayName":"SmsJob","job":"sms"}',
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => 3,
                'created_at' => 3,
            ],
        ]);

        $response_ = $this->get('/tool/page/queue/viewer?tab=queue&connection=database&db=sqlite&queue[0]=emails&queue[1]=reports');

        $response_->assertStatus(200);
        $response_->assertSee('目前篩選 queue：emails, reports');
        $response_->assertSee('emails');
        $response_->assertSee('reports');
        $response_->assertSee('Display Name');
        $response_->assertSee('MailJob');
        $response_->assertSee('ReportJob');
        $response_->assertSee(Carbon::createFromTimestamp(1, config('app.timezone'))->format('Y-m-d H:i:s'));
        $response_->assertSee(Carbon::createFromTimestamp(2, config('app.timezone'))->format('Y-m-d H:i:s'));
        $response_->assertSee('data-queue-row_', false);
        $response_->assertSee('data-job-display-name_="MailJob"', false);
        $response_->assertSee('data-payload-detail-row_', false);
        $response_->assertDontSee('job&quot;:&quot;report', false);
        $response_->assertDontSee('job&quot;:&quot;sms', false);
        $response_->assertSee('data-queue-multiselect_', false);
        $response_->assertSee('data-selected-queues_', false);
        $response_->assertSee('data-queue-input_', false);
        $response_->assertSee('datalist', false);
        $response_->assertSee('可連續加入多個 queue');
        $response_->assertSee('data-queue-shortcuts_', false);
        $response_->assertDontSee('顯示全部 queue');
        $response_->assertDontSee('選這個 queue');
        $response_->assertDontSee('新增 Queue Job');
        $response_->assertDontSee('修改 Queue Job');
    }

    public function test_queue_viewer_page_keeps_custom_queue_query_value(): void
    {
        $response_ = $this->get('/tool/page/queue/viewer?tab=queue&connection=database&db=sqlite&queue[0]=test');

        $response_->assertStatus(200);
        $response_->assertSee('目前篩選 queue：test');
        $response_->assertSee('"test"', false);
        $response_->assertSee('data-selected-queues_', false);
    }

    public function test_queue_viewer_page_displays_available_and_created_as_datetime(): void
    {
        DB::connection('sqlite')->table('jobs')->insert([
            'queue' => 'datetime',
            'payload' => '{"job":"datetime"}',
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => 1718323200,
            'created_at' => 1718326800,
        ]);

        $response_ = $this->get('/tool/page/queue/viewer?tab=queue&connection=database&db=sqlite&queue[0]=datetime');

        $response_->assertStatus(200);
        $response_->assertSee(Carbon::createFromTimestamp(1718323200, config('app.timezone'))->format('Y-m-d H:i:s'));
        $response_->assertSee(Carbon::createFromTimestamp(1718326800, config('app.timezone'))->format('Y-m-d H:i:s'));
    }
}
