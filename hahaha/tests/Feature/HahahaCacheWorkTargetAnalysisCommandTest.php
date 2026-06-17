<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HahahaCacheWorkTargetAnalysisCommandTest extends TestCase
{
    private string $output_dir_;

    private string $fixture_dir_;

    protected function setUp(): void
    {
        parent::setUp();

        $this->output_dir_ = storage_path('app/testing-ai-context/work-target-analysis');
        $this->fixture_dir_ = base_path('tool/page/testing_target');

        File::deleteDirectory($this->output_dir_);
        File::deleteDirectory($this->fixture_dir_);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->output_dir_);
        File::deleteDirectory($this->fixture_dir_);

        parent::tearDown();
    }

    public function test_it_generates_work_target_analysis_cache_files(): void
    {
        $this->fixtureCreate_();

        $this->artisan('app:hahaha-cache-work-target-analysis', [
            '--output-dir' => $this->output_dir_,
            '--force' => true,
        ])
            ->expectsOutputToContain('Work target 分析快取已輸出')
            ->assertExitCode(0);

        $markdown_path_ = $this->output_dir_.DIRECTORY_SEPARATOR.'work-target-analysis.md';
        $json_path_ = $this->output_dir_.DIRECTORY_SEPARATOR.'work-target-analysis.json';
        $meta_path_ = $this->output_dir_.DIRECTORY_SEPARATOR.'.hahaha_cache_work_target_analysis.meta.json';

        $this->assertFileExists($markdown_path_);
        $this->assertFileExists($json_path_);
        $this->assertFileExists($meta_path_);
        $this->assertStringContainsString('# Work Target Analysis', File::get($markdown_path_));

        $payload_ = json_decode(File::get($json_path_), true);

        $this->assertIsArray($payload_);
        $this->assertArrayHasKey('summary', $payload_);
        $this->assertArrayHasKey('targets', $payload_);

        $target_keys_ = array_column($payload_['targets'] ?? [], 'key');
        $this->assertContains('testing_target', $target_keys_);
        $this->assertContains('queue_viewer', $target_keys_);
        $this->assertContains('animal', $target_keys_);

        $fixture_target_index_ = array_search('testing_target', $target_keys_, true);
        $fixture_target_ = $payload_['targets'][$fixture_target_index_] ?? [];

        $this->assertSame('tool/page/testing_target', $fixture_target_['primary_path'] ?? null);
        $this->assertContains('tool/page/testing_target/route/hahaha_route_testing_target.php', $fixture_target_['files']['routes'] ?? []);
        $this->assertContains('tool/page/testing_target/controller/hahaha_controller_testing_target.php', $fixture_target_['files']['controllers'] ?? []);
        $this->assertContains('tool/page/testing_target/config/hahaha_config_testing_target.php', $fixture_target_['files']['configs'] ?? []);
        $this->assertContains('tool/page/testing_target/view/hahaha_view_testing_target.blade.php', $fixture_target_['files']['views'] ?? []);
        $this->assertContains('tool/page/testing_target/test/hahaha_test_testing_target.php', $fixture_target_['files']['tests'] ?? []);
        $this->assertSame([
            'tool/page/testing_target/route/hahaha_route_testing_target.php',
            'tool/page/testing_target/controller/hahaha_controller_testing_target.php',
            'tool/page/testing_target/config/hahaha_config_testing_target.php',
            'tool/page/testing_target/view/hahaha_view_testing_target.blade.php',
            'tool/page/testing_target/test/hahaha_test_testing_target.php',
        ], $fixture_target_['open_order'] ?? []);
    }

    public function test_it_skips_rebuild_when_fingerprint_is_unchanged(): void
    {
        $this->fixtureCreate_();

        $this->artisan('app:hahaha-cache-work-target-analysis', [
            '--output-dir' => $this->output_dir_,
            '--force' => true,
        ])->assertExitCode(0);

        $this->artisan('app:hahaha-cache-work-target-analysis', [
            '--output-dir' => $this->output_dir_,
        ])
            ->expectsOutputToContain('程式碼未變更，略過重建')
            ->assertExitCode(0);
    }

    private function fixtureCreate_(): void
    {
        File::ensureDirectoryExists($this->fixture_dir_.DIRECTORY_SEPARATOR.'route');
        File::ensureDirectoryExists($this->fixture_dir_.DIRECTORY_SEPARATOR.'controller');
        File::ensureDirectoryExists($this->fixture_dir_.DIRECTORY_SEPARATOR.'config');
        File::ensureDirectoryExists($this->fixture_dir_.DIRECTORY_SEPARATOR.'view');
        File::ensureDirectoryExists($this->fixture_dir_.DIRECTORY_SEPARATOR.'test');

        File::put($this->fixture_dir_.DIRECTORY_SEPARATOR.'route/hahaha_route_testing_target.php', "<?php\n\nclass hahaha_route_testing_target {}\n");
        File::put($this->fixture_dir_.DIRECTORY_SEPARATOR.'controller/hahaha_controller_testing_target.php', "<?php\n\nnamespace hahaha\\tool\\page\\testing_target;\n\nclass hahaha_controller_testing_target {}\n");
        File::put($this->fixture_dir_.DIRECTORY_SEPARATOR.'config/hahaha_config_testing_target.php', "<?php\n\nclass hahaha_config_testing_target {}\n");
        File::put($this->fixture_dir_.DIRECTORY_SEPARATOR.'view/hahaha_view_testing_target.blade.php', "<div>testing target</div>\n");
        File::put($this->fixture_dir_.DIRECTORY_SEPARATOR.'test/hahaha_test_testing_target.php', "<?php\n\nclass hahaha_test_testing_target {}\n");
    }
}
