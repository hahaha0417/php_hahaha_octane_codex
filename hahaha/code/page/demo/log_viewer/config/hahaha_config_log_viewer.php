<?php

namespace hahaha\page\demo\node;

use hahaha\hahaha_instance_clear;
use Illuminate\Support\Facades\File;
use SplFileInfo;

class hahaha_config_log_viewer
{
    use hahaha_instance_clear;

    public $Page_Title_ = '';

    public $Page_Subtitle_ = '';

    public $Log_Directory_Default_Path_ = '';

    public $Log_Directory_Allowed_Root_Path_ = '';

    public $Log_Directory_Input_ = '';

    public $Log_Directory_Path_ = '';

    public $Log_Directory_Display_Input_ = '';

    public $Log_Directory_Options_ = [];

    public $Log_Directory_Manual_Options_ = [];

    public $Log_Directory_Status_ = '';

    public $Error_Message_ = '';

    public $Log_File_Options_ = [];

    public $Selected_Log_File_ = '';

    public $Selected_Log_File_Label_ = '';

    public $Selected_Log_File_Size_ = '';

    public $Selected_Log_File_Updated_ = '';

    public $Keyword_Input_ = '';

    public $Severity_Filter_ = 'all';

    public $Severity_Filter_Options_ = [];

    public $Block_Limit_ = 99;

    public function Initial(string $log_directory_path = '', string $log_file = '', string $keyword = '', string $severity_filter = 'all', int $block_limit = 99): static
    {
        $this->Page_Title_ = 'Log檢視器';
        $this->Page_Subtitle_ = '用 multiple node 規則快速查看指定資料夾內的 log 檔，檔案內容由前台下載並於瀏覽器端完成行號、上色、搜尋、篩選、顯示區塊數與區塊折疊。';
        $this->Log_Directory_Default_Path_ = storage_path('logs');
        $this->Log_Directory_Allowed_Root_Path_ = $this->Log_Directory_Allowed_Root_Path_Resolve();
        $this->Log_Directory_Input_ = $log_directory_path !== '' ? $log_directory_path : $this->Log_Directory_Default_Path_;
        $this->Log_Directory_Path_ = '';
        $this->Log_Directory_Display_Input_ = '';
        $this->Log_Directory_Options_ = [];
        $this->Log_Directory_Manual_Options_ = $this->Log_Directory_Manual_Options_Default_Resolve();
        $this->Log_Directory_Input_ = $this->Log_Directory_Input_Resolve($this->Log_Directory_Input_);
        $this->Log_Directory_Status_ = '';
        $this->Error_Message_ = '';
        $this->Log_File_Options_ = [];
        $this->Selected_Log_File_ = '';
        $this->Selected_Log_File_Label_ = '';
        $this->Selected_Log_File_Size_ = '';
        $this->Selected_Log_File_Updated_ = '';
        $this->Keyword_Input_ = trim($keyword);
        $this->Severity_Filter_Options_ = [
            'all' => '全部',
            'error' => '只看 Error',
            'warning' => '只看 Warning',
            'json' => '只看 Json',
            'non_laravel' => '只看非laravel log',
        ];
        $this->Severity_Filter_ = $this->Severity_Filter_Resolve($severity_filter);
        $this->Block_Limit_ = $this->Block_Limit_Resolve($block_limit);
        $this->Log_Directory_Options_ = $this->Log_Directory_Options_Resolve($this->Log_Directory_Input_);
        $this->Log_Directory_Display_Input_ = $this->Log_Directory_Display_Input_Resolve($this->Log_Directory_Input_);

        $this->Log_Directory_Path_ = $this->Log_Directory_Path_Resolve($this->Log_Directory_Input_);

        if ($this->Log_Directory_Path_ === '') {
            $this->Error_Message_ = $this->Log_Directory_Error_Message_Resolve($this->Log_Directory_Input_);

            return $this;
        }

        $this->Log_Directory_Status_ = '目前讀取資料夾：'.$this->Log_Directory_Path_;
        $this->Log_File_Options_ = $this->Log_File_Options_Resolve($this->Log_Directory_Path_);

        if ($this->Log_File_Options_ === []) {
            $this->Error_Message_ = '這個資料夾目前沒有可讀取的 .log 檔案。';

            return $this;
        }

        $this->Selected_Log_File_ = $this->Selected_Log_File_Resolve($log_file);

        if (! array_key_exists($this->Selected_Log_File_, $this->Log_File_Options_)) {
            $this->Selected_Log_File_ = (string) array_key_first($this->Log_File_Options_);
        }

        $selected_log_file_option_ = $this->Log_File_Options_[$this->Selected_Log_File_];

        $this->Selected_Log_File_Label_ = (string) ($selected_log_file_option_['name'] ?? '');
        $this->Selected_Log_File_Size_ = (string) ($selected_log_file_option_['size'] ?? '');
        $this->Selected_Log_File_Updated_ = (string) ($selected_log_file_option_['updated'] ?? '');

        return $this;
    }

