<?php

namespace App\Console\Commands\tool;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use ParaTest\WrapperRunner\WrapperRunner;

class hahaha_command_laravel_test extends Command
{
    /**
     * Accepts either test files or directories. Directories are scanned recursively.
     *
     * @var array<int, string>
     */
    public array $test_paths_ = [
        // 'tests/Feature/ExampleTest.php',
        // 'tests/Feature/command',
        'tests',
    ];

    public bool $is_parallel_ = false;

    public int $processes_ = 4;

    public ?bool $has_paratest_ = null;

    protected $signature = 'tool:laravel:test
        {--parallel : Run tests in parallel}
        {--processes= : Number of parallel processes to use}';

    protected $description = 'Run Laravel tests for configured test files or directories recursively';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $test_targets_ = $this->Test_Targets_Resolve();
        $is_parallel_ = $this->Parallel_Option_Resolve();
        $processes_ = $this->Processes_Option_Resolve();

        if ($test_targets_ === []) {
            $this->components->error('No test targets were resolved from the configured test paths.');

            return self::FAILURE;
        }

        if ($is_parallel_ && ! $this->Paratest_Is_Available()) {
            $this->components->warn(
                'Parallel testing requires brianium/paratest. Falling back to sequential PHPUnit execution.'
            );

            $is_parallel_ = false;
        }

        $this->components->info('Running '.count($test_targets_).' test targets.');

        foreach ($test_targets_ as $test_target_) {
            $process_result_ = Process::path(base_path())
                ->forever()
                ->run($this->Test_Command_Resolve($test_target_, $is_parallel_, $processes_), function (string $type_, string $output_): void {
                    $this->output->write($output_);
                });

            if ($process_result_->successful()) {
                continue;
            }

            return $process_result_->exitCode() ?: self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    public function Test_Targets_Resolve(): array
    {
        $test_targets_ = [];

        foreach ($this->test_paths_ as $test_path_) {
            $resolved_test_path_ = trim((string) $test_path_);

            if ($resolved_test_path_ === '') {
                continue;
            }

            $absolute_test_path_ = $this->Path_Absolute_Resolve($resolved_test_path_);

            if (File::isFile($absolute_test_path_)) {
                if (! $this->Test_File_Name_Is_Supported(basename($absolute_test_path_))) {
                    continue;
                }

                $test_targets_[] = $absolute_test_path_;

                continue;
            }

            if (! File::isDirectory($absolute_test_path_)) {
                $this->components->warn('Skipped missing path: '.$resolved_test_path_);

                continue;
            }

            if ($this->Directory_Includes_Supported_Tests($absolute_test_path_)) {
                $test_targets_[] = $absolute_test_path_;
            }
        }

        $test_targets_ = array_values(array_unique($test_targets_));
        sort($test_targets_);

        return $this->Redundant_Nested_Targets_Remove($test_targets_);
    }

    /**
     * @return array<int, string>
     */
    public function Test_Command_Resolve(string $test_target_, bool $is_parallel_, int $processes): array
    {
        $test_command_ = [
            PHP_BINARY,
            base_path('artisan'),
            'test',
            '--compact',
        ];

        if ($is_parallel_) {
            $test_command_[] = '--parallel';

            if ($processes > 0) {
                $test_command_[] = '--processes='.$processes;
            }
        }

        return [
            ...$test_command_,
            $test_target_,
        ];
    }

    public function Parallel_Option_Resolve(): bool
    {
        if ((bool) $this->option('parallel')) {
            return true;
        }

        return $this->is_parallel_;
    }

    public function Processes_Option_Resolve(): int
    {
        $processes_option_ = trim((string) $this->option('processes'));

        if ($processes_option_ === '') {
            return $this->processes_;
        }

        if (preg_match('/^\d+$/', $processes_option_) !== 1) {
            $this->components->warn('Invalid --processes value ['.$processes_option_.'], fallback to default.');

            return $this->processes_;
        }

        return (int) $processes_option_;
    }

    public function Paratest_Is_Available(): bool
    {
        if (is_bool($this->has_paratest_)) {
            return $this->has_paratest_;
        }

        return class_exists(WrapperRunner::class);
    }

    public function Test_File_Name_Is_Supported(string $test_file_name_): bool
    {
        return Str::endsWith($test_file_name_, 'Test.php')
            || Str::endsWith($test_file_name_, '_test.php');
    }

    public function Directory_Includes_Supported_Tests(string $directory_path_): bool
    {
        foreach (File::allFiles($directory_path_) as $test_file_) {
            if ($this->Test_File_Name_Is_Supported($test_file_->getFilename())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, string>  $test_targets_
     * @return array<int, string>
     */
    public function Redundant_Nested_Targets_Remove(array $test_targets_): array
    {
        $resolved_targets_ = [];

        foreach ($test_targets_ as $test_target_) {
            $is_redundant_ = false;

            foreach ($resolved_targets_ as $resolved_target_) {
                if (! File::isDirectory($resolved_target_)) {
                    continue;
                }

                $resolved_target_prefix_ = rtrim($resolved_target_, '\\/').DIRECTORY_SEPARATOR;
                $normalized_test_target_ = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $test_target_);
                $normalized_prefix_ = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $resolved_target_prefix_);

                if (str_starts_with($normalized_test_target_, $normalized_prefix_)) {
                    $is_redundant_ = true;

                    break;
                }
            }

            if ($is_redundant_) {
                continue;
            }

            $resolved_targets_[] = $test_target_;
        }

        return $resolved_targets_;
    }

    public function Path_Is_Absolute(string $path_input_): bool
    {
        if ($path_input_ === '') {
            return false;
        }

        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $path_input_) === 1) {
            return true;
        }

        return str_starts_with($path_input_, '/')
            || str_starts_with($path_input_, '\\');
    }

    public function Path_Absolute_Resolve(string $path_input_): string
    {
        if ($this->Path_Is_Absolute($path_input_)) {
            return $path_input_;
        }

        return base_path(str_replace('/', DIRECTORY_SEPARATOR, $path_input_));
    }
}
