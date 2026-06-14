<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $page_config_->Page_Title_ }}</title>
        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                color: #e5eef8;
                font-family: "Segoe UI", sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(249, 115, 22, 0.18), transparent 26%),
                    radial-gradient(circle at top right, rgba(14, 165, 233, 0.14), transparent 28%),
                    linear-gradient(180deg, #020617 0%, #050b14 52%, #020617 100%);
            }
            .page_wrap_ { width: 100%; max-width: none; margin: 0; padding: 28px 18px 56px; }
            .hero_panel_ {
                padding: 28px;
                border: 1px solid rgba(148, 163, 184, 0.16);
                border-radius: 28px;
                background: rgba(7, 12, 20, 0.82);
                box-shadow: 0 28px 80px rgba(0, 0, 0, 0.34);
                backdrop-filter: blur(10px);
            }
            .hero_eyebrow_ {
                display: inline-block;
                padding: 7px 12px;
                border-radius: 999px;
                color: #9a3412;
                background: rgba(251, 146, 60, 0.14);
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            .hero_title_ { margin: 18px 0 12px; font-size: clamp(30px, 6vw, 52px); line-height: 1.04; }
            .hero_copy_ { max-width: 860px; margin: 0; color: #cbd5e1; font-size: 16px; line-height: 1.8; }
            .tab_nav_ { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 24px; }
            .tab_link_ {
                padding: 12px 18px;
                border: 1px solid rgba(71, 85, 105, 0.9);
                border-radius: 16px;
                color: #cbd5e1;
                text-decoration: none;
                background: rgba(15, 23, 42, 0.92);
                font-size: 14px;
                font-weight: 700;
                letter-spacing: 0.04em;
            }
            .tab_link_.is_active_ { color: #f8fafc; background: #475569; border-color: #475569; }
            .status_panel_ {
                margin-top: 20px;
                padding: 16px 18px;
                border-radius: 20px;
                background: rgba(15, 23, 42, 0.72);
                color: #cbd5e1;
                border: 1px solid rgba(51, 65, 85, 0.72);
            }
            .status_panel_.is_error_ {
                color: #fecaca;
                border-color: rgba(239, 68, 68, 0.35);
                background: rgba(69, 10, 10, 0.45);
            }
            .filter_grid_, .section_grid_, .editor_grid_ {
                display: grid;
                gap: 20px;
                margin-top: 24px;
            }
            .filter_grid_ { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .filter_grid_.is_single_ { grid-template-columns: minmax(0, 1fr); }
            .section_grid_ { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .section_grid_.is_single_ { grid-template-columns: minmax(0, 1fr); }
            .filter_box_, .data_panel_ {
                padding: 20px;
                border-radius: 22px;
                background: rgba(15, 23, 42, 0.88);
                border: 1px solid rgba(51, 65, 85, 0.88);
                box-shadow: 0 18px 50px rgba(0, 0, 0, 0.24);
            }
            .panel_title_ { margin: 0 0 14px; font-size: 24px; }
            .filter_label_, .field_label_ {
                display: block;
                margin-bottom: 10px;
                color: #94a3b8;
                font-size: 13px;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }
            .switcher_ { display: flex; flex-wrap: wrap; gap: 10px; }
            .filter_form_ { display: grid; gap: 14px; }
            .queue_multiselect_ {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                min-height: 52px;
                padding: 10px 14px;
                border: 1px solid rgba(71, 85, 105, 0.9);
                border-radius: 16px;
                background: rgba(2, 6, 23, 0.78);
            }
            .queue_multiselect_tags_ {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                align-items: center;
            }
            .queue_multiselect_tag_ {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                min-height: 32px;
                padding: 6px 10px;
                border-radius: 999px;
                background: #334155;
                color: #f8fafc;
                line-height: 1;
            }
            .queue_multiselect_remove_ {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 22px;
                height: 22px;
                border: 0;
                border-radius: 999px;
                background: rgba(15, 23, 42, 0.92);
                color: #cbd5e1;
                font: inherit;
                cursor: pointer;
            }
            .queue_multiselect_input_ {
                flex: 1 1 220px;
                min-width: 180px;
                padding: 0;
                border: 0;
                outline: 0;
                background: transparent;
                color: #f8fafc;
                font: inherit;
            }
            .queue_multiselect_input_::placeholder { color: #94a3b8; }
            .queue_filter_hint_ {
                color: #94a3b8;
                font-size: 13px;
                line-height: 1.7;
            }
            .switcher_link_ {
                padding: 10px 14px;
                border: 1px solid rgba(71, 85, 105, 0.9);
                border-radius: 999px;
                color: #e2e8f0;
                text-decoration: none;
                background: rgba(15, 23, 42, 0.92);
            }
            .switcher_link_.is_active_ {
                color: #f8fafc;
                background: #475569;
                border-color: #475569;
                font-weight: 700;
            }
            .summary_grid_ {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 18px;
                margin-top: 24px;
            }
            .summary_card_ {
                padding: 20px;
                border-radius: 24px;
                background: #0f172a;
                color: #e2e8f0;
                box-shadow: 0 20px 48px rgba(15, 23, 42, 0.16);
            }
            .summary_label_ {
                color: #94a3b8;
                font-size: 13px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }
            .summary_value_ { margin-top: 12px; font-size: 28px; font-weight: 800; word-break: break-word; }
            .meta_list_ { display: grid; gap: 10px; margin: 0; }
            .meta_item_ {
                display: flex;
                justify-content: space-between;
                gap: 16px;
                padding-bottom: 10px;
                border-bottom: 1px solid rgba(51, 65, 85, 0.82);
            }
            .meta_key_ { color: #64748b; font-weight: 700; }
            .meta_value_ { color: #e2e8f0; text-align: right; word-break: break-word; }
            .field_block_ { margin-bottom: 16px; }
            .field_input_, .field_textarea_ {
                width: 100%;
                padding: 12px 14px;
                border: 1px solid rgba(71, 85, 105, 0.9);
                border-radius: 14px;
                background: rgba(2, 6, 23, 0.78);
                color: #e2e8f0;
                font: inherit;
            }
            .field_textarea_ { min-height: 220px; resize: vertical; }
            .hint_text_ { margin-top: 8px; color: #94a3b8; font-size: 13px; line-height: 1.7; }
            .action_row_ { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 18px; }
            .action_button_, .ghost_button_ {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 42px;
                padding: 10px 16px;
                border-radius: 14px;
                border: 1px solid rgba(71, 85, 105, 0.9);
                background: rgba(15, 23, 42, 0.92);
                color: #e2e8f0;
                text-decoration: none;
                font: inherit;
                cursor: pointer;
            }
            .action_button_.is_primary_ { background: #475569; border-color: #475569; color: #f8fafc; font-weight: 700; }
            .action_button_.is_danger_ { background: rgba(127, 29, 29, 0.92); border-color: rgba(248, 113, 113, 0.44); color: #fee2e2; }
            .ghost_button_.is_disabled_ {
                opacity: 0.45;
                cursor: not-allowed;
                pointer-events: none;
            }
            .table_wrap_ { overflow-x: auto; }
            table { width: 100%; border-collapse: collapse; }
            th, td {
                padding: 12px 10px;
                border-bottom: 1px solid rgba(51, 65, 85, 0.92);
                text-align: left;
                vertical-align: top;
                white-space: nowrap;
            }
            th {
                color: #64748b;
                font-size: 12px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }
            td { color: #dbe7f3; }
            .payload_cell_ {
                max-width: 320px;
                white-space: pre-wrap;
                word-break: break-word;
                color: #cbd5e1;
                font-size: 13px;
                line-height: 1.6;
            }
            .empty_state_ {
                padding: 16px;
                border-radius: 18px;
                background: rgba(15, 23, 42, 0.88);
                color: #94a3b8;
            }
            .inline_form_ { display: inline-flex; }
            .pagination_bar_ {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                margin-top: 18px;
            }
            .pagination_bar_.is_right_ { justify-content: flex-end; }
            .pagination_info_ { color: #94a3b8; font-size: 13px; }
            .pagination_links_ { display: flex; flex-wrap: wrap; gap: 10px; }
            .pagination_page_link_.is_active_ {
                color: #f8fafc;
                background: #475569;
                border-color: #475569;
                font-weight: 700;
            }
            .pagination_jump_ {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px;
            }
            .pagination_jump_.is_align_right_ {
                margin-left: auto;
                justify-content: flex-end;
            }
            .pagination_jump_label_ {
                color: #94a3b8;
                font-size: 13px;
                white-space: nowrap;
            }
            .pagination_jump_input_ {
                width: 110px;
            }
            .footer_ { margin-top: 24px; color: #94a3b8; font-size: 13px; }
            .checkbox_cell_ { width: 42px; }
            .payload_toggle_cell_ {
                color: #94a3b8;
                font-size: 13px;
                user-select: none;
                cursor: pointer;
            }
            .payload_toggle_cell_:hover { color: #e2e8f0; }
            .payload_detail_row_ { display: none; }
            .payload_detail_row_.is_open_ { display: table-row; }
            .payload_detail_cell_ {
                padding: 0;
                background: rgba(2, 6, 23, 0.66);
            }
            .payload_detail_inner_ {
                padding: 16px 18px;
                border-top: 1px solid rgba(51, 65, 85, 0.92);
            }
            .payload_detail_meta_ { margin-bottom: 10px; color: #94a3b8; font-size: 13px; }
            .payload_detail_body_ {
                margin: 0;
                white-space: pre-wrap;
                word-break: break-word;
                color: #dbe7f3;
                font-size: 13px;
                line-height: 1.7;
            }
            @media (max-width: 980px) {
                .filter_grid_, .summary_grid_, .section_grid_ { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <main class="page_wrap_">
            @php
                $queue_route_query_ = [
                    'connection' => $page_config_->Selected_Connection_,
                    'db' => $page_config_->Selected_Database_,
                    'queue' => $page_config_->Selected_Queue_List_,
                ];
            @endphp
            <section class="hero_panel_">
                <div class="hero_eyebrow_">Queue Viewer</div>
                <h1 class="hero_title_">{{ $page_config_->Page_Title_ }}</h1>
                <p class="hero_copy_">{{ $page_config_->Page_Subtitle_ }}</p>

                <nav class="tab_nav_">
                    @foreach ($page_config_->Tab_Options_ as $tab_key_ => $tab_label_)
                        <a
                            class="tab_link_ {{ $page_config_->Selected_Tab_ === $tab_key_ ? 'is_active_' : '' }}"
                            href="{{ route($page_config_->Route_Name_, ['tab' => $tab_key_, ...$queue_route_query_]) }}"
                        >
                            {{ $tab_label_ }}
                        </a>
                    @endforeach
                </nav>

                @if ($error_message_ !== '')
                    <div class="status_panel_ is_error_">{{ $error_message_ }}</div>
                @else
                    <div class="status_panel_">{{ $status_message_ }}</div>
                @endif
            </section>

            @if ($page_config_->Selected_Tab_ === 'setting')
                <section class="filter_grid_">
                    <section class="filter_box_">
                        <label class="filter_label_">Connection</label>
                        <nav class="switcher_">
                            @foreach ($page_config_->Connection_Options_ as $connection_key_ => $connection_label_)
                                <a
                                    class="switcher_link_ {{ $page_config_->Selected_Connection_ === $connection_key_ ? 'is_active_' : '' }}"
                                    href="{{ route($page_config_->Route_Name_, ['tab' => $page_config_->Selected_Tab_, 'connection' => $connection_key_, 'db' => $page_config_->Selected_Database_, 'queue' => $page_config_->Selected_Queue_List_]) }}"
                                >
                                    {{ $connection_label_ }}
                                </a>
                            @endforeach
                        </nav>
                    </section>

                    <section class="filter_box_">
                        <label class="filter_label_">DB</label>
                        <nav class="switcher_">
                            @foreach ($page_config_->Database_Options_ as $database_key_ => $database_label_)
                                <a
                                    class="switcher_link_ {{ $page_config_->Selected_Database_ === $database_key_ ? 'is_active_' : '' }}"
                                    href="{{ route($page_config_->Route_Name_, ['tab' => $page_config_->Selected_Tab_, 'connection' => $page_config_->Selected_Connection_, 'db' => $database_key_, 'queue' => $page_config_->Selected_Queue_List_]) }}"
                                >
                                    {{ $database_label_ }}
                                </a>
                            @endforeach
                        </nav>
                    </section>

                </section>
            @elseif ($page_config_->Selected_Tab_ === 'info')
                <section class="summary_grid_">
                    <article class="summary_card_">
                        <div class="summary_label_">Mode</div>
                        <div class="summary_value_">{{ $queue_snapshot_['headline'] }}</div>
                    </article>
                    <article class="summary_card_">
                        <div class="summary_label_">Selected Connection</div>
                        <div class="summary_value_">{{ $queue_snapshot_['selected_connection'] }}</div>
                    </article>
                    <article class="summary_card_">
                        <div class="summary_label_">Selected DB</div>
                        <div class="summary_value_">{{ $queue_snapshot_['selected_database'] }}</div>
                    </article>
                    <article class="summary_card_">
                        <div class="summary_label_">Selected Queue</div>
                        <div class="summary_value_">{{ $queue_snapshot_['selected_queue'] !== '' ? $queue_snapshot_['selected_queue'] : '全部 queue' }}</div>
                    </article>
                </section>

                <section class="section_grid_ is_single_">
                    <article class="data_panel_">
                        <h2 class="panel_title_">Connection Summary</h2>
                        <div class="meta_list_">
                            <div class="meta_item_">
                                <div class="meta_key_">Queue Driver</div>
                                <div class="meta_value_">{{ $queue_snapshot_['queue_driver'] }}</div>
                            </div>
                            <div class="meta_item_">
                                <div class="meta_key_">Jobs Table</div>
                                <div class="meta_value_">{{ $queue_snapshot_['jobs_table'] }}</div>
                            </div>
                            <div class="meta_item_">
                                <div class="meta_key_">Failed Jobs Table</div>
                                <div class="meta_value_">{{ $queue_snapshot_['failed_jobs_table'] }}</div>
                            </div>

                            @if ($queue_snapshot_['mode'] === 'database')
                                <div class="meta_item_">
                                    <div class="meta_key_">Jobs Count</div>
                                    <div class="meta_value_">{{ $queue_snapshot_['jobs_count'] === null ? '-' : $queue_snapshot_['jobs_count'] }}</div>
                                </div>
                                <div class="meta_item_">
                                    <div class="meta_key_">Failed Jobs Count</div>
                                    <div class="meta_value_">{{ $queue_snapshot_['failed_jobs_count'] === null ? '-' : $queue_snapshot_['failed_jobs_count'] }}</div>
                                </div>
                            @else
                                <div class="meta_item_">
                                    <div class="meta_key_">Pending</div>
                                    <div class="meta_value_">{{ $queue_snapshot_['redis_counts']['pending'] === null ? '-' : $queue_snapshot_['redis_counts']['pending'] }}</div>
                                </div>
                                <div class="meta_item_">
                                    <div class="meta_key_">Delayed</div>
                                    <div class="meta_value_">{{ $queue_snapshot_['redis_counts']['delayed'] === null ? '-' : $queue_snapshot_['redis_counts']['delayed'] }}</div>
                                </div>
                                <div class="meta_item_">
                                    <div class="meta_key_">Reserved</div>
                                    <div class="meta_value_">{{ $queue_snapshot_['redis_counts']['reserved'] === null ? '-' : $queue_snapshot_['redis_counts']['reserved'] }}</div>
                                </div>
                            @endif
                        </div>
                    </article>

                    <article class="data_panel_">
                        <h2 class="panel_title_">Key Preview</h2>

                        @if ($queue_snapshot_['mode'] === 'redis')
                            <div class="meta_list_">
                                @foreach ($queue_snapshot_['redis_keys'] as $redis_key_label_ => $redis_key_value_)
                                    <div class="meta_item_">
                                        <div class="meta_key_">{{ strtoupper($redis_key_label_) }}</div>
                                        <div class="meta_value_">{{ $redis_key_value_ }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="meta_list_">
                                <div class="meta_item_">
                                    <div class="meta_key_">Jobs Table Exists</div>
                                    <div class="meta_value_">{{ $queue_snapshot_['jobs_table_exists'] ? 'yes' : 'no' }}</div>
                                </div>
                                <div class="meta_item_">
                                    <div class="meta_key_">Failed Jobs Table Exists</div>
                                    <div class="meta_value_">{{ $queue_snapshot_['failed_jobs_table_exists'] ? 'yes' : 'no' }}</div>
                                </div>
                            </div>
                        @endif
                    </article>
                </section>
            @elseif ($page_config_->Selected_Tab_ === 'queue')
                <section class="filter_grid_ is_single_">
                    <section class="filter_box_">
                        <label class="filter_label_">Queue</label>
                        <form class="filter_form_" method="GET" action="{{ route($page_config_->Route_Name_) }}">
                            <input type="hidden" name="tab" value="queue">
                            <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                            <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                            <div
                                class="queue_multiselect_"
                                data-queue-multiselect_
                                data-selected-queues_='@json($page_config_->Selected_Queue_List_)'
                            >
                                <div class="queue_multiselect_tags_" data-queue-tags_></div>
                                <input
                                    class="queue_multiselect_input_"
                                    type="text"
                                    list="queue_filter_options_"
                                    placeholder="輸入 queue 後按 Enter，可多選"
                                    data-queue-input_
                                >
                            </div>
                            <datalist id="queue_filter_options_">
                                @foreach ($page_config_->Queue_Options_ as $queue_key_ => $queue_label_)
                                    @continue($queue_key_ === '')
                                    <option value="{{ $queue_key_ }}">{{ $queue_label_ }}</option>
                                @endforeach
                            </datalist>
                            <div class="queue_filter_hint_">可輸入自訂 queue 名稱，按 Enter 先加入 tag；可連續加入多個 queue。當輸入框為空時，再按一次 Enter 才會送出篩選。</div>
                        </form>
                    </section>
                </section>

                <section class="section_grid_ is_single_">
                    <article class="data_panel_">
                        <h2 class="panel_title_">Queue Jobs</h2>
                        <div class="hint_text_">
                            目前篩選 queue：{{ $queue_snapshot_['selected_queue'] !== '' ? $queue_snapshot_['selected_queue'] : '全部 queue' }}
                        </div>

                        @if (! $queue_snapshot_['queue_actions_supported'])
                            <div class="empty_state_">redis queue 目前只有檢視摘要，不提供直接新增 / 修改 / 刪除。</div>
                        @elseif ($queue_snapshot_['recent_jobs'] === [])
                            <div class="empty_state_">目前沒有可顯示的 jobs 資料。</div>
                        @else
                            <div class="action_row_">
                                <button class="action_button_ is_danger_" type="submit" form="queue_bulk_delete_form_">刪除多選</button>
                                <form class="inline_form_" method="POST" action="{{ route('tool.page.queue_viewer.queue_clear_selected') }}">
                                    @csrf
                                    <input type="hidden" name="tab" value="queue">
                                    <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                                    <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                                    @foreach ($page_config_->Selected_Queue_List_ as $selected_queue_name_)
                                        <input type="hidden" name="selected_queue[]" value="{{ $selected_queue_name_ }}">
                                    @endforeach
                                    <input type="hidden" name="queue_page" value="{{ $queue_snapshot_['queue_pagination']['current_page'] ?? 1 }}">
                                    <button class="action_button_ is_danger_" type="submit" @disabled($page_config_->Selected_Queue_List_ === [])>清空指定 queue 所有 job</button>
                                </form>
                                <form class="inline_form_" method="POST" action="{{ route('tool.page.queue_viewer.queue_clear_all') }}">
                                    @csrf
                                    <input type="hidden" name="tab" value="queue">
                                    <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                                    <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                                    <input type="hidden" name="queue_page" value="{{ $queue_snapshot_['queue_pagination']['current_page'] ?? 1 }}">
                                    <button class="action_button_ is_danger_" type="submit">清空 queue 所有 job</button>
                                </form>
                                @if ($queue_snapshot_['queue_pagination'] !== null)
                                    <form class="pagination_jump_ is_align_right_" method="GET" action="{{ route($page_config_->Route_Name_) }}">
                                        <input type="hidden" name="tab" value="queue">
                                        <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                                        <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                                        @foreach ($page_config_->Selected_Queue_List_ as $selected_queue_name_)
                                            <input type="hidden" name="queue[]" value="{{ $selected_queue_name_ }}">
                                        @endforeach
                                        <span class="pagination_jump_label_">總頁數 {{ $queue_snapshot_['queue_pagination']['last_page'] }}</span>
                                        <input
                                            class="field_input_ pagination_jump_input_"
                                            type="number"
                                            name="queue_page"
                                            min="1"
                                            max="{{ $queue_snapshot_['queue_pagination']['last_page'] }}"
                                            value="{{ $queue_snapshot_['queue_pagination']['current_page'] }}"
                                            aria-label="queue page"
                                        >
                                        <button class="action_button_ is_primary_" type="submit">跳頁</button>
                                    </form>
                                @endif
                            </div>

                            <form id="queue_bulk_delete_form_" method="POST" action="{{ route('tool.page.queue_viewer.queue_bulk_delete') }}">
                                @csrf
                                <input type="hidden" name="tab" value="queue">
                                <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                                <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                                @foreach ($page_config_->Selected_Queue_List_ as $selected_queue_name_)
                                    <input type="hidden" name="selected_queue[]" value="{{ $selected_queue_name_ }}">
                                @endforeach
                                <input type="hidden" name="queue_page" value="{{ $queue_snapshot_['queue_pagination']['current_page'] ?? 1 }}">
                            </form>

                            <div class="table_wrap_">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="checkbox_cell_">
                                                <input type="checkbox" aria-label="全選 jobs" data-queue-select-all_>
                                            </th>
                                            <th>ID</th>
                                            <th>Queue</th>
                                            <th>Display Name</th>
                                            <th>Attempts</th>
                                            <th>Reserved</th>
                                            <th>Available</th>
                                            <th>Created</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($queue_snapshot_['recent_jobs'] as $job_row_)
                                            <tr data-queue-row_ data-job-id_="{{ $job_row_['id'] }}" data-job-queue_="{{ $job_row_['queue'] }}" data-job-display-name_="{{ $job_row_['display_name'] }}" data-job-payload_="{{ base64_encode($job_row_['payload']) }}">
                                                <td class="checkbox_cell_">
                                                    <input type="checkbox" name="selected_job_ids[]" value="{{ $job_row_['id'] }}" form="queue_bulk_delete_form_" data-queue-select-item_>
                                                </td>
                                                <td>{{ $job_row_['id'] }}</td>
                                                <td>{{ $job_row_['queue'] }}</td>
                                                <td>{{ $job_row_['display_name'] }}</td>
                                                <td>{{ $job_row_['attempts'] }}</td>
                                                <td>{{ $job_row_['reserved_at'] }}</td>
                                                <td>{{ $job_row_['available_at'] }}</td>
                                                <td>{{ $job_row_['created_at'] }}</td>
                                                <td>
                                                    <div class="action_row_">
                                                        <form class="inline_form_" method="POST" action="{{ route('tool.page.queue_viewer.queue_delete', ['job_id' => $job_row_['id']]) }}">
                                                            @csrf
                                                            <input type="hidden" name="tab" value="queue">
                                                            <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                                                            <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                                                            @foreach ($page_config_->Selected_Queue_List_ as $selected_queue_name_)
                                                                <input type="hidden" name="selected_queue[]" value="{{ $selected_queue_name_ }}">
                                                            @endforeach
                                                            <input type="hidden" name="queue_page" value="{{ $queue_snapshot_['queue_pagination']['current_page'] ?? 1 }}">
                                                            <button class="action_button_ is_danger_" type="submit">刪除</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="payload_detail_row_" data-payload-detail-row_>
                                                <td class="payload_detail_cell_" colspan="9">
                                                    <div class="payload_detail_inner_">
                                                        <div class="payload_detail_meta_" data-payload-detail-meta_>Job #{{ $job_row_['id'] }} | Queue: {{ $job_row_['queue'] }} | Display Name: {{ $job_row_['display_name'] }}</div>
                                                        <pre class="payload_detail_body_" data-payload-detail-body_>-</pre>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($queue_snapshot_['queue_pagination'] !== null)
                                <div class="pagination_bar_">
                                    <div class="pagination_info_">
                                        queue page {{ $queue_snapshot_['queue_pagination']['current_page'] }} / {{ $queue_snapshot_['queue_pagination']['last_page'] }}
                                        ，總筆數 {{ $queue_snapshot_['queue_pagination']['total'] }}
                                        ，快捷鍵 q(最前) w(前一頁) e(下一頁) r(最後)
                                    </div>
                                    <div
                                        class="pagination_links_"
                                        data-queue-shortcuts_
                                        data-first-url_="{{ route($page_config_->Route_Name_, ['tab' => 'queue', ...$queue_route_query_, 'queue_page' => 1]) }}"
                                        data-prev-url_="{{ route($page_config_->Route_Name_, ['tab' => 'queue', ...$queue_route_query_, 'queue_page' => $queue_snapshot_['queue_pagination']['previous_page']]) }}"
                                        data-next-url_="{{ route($page_config_->Route_Name_, ['tab' => 'queue', ...$queue_route_query_, 'queue_page' => $queue_snapshot_['queue_pagination']['next_page']]) }}"
                                        data-last-url_="{{ route($page_config_->Route_Name_, ['tab' => 'queue', ...$queue_route_query_, 'queue_page' => $queue_snapshot_['queue_pagination']['last_page']]) }}"
                                    >
                                        @if ($queue_snapshot_['queue_pagination']['current_page'] > 1)
                                            <a class="ghost_button_" href="{{ route($page_config_->Route_Name_, ['tab' => 'queue', ...$queue_route_query_, 'queue_page' => 1]) }}">最前</a>
                                        @else
                                            <span class="ghost_button_ is_disabled_">最前</span>
                                        @endif
                                        @if ($queue_snapshot_['queue_pagination']['has_previous_page'])
                                            <a class="ghost_button_" href="{{ route($page_config_->Route_Name_, ['tab' => 'queue', ...$queue_route_query_, 'queue_page' => $queue_snapshot_['queue_pagination']['previous_page']]) }}">上一頁</a>
                                        @else
                                            <span class="ghost_button_ is_disabled_">上一頁</span>
                                        @endif
                                        @php
                                            $page_window_start_ = max($queue_snapshot_['queue_pagination']['current_page'] - 2, 1);
                                            $page_window_end_ = min($queue_snapshot_['queue_pagination']['current_page'] + 2, $queue_snapshot_['queue_pagination']['last_page']);
                                        @endphp
                                        @foreach (range($page_window_start_, $page_window_end_) as $page_number_)
                                            <a
                                                class="ghost_button_ pagination_page_link_ {{ $page_number_ === $queue_snapshot_['queue_pagination']['current_page'] ? 'is_active_' : '' }}"
                                                href="{{ route($page_config_->Route_Name_, ['tab' => 'queue', ...$queue_route_query_, 'queue_page' => $page_number_]) }}"
                                            >
                                                {{ $page_number_ }}
                                            </a>
                                        @endforeach
                                        @if ($queue_snapshot_['queue_pagination']['has_more_pages'])
                                            <a class="ghost_button_" href="{{ route($page_config_->Route_Name_, ['tab' => 'queue', ...$queue_route_query_, 'queue_page' => $queue_snapshot_['queue_pagination']['next_page']]) }}">下一頁</a>
                                        @else
                                            <span class="ghost_button_ is_disabled_">下一頁</span>
                                        @endif
                                        @if ($queue_snapshot_['queue_pagination']['current_page'] < $queue_snapshot_['queue_pagination']['last_page'])
                                            <a class="ghost_button_" href="{{ route($page_config_->Route_Name_, ['tab' => 'queue', ...$queue_route_query_, 'queue_page' => $queue_snapshot_['queue_pagination']['last_page']]) }}">最後</a>
                                        @else
                                            <span class="ghost_button_ is_disabled_">最後</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                        @endif
                    </article>
                </section>
            @else
                <section class="section_grid_ is_single_">
                    <article class="data_panel_">
                        <h2 class="panel_title_">Failed Queue Jobs</h2>

                        @if ($queue_snapshot_['recent_failed_jobs'] === [])
                            <div class="empty_state_">目前沒有可顯示的 failed jobs 資料。</div>
                        @else
                            @if ($queue_snapshot_['fail_queue_pagination'] !== null)
                                <div class="action_row_">
                                    <button class="action_button_ is_danger_" type="submit" form="fail_queue_bulk_delete_form_">刪除多選</button>
                                    <form class="inline_form_" method="POST" action="{{ route('tool.page.queue_viewer.fail_queue_clear_selected') }}">
                                        @csrf
                                        <input type="hidden" name="tab" value="fail_queue">
                                        <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                                        <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                                        @foreach ($page_config_->Selected_Queue_List_ as $selected_queue_name_)
                                            <input type="hidden" name="selected_queue[]" value="{{ $selected_queue_name_ }}">
                                        @endforeach
                                        <input type="hidden" name="fail_queue_page" value="{{ $queue_snapshot_['fail_queue_pagination']['current_page'] ?? 1 }}">
                                        <button class="action_button_ is_danger_" type="submit" @disabled($page_config_->Selected_Queue_List_ === [])>清空指定 queue 所有 job</button>
                                    </form>
                                    <form class="inline_form_" method="POST" action="{{ route('tool.page.queue_viewer.fail_queue_clear_all') }}">
                                        @csrf
                                        <input type="hidden" name="tab" value="fail_queue">
                                        <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                                        <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                                        <input type="hidden" name="fail_queue_page" value="{{ $queue_snapshot_['fail_queue_pagination']['current_page'] ?? 1 }}">
                                        <button class="action_button_ is_danger_" type="submit">清空 queue 所有 job</button>
                                    </form>
                                    <form class="pagination_jump_ is_align_right_" method="GET" action="{{ route($page_config_->Route_Name_) }}">
                                        <input type="hidden" name="tab" value="fail_queue">
                                        <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                                        <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                                        @foreach ($page_config_->Selected_Queue_List_ as $selected_queue_name_)
                                            <input type="hidden" name="queue[]" value="{{ $selected_queue_name_ }}">
                                        @endforeach
                                        <span class="pagination_jump_label_">總頁數 {{ $queue_snapshot_['fail_queue_pagination']['last_page'] }}</span>
                                        <input
                                            class="field_input_ pagination_jump_input_"
                                            type="number"
                                            name="fail_queue_page"
                                            min="1"
                                            max="{{ $queue_snapshot_['fail_queue_pagination']['last_page'] }}"
                                            value="{{ $queue_snapshot_['fail_queue_pagination']['current_page'] }}"
                                            aria-label="fail_queue page"
                                        >
                                        <button class="action_button_ is_primary_" type="submit">跳頁</button>
                                    </form>
                                </div>
                            @endif

                            <form id="fail_queue_bulk_delete_form_" method="POST" action="{{ route('tool.page.queue_viewer.fail_queue_bulk_delete') }}">
                                @csrf
                                <input type="hidden" name="tab" value="fail_queue">
                                <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                                <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                                @foreach ($page_config_->Selected_Queue_List_ as $selected_queue_name_)
                                    <input type="hidden" name="selected_queue[]" value="{{ $selected_queue_name_ }}">
                                @endforeach
                                <input type="hidden" name="fail_queue_page" value="{{ $queue_snapshot_['fail_queue_pagination']['current_page'] ?? 1 }}">
                            </form>

                            <div class="table_wrap_">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="checkbox_cell_">
                                                <input type="checkbox" aria-label="全選 failed jobs" data-failed-queue-select-all_>
                                            </th>
                                            <th>ID</th>
                                            <th>UUID</th>
                                            <th>Connection</th>
                                            <th>Queue</th>
                                            <th>Display Name</th>
                                            <th>Failed At</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($queue_snapshot_['recent_failed_jobs'] as $failed_job_row_)
                                            <tr data-failed-queue-row_ data-failed-job-id_="{{ $failed_job_row_['id'] }}" data-failed-job-queue_="{{ $failed_job_row_['queue'] }}" data-failed-job-display-name_="{{ $failed_job_row_['display_name'] }}" data-failed-job-payload_="{{ base64_encode($failed_job_row_['payload']) }}">
                                                <td class="checkbox_cell_">
                                                    <input type="checkbox" name="selected_failed_job_ids[]" value="{{ $failed_job_row_['id'] }}" form="fail_queue_bulk_delete_form_" data-failed-queue-select-item_>
                                                </td>
                                                <td>{{ $failed_job_row_['id'] }}</td>
                                                <td>{{ $failed_job_row_['uuid'] }}</td>
                                                <td>{{ $failed_job_row_['connection'] }}</td>
                                                <td>{{ $failed_job_row_['queue'] }}</td>
                                                <td>{{ $failed_job_row_['display_name'] }}</td>
                                                <td>{{ $failed_job_row_['failed_at'] }}</td>
                                                <td>
                                                    <div class="action_row_">
                                                        <form class="inline_form_" method="POST" action="{{ route('tool.page.queue_viewer.fail_queue_delete', ['job_id' => $failed_job_row_['id']]) }}">
                                                            @csrf
                                                            <input type="hidden" name="tab" value="fail_queue">
                                                            <input type="hidden" name="connection" value="{{ $page_config_->Selected_Connection_ }}">
                                                            <input type="hidden" name="db" value="{{ $page_config_->Selected_Database_ }}">
                                                            @foreach ($page_config_->Selected_Queue_List_ as $selected_queue_name_)
                                                                <input type="hidden" name="selected_queue[]" value="{{ $selected_queue_name_ }}">
                                                            @endforeach
                                                            <input type="hidden" name="fail_queue_page" value="{{ $queue_snapshot_['fail_queue_pagination']['current_page'] ?? 1 }}">
                                                            <button class="action_button_ is_danger_" type="submit">刪除</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="payload_detail_row_" data-failed-payload-detail-row_>
                                                <td class="payload_detail_cell_" colspan="8">
                                                    <div class="payload_detail_inner_">
                                                        <div class="payload_detail_meta_">Failed Job #{{ $failed_job_row_['id'] }} | Queue: {{ $failed_job_row_['queue'] }} | Display Name: {{ $failed_job_row_['display_name'] }}</div>
                                                        <pre class="payload_detail_body_" data-failed-payload-detail-body_>-</pre>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($queue_snapshot_['fail_queue_pagination'] !== null)
                                <div class="pagination_bar_">
                                    <div class="pagination_info_">
                                        fail_queue page {{ $queue_snapshot_['fail_queue_pagination']['current_page'] }} / {{ $queue_snapshot_['fail_queue_pagination']['last_page'] }}
                                        ，總筆數 {{ $queue_snapshot_['fail_queue_pagination']['total'] }}
                                    </div>
                                    <div class="pagination_links_">
                                        @if ($queue_snapshot_['fail_queue_pagination']['current_page'] > 1)
                                            <a class="ghost_button_" href="{{ route($page_config_->Route_Name_, ['tab' => 'fail_queue', ...$queue_route_query_, 'fail_queue_page' => 1]) }}">最前</a>
                                        @else
                                            <span class="ghost_button_ is_disabled_">最前</span>
                                        @endif
                                        @if ($queue_snapshot_['fail_queue_pagination']['has_previous_page'])
                                            <a class="ghost_button_" href="{{ route($page_config_->Route_Name_, ['tab' => 'fail_queue', ...$queue_route_query_, 'fail_queue_page' => $queue_snapshot_['fail_queue_pagination']['previous_page']]) }}">上一頁</a>
                                        @else
                                            <span class="ghost_button_ is_disabled_">上一頁</span>
                                        @endif
                                        @php
                                            $fail_page_window_start_ = max($queue_snapshot_['fail_queue_pagination']['current_page'] - 2, 1);
                                            $fail_page_window_end_ = min($queue_snapshot_['fail_queue_pagination']['current_page'] + 2, $queue_snapshot_['fail_queue_pagination']['last_page']);
                                        @endphp
                                        @foreach (range($fail_page_window_start_, $fail_page_window_end_) as $fail_page_number_)
                                            <a
                                                class="ghost_button_ pagination_page_link_ {{ $fail_page_number_ === $queue_snapshot_['fail_queue_pagination']['current_page'] ? 'is_active_' : '' }}"
                                                href="{{ route($page_config_->Route_Name_, ['tab' => 'fail_queue', ...$queue_route_query_, 'fail_queue_page' => $fail_page_number_]) }}"
                                            >
                                                {{ $fail_page_number_ }}
                                            </a>
                                        @endforeach
                                        @if ($queue_snapshot_['fail_queue_pagination']['has_more_pages'])
                                            <a class="ghost_button_" href="{{ route($page_config_->Route_Name_, ['tab' => 'fail_queue', ...$queue_route_query_, 'fail_queue_page' => $queue_snapshot_['fail_queue_pagination']['next_page']]) }}">下一頁</a>
                                        @else
                                            <span class="ghost_button_ is_disabled_">下一頁</span>
                                        @endif
                                        @if ($queue_snapshot_['fail_queue_pagination']['current_page'] < $queue_snapshot_['fail_queue_pagination']['last_page'])
                                            <a class="ghost_button_" href="{{ route($page_config_->Route_Name_, ['tab' => 'fail_queue', ...$queue_route_query_, 'fail_queue_page' => $queue_snapshot_['fail_queue_pagination']['last_page']]) }}">最後</a>
                                        @else
                                            <span class="ghost_button_ is_disabled_">最後</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    </article>
                </section>
            @endif

            <div class="footer_">
                route: `{{ $page_config_->Route_Name_ }}`
            </div>
        </main>
        @if ($page_config_->Selected_Tab_ === 'queue')
            <script>
                (function () {
                    const multiselect_wrap_ = document.querySelector('[data-queue-multiselect_]');
                    if (!(multiselect_wrap_ instanceof HTMLElement)) {
                        return;
                    }

                    const tags_wrap_ = multiselect_wrap_.querySelector('[data-queue-tags_]');
                    const input_ = multiselect_wrap_.querySelector('[data-queue-input_]');
                    const form_ = multiselect_wrap_.closest('form');
                    if (!(tags_wrap_ instanceof HTMLElement) || !(input_ instanceof HTMLInputElement) || !(form_ instanceof HTMLFormElement)) {
                        return;
                    }

                    let selected_queue_list_ = [];

                    try {
                        const parsed_selected_queue_list_ = JSON.parse(multiselect_wrap_.dataset.selectedQueues_ ?? '[]');
                        if (Array.isArray(parsed_selected_queue_list_)) {
                            selected_queue_list_ = parsed_selected_queue_list_
                                .map((queue_name_) => String(queue_name_).trim())
                                .filter((queue_name_) => queue_name_ !== '');
                        }
                    } catch (error_) {
                        selected_queue_list_ = [];
                    }

                    const queue_hidden_inputs_sync_ = () => {
                        form_.querySelectorAll('input[name="queue[]"][data-generated-queue-input_]').forEach((input_wrap_) => {
                            input_wrap_.remove();
                        });

                        for (const queue_name_ of selected_queue_list_) {
                            const hidden_input_ = document.createElement('input');
                            hidden_input_.type = 'hidden';
                            hidden_input_.name = 'queue[]';
                            hidden_input_.value = queue_name_;
                            hidden_input_.setAttribute('data-generated-queue-input_', '1');
                            form_.appendChild(hidden_input_);
                        }
                    };

                    const queue_tag_render_ = () => {
                        tags_wrap_.innerHTML = '';

                        for (const queue_name_ of selected_queue_list_) {
                            const tag_wrap_ = document.createElement('span');
                            tag_wrap_.className = 'queue_multiselect_tag_';
                            tag_wrap_.textContent = queue_name_;

                            const remove_button_ = document.createElement('button');
                            remove_button_.className = 'queue_multiselect_remove_';
                            remove_button_.type = 'button';
                            remove_button_.textContent = 'x';
                            remove_button_.setAttribute('aria-label', `移除 ${queue_name_}`);
                            remove_button_.addEventListener('click', () => {
                                selected_queue_list_ = selected_queue_list_.filter((selected_queue_name_) => selected_queue_name_ !== queue_name_);
                                queue_hidden_inputs_sync_();
                                queue_tag_render_();
                                input_.focus();
                            });

                            tag_wrap_.appendChild(remove_button_);
                            tags_wrap_.appendChild(tag_wrap_);
                        }
                    };

                    const queue_add_ = (queue_name_) => {
                        const normalized_queue_name_ = queue_name_.trim();
                        if (normalized_queue_name_ === '' || selected_queue_list_.includes(normalized_queue_name_)) {
                            return;
                        }

                        selected_queue_list_.push(normalized_queue_name_);
                        queue_hidden_inputs_sync_();
                        queue_tag_render_();
                        input_.value = '';
                    };

                    input_.addEventListener('keydown', (event_) => {
                        if (event_.key !== 'Enter') {
                            return;
                        }

                        event_.preventDefault();
                        if (input_.value.trim() !== '') {
                            queue_add_(input_.value);

                            return;
                        }

                        form_.requestSubmit();
                    });

                    form_.addEventListener('submit', () => {
                        queue_add_(input_.value);
                    });

                    queue_hidden_inputs_sync_();
                    queue_tag_render_();
                }());
            </script>
        @endif
        @if ($page_config_->Selected_Tab_ === 'queue' && $queue_snapshot_['queue_pagination'] !== null)
            <script>
                (function () {
                    const select_all_input_ = document.querySelector('[data-queue-select-all_]');
                    const select_item_inputs_ = Array.from(document.querySelectorAll('[data-queue-select-item_]'));
                    if (!(select_all_input_ instanceof HTMLInputElement) || select_item_inputs_.length === 0) {
                        return;
                    }

                    const select_all_state_sync_ = () => {
                        const checked_count_ = select_item_inputs_.filter((input_) => input_.checked).length;
                        select_all_input_.checked = checked_count_ > 0 && checked_count_ === select_item_inputs_.length;
                        select_all_input_.indeterminate = checked_count_ > 0 && checked_count_ < select_item_inputs_.length;
                    };

                    select_all_input_.addEventListener('change', () => {
                        for (const select_item_input_ of select_item_inputs_) {
                            select_item_input_.checked = select_all_input_.checked;
                        }

                        select_all_state_sync_();
                    });

                    for (const select_item_input_ of select_item_inputs_) {
                        select_item_input_.addEventListener('change', select_all_state_sync_);
                    }

                    select_all_state_sync_();
                }());
            </script>
            <script>
                (function () {
                    const job_rows_ = Array.from(document.querySelectorAll('[data-queue-row_]'));
                    if (job_rows_.length === 0) {
                        return;
                    }

                    const detail_row_close_all_ = (except_detail_row_ = null) => {
                        for (const job_row_ of job_rows_) {
                            if (!(job_row_ instanceof HTMLElement)) {
                                continue;
                            }

                            const detail_row_ = job_row_.nextElementSibling;
                            if (!(detail_row_ instanceof HTMLElement) || !detail_row_.hasAttribute('data-payload-detail-row_') || detail_row_ === except_detail_row_) {
                                continue;
                            }

                            detail_row_.classList.remove('is_open_');
                        }
                    };

                    for (const job_row_ of job_rows_) {
                        if (!(job_row_ instanceof HTMLElement)) {
                            continue;
                        }

                        const detail_row_ = job_row_.nextElementSibling;
                        if (!(detail_row_ instanceof HTMLElement) || !detail_row_.hasAttribute('data-payload-detail-row_')) {
                            continue;
                        }

                        const detail_body_ = detail_row_.querySelector('[data-payload-detail-body_]');
                        if (!(detail_body_ instanceof HTMLElement)) {
                            continue;
                        }

                        job_row_.addEventListener('dblclick', (event_) => {
                            const target_ = event_.target;
                            if (target_ instanceof HTMLElement) {
                                const tag_name_ = target_.tagName.toLowerCase();
                                if (tag_name_ === 'input' || tag_name_ === 'button' || tag_name_ === 'a' || target_.closest('form') !== null) {
                                    return;
                                }
                            }

                            const payload_base64_ = job_row_.dataset.jobPayload_ ?? '';
                            const payload_ = payload_base64_ === '' ? '-' : atob(payload_base64_);
                            const is_open_ = detail_row_.classList.contains('is_open_');

                            detail_row_close_all_(detail_row_);
                            detail_body_.textContent = payload_;
                            detail_row_.classList.toggle('is_open_', !is_open_);
                        });
                    }
                }());
            </script>
            <script>
                document.addEventListener('keydown', function (event_) {
                    if (event_.altKey || event_.ctrlKey || event_.metaKey) {
                        return;
                    }

                    const target_ = event_.target;
                    if (target_ instanceof HTMLElement) {
                        const tag_name_ = target_.tagName.toLowerCase();
                        if (tag_name_ === 'input' || tag_name_ === 'textarea' || tag_name_ === 'select' || target_.isContentEditable) {
                            return;
                        }
                    }

                    const shortcut_wrap_ = document.querySelector('[data-queue-shortcuts_]');
                    if (!(shortcut_wrap_ instanceof HTMLElement)) {
                        return;
                    }

                    const shortcut_map_ = {
                        q: shortcut_wrap_.dataset.firstUrl_,
                        w: shortcut_wrap_.dataset.prevUrl_,
                        e: shortcut_wrap_.dataset.nextUrl_,
                        r: shortcut_wrap_.dataset.lastUrl_,
                    };

                    const target_url_ = shortcut_map_[event_.key];

                    if (typeof target_url_ === 'string' && target_url_ !== '') {
                        window.location.href = target_url_;
                    }
                });
            </script>
        @endif
        @if ($page_config_->Selected_Tab_ === 'fail_queue' && $queue_snapshot_['fail_queue_pagination'] !== null)
            <script>
                (function () {
                    const select_all_input_ = document.querySelector('[data-failed-queue-select-all_]');
                    const select_item_inputs_ = Array.from(document.querySelectorAll('[data-failed-queue-select-item_]'));
                    if (!(select_all_input_ instanceof HTMLInputElement) || select_item_inputs_.length === 0) {
                        return;
                    }

                    const select_all_state_sync_ = () => {
                        const checked_count_ = select_item_inputs_.filter((input_) => input_.checked).length;
                        select_all_input_.checked = checked_count_ > 0 && checked_count_ === select_item_inputs_.length;
                        select_all_input_.indeterminate = checked_count_ > 0 && checked_count_ < select_item_inputs_.length;
                    };

                    select_all_input_.addEventListener('change', () => {
                        for (const select_item_input_ of select_item_inputs_) {
                            select_item_input_.checked = select_all_input_.checked;
                        }

                        select_all_state_sync_();
                    });

                    for (const select_item_input_ of select_item_inputs_) {
                        select_item_input_.addEventListener('change', select_all_state_sync_);
                    }

                    select_all_state_sync_();
                }());
            </script>
            <script>
                (function () {
                    const failed_job_rows_ = Array.from(document.querySelectorAll('[data-failed-queue-row_]'));
                    if (failed_job_rows_.length === 0) {
                        return;
                    }

                    const detail_row_close_all_ = (except_detail_row_ = null) => {
                        for (const failed_job_row_ of failed_job_rows_) {
                            if (!(failed_job_row_ instanceof HTMLElement)) {
                                continue;
                            }

                            const detail_row_ = failed_job_row_.nextElementSibling;
                            if (!(detail_row_ instanceof HTMLElement) || !detail_row_.hasAttribute('data-failed-payload-detail-row_') || detail_row_ === except_detail_row_) {
                                continue;
                            }

                            detail_row_.classList.remove('is_open_');
                        }
                    };

                    for (const failed_job_row_ of failed_job_rows_) {
                        if (!(failed_job_row_ instanceof HTMLElement)) {
                            continue;
                        }

                        const detail_row_ = failed_job_row_.nextElementSibling;
                        if (!(detail_row_ instanceof HTMLElement) || !detail_row_.hasAttribute('data-failed-payload-detail-row_')) {
                            continue;
                        }

                        const detail_body_ = detail_row_.querySelector('[data-failed-payload-detail-body_]');
                        if (!(detail_body_ instanceof HTMLElement)) {
                            continue;
                        }

                        failed_job_row_.addEventListener('dblclick', (event_) => {
                            const target_ = event_.target;
                            if (target_ instanceof HTMLElement) {
                                const tag_name_ = target_.tagName.toLowerCase();
                                if (tag_name_ === 'input' || tag_name_ === 'button' || tag_name_ === 'a' || target_.closest('form') !== null) {
                                    return;
                                }
                            }

                            const payload_base64_ = failed_job_row_.dataset.failedJobPayload_ ?? '';
                            const payload_ = payload_base64_ === '' ? '-' : atob(payload_base64_);
                            const is_open_ = detail_row_.classList.contains('is_open_');

                            detail_row_close_all_(detail_row_);
                            detail_body_.textContent = payload_;
                            detail_row_.classList.toggle('is_open_', !is_open_);
                        });
                    }
                }());
            </script>
        @endif
    </body>
</html>