    public function Log_Directory_Input_Resolve(string $log_directory_input = ''): string
    {
        $log_directory_input_ = trim($log_directory_input);
        $log_directory_manual_options_ = $this->Log_Directory_Manual_Options_Default_Resolve();

        if ($log_directory_input_ === '') {
            return $log_directory_input_;
        }

        foreach ($log_directory_manual_options_ as $log_directory_option_key_ => $log_directory_option_) {
            if (! is_string($log_directory_option_key_)) {
                continue;
            }

            if ($log_directory_input_ === trim($log_directory_option_key_)) {
                return (string) (realpath((string) $log_directory_option_) ?: (string) $log_directory_option_);
            }
        }

        return $log_directory_input_;
    }

    public function Log_Directory_Display_Input_Resolve(string $log_directory_path = ''): string
    {
        $resolved_log_directory_path_ = $this->Path_Normalize_Resolve($log_directory_path);

        if ($resolved_log_directory_path_ === '') {
            return trim($log_directory_path);
        }

        foreach ($this->Log_Directory_Options_ as $log_directory_option_) {
            if ($this->Path_Normalize_Resolve((string) ($log_directory_option_['path'] ?? '')) !== $resolved_log_directory_path_) {
                continue;
            }

            return (string) ($log_directory_option_['label'] ?? $log_directory_path);
        }

        return trim($log_directory_path);
    }

    public function Log_Directory_Path_Resolve(string $log_directory_path = ''): string
    {
        $log_directory_path_ = trim($log_directory_path);

        if ($log_directory_path_ === '') {
            return '';
        }

        if (! preg_match('/^[A-Za-z]:[\\\\\\/]/', $log_directory_path_) && ! str_starts_with($log_directory_path_, DIRECTORY_SEPARATOR)) {
            $log_directory_path_ = base_path($log_directory_path_);
        }

        $resolved_log_directory_path_ = realpath($log_directory_path_);

        if ($resolved_log_directory_path_ === false || ! is_dir($resolved_log_directory_path_)) {
            return '';
        }

        if (! $this->Log_Directory_Is_Allowed_Path($resolved_log_directory_path_)) {
            return '';
        }

        return $resolved_log_directory_path_;
    }

    public function Log_Directory_Allowed_Root_Path_Resolve(): string
    {
        return dirname(base_path());
    }

    /**
     * @return array<int|string, string>
     */
    public function Log_Directory_Manual_Options_Default_Resolve(): array
    {
        return [
            'logs' => storage_path('logs'),
            'testing' => storage_path('framework/testing/log_viewer'),
            'hahaha' => storage_path('logs/hahaha'),
            'hehehe' => storage_path('../../../hahaha'),
            // dirname(base_path()).DIRECTORY_SEPARATOR.'another_project'.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'logs',
        ];
    }

    public function Log_Directory_Is_Within_Allowed_Root(string $log_directory_path = ''): bool
    {
        $allowed_root_path_ = $this->Path_Normalize_Resolve($this->Log_Directory_Allowed_Root_Path_);
        $resolved_log_directory_path_ = $this->Path_Normalize_Resolve($log_directory_path);

        if ($allowed_root_path_ === '' || $resolved_log_directory_path_ === '') {
            return false;
        }

        if ($resolved_log_directory_path_ === $allowed_root_path_) {
            return true;
        }

        return str_starts_with($resolved_log_directory_path_, $allowed_root_path_.DIRECTORY_SEPARATOR);
    }

    public function Log_Directory_Is_Configured_Option_Path(string $log_directory_path = ''): bool
    {
        $resolved_log_directory_path_ = $this->Path_Normalize_Resolve($log_directory_path);

        if ($resolved_log_directory_path_ === '') {
            return false;
        }

        foreach ($this->Log_Directory_Manual_Options_ as $log_directory_option_) {
            if ($this->Path_Normalize_Resolve((string) (realpath((string) $log_directory_option_) ?: (string) $log_directory_option_)) === $resolved_log_directory_path_) {
                return true;
            }
        }

        return false;
    }

    public function Log_Directory_Is_Allowed_Path(string $log_directory_path = ''): bool
    {
        if ($this->Log_Directory_Is_Within_Allowed_Root($log_directory_path)) {
            return true;
        }

        return $this->Log_Directory_Is_Configured_Option_Path($log_directory_path);
    }

