<?php

namespace hahaha\page\demo\node;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class hahaha_test_log_viewer extends TestCase
{
    protected string $log_directory_path_ = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->log_directory_path_ = storage_path('framework/testing/log_viewer');

        File::deleteDirectory($this->log_directory_path_);
        File::ensureDirectoryExists($this->log_directory_path_);

        $older_log_file_path_ = $this->log_directory_path_.DIRECTORY_SEPARATOR.'laravel-test.log';
        $newer_log_file_path_ = $this->log_directory_path_.DIRECTORY_SEPARATOR.'queue-test.log';

        file_put_contents($older_log_file_path_, "older line one\n[2026-06-13 10:00:00] local.ERROR: older error line\n");
        file_put_contents($newer_log_file_path_, "[2026-06-13 11:00:00] local.INFO: newest line one\n[2026-06-13 11:00:01] local.WARNING: Latest warning line\n#1 C:\\test\\trace.php:88\n");

        touch($older_log_file_path_, time() - 120);
        touch($newer_log_file_path_, time());
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->log_directory_path_);

        parent::tearDown();
    }

    public function test_log_viewer_page_can_render_frontend_shell_for_a_specified_directory(): void
    {
        $response_ = $this->get('/page/demo/log-viewer?'.http_build_query([
            'log_directory' => $this->log_directory_path_,
        ]));

        $response_->assertStatus(200);
        $response_->assertSee('Log檢視器');
        $response_->assertDontSee('change_directory_button_');
        $response_->assertSee('log_directory_options_panel_');
        $response_->assertSee('這是一個輸入選擇框');
        $response_->assertSee('logs');
        $response_->assertSee('testing');
        $response_->assertSee('hahaha');
        $response_->assertSee('hehehe');
        $response_->assertSee((string) realpath(storage_path('logs')));
        $response_->assertSee('搜尋');
        $response_->assertSee('清除');
        $response_->assertSee('Log目錄');
        $response_->assertSee('關鍵字搜尋');
        $response_->assertSee('過濾條件');
        $response_->assertSee('顯示區塊數');
        $response_->assertSee('log檔案');
        $response_->assertSee('檔案搜尋');
        $response_->assertSee('只看 Json');
        $response_->assertSee('只看非laravel log');
        $response_->assertSee('前台載入中');
        $response_->assertSee('最新區塊在上');
        $response_->assertSee('全部展開');
        $response_->assertSee('全部折疊');
        $response_->assertSee('上一頁');
        $response_->assertSee('下一頁');
        $response_->assertSee('總頁數');
        $response_->assertSee('跳頁');
        $response_->assertSee('頁數');
        $response_->assertSee('log-viewer/files');
        $response_->assertSee('log-viewer/content');
        $response_->assertSee('sweetalert2');
        $response_->assertSee('99');
        $response_->assertSee('queue-test.log');
        $response_->assertDontSee('Latest warning line');
    }

    public function test_log_viewer_files_endpoint_returns_sorted_file_options(): void
    {
        $response_ = $this->get('/page/demo/log-viewer/files?'.http_build_query([
            'log_directory' => $this->log_directory_path_,
        ]));

        $response_->assertStatus(200);
        $response_->assertJsonPath('log_directory_path', realpath($this->log_directory_path_) ?: $this->log_directory_path_);
        $response_->assertJsonPath('log_directory_allowed_root_path', dirname(base_path()));
        $response_->assertJsonPath('selected_log_file', 'queue-test.log');
        $response_->assertJsonPath('block_limit', 99);
        $response_->assertJsonFragment([
            'label' => 'testing',
            'path' => realpath(storage_path('framework/testing/log_viewer')) ?: storage_path('framework/testing/log_viewer'),
        ]);
        $response_->assertJsonFragment([
            'label' => 'logs',
            'path' => realpath(storage_path('logs')) ?: storage_path('logs'),
        ]);
        $response_->assertJsonFragment([
            'label' => 'hahaha',
            'path' => realpath(storage_path('logs/hahaha')) ?: storage_path('logs/hahaha'),
        ]);
        $response_->assertJsonPath('log_file_options.0.key', 'queue-test.log');
        $response_->assertJsonPath('log_file_options.1.key', 'laravel-test.log');
    }

    public function test_log_viewer_content_endpoint_returns_raw_file_with_cache_headers(): void
    {
        $response_ = $this->get('/page/demo/log-viewer/content?'.http_build_query([
            'log_directory' => $this->log_directory_path_,
            'log_file' => 'queue-test.log',
        ]));

        $response_->assertStatus(200);
        $response_->assertSee('Latest warning line');
        $response_->assertSee('#1 C:\\test\\trace.php:88');
        $response_->assertHeader('content-type', 'text/plain; charset=UTF-8');
        $response_->assertHeader('cache-control', 'max-age=0, must-revalidate, private');
        $response_->assertHeader('etag');
        $response_->assertHeader('last-modified');
    }

    public function test_log_viewer_content_endpoint_returns_not_modified_when_etag_matches(): void
    {
        $first_response_ = $this->get('/page/demo/log-viewer/content?'.http_build_query([
            'log_directory' => $this->log_directory_path_,
            'log_file' => 'queue-test.log',
        ]));

        $entity_tag_ = (string) $first_response_->headers->get('ETag');

        $second_response_ = $this->withHeaders([
            'If-None-Match' => $entity_tag_,
        ])->get('/page/demo/log-viewer/content?'.http_build_query([
            'log_directory' => $this->log_directory_path_,
            'log_file' => 'queue-test.log',
        ]));

        $second_response_->assertStatus(304);
    }

    public function test_log_viewer_files_endpoint_shows_error_for_invalid_directory(): void
    {
        $response_ = $this->get('/page/demo/log-viewer/files?'.http_build_query([
            'log_directory' => storage_path('framework/testing/not-found-log-viewer'),
        ]));

        $response_->assertStatus(200);
        $response_->assertJsonPath('error_message', '找不到指定的 log 資料夾，請確認路徑存在且為目錄。');
    }

    public function test_log_viewer_files_endpoint_shows_error_for_directory_outside_allowed_root(): void
    {
        $response_ = $this->get('/page/demo/log-viewer/files?'.http_build_query([
            'log_directory' => dirname(dirname(base_path())),
        ]));

        $response_->assertStatus(200);
        $response_->assertJsonPath('error_message', '指定路徑超出允許範圍，僅可查看 base_path 上層目錄內的資料夾。');
    }

    public function test_log_viewer_content_endpoint_shows_error_for_invalid_directory(): void
    {
        $response_ = $this->get('/page/demo/log-viewer/content?'.http_build_query([
            'log_directory' => storage_path('framework/testing/not-found-log-viewer'),
            'log_file' => 'queue-test.log',
        ]));

        $response_->assertStatus(404);
        $response_->assertSee('找不到指定的 log 資料夾');
    }
}
