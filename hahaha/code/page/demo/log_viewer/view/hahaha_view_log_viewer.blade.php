<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $page_config_->Page_Title_ }}</title>
        <style>
            :root {
                --page-bg: rgb(60, 60, 60);
                --panel-bg: rgba(12, 18, 28, 0.78);
                --panel-alt-bg: rgba(8, 13, 20, 0.88);
                --line: rgba(148, 163, 184, 0.18);
                --line-strong: rgba(251, 191, 36, 0.35);
                --text-main: #f8fafc;
                --text-soft: #cbd5e1;
                --text-muted: #94a3b8;
                --accent-soft: #fde68a;
                --error-bg: rgba(239, 68, 68, 0.14);
                --error-line: rgba(248, 113, 113, 0.35);
                --warning-bg: rgba(245, 158, 11, 0.14);
                --warning-line: rgba(251, 191, 36, 0.35);
                --info-bg: rgba(56, 189, 248, 0.12);
                --info-line: rgba(125, 211, 252, 0.28);
                --debug-bg: rgba(168, 85, 247, 0.12);
                --debug-line: rgba(196, 181, 253, 0.28);
                --trace-bg: rgba(148, 163, 184, 0.1);
                --trace-line: rgba(148, 163, 184, 0.2);
                --card-shadow: 0 24px 64px rgba(0, 0, 0, 0.24);
            }
            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: Consolas, "Courier New", monospace;
                color: var(--text-main);
                background:
                    radial-gradient(circle at top left, rgba(245, 158, 11, 0.18), transparent 28%),
                    radial-gradient(circle at top right, rgba(56, 189, 248, 0.12), transparent 24%),
                    var(--page-bg);
            }
            .page_wrap_ {
                max-width: 1320px;
                margin: 0 auto;
                padding: 28px 18px 56px;
            }
            .hero_panel_ {
                padding: 28px;
                border-radius: 28px;
                border: 1px solid var(--line);
                background: var(--panel-bg);
                box-shadow: var(--card-shadow);
            }
            .hero_badge_ {
                display: inline-block;
                padding: 7px 12px;
                border-radius: 999px;
                background: rgba(245, 158, 11, 0.14);
                color: var(--accent-soft);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.14em;
                text-transform: uppercase;
            }
            .hero_title_ {
                margin: 18px 0 10px;
                font-size: clamp(30px, 6vw, 54px);
                line-height: 1.05;
            }
            .hero_copy_ {
                max-width: 880px;
                margin: 0;
                color: var(--text-soft);
                font-size: 15px;
                line-height: 1.85;
            }
            .toolbar_stack_ {
                display: grid;
                gap: 14px;
                margin-top: 24px;
            }
            .toolbar_path_ {
                display: block;
            }
            .toolbar_search_ {
                display: grid;
                grid-template-columns: minmax(0, 1.1fr) 220px 140px auto auto;
                gap: 14px;
                align-items: end;
            }
            .field_label_ {
                display: block;
                margin-bottom: 8px;
                color: var(--text-muted);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            .field_input_ {
                width: 100%;
                padding: 14px 16px;
                border: 1px solid rgba(148, 163, 184, 0.2);
                border-radius: 16px;
                background: rgba(15, 23, 42, 0.6);
                color: var(--text-main);
                font: inherit;
            }
            .field_help_ {
                margin-top: 8px;
                color: var(--text-muted);
                font-size: 12px;
                line-height: 1.7;
            }
            .directory_input_group_ {
                position: relative;
            }
            .directory_input_wrap_ {
                position: relative;
            }
            .directory_input_field_ {
                padding-right: 54px;
            }
            .directory_toggle_button_ {
                position: absolute;
                top: 8px;
                right: 8px;
                width: 38px;
                height: calc(100% - 16px);
                border: 1px solid rgba(148, 163, 184, 0.18);
                border-radius: 12px;
                background: rgba(30, 41, 59, 0.9);
                color: var(--text-soft);
                font: inherit;
                cursor: pointer;
            }
            .directory_options_panel_ {
                position: absolute;
                top: calc(100% + 8px);
                left: 0;
                right: 0;
                z-index: 30;
                display: grid;
                gap: 6px;
                max-height: 240px;
                overflow: auto;
                padding: 10px;
                border: 1px solid rgba(148, 163, 184, 0.18);
                border-radius: 18px;
                background: rgba(8, 13, 20, 0.98);
                box-shadow: 0 20px 48px rgba(0, 0, 0, 0.28);
            }
            .directory_options_panel_[hidden] {
                display: none;
            }
            .directory_option_button_ {
                width: 100%;
                padding: 11px 12px;
                border: 1px solid rgba(148, 163, 184, 0.12);
                border-radius: 12px;
                background: rgba(15, 23, 42, 0.55);
                color: var(--text-main);
                font: inherit;
                text-align: left;
                cursor: pointer;
            }
            .directory_option_button_.is_active_ {
                border-color: var(--line-strong);
                background: rgba(245, 158, 11, 0.18);
            }
            .directory_option_label_ {
                display: block;
                font-weight: 700;
            }
            .directory_option_path_ {
                display: block;
                margin-top: 4px;
                color: var(--text-muted);
                font-size: 11px;
                line-height: 1.6;
                word-break: break-all;
            }
            .directory_options_empty_ {
                padding: 10px 12px;
                color: var(--text-muted);
                font-size: 12px;
                line-height: 1.7;
            }
            .submit_button_ {
                padding: 14px 20px;
                border: 0;
                border-radius: 16px;
                background: linear-gradient(135deg, #f59e0b, #f97316);
                color: #111827;
                font: inherit;
                font-weight: 700;
                cursor: pointer;
            }
            .secondary_button_ {
                padding: 14px 20px;
                border: 1px solid rgba(148, 163, 184, 0.2);
                border-radius: 16px;
                background: rgba(15, 23, 42, 0.6);
                color: var(--text-main);
                font: inherit;
                font-weight: 700;
                cursor: pointer;
            }
            #search_button_,
            #clear_button_ {
                border: 1px solid rgba(255, 255, 255, 0.14);
                background: #1f2937;
                color: #f8fafc;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
            }
            #search_button_:hover,
            #clear_button_:hover {
                background: #374151;
                color: #ffffff;
                border-color: rgba(255, 255, 255, 0.22);
            }
            .viewer_stats_ {
                margin-top: 10px;
                color: var(--text-muted);
                font-size: 12px;
                line-height: 1.7;
            }
            .status_row_ {
                margin-top: 14px;
                color: var(--text-soft);
                font-size: 14px;
                line-height: 1.7;
                white-space: pre-wrap;
                word-break: break-word;
            }
            .status_row_.is_error_ { color: #fecaca; }
            .layout_ {
                display: grid;
                grid-template-columns: 320px minmax(0, 1fr);
                gap: 20px;
                margin-top: 22px;
            }
            .panel_ {
                border-radius: 24px;
                border: 1px solid var(--line);
                background: var(--panel-alt-bg);
                box-shadow: var(--card-shadow);
            }
            .panel_header_ {
                padding: 18px 20px;
                border-bottom: 1px solid var(--line);
            }
            .panel_title_ {
                margin: 0;
                font-size: 18px;
            }
            .panel_subtitle_ {
                margin: 8px 0 0;
                color: var(--text-muted);
                font-size: 13px;
                line-height: 1.7;
            }
            .file_list_ {
                display: grid;
                gap: 10px;
                padding: 14px;
            }
            .file_search_wrap_ {
                padding: 0 14px 14px;
            }
            .file_link_ {
                display: block;
                width: 100%;
                padding: 14px;
                border: 1px solid rgba(148, 163, 184, 0.12);
                border-radius: 18px;
                color: var(--text-main);
                text-align: left;
                background: rgba(15, 23, 42, 0.42);
                cursor: pointer;
            }
            .file_link_.is_active_ {
                border-color: var(--line-strong);
                background: rgba(245, 158, 11, 0.14);
            }
            .file_name_ {
                display: block;
                font-weight: 700;
                word-break: break-all;
            }
            .file_meta_ {
                display: block;
                margin-top: 8px;
                color: var(--text-muted);
                font-size: 12px;
            }
            .viewer_head_ {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 12px;
                padding: 20px;
                border-bottom: 1px solid var(--line);
            }
            .viewer_actions_ {
                display: grid;
                gap: 12px;
                align-content: start;
                justify-items: end;
            }
            .viewer_batch_actions_ {
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 8px;
            }
            .viewer_toggle_ {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                color: var(--text-soft);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }
            .viewer_toggle_ input {
                width: 16px;
                height: 16px;
                margin: 0;
            }
            .pagination_row_ {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
            }
            .pagination_row_top_ {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
            }
            .pagination_row_bottom_ {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 16px 20px 20px;
                border-top: 1px solid var(--line);
            }
            .pagination_pages_ {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .pagination_label_ {
                color: var(--text-muted);
                font-size: 12px;
                white-space: nowrap;
            }
            .pagination_button_ {
                min-width: 74px;
                padding: 10px 12px;
                border: 1px solid rgba(148, 163, 184, 0.2);
                border-radius: 12px;
                background: rgba(15, 23, 42, 0.6);
                color: var(--text-main);
                font: inherit;
                cursor: pointer;
            }
            .pagination_button_:disabled {
                opacity: 0.45;
                cursor: not-allowed;
            }
            .pagination_button_.is_active_ {
                border-color: var(--line-strong);
                background: rgba(245, 158, 11, 0.18);
                color: var(--accent-soft);
            }
            .pagination_jump_ {
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }
            .pagination_jump_input_ {
                width: 92px;
                padding: 10px 12px;
                border: 1px solid rgba(148, 163, 184, 0.2);
                border-radius: 12px;
                background: rgba(15, 23, 42, 0.6);
                color: var(--text-main);
                font: inherit;
            }
            .viewer_title_ {
                margin: 0;
                font-size: 20px;
                word-break: break-all;
            }
            .viewer_meta_ {
                margin: 8px 0 0;
                color: var(--text-muted);
                font-size: 12px;
                line-height: 1.7;
            }
            .log_body_ {
                min-height: 480px;
                overflow: auto;
            }
            .log_grid_ {
                width: 100%;
                min-width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
                font-size: 13px;
            }
            .log_row_ {
                border-bottom: 1px solid rgba(148, 163, 184, 0.08);
            }
            .log_row_.is_error_ {
                background: var(--error-bg);
                box-shadow: inset 3px 0 0 var(--error-line);
            }
            .log_row_.is_warning_ {
                background: var(--warning-bg);
                box-shadow: inset 3px 0 0 var(--warning-line);
            }
            .log_row_.is_info_ {
                background: var(--info-bg);
                box-shadow: inset 3px 0 0 var(--info-line);
            }
            .log_row_.is_debug_ {
                background: var(--debug-bg);
                box-shadow: inset 3px 0 0 var(--debug-line);
            }
            .log_row_.is_trace_ {
                background: var(--trace-bg);
                box-shadow: inset 3px 0 0 var(--trace-line);
            }
            .log_row_.is_collapsible_ .log_line_text_ {
                padding-top: 4px;
                padding-bottom: 4px;
            }
            .log_line_number_ {
                width: 88px;
                padding: 10px 12px 10px 18px;
                color: #64748b;
                text-align: right;
                vertical-align: top;
                user-select: none;
                border-right: 1px solid rgba(148, 163, 184, 0.1);
            }
            .log_line_text_ {
                padding: 10px 18px;
                color: #e2e8f0;
                line-height: 1.75;
                white-space: pre-wrap;
                word-break: break-word;
            }
            .log_row_.is_error_ .log_line_text_ { color: #fecaca; }
            .log_row_.is_warning_ .log_line_text_ { color: #fde68a; }
            .log_row_.is_info_ .log_line_text_ { color: #bae6fd; }
            .log_row_.is_debug_ .log_line_text_ { color: #ddd6fe; }
            .log_row_.is_trace_ .log_line_text_ { color: #cbd5e1; }
            .log_details_ {
                width: 100%;
            }
            .log_summary_ {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                cursor: default;
                list-style: none;
                padding: 6px 0;
            }
            .log_summary_::-webkit-details-marker {
                display: none;
            }
            .log_summary_text_ {
                white-space: pre-wrap;
                word-break: break-word;
            }
            .log_block_meta_ {
                flex-shrink: 0;
                color: var(--text-muted);
                font-size: 11px;
            }
            .log_block_hint_ {
                margin-left: 8px;
                color: var(--text-muted);
                font-size: 10px;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }
            .log_block_body_ {
                display: grid;
                gap: 6px;
                margin-top: 8px;
                padding-top: 10px;
                border-top: 1px dashed rgba(148, 163, 184, 0.18);
            }
            .log_block_line_ {
                display: grid;
                grid-template-columns: 72px minmax(0, 1fr);
                gap: 12px;
                align-items: start;
            }
            .log_block_line_number_ {
                color: #64748b;
                text-align: right;
                user-select: none;
            }
            .log_block_line_text_ {
                white-space: pre-wrap;
                word-break: break-word;
            }
            .empty_state_ {
                padding: 24px 18px;
                color: var(--text-muted);
                line-height: 1.8;
            }
            .footer_ {
                margin-top: 18px;
                color: var(--text-muted);
                font-size: 12px;
            }
            @media (max-width: 980px) {
                .toolbar_path_ { grid-template-columns: 1fr; }
                .toolbar_search_ { grid-template-columns: 1fr; }
                .layout_ { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <main class="page_wrap_">
            <section class="hero_panel_">
                <div class="hero_badge_">多節點 Log 檢視器</div>
                <h1 class="hero_title_" id="page_title_">{{ $page_config_->Page_Title_ }}</h1>
                <p class="hero_copy_" id="page_subtitle_">{{ $page_config_->Page_Subtitle_ }}</p>

                <div class="toolbar_stack_">
                    <div class="toolbar_path_">
                        <label>
                            <span class="field_label_">Log目錄</span>
                            <div class="directory_input_group_">
                                <div class="directory_input_wrap_">
                                    <input class="field_input_ directory_input_field_" id="log_directory_input_" type="text" value="{{ $page_config_->Log_Directory_Display_Input_ }}" placeholder="例如：{{ $page_config_->Log_Directory_Default_Path_ }}" autocomplete="off">
                                    <button class="directory_toggle_button_" id="log_directory_toggle_" type="button">▾</button>
                                </div>
                                <div class="directory_options_panel_" id="log_directory_options_panel_" hidden></div>
                            </div>
                            <div class="field_help_">這是一個輸入選擇框；可直接輸入完整路徑，或輸入 / 選擇短名稱。</div>
                        </label>
                    </div>

                    <div class="toolbar_search_">
                        <label>
                            <span class="field_label_">關鍵字搜尋</span>
                            <input class="field_input_" id="keyword_input_" type="text" value="{{ $page_config_->Keyword_Input_ }}" placeholder="例如：例外、逾時、使用者編號">
                        </label>

                        <label>
                            <span class="field_label_">過濾條件</span>
                            <select class="field_input_" id="severity_filter_input_">
                                @foreach ($page_config_->Severity_Filter_Options_ as $severity_filter_key_ => $severity_filter_label_)
                                    <option value="{{ $severity_filter_key_ }}" @selected($page_config_->Severity_Filter_ === $severity_filter_key_)>{{ $severity_filter_label_ }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            <span class="field_label_">顯示區塊數</span>
                            <input class="field_input_" id="block_limit_input_" type="number" min="1" max="1000" step="1" value="{{ $page_config_->Block_Limit_ }}">
                        </label>

                        <button class="submit_button_" id="search_button_" type="button">搜尋</button>
                        <button class="secondary_button_" id="clear_button_" type="button">清除</button>
                    </div>
                </div>

                <div class="status_row_ {{ $page_config_->Error_Message_ !== '' ? 'is_error_' : '' }}" id="status_row_">
                    {{ $page_config_->Error_Message_ !== '' ? $page_config_->Error_Message_ : $page_config_->Log_Directory_Status_ }}
                </div>
            </section>

            <section class="layout_">
                <aside class="panel_">
                    <div class="panel_header_">
                        <h2 class="panel_title_">log檔案</h2>
                        <p class="panel_subtitle_">切換資料夾或檔案時由瀏覽器下載原始 .log，若檔案未更新則沿用瀏覽器快取。</p>
                    </div>

                    <div class="file_search_wrap_">
                        <label>
                            <span class="field_label_">檔案搜尋</span>
                            <input class="field_input_" id="file_search_input_" type="text" placeholder="搜尋檔名，例如：laravel、queue、2026">
                        </label>
                    </div>

                    <div class="file_list_" id="file_list_">
                        @foreach ($page_config_->Log_File_Options_ as $log_file_key_ => $log_file_option_)
                            <button class="file_link_ {{ $page_config_->Selected_Log_File_ === $log_file_key_ ? 'is_active_' : '' }}" type="button" data-log-file="{{ $log_file_key_ }}">
                                <span class="file_name_">{{ $log_file_option_['name'] }}</span>
                                <span class="file_meta_">{{ $log_file_option_['size'] }} / {{ $log_file_option_['updated'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </aside>

                <article class="panel_">
                    <div class="viewer_head_">
                        <div>
                            <h2 class="viewer_title_" id="viewer_title_">{{ $page_config_->Selected_Log_File_Label_ !== '' ? $page_config_->Selected_Log_File_Label_ : '尚未選擇檔案' }}</h2>
                            <p class="viewer_meta_" id="viewer_meta_">
                                大小：{{ $page_config_->Selected_Log_File_Size_ !== '' ? $page_config_->Selected_Log_File_Size_ : '無' }}<br>
                                更新時間：{{ $page_config_->Selected_Log_File_Updated_ !== '' ? $page_config_->Selected_Log_File_Updated_ : '無' }}
                            </p>
                            <div class="viewer_stats_" id="viewer_stats_">目前顯示區塊：0 / 總行數：0</div>
                        </div>

                        <div class="viewer_actions_">
                            <label class="viewer_toggle_">
                                <input id="reverse_blocks_input_" type="checkbox">
                                <span>最新區塊在上</span>
                            </label>

                            <div class="viewer_batch_actions_">
                                <button class="pagination_button_" id="expand_all_button_" type="button">全部展開</button>
                                <button class="pagination_button_" id="collapse_all_button_" type="button">全部折疊</button>
                            </div>

                            <div class="pagination_row_top_">
                                <span class="pagination_label_" id="pagination_total_label_">總頁數 0</span>
                                <div class="pagination_jump_">
                                    <input class="pagination_jump_input_" id="page_jump_input_" type="number" min="1" step="1" placeholder="頁數">
                                    <button class="pagination_button_" id="page_jump_button_" type="button">跳頁</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="log_body_" id="log_body_">
                        <div class="empty_state_" id="empty_state_">前台載入中...</div>
                        <table class="log_grid_" id="log_grid_" hidden>
                            <tbody id="log_grid_body_"></tbody>
                        </table>
                    </div>

                    <div class="pagination_row_bottom_">
                        <span class="pagination_label_" id="pagination_label_">第 0 頁 / 共 0 頁</span>
                        <button class="pagination_button_" id="page_first_button_" type="button">最前</button>
                        <button class="pagination_button_" id="page_previous_button_" type="button">上一頁</button>
                        <div class="pagination_pages_" id="pagination_pages_"></div>
                        <button class="pagination_button_" id="page_next_button_" type="button">下一頁</button>
                        <button class="pagination_button_" id="page_last_button_" type="button">最後</button>
                    </div>
                </article>
            </section>

            <div class="footer_">路由：`page.demo.log_viewer` / 快捷鍵：`q` 最前、`w` 上頁、`e` 下頁、`r` 最後</div>
        </main>

        <script id="log_viewer_bootstrap_" type="application/json">
            {!! $frontend_bootstrap_ !!}
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            (function () {
                var bootstrapElement = document.getElementById('log_viewer_bootstrap_');

                if (! bootstrapElement) {
                    return;
                }

                var bootstrap = JSON.parse(bootstrapElement.textContent || '{}');
                var endpoints = bootstrap.endpoints || {};
                var initialState = bootstrap.state || {};
                var currentUrl = new URL(window.location.href);
                var contentCacheName = 'page_demo_log_viewer_content_v1';
                var elements = {
                    logDirectoryInput: document.getElementById('log_directory_input_'),
                    logDirectoryToggle: document.getElementById('log_directory_toggle_'),
                    logDirectoryOptionsPanel: document.getElementById('log_directory_options_panel_'),
                    keywordInput: document.getElementById('keyword_input_'),
                    severityFilterInput: document.getElementById('severity_filter_input_'),
                    blockLimitInput: document.getElementById('block_limit_input_'),
                    searchButton: document.getElementById('search_button_'),
                    clearButton: document.getElementById('clear_button_'),
                    fileSearchInput: document.getElementById('file_search_input_'),
                    statusRow: document.getElementById('status_row_'),
                    fileList: document.getElementById('file_list_'),
                    viewerTitle: document.getElementById('viewer_title_'),
                    viewerMeta: document.getElementById('viewer_meta_'),
                    viewerStats: document.getElementById('viewer_stats_'),
                    reverseBlocksInput: document.getElementById('reverse_blocks_input_'),
                    expandAllButton: document.getElementById('expand_all_button_'),
                    collapseAllButton: document.getElementById('collapse_all_button_'),
                    paginationLabel: document.getElementById('pagination_label_'),
                    paginationTotalLabel: document.getElementById('pagination_total_label_'),
                    paginationPages: document.getElementById('pagination_pages_'),
                    pageFirstButton: document.getElementById('page_first_button_'),
                    pagePreviousButton: document.getElementById('page_previous_button_'),
                    pageNextButton: document.getElementById('page_next_button_'),
                    pageLastButton: document.getElementById('page_last_button_'),
                    pageJumpInput: document.getElementById('page_jump_input_'),
                    pageJumpButton: document.getElementById('page_jump_button_'),
                    logGrid: document.getElementById('log_grid_'),
                    logGridBody: document.getElementById('log_grid_body_'),
                    emptyState: document.getElementById('empty_state_'),
                };
                var state = {
                    directoryInput: initialState.log_directory_input || '',
                    directoryDisplayInput: initialState.log_directory_display_input || '',
                    directoryPath: initialState.log_directory_path || '',
                    allowedRootPath: initialState.log_directory_allowed_root_path || '',
                    directoryOptions: initialState.log_directory_options || [],
                    directoryStatus: initialState.log_directory_status || '',
                    errorMessage: initialState.error_message || '',
                    files: initialState.log_file_options || [],
                    selectedLogFile: initialState.selected_log_file || '',
                    selectedLogFileLabel: initialState.selected_log_file_label || '',
                    selectedLogFileSize: initialState.selected_log_file_size || '',
                    selectedLogFileUpdated: initialState.selected_log_file_updated || '',
                    selectedLogFileUpdatedTimestamp: 0,
                    keywordInput: initialState.keyword_input || '',
                    fileSearchInput: '',
                    severityFilter: initialState.severity_filter || 'all',
                    blockLimit: Number(initialState.block_limit || 99),
                    reverseBlocks: currentUrl.searchParams.get('reverse') === '1',
                    currentPage: parseInt(currentUrl.searchParams.get('page') || '0', 10),
                    rawLogLines: [],
                    isLoading: false,
                };

                if (isNaN(state.currentPage) || state.currentPage < 0) {
                    state.currentPage = 0;
                }

                function escapeHtml(value) {
                    return String(value)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function showWarningDialog(message) {
                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        window.Swal.fire({
                            icon: 'warning',
                            title: '路徑超出範圍',
                            text: message,
                            confirmButtonText: '知道了',
                        });

                        return;
                    }

                    window.alert(message);
                }

                function resolveTone(logLine) {
                    var logLineUpper = String(logLine).toUpperCase();
                    var trimmedLine = String(logLine).trim();

                    if (
                        logLineUpper.indexOf('EMERGENCY') !== -1
                        || logLineUpper.indexOf('ALERT') !== -1
                        || logLineUpper.indexOf('CRITICAL') !== -1
                        || logLineUpper.indexOf('FATAL') !== -1
                        || logLineUpper.indexOf('ERROR') !== -1
                        || logLineUpper.indexOf(' EXCEPTION') !== -1
                    ) {
                        return 'error';
                    }

                    if (
                        logLineUpper.indexOf('WARNING') !== -1
                        || logLineUpper.indexOf('WARN') !== -1
                        || logLineUpper.indexOf('DEPRECATED') !== -1
                    ) {
                        return 'warning';
                    }

                    if (
                        logLineUpper.indexOf('NOTICE') !== -1
                        || logLineUpper.indexOf('INFO') !== -1
                    ) {
                        return 'info';
                    }

                    if (
                        logLineUpper.indexOf('DEBUG') !== -1
                        || logLineUpper.indexOf('TRACE') !== -1
                    ) {
                        return 'debug';
                    }

                    if (
                        trimmedLine.indexOf('#') === 0
                        || String(logLine).indexOf('.php:') !== -1
                        || String(logLine).indexOf('Stack trace:') !== -1
                    ) {
                        return 'trace';
                    }

                    return 'plain';
                }

                function parseLogLines(rawLogContent) {
                    var logLines = String(rawLogContent).split(/\r\n|\r|\n/);
                    var parsedLines = [];
                    var index = 0;

                    if (logLines.length > 0 && logLines[logLines.length - 1] === '') {
                        logLines.pop();
                    }

                    for (index = 0; index < logLines.length; index++) {
                        parsedLines.push({
                            line_number: index + 1,
                            text: logLines[index],
                            tone: resolveTone(logLines[index]),
                        });
                    }

                    return parsedLines;
                }

                function resolveSelectedFile() {
                    var index = 0;

                    for (index = 0; index < state.files.length; index++) {
                        if (state.files[index].key === state.selectedLogFile) {
                            return state.files[index];
                        }
                    }

                    return null;
                }

                function resolveDirectoryDisplayInput(directoryPath) {
                    var normalizedDirectoryPath = normalizePath(directoryPath);
                    var index = 0;
                    var directoryOption = null;

                    for (index = 0; index < state.directoryOptions.length; index++) {
                        directoryOption = state.directoryOptions[index] || {};

                        if (normalizePath(directoryOption.path || '') === normalizedDirectoryPath) {
                            return String(directoryOption.label || directoryPath || '');
                        }
                    }

                    return String(directoryPath || '');
                }

                function resolveDirectoryPathFromInput(directoryInput) {
                    var trimmedDirectoryInput = String(directoryInput || '').trim();
                    var index = 0;
                    var directoryOption = null;

                    if (trimmedDirectoryInput === '') {
                        return '';
                    }

                    for (index = 0; index < state.directoryOptions.length; index++) {
                        directoryOption = state.directoryOptions[index] || {};

                        if (trimmedDirectoryInput === String(directoryOption.label || '')) {
                            return String(directoryOption.path || '');
                        }
                    }

                    return trimmedDirectoryInput;
                }

                function resolveDirectoryOptionFromInput(directoryInput) {
                    var trimmedDirectoryInput = String(directoryInput || '').trim();
                    var index = 0;
                    var directoryOption = null;

                    if (trimmedDirectoryInput === '') {
                        return null;
                    }

                    for (index = 0; index < state.directoryOptions.length; index++) {
                        directoryOption = state.directoryOptions[index] || {};

                        if (
                            trimmedDirectoryInput === String(directoryOption.label || '')
                            || normalizePath(trimmedDirectoryInput) === normalizePath(String(directoryOption.path || ''))
                        ) {
                            return directoryOption;
                        }
                    }

                    return null;
                }

                function renderDirectoryOptions() {
                    var html = '';
                    var index = 0;
                    var directoryOption = null;
                    var isActive = false;

                    for (index = 0; index < state.directoryOptions.length; index++) {
                        directoryOption = state.directoryOptions[index] || {};

                        isActive = normalizePath(String(directoryOption.path || '')) === normalizePath(state.directoryInput);

                        html += '<button class="directory_option_button_' + (isActive ? ' is_active_' : '') + '" type="button" data-log-directory-path="' + escapeHtml(String(directoryOption.path || '')) + '" data-log-directory-label="' + escapeHtml(String(directoryOption.label || '')) + '">'
                            + '<span class="directory_option_label_">' + escapeHtml(String(directoryOption.label || '')) + '</span>'
                            + '<span class="directory_option_path_">' + escapeHtml(String(directoryOption.path || '')) + '</span>'
                            + '</button>';
                    }

                    if (html === '') {
                        html = '<div class="directory_options_empty_">找不到符合的候選路徑。</div>';
                    }

                    elements.logDirectoryOptionsPanel.innerHTML = html;
                    bindDirectoryOptionButtons();
                }

                function bindDirectoryOptionButtons() {
                    var optionButtons = elements.logDirectoryOptionsPanel.querySelectorAll('[data-log-directory-path]');
                    var index = 0;

                    for (index = 0; index < optionButtons.length; index++) {
                        optionButtons[index].addEventListener('mousedown', function (event) {
                            event.preventDefault();
                        });

                        optionButtons[index].addEventListener('click', function () {
                            var directoryPath = this.getAttribute('data-log-directory-path') || '';
                            var directoryLabel = this.getAttribute('data-log-directory-label') || '';

                            state.directoryDisplayInput = directoryLabel;
                            elements.logDirectoryInput.value = directoryLabel;
                            closeDirectoryOptions();
                            loadDirectory(directoryPath, '');
                        });
                    }
                }

                function openDirectoryOptions() {
                    renderDirectoryOptions();
                    elements.logDirectoryOptionsPanel.hidden = false;
                }

                function closeDirectoryOptions() {
                    elements.logDirectoryOptionsPanel.hidden = true;
                }

                function toggleDirectoryOptions() {
                    if (elements.logDirectoryOptionsPanel.hidden) {
                        openDirectoryOptions();

                        return;
                    }

                    closeDirectoryOptions();
                }

                function resolveContentCacheKey() {
                    var selectedLogFile = resolveSelectedFile();
                    var url = null;

                    if (! selectedLogFile) {
                        return '';
                    }

                    url = new URL(endpoints.content, window.location.origin);
                    url.searchParams.set('log_directory', state.directoryInput);
                    url.searchParams.set('log_file', state.selectedLogFile);
                    url.searchParams.set('updated_timestamp', String(selectedLogFile.updated_timestamp || 0));

                    return url.toString();
                }

                function normalizePath(value) {
                    return String(value || '')
                        .replace(/\//g, '\\')
                        .replace(/\\+/g, '\\')
                        .replace(/[\\]+$/, '')
                        .toLowerCase();
                }

                function isDirectoryWithinAllowedRoot(directoryPath) {
                    var allowedRootPath = normalizePath(state.allowedRootPath);
                    var requestedDirectoryPath = normalizePath(directoryPath);

                    if (allowedRootPath === '' || requestedDirectoryPath === '') {
                        return false;
                    }

                    if (requestedDirectoryPath === allowedRootPath) {
                        return true;
                    }

                    return requestedDirectoryPath.indexOf(allowedRootPath + '\\') === 0;
                }

                function isConfiguredDirectoryOption(directoryPath) {
                    var requestedDirectoryPath = normalizePath(directoryPath);
                    var index = 0;
                    var directoryOption = null;

                    if (requestedDirectoryPath === '') {
                        return false;
                    }

                    for (index = 0; index < state.directoryOptions.length; index++) {
                        directoryOption = state.directoryOptions[index] || {};

                        if (normalizePath(String(directoryOption.path || '')) === requestedDirectoryPath) {
                            return true;
                        }
                    }

                    return false;
                }

                function validateDirectoryInput(requestedDirectory) {
                    var trimmedDirectory = String(requestedDirectory || '').trim();
                    var message = '';

                    if (trimmedDirectory === '') {
                        return true;
                    }

                    if (isDirectoryWithinAllowedRoot(trimmedDirectory)) {
                        return true;
                    }

                    if (isConfiguredDirectoryOption(trimmedDirectory)) {
                        return true;
                    }

                    message = '指定路徑超出允許範圍，僅可查看 base_path 上層目錄內的資料夾。';
                    showWarningDialog(message);
                    setStatus(message, true);

                    return false;
                }

                function resolveCollapseStateStorageKey() {
                    var contentCacheKey = resolveContentCacheKey();

                    if (contentCacheKey === '') {
                        return '';
                    }

                    return 'page_demo_log_viewer_fold_v1:' + encodeURIComponent(contentCacheKey);
                }

                function resolvePageStateStorageKey() {
                    var contentCacheKey = resolveContentCacheKey();
                    var pageStateDescriptor = '';

                    if (contentCacheKey === '') {
                        return '';
                    }

                    pageStateDescriptor = contentCacheKey
                        + '|keyword=' + state.keywordInput
                        + '|severity=' + state.severityFilter
                        + '|block_limit=' + state.blockLimit
                        + '|reverse=' + (state.reverseBlocks ? '1' : '0');

                    return 'page_demo_log_viewer_page_v1:' + encodeURIComponent(pageStateDescriptor);
                }

                function readPersistedPageNumber() {
                    var storageKey = resolvePageStateStorageKey();
                    var rawState = '';
                    var parsedPageNumber = 0;

                    if (storageKey === '' || ! ('localStorage' in window)) {
                        return 0;
                    }

                    try {
                        rawState = window.localStorage.getItem(storageKey) || '';

                        if (rawState === '') {
                            return 0;
                        }

                        parsedPageNumber = parseInt(rawState, 10);

                        if (isNaN(parsedPageNumber) || parsedPageNumber <= 0) {
                            return 0;
                        }

                        return parsedPageNumber;
                    } catch (error) {
                        return 0;
                    }
                }

                function persistCurrentPageNumber() {
                    var storageKey = resolvePageStateStorageKey();

                    if (storageKey === '' || ! ('localStorage' in window) || state.currentPage <= 0) {
                        return;
                    }

                    try {
                        window.localStorage.setItem(storageKey, String(state.currentPage));
                    } catch (error) {
                    }
                }

                function readCollapseStateMap() {
                    var storageKey = resolveCollapseStateStorageKey();
                    var rawState = '';
                    var parsedState = null;

                    if (storageKey === '' || ! ('localStorage' in window)) {
                        return {};
                    }

                    try {
                        rawState = window.localStorage.getItem(storageKey) || '';

                        if (rawState === '') {
                            return {};
                        }

                        parsedState = JSON.parse(rawState);

                        if (! parsedState || typeof parsedState !== 'object') {
                            return {};
                        }

                        return parsedState;
                    } catch (error) {
                        return {};
                    }
                }

                function writeCollapseStateMap(collapseStateMap) {
                    var storageKey = resolveCollapseStateStorageKey();

                    if (storageKey === '' || ! ('localStorage' in window)) {
                        return;
                    }

                    try {
                        window.localStorage.setItem(storageKey, JSON.stringify(collapseStateMap || {}));
                    } catch (error) {
                    }
                }

                function resolveBlockStateKey(displayBlock) {
                    return String(displayBlock.kind || 'block')
                        + '|'
                        + String(displayBlock.summary.line_number || 0)
                        + '|'
                        + String(displayBlock.lines.length || 0)
                        + '|'
                        + String(displayBlock.summary.text || '');
                }

                function resolveBlockOpen(displayBlock, collapseStateMap) {
                    var blockStateKey = resolveBlockStateKey(displayBlock);

                    if (! blockStateKey || ! collapseStateMap || typeof collapseStateMap[blockStateKey] === 'undefined') {
                        return false;
                    }

                    return collapseStateMap[blockStateKey] === 1;
                }

                function persistBlockOpen(blockStateKey, isOpen) {
                    var collapseStateMap = null;

                    if (blockStateKey === '') {
                        return;
                    }

                    collapseStateMap = readCollapseStateMap();
                    collapseStateMap[blockStateKey] = isOpen ? 1 : 0;
                    writeCollapseStateMap(collapseStateMap);
                }

                function readCachedLogContent() {
                    var contentCacheKey = resolveContentCacheKey();

                    if (! ('caches' in window) || contentCacheKey === '') {
                        return Promise.resolve(null);
                    }

                    return caches.open(contentCacheName).then(function (contentCache) {
                        return contentCache.match(contentCacheKey);
                    }).then(function (cachedResponse) {
                        if (! cachedResponse || ! cachedResponse.ok) {
                            return null;
                        }

                        return cachedResponse.text();
                    });
                }

                function writeCachedLogContent(logContent) {
                    var selectedLogFile = resolveSelectedFile();
                    var contentCacheKey = resolveContentCacheKey();

                    if (! ('caches' in window) || ! selectedLogFile || contentCacheKey === '') {
                        return Promise.resolve();
                    }

                    return caches.open(contentCacheName).then(function (contentCache) {
                        return contentCache.keys().then(function (cacheRequests) {
                            var fileCachePrefix = new URL(endpoints.content, window.location.origin).toString()
                                + '?log_directory=' + encodeURIComponent(state.directoryInput)
                                + '&log_file=' + encodeURIComponent(state.selectedLogFile)
                                + '&updated_timestamp=';
                            var deletePromises = [];
                            var index = 0;

                            for (index = 0; index < cacheRequests.length; index++) {
                                if (cacheRequests[index].url.indexOf(fileCachePrefix) === 0 && cacheRequests[index].url !== contentCacheKey) {
                                    deletePromises.push(contentCache.delete(cacheRequests[index]));
                                }
                            }

                            return Promise.all(deletePromises).then(function () {
                                return contentCache.put(contentCacheKey, new Response(logContent, {
                                    headers: {
                                        'Content-Type': 'text/plain; charset=UTF-8',
                                    },
                                }));
                            });
                        });
                    });
                }

                function resolveBlockLimit() {
                    var parsedBlockLimit = parseInt(elements.blockLimitInput.value, 10);

                    if (isNaN(parsedBlockLimit) || parsedBlockLimit <= 0) {
                        parsedBlockLimit = 99;
                    }

                    if (parsedBlockLimit > 1000) {
                        parsedBlockLimit = 1000;
                    }

                    return parsedBlockLimit;
                }

                function isLogHeaderLine(logLine) {
                    return /^\[\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}\]/.test(String(logLine.text || ''));
                }

                function isJsonStartLine(logLine) {
                    var trimmedText = String(logLine.text || '').trim();

                    return trimmedText.indexOf('{') === 0 || trimmedText.indexOf('[') === 0;
                }

                function buildDisplayBlocks(displayLines) {
                    var blocks = [];
                    var currentBlock = null;
                    var index = 0;

                    function pushCurrentBlock() {
                        if (currentBlock) {
                            currentBlock.collapsible = currentBlock.lines.length > 1 || currentBlock.kind === 'json';
                            blocks.push(currentBlock);
                            currentBlock = null;
                        }
                    }

                    for (index = 0; index < displayLines.length; index++) {
                        if (isLogHeaderLine(displayLines[index])) {
                            pushCurrentBlock();
                            currentBlock = {
                                kind: 'log',
                                summary: displayLines[index],
                                lines: [displayLines[index]],
                                collapsible: false,
                            };

                            continue;
                        }

                        if (isJsonStartLine(displayLines[index]) && ! currentBlock) {
                            currentBlock = {
                                kind: 'json',
                                summary: displayLines[index],
                                lines: [displayLines[index]],
                                collapsible: true,
                            };

                            continue;
                        }

                        if (currentBlock) {
                            currentBlock.lines.push(displayLines[index]);
                        } else {
                            blocks.push({
                                kind: 'plain',
                                summary: displayLines[index],
                                lines: [displayLines[index]],
                                collapsible: false,
                            });
                        }
                    }

                    pushCurrentBlock();

                    return blocks;
                }

                function blockMatchesFilters(displayBlock) {
                    var keyword = state.keywordInput.trim().toLowerCase();
                    var index = 0;

                    if (state.severityFilter === 'error' && displayBlock.summary.tone !== 'error') {
                        return false;
                    }

                    if (state.severityFilter === 'warning' && displayBlock.summary.tone !== 'warning') {
                        return false;
                    }

                    if (state.severityFilter === 'json' && displayBlock.kind !== 'json') {
                        return false;
                    }

                    if (state.severityFilter === 'non_laravel' && displayBlock.kind === 'log') {
                        return false;
                    }

                    if (keyword === '') {
                        return true;
                    }

                    for (index = 0; index < displayBlock.lines.length; index++) {
                        if (String(displayBlock.lines[index].text || '').toLowerCase().indexOf(keyword) !== -1) {
                            return true;
                        }
                    }

                    return false;
                }

                function applyBlockFilters(displayBlocks) {
                    var filteredBlocks = [];
                    var index = 0;

                    for (index = 0; index < displayBlocks.length; index++) {
                        if (! blockMatchesFilters(displayBlocks[index])) {
                            continue;
                        }

                        filteredBlocks.push(displayBlocks[index]);
                    }

                    return filteredBlocks;
                }

                function buildPaginatedBlocks(displayBlocks) {
                    var orderedBlocks = displayBlocks.slice(0);
                    var pages = [];
                    var currentPageBlocks = [];
                    var currentPageBlockCount = 0;
                    var index = 0;

                    if (state.reverseBlocks) {
                        orderedBlocks.reverse();
                    }

                    for (index = 0; index < orderedBlocks.length; index++) {
                        if (
                            currentPageBlocks.length > 0
                            && currentPageBlockCount >= state.blockLimit
                        ) {
                            pages.push(currentPageBlocks);
                            currentPageBlocks = [];
                            currentPageBlockCount = 0;
                        }

                        currentPageBlocks.push(orderedBlocks[index]);
                        currentPageBlockCount += 1;
                    }

                    if (currentPageBlocks.length > 0) {
                        pages.push(currentPageBlocks);
                    }

                    return pages;
                }

                function resolvePageNumber(totalPages) {
                    var persistedPageNumber = 0;

                    if (totalPages <= 0) {
                        return 0;
                    }

                    if (! state.currentPage || state.currentPage <= 0) {
                        persistedPageNumber = readPersistedPageNumber();

                        if (persistedPageNumber > 0) {
                            if (persistedPageNumber > totalPages) {
                                return totalPages;
                            }

                            return persistedPageNumber;
                        }

                        return state.reverseBlocks ? 1 : totalPages;
                    }

                    if (state.currentPage > totalPages) {
                        return totalPages;
                    }

                    return state.currentPage;
                }

                function bindPaginationPageButtons() {
                    var paginationPageButtons = elements.paginationPages.querySelectorAll('[data-page-number]');
                    var index = 0;

                    for (index = 0; index < paginationPageButtons.length; index++) {
                        paginationPageButtons[index].addEventListener('click', function () {
                            var targetPage = parseInt(this.getAttribute('data-page-number') || '0', 10);

                            if (isNaN(targetPage) || targetPage <= 0) {
                                return;
                            }

                            state.currentPage = targetPage;
                            renderRows();
                            updateHistory();
                        });
                    }
                }

                function renderPaginationPages(totalPages, resolvedPageNumber) {
                    var startPage = 1;
                    var endPage = totalPages;
                    var html = '';
                    var pageNumber = 0;

                    if (totalPages <= 7) {
                        startPage = 1;
                        endPage = totalPages;
                    } else if (resolvedPageNumber <= 4) {
                        startPage = 1;
                        endPage = 5;
                    } else if (resolvedPageNumber >= totalPages - 3) {
                        startPage = totalPages - 4;
                        endPage = totalPages;
                    } else {
                        startPage = resolvedPageNumber - 2;
                        endPage = resolvedPageNumber + 2;
                    }

                    if (startPage > 1) {
                        html += '<button class="pagination_button_" type="button" data-page-number="1">1</button>';

                        if (startPage > 2) {
                            html += '<span class="pagination_label_">...</span>';
                        }
                    }

                    for (pageNumber = startPage; pageNumber <= endPage; pageNumber++) {
                        html += '<button class="pagination_button_'
                            + (pageNumber === resolvedPageNumber ? ' is_active_' : '')
                            + '" type="button" data-page-number="' + pageNumber + '">' + pageNumber + '</button>';
                    }

                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            html += '<span class="pagination_label_">...</span>';
                        }

                        html += '<button class="pagination_button_" type="button" data-page-number="' + totalPages + '">' + totalPages + '</button>';
                    }

                    elements.paginationPages.innerHTML = html;
                    bindPaginationPageButtons();
                }

                function resolveJumpPageNumber(totalPages) {
                    var requestedPage = parseInt(elements.pageJumpInput.value || '0', 10);

                    if (isNaN(requestedPage) || requestedPage <= 0) {
                        return 0;
                    }

                    if (requestedPage > totalPages) {
                        return totalPages;
                    }

                    return requestedPage;
                }

                function setAllVisibleBlocksOpen(isOpen) {
                    var logDetailsElements = elements.logGridBody.querySelectorAll('.log_details_');
                    var index = 0;
                    var blockStateKey = '';

                    for (index = 0; index < logDetailsElements.length; index++) {
                        logDetailsElements[index].open = !! isOpen;
                        blockStateKey = logDetailsElements[index].getAttribute('data-block-key') || '';
                        persistBlockOpen(blockStateKey, !! isOpen);
                    }
                }

                function renderPagination(totalPages, visibleLinesOnPage) {
                    var resolvedPageNumber = resolvePageNumber(totalPages);

                    state.currentPage = resolvedPageNumber;
                    persistCurrentPageNumber();
                    elements.reverseBlocksInput.checked = state.reverseBlocks;
                    elements.paginationLabel.textContent = '第 ' + resolvedPageNumber + ' 頁 / 共 ' + totalPages + ' 頁 / 本頁 ' + visibleLinesOnPage + ' 行';
                    elements.paginationTotalLabel.textContent = '總頁數 ' + totalPages;
                    elements.pageJumpInput.max = totalPages > 0 ? String(totalPages) : '1';
                    elements.pageJumpInput.value = resolvedPageNumber > 0 ? String(resolvedPageNumber) : '';
                    elements.expandAllButton.disabled = totalPages <= 0;
                    elements.collapseAllButton.disabled = totalPages <= 0;
                    elements.pageFirstButton.disabled = totalPages <= 1 || resolvedPageNumber <= 1;
                    elements.pagePreviousButton.disabled = totalPages <= 1 || resolvedPageNumber <= 1;
                    elements.pageNextButton.disabled = totalPages <= 1 || resolvedPageNumber >= totalPages;
                    elements.pageLastButton.disabled = totalPages <= 1 || resolvedPageNumber >= totalPages;
                    elements.pageJumpButton.disabled = totalPages <= 1;
                    renderPaginationPages(totalPages, resolvedPageNumber);
                }

                function setStatus(message, isError) {
                    elements.statusRow.textContent = message;
                    elements.statusRow.classList.toggle('is_error_', !! isError);
                }

                function updateHistory() {
                    var url = new URL(endpoints.page || window.location.href, window.location.origin);

                    if (state.directoryInput !== '') {
                        url.searchParams.set('log_directory', state.directoryInput);
                    }

                    if (state.selectedLogFile !== '') {
                        url.searchParams.set('log_file', state.selectedLogFile);
                    }

                    if (state.keywordInput !== '') {
                        url.searchParams.set('keyword', state.keywordInput);
                    }

                    if (state.severityFilter !== '' && state.severityFilter !== 'all') {
                        url.searchParams.set('severity_filter', state.severityFilter);
                    }

                    if (state.blockLimit !== 99) {
                        url.searchParams.set('block_limit', String(state.blockLimit));
                    }

                    if (state.reverseBlocks) {
                        url.searchParams.set('reverse', '1');
                    }

                    if (state.currentPage > 0) {
                        url.searchParams.set('page', String(state.currentPage));
                    }

                    window.history.replaceState({}, '', url.toString());
                }

                function bindFileButtons() {
                    var fileButtons = elements.fileList.querySelectorAll('[data-log-file]');
                    var index = 0;

                    for (index = 0; index < fileButtons.length; index++) {
                        fileButtons[index].addEventListener('click', function () {
                            var nextLogFile = this.getAttribute('data-log-file') || '';

                            if (nextLogFile === '' || nextLogFile === state.selectedLogFile) {
                                return;
                            }

                            state.selectedLogFile = nextLogFile;
                            state.currentPage = 0;
                            state.rawLogLines = [];
                            state.isLoading = true;
                            syncSelectedFileMeta();
                            renderFiles();
                            renderViewerMeta();
                            renderRows();
                            updateHistory();
                            loadLogFile();
                        });
                    }
                }

                function bindCollapsibleBlocks() {
                    var logDetailsElements = elements.logGridBody.querySelectorAll('.log_details_');
                    var index = 0;

                    for (index = 0; index < logDetailsElements.length; index++) {
                        var summaryElement = logDetailsElements[index].querySelector('.log_summary_');

                        if (! summaryElement) {
                            continue;
                        }

                        summaryElement.addEventListener('click', function (event) {
                            event.preventDefault();
                        });

                        summaryElement.addEventListener('dblclick', function (event) {
                            var detailsElement = this.parentNode;
                            var blockStateKey = '';

                            event.preventDefault();

                            if (! detailsElement) {
                                return;
                            }

                            detailsElement.open = ! detailsElement.open;
                            blockStateKey = detailsElement.getAttribute('data-block-key') || '';
                            persistBlockOpen(blockStateKey, detailsElement.open);
                        });
                    }
                }

                function renderFiles() {
                    var html = '';
                    var fileSearchKeyword = String(state.fileSearchInput || '').trim().toLowerCase();
                    var visibleFileCount = 0;
                    var index = 0;

                    if (state.files.length === 0) {
                        elements.fileList.innerHTML = '<div class="file_meta_">目前沒有可顯示的檔案。</div>';

                        return;
                    }

                    for (index = 0; index < state.files.length; index++) {
                        if (
                            fileSearchKeyword !== ''
                            && String(state.files[index].name || '').toLowerCase().indexOf(fileSearchKeyword) === -1
                        ) {
                            continue;
                        }

                        visibleFileCount += 1;
                        html += '<button class="file_link_ '
                            + (state.files[index].key === state.selectedLogFile ? 'is_active_' : '')
                            + '" type="button" data-log-file="' + escapeHtml(state.files[index].key) + '">'
                            + '<span class="file_name_">' + escapeHtml(state.files[index].name) + '</span>'
                            + '<span class="file_meta_">' + escapeHtml(state.files[index].size) + ' / ' + escapeHtml(state.files[index].updated) + '</span>'
                            + '</button>';
                    }

                    if (visibleFileCount === 0) {
                        elements.fileList.innerHTML = '<div class="file_meta_">目前沒有符合檔名搜尋條件的檔案。</div>';

                        return;
                    }

                    elements.fileList.innerHTML = html;
                    bindFileButtons();
                }

                function syncSelectedFileMeta() {
                    var selectedLogFile = resolveSelectedFile();

                    state.selectedLogFileLabel = selectedLogFile ? selectedLogFile.name : '尚未選擇檔案';
                    state.selectedLogFileSize = selectedLogFile ? selectedLogFile.size : '無';
                    state.selectedLogFileUpdated = selectedLogFile ? selectedLogFile.updated : '無';
                    state.selectedLogFileUpdatedTimestamp = selectedLogFile ? (selectedLogFile.updated_timestamp || 0) : 0;
                }

                function renderViewerMeta() {
                    elements.viewerTitle.textContent = state.selectedLogFileLabel || '尚未選擇檔案';
                    elements.viewerMeta.innerHTML = '大小：' + escapeHtml(state.selectedLogFileSize || '無') + '<br>更新時間：' + escapeHtml(state.selectedLogFileUpdated || '無');
                }

                function renderRows() {
                    var allDisplayBlocks = buildDisplayBlocks(state.rawLogLines);
                    var filteredBlocks = applyBlockFilters(allDisplayBlocks);
                    var paginatedBlocks = buildPaginatedBlocks(filteredBlocks);
                    var collapseStateMap = readCollapseStateMap();
                    var resolvedPageNumber = resolvePageNumber(paginatedBlocks.length);
                    var currentPageBlocks = resolvedPageNumber > 0 ? (paginatedBlocks[resolvedPageNumber - 1] || []) : [];
                    var visibleLinesOnPage = 0;
                    var visibleBlocksOnPage = currentPageBlocks.length;
                    var filteredLinesCount = 0;
                    var html = '';
                    var index = 0;

                    for (index = 0; index < currentPageBlocks.length; index++) {
                        visibleLinesOnPage += currentPageBlocks[index].lines.length;
                    }

                    for (index = 0; index < filteredBlocks.length; index++) {
                        filteredLinesCount += filteredBlocks[index].lines.length;
                    }

                    renderPagination(paginatedBlocks.length, visibleLinesOnPage);
                    elements.viewerStats.textContent = '顯示區塊：' + visibleBlocksOnPage + ' / 本頁行數：' + visibleLinesOnPage + ' / 篩選後行數：' + filteredLinesCount + ' / 總行數：' + state.rawLogLines.length;

                    if (state.isLoading) {
                        elements.logGrid.hidden = true;
                        elements.emptyState.hidden = false;
                        elements.emptyState.textContent = '前台下載檔案中...';

                        return;
                    }

                    if (state.selectedLogFile === '') {
                        elements.logGrid.hidden = true;
                        elements.emptyState.hidden = false;
                        elements.emptyState.textContent = state.errorMessage !== '' ? state.errorMessage : '請先指定 log 檔案。';

                        return;
                    }

                    if (filteredBlocks.length === 0) {
                        elements.logGrid.hidden = true;
                        elements.emptyState.hidden = false;
                        elements.emptyState.textContent = state.rawLogLines.length === 0 ? '目前檔案沒有內容。' : '目前條件沒有符合的 log 行。';

                        return;
                    }

                    for (index = 0; index < currentPageBlocks.length; index++) {
                        if (currentPageBlocks[index].collapsible) {
                            var blockStateKey = resolveBlockStateKey(currentPageBlocks[index]);
                            var shouldOpenBlock = resolveBlockOpen(currentPageBlocks[index], collapseStateMap);

                            html += '<tr class="log_row_ is_collapsible_ '
                                + (currentPageBlocks[index].summary.tone === 'error' ? 'is_error_ ' : '')
                                + (currentPageBlocks[index].summary.tone === 'warning' ? 'is_warning_ ' : '')
                                + (currentPageBlocks[index].summary.tone === 'info' ? 'is_info_ ' : '')
                                + (currentPageBlocks[index].summary.tone === 'debug' ? 'is_debug_ ' : '')
                                + (currentPageBlocks[index].summary.tone === 'trace' ? 'is_trace_ ' : '')
                                + '">'
                                + '<td class="log_line_number_">' + currentPageBlocks[index].summary.line_number + '</td>'
                                + '<td class="log_line_text_"><details class="log_details_" data-block-key="' + escapeHtml(blockStateKey) + '"' + (shouldOpenBlock ? ' open' : '') + '><summary class="log_summary_" title="雙擊展開或收合"><span class="log_summary_text_">' + escapeHtml(currentPageBlocks[index].summary.text) + '</span><span class="log_block_meta_">' + currentPageBlocks[index].lines.length + ' 行<span class="log_block_hint_">雙擊展開</span></span></summary><div class="log_block_body_">';

                            for (var lineIndex = 1; lineIndex < currentPageBlocks[index].lines.length; lineIndex++) {
                                html += '<div class="log_block_line_"><span class="log_block_line_number_">' + currentPageBlocks[index].lines[lineIndex].line_number + '</span><span class="log_block_line_text_">' + escapeHtml(currentPageBlocks[index].lines[lineIndex].text) + '</span></div>';
                            }

                            html += '</div></details></td></tr>';
                        } else {
                            html += '<tr class="log_row_ '
                                + (currentPageBlocks[index].summary.tone === 'error' ? 'is_error_ ' : '')
                                + (currentPageBlocks[index].summary.tone === 'warning' ? 'is_warning_ ' : '')
                                + (currentPageBlocks[index].summary.tone === 'info' ? 'is_info_ ' : '')
                                + (currentPageBlocks[index].summary.tone === 'debug' ? 'is_debug_ ' : '')
                                + (currentPageBlocks[index].summary.tone === 'trace' ? 'is_trace_ ' : '')
                                + '">'
                                + '<td class="log_line_number_">' + currentPageBlocks[index].summary.line_number + '</td>'
                                + '<td class="log_line_text_">' + escapeHtml(currentPageBlocks[index].summary.text) + '</td>'
                                + '</tr>';
                        }
                    }

                    elements.emptyState.hidden = true;
                    elements.logGrid.hidden = false;
                    elements.logGridBody.innerHTML = html;
                    bindCollapsibleBlocks();
                }

                function renderAll() {
                    setStatus(state.errorMessage !== '' ? state.errorMessage : state.directoryStatus, state.errorMessage !== '');
                    renderDirectoryOptions();
                    elements.logDirectoryInput.value = state.directoryDisplayInput;
                    elements.keywordInput.value = state.keywordInput;
                    elements.fileSearchInput.value = state.fileSearchInput;
                    elements.severityFilterInput.value = state.severityFilter;
                    elements.blockLimitInput.value = state.blockLimit;
                    syncSelectedFileMeta();
                    renderFiles();
                    renderViewerMeta();
                    renderRows();
                }

                function loadDirectory(requestedDirectory, requestedLogFile) {
                    var url = new URL(endpoints.files, window.location.origin);
                    var resolvedRequestedDirectory = resolveDirectoryPathFromInput(requestedDirectory);

                    if (! validateDirectoryInput(resolvedRequestedDirectory)) {
                        return;
                    }

                    state.directoryInput = String(resolvedRequestedDirectory || '').trim();
                    state.directoryDisplayInput = String(requestedDirectory || '').trim();
                    state.errorMessage = '';
                    setStatus('前台更新資料夾與檔案清單中...', false);
                    updateHistory();

                    url.searchParams.set('log_directory', state.directoryInput);

                    if (requestedLogFile) {
                        url.searchParams.set('log_file', requestedLogFile);
                    }

                    url.searchParams.set('keyword', state.keywordInput);
                    url.searchParams.set('severity_filter', state.severityFilter);
                    url.searchParams.set('block_limit', String(state.blockLimit));

                    fetch(url.toString(), {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }).then(function (response) {
                        return response.json();
                    }).then(function (payload) {
                        state.directoryInput = payload.log_directory_input || state.directoryInput;
                        state.directoryDisplayInput = payload.log_directory_display_input || resolveDirectoryDisplayInput(state.directoryInput);
                        state.directoryOptions = payload.log_directory_options || state.directoryOptions || [];
                        state.directoryPath = payload.log_directory_path || '';
                        state.directoryStatus = payload.log_directory_status || '';
                        state.errorMessage = payload.error_message || '';
                        state.files = payload.log_file_options || [];
                        state.selectedLogFile = payload.selected_log_file || '';
                        state.selectedLogFileLabel = payload.selected_log_file_label || '';
                        state.selectedLogFileSize = payload.selected_log_file_size || '';
                        state.selectedLogFileUpdated = payload.selected_log_file_updated || '';
                        state.blockLimit = Number(payload.block_limit || state.blockLimit || 99);
                        state.currentPage = 0;
                        state.rawLogLines = [];

                        renderAll();
                        updateHistory();

                        if (state.errorMessage === '' && state.selectedLogFile !== '') {
                            loadLogFile();
                        }
                    }).catch(function () {
                        state.errorMessage = '讀取資料夾清單失敗。';
                        setStatus(state.errorMessage, true);
                    });
                }

                function loadLogFile() {
                    var url = null;

                    if (state.selectedLogFile === '') {
                        state.rawLogLines = [];
                        renderRows();

                        return;
                    }

                    state.isLoading = true;
                    renderRows();

                    url = new URL(endpoints.content, window.location.origin);
                    url.searchParams.set('log_directory', state.directoryInput);
                    url.searchParams.set('log_file', state.selectedLogFile);

                    readCachedLogContent().then(function (cachedLogContent) {
                        if (cachedLogContent !== null) {
                            state.rawLogLines = parseLogLines(cachedLogContent);
                            state.errorMessage = '';
                            state.isLoading = false;
                            setStatus(state.directoryStatus + '（直接使用瀏覽器快取）', false);
                            renderRows();
                            updateHistory();

                            return null;
                        }

                        return fetch(url.toString(), {
                            method: 'GET',
                            cache: 'no-store',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        }).then(function (response) {
                            if (! response.ok) {
                                return response.text().then(function (message) {
                                    throw new Error(message);
                                });
                            }

                            return response.text();
                        }).then(function (logContent) {
                            state.rawLogLines = parseLogLines(logContent);
                            state.errorMessage = '';

                            return writeCachedLogContent(logContent).then(function () {
                                setStatus(state.directoryStatus + '（已下載並寫入瀏覽器快取）', false);
                            });
                        });
                    }).catch(function (error) {
                        state.rawLogLines = [];
                        state.errorMessage = error && error.message ? error.message : '讀取 log 檔失敗。';
                        setStatus(state.errorMessage, true);
                    }).finally(function () {
                        state.isLoading = false;
                        renderRows();
                        updateHistory();
                    });
                }

                function runSearch() {
                    state.keywordInput = elements.keywordInput.value.trim();
                    state.severityFilter = elements.severityFilterInput.value;
                    state.blockLimit = resolveBlockLimit();
                    state.currentPage = 0;
                    renderRows();
                    updateHistory();
                }

                function goToFirstPage() {
                    state.currentPage = 1;
                    renderRows();
                    updateHistory();
                }

                function goToPreviousPage() {
                    if (state.currentPage > 1) {
                        state.currentPage -= 1;
                        renderRows();
                        updateHistory();
                    }
                }

                function goToNextPage() {
                    state.currentPage += 1;
                    renderRows();
                    updateHistory();
                }

                function goToLastPage() {
                    state.currentPage = 999999;
                    renderRows();
                    updateHistory();
                }

                function isTypingTarget(target) {
                    var tagName = '';

                    if (! target) {
                        return false;
                    }

                    tagName = String(target.tagName || '').toUpperCase();

                    if (target.isContentEditable) {
                        return true;
                    }

                    return tagName === 'INPUT' || tagName === 'TEXTAREA' || tagName === 'SELECT';
                }

                elements.logDirectoryInput.value = state.directoryDisplayInput;
                renderDirectoryOptions();
                elements.keywordInput.value = state.keywordInput;
                elements.fileSearchInput.value = state.fileSearchInput;
                elements.severityFilterInput.value = state.severityFilter;
                elements.blockLimitInput.value = state.blockLimit;
                elements.reverseBlocksInput.checked = state.reverseBlocks;

                elements.logDirectoryToggle.addEventListener('click', function () {
                    toggleDirectoryOptions();
                });

                elements.logDirectoryInput.addEventListener('focus', function () {
                    openDirectoryOptions();
                });

                elements.logDirectoryInput.addEventListener('input', function () {
                    state.directoryDisplayInput = this.value || '';
                    openDirectoryOptions();
                });

                elements.logDirectoryInput.addEventListener('change', function () {
                    var matchedDirectoryOption = resolveDirectoryOptionFromInput(this.value);

                    state.directoryDisplayInput = this.value || '';

                    if (! matchedDirectoryOption) {
                        return;
                    }

                    loadDirectory(String(matchedDirectoryOption.path || this.value || ''), '');
                });

                elements.logDirectoryInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeDirectoryOptions();

                        return;
                    }

                    if (event.key === 'Enter') {
                        event.preventDefault();
                        closeDirectoryOptions();
                        loadDirectory(elements.logDirectoryInput.value, '');
                    }
                });

                document.addEventListener('click', function (event) {
                    if (elements.logDirectoryInput.contains(event.target) || elements.logDirectoryToggle.contains(event.target) || elements.logDirectoryOptionsPanel.contains(event.target)) {
                        return;
                    }

                    closeDirectoryOptions();
                });

                elements.searchButton.addEventListener('click', function () {
                    runSearch();
                });

                elements.clearButton.addEventListener('click', function () {
                    state.keywordInput = '';
                    state.severityFilter = 'all';
                    state.blockLimit = 99;
                    state.currentPage = 0;
                    elements.keywordInput.value = '';
                    elements.severityFilterInput.value = 'all';
                    elements.blockLimitInput.value = '99';
                    renderRows();
                    updateHistory();
                });

                elements.fileSearchInput.addEventListener('input', function () {
                    state.fileSearchInput = this.value || '';
                    renderFiles();
                });

                elements.reverseBlocksInput.addEventListener('change', function () {
                    state.reverseBlocks = !! this.checked;
                    state.currentPage = 0;
                    renderRows();
                    updateHistory();
                });

                elements.expandAllButton.addEventListener('click', function () {
                    setAllVisibleBlocksOpen(true);
                });

                elements.collapseAllButton.addEventListener('click', function () {
                    setAllVisibleBlocksOpen(false);
                });

                elements.pageFirstButton.addEventListener('click', function () {
                    goToFirstPage();
                });

                elements.pagePreviousButton.addEventListener('click', function () {
                    goToPreviousPage();
                });

                elements.pageNextButton.addEventListener('click', function () {
                    goToNextPage();
                });

                elements.pageLastButton.addEventListener('click', function () {
                    goToLastPage();
                });

                elements.pageJumpButton.addEventListener('click', function () {
                    var jumpPage = resolveJumpPageNumber(parseInt(elements.pageJumpInput.max || '0', 10));

                    if (jumpPage <= 0) {
                        return;
                    }

                    state.currentPage = jumpPage;
                    renderRows();
                    updateHistory();
                });

                elements.pageJumpInput.addEventListener('keydown', function (event) {
                    var jumpPage = 0;

                    if (event.key !== 'Enter') {
                        return;
                    }

                    event.preventDefault();
                    jumpPage = resolveJumpPageNumber(parseInt(elements.pageJumpInput.max || '0', 10));

                    if (jumpPage <= 0) {
                        return;
                    }

                    state.currentPage = jumpPage;
                    renderRows();
                    updateHistory();
                });

                elements.keywordInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        runSearch();
                    }
                });

                elements.blockLimitInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        runSearch();
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.defaultPrevented || event.altKey || event.ctrlKey || event.metaKey) {
                        return;
                    }

                    if (isTypingTarget(event.target)) {
                        return;
                    }

                    if (event.key === 'q' || event.key === 'Q') {
                        event.preventDefault();
                        goToFirstPage();

                        return;
                    }

                    if (event.key === 'w' || event.key === 'W') {
                        event.preventDefault();
                        goToPreviousPage();

                        return;
                    }

                    if (event.key === 'e' || event.key === 'E') {
                        event.preventDefault();
                        goToNextPage();

                        return;
                    }

                    if (event.key === 'r' || event.key === 'R') {
                        event.preventDefault();
                        goToLastPage();
                    }
                });

                renderAll();

                if (state.errorMessage === '' && state.selectedLogFile !== '') {
                    loadLogFile();
                }
            })();
        </script>
    </body>
</html>