    public function Log_Directory_Error_Message_Resolve(string $log_directory_path = ''): string
    {
        $log_directory_path_ = trim($log_directory_path);

        if ($log_directory_path_ === '') {
            return '找不到指定的 log 資料夾，請確認路徑存在且為目錄。';
        }

        if (! preg_match('/^[A-Za-z]:[\\\\\\/]/', $log_directory_path_) && ! str_starts_with($log_directory_path_, DIRECTORY_SEPARATOR)) {
            $log_directory_path_ = base_path($log_directory_path_);
        }

        $resolved_log_directory_path_ = realpath($log_directory_path_);

        if ($resolved_log_directory_path_ !== false && ! $this->Log_Directory_Is_Allowed_Path($resolved_log_directory_path_)) {
            return '指定路徑超出允許範圍，僅可查看 base_path 上層目錄內的資料夾。';
        }

        return '找不到指定的 log 資料夾，請確認路徑存在且為目錄。';
    }

    /**
     * @return array<string, array{name: string, path: string, size: string, updated: string, updated_timestamp: int}>
     */
    public function Log_File_Options_Resolve(string $log_directory_path = ''): array
    {
        $log_file_options_ = [];
        $log_files_ = File::files($log_directory_path);

        usort($log_files_, function (SplFileInfo $left_file_, SplFileInfo $right_file_): int {
            return $right_file_->getMTime() <=> $left_file_->getMTime();
        });

        foreach ($log_files_ as $log_file_) {
            $log_file_name_ = $log_file_->getFilename();

            if (strtolower($log_file_->getExtension()) !== 'log') {
                continue;
            }

            $log_file_path_ = $log_file_->getRealPath() ?: $log_file_->getPathname();
            $log_file_updated_timestamp_ = $log_file_->getMTime();

            $log_file_options_[$log_file_name_] = [
                'name' => $log_file_name_,
                'path' => $log_file_path_,
                'size' => $this->Bytes_Label_Resolve($log_file_->getSize()),
                'updated' => $this->Timestamp_Label_Resolve($log_file_updated_timestamp_),
                'updated_timestamp' => $log_file_updated_timestamp_,
            ];
        }

        return $log_file_options_;
    }

    /**
     * @return array<int, array{label: string, path: string}>
     */
    public function Log_Directory_Options_Resolve(string $selected_log_directory_path = ''): array
    {
        $resolved_log_directory_options_ = [];
        $selected_log_directory_path_ = trim($selected_log_directory_path);

        foreach ($this->Log_Directory_Manual_Options_ as $log_directory_option_key_ => $log_directory_option_) {
            $this->Log_Directory_Option_Add(
                $resolved_log_directory_options_,
                (string) $log_directory_option_,
                is_string($log_directory_option_key_) ? $log_directory_option_key_ : '',
                true
            );
        }

        $this->Log_Directory_Option_Add($resolved_log_directory_options_, $selected_log_directory_path_);

        usort($resolved_log_directory_options_, function (array $left_option_, array $right_option_): int {
            return [$left_option_['label'], $left_option_['path']] <=> [$right_option_['label'], $right_option_['path']];
        });

        return array_values($resolved_log_directory_options_);
    }

    /**
     * @param  array<int, array{label: string, path: string}>  $resolved_log_directory_options
     */
    public function Log_Directory_Option_Add(array &$resolved_log_directory_options, string $directory_path = '', string $directory_label = '', bool $allow_outside_allowed_root = false): void
    {
        $resolved_directory_path_ = realpath($directory_path);
        $resolved_directory_label_ = $this->Log_Directory_Option_Label_Resolve($directory_label, $resolved_directory_path_ ?: $directory_path);

        if ($resolved_directory_path_ === false || ! is_dir($resolved_directory_path_)) {
            return;
        }

        if (! $allow_outside_allowed_root && ! $this->Log_Directory_Is_Within_Allowed_Root($resolved_directory_path_)) {
            return;
        }

        foreach ($resolved_log_directory_options as $resolved_log_directory_option_) {
            if (($resolved_log_directory_option_['path'] ?? '') === $resolved_directory_path_) {
                return;
            }
        }

        $resolved_log_directory_options[] = [
            'label' => $resolved_directory_label_,
            'path' => $resolved_directory_path_,
        ];
    }

    public function Log_Directory_Option_Label_Resolve(string $directory_label = '', string $directory_path = ''): string
    {
        $directory_label_ = trim($directory_label);

        if ($directory_label_ !== '') {
            return $directory_label_;
        }

        $resolved_directory_name_ = basename(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($directory_path)));

