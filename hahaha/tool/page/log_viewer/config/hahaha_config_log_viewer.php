<?php

namespace hahaha\tool\page\log_viewer;

class hahaha_config_log_viewer
{
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

    public function Page_Title_Default_Resolve(): string
    {
        return 'Log檢視器';
    }

    public function Page_Subtitle_Default_Resolve(): string
    {
        return '用 multiple node 規則快速查看指定資料夾內的 log 檔，檔案內容由前台下載並於瀏覽器端完成行號、上色、搜尋、篩選、顯示區塊數與區塊折疊。';
    }

    public function Log_Directory_Default_Path_Resolve(): string
    {
        return storage_path('logs');
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

    /**
     * @return array<string, string>
     */
    public function Severity_Filter_Options_Default_Resolve(): array
    {
        return [
            'all' => '全部',
            'error' => '只看 Error',
            'warning' => '只看 Warning',
            'json' => '只看 Json',
            'non_laravel' => '只看非laravel log',
        ];
    }

    public function Block_Limit_Default_Resolve(): int
    {
        return 99;
    }
}