        if ($resolved_directory_name_ !== '') {
            return $resolved_directory_name_;
        }

        return trim($directory_path);
    }

    /**
     * @return array<int, array{key: string, name: string, size: string, updated: string, updated_timestamp: int}>
     */
    public function Log_File_Options_List_Resolve(array $log_file_options = []): array
    {
        $resolved_log_file_options_ = [];

        foreach ($log_file_options as $log_file_key_ => $log_file_option_) {
            $resolved_log_file_options_[] = [
                'key' => $log_file_key_,
                'name' => (string) ($log_file_option_['name'] ?? ''),
                'size' => (string) ($log_file_option_['size'] ?? ''),
                'updated' => (string) ($log_file_option_['updated'] ?? ''),
                'updated_timestamp' => (int) ($log_file_option_['updated_timestamp'] ?? 0),
            ];
        }

        return $resolved_log_file_options_;
    }

    public function Selected_Log_File_Resolve(string $log_file = ''): string
    {
        return basename(trim($log_file));
    }

    public function Selected_Log_File_Path_Resolve(string $log_directory_path = '', string $log_file = ''): string
    {
        $log_file_options_ = $this->Log_File_Options_Resolve($log_directory_path);
        $selected_log_file_ = $this->Selected_Log_File_Resolve($log_file);

        if ($selected_log_file_ === '' || ! array_key_exists($selected_log_file_, $log_file_options_)) {
            return '';
        }

        return (string) ($log_file_options_[$selected_log_file_]['path'] ?? '');
    }

    public function Severity_Filter_Resolve(string $severity_filter = 'all'): string
    {
        $severity_filter_ = trim($severity_filter);

        if (! array_key_exists($severity_filter_, $this->Severity_Filter_Options_)) {
            return 'all';
        }

        return $severity_filter_;
    }

    public function Block_Limit_Resolve(int $block_limit = 99): int
    {
        if ($block_limit <= 0) {
            return 99;
        }

        return min($block_limit, 1000);
    }

    /**
     * @return array{
     *     page_title: string,
     *     page_subtitle: string,
     *     log_directory_default_path: string,
     *     log_directory_allowed_root_path: string,
     *     log_directory_input: string,
     *     log_directory_display_input: string,
     *     log_directory_path: string,
     *     log_directory_options: array<int, array{label: string, path: string}>,
     *     log_directory_status: string,
     *     error_message: string,
     *     log_file_options: array<int, array{key: string, name: string, size: string, updated: string, updated_timestamp: int}>,
     *     selected_log_file: string,
     *     selected_log_file_label: string,
     *     selected_log_file_size: string,
     *     selected_log_file_updated: string,
     *     keyword_input: string,
     *     severity_filter: string,
     *     severity_filter_options: array<string, string>,
     *     block_limit: int
     * }
     */
    public function Frontend_State_Resolve(): array
    {
        return [
            'page_title' => $this->Page_Title_,
            'page_subtitle' => $this->Page_Subtitle_,
            'log_directory_default_path' => $this->Log_Directory_Default_Path_,
            'log_directory_allowed_root_path' => $this->Log_Directory_Allowed_Root_Path_,
            'log_directory_input' => $this->Log_Directory_Input_,
            'log_directory_display_input' => $this->Log_Directory_Display_Input_,
            'log_directory_path' => $this->Log_Directory_Path_,
            'log_directory_options' => $this->Log_Directory_Options_,
            'log_directory_status' => $this->Log_Directory_Status_,
            'error_message' => $this->Error_Message_,
            'log_file_options' => $this->Log_File_Options_List_Resolve($this->Log_File_Options_),
            'selected_log_file' => $this->Selected_Log_File_,
            'selected_log_file_label' => $this->Selected_Log_File_Label_,
            'selected_log_file_size' => $this->Selected_Log_File_Size_,
            'selected_log_file_updated' => $this->Selected_Log_File_Updated_,
            'keyword_input' => $this->Keyword_Input_,
            'severity_filter' => $this->Severity_Filter_,
            'severity_filter_options' => $this->Severity_Filter_Options_,
            'block_limit' => $this->Block_Limit_,
        ];
    }

    public function Bytes_Label_Resolve(int $bytes = 0): string
    {
        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / 1024 / 1024, 2).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return (string) $bytes.' B';
    }

    public function Timestamp_Label_Resolve(int $timestamp = 0): string
    {
        if ($timestamp <= 0) {
            return '';
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    public function Path_Normalize_Resolve(string $path = ''): string
    {
        $path_ = trim($path);

        if ($path_ === '') {
            return '';
        }

        return rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, strtolower($path_)), DIRECTORY_SEPARATOR);
    }
}
