<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $page_config_->Page_Title_ }}</title>
        <style>
            * { box-sizing: border-box; }
            :root {
                color-scheme: dark;
                --bg: #08111f;
                --panel: rgba(10, 22, 39, 0.86);
                --panel-border: rgba(148, 163, 184, 0.18);
                --text: #e5eef8;
                --muted: #93a4bb;
                --healthy: #34d399;
                --warning: #f59e0b;
                --critical: #fb7185;
                --cpu: #fb7185;
                --memory: #38bdf8;
                --disk: #f59e0b;
            }
            body {
                margin: 0;
                color: var(--text);
                font-family: "Segoe UI", sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(56, 189, 248, 0.16), transparent 28%),
                    radial-gradient(circle at top right, rgba(251, 113, 133, 0.14), transparent 24%),
                    linear-gradient(180deg, #040b14 0%, #08111f 46%, #030812 100%);
            }
            .page_wrap_ { max-width: 1320px; margin: 0 auto; padding: 28px 18px 56px; }
            .hero_panel_ {
                padding: 28px;
                border-radius: 28px;
                border: 1px solid var(--panel-border);
                background: rgba(7, 15, 28, 0.84);
                box-shadow: 0 28px 72px rgba(0, 0, 0, 0.28);
            }
            .hero_eyebrow_ {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 7px 12px;
                border-radius: 999px;
                background: rgba(56, 189, 248, 0.14);
                color: #bae6fd;
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            .hero_title_ { margin: 18px 0 12px; font-size: clamp(32px, 5vw, 54px); line-height: 1.04; }
            .hero_copy_ { max-width: 860px; margin: 0; color: #c8d6e5; line-height: 1.8; }
            .hero_meta_ {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 14px;
                margin-top: 22px;
            }
            .hero_meta_card_ {
                padding: 16px 18px;
                border-radius: 18px;
                background: rgba(15, 23, 42, 0.74);
                border: 1px solid rgba(71, 85, 105, 0.56);
            }
            .hero_meta_label_ { color: var(--muted); font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; }
            .hero_meta_value_ { margin-top: 10px; font-size: 18px; font-weight: 700; word-break: break-word; }
            .section_grid_ {
                display: grid;
                grid-template-columns: minmax(0, 1.8fr) minmax(320px, 0.95fr);
                gap: 20px;
                margin-top: 22px;
            }
            .panel_ {
                padding: 22px;
                border-radius: 24px;
                background: var(--panel);
                border: 1px solid var(--panel-border);
                box-shadow: 0 20px 48px rgba(0, 0, 0, 0.18);
            }
            .panel_title_ { margin: 0; font-size: 22px; }
            .panel_subtitle_ { margin: 10px 0 0; color: var(--muted); line-height: 1.7; }
            .chart_wrap_ {
                position: relative;
                margin-top: 18px;
                min-height: 360px;
                border-radius: 24px;
                background: linear-gradient(180deg, rgba(15, 23, 42, 0.8), rgba(2, 6, 23, 0.92));
                border: 1px solid rgba(51, 65, 85, 0.82);
                overflow: hidden;
            }
            .chart_canvas_ {
                position: absolute;
                inset: 0;
                display: block;
                width: 100%;
                height: 360px;
            }
            .chart_legend_ { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
            .chart_legend_item_ { display: inline-flex; align-items: center; gap: 8px; color: #cbd5e1; font-size: 13px; }
            .chart_dot_ { width: 10px; height: 10px; border-radius: 999px; }
            .chart_dot_line_ { width: 16px; height: 3px; border-radius: 999px; }
            .stats_grid_ {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
                margin-top: 20px;
            }
            .stat_card_ {
                padding: 20px;
                border-radius: 22px;
                background: rgba(15, 23, 42, 0.86);
                border: 1px solid rgba(51, 65, 85, 0.88);
            }
            .stat_label_ { color: var(--muted); font-size: 13px; letter-spacing: 0.08em; text-transform: uppercase; }
            .stat_value_ { margin-top: 10px; font-size: 30px; font-weight: 800; }
            .stat_desc_ { margin-top: 8px; color: #cbd5e1; line-height: 1.7; }
            .stat_bar_ {
                height: 10px;
                margin-top: 14px;
                border-radius: 999px;
                background: rgba(30, 41, 59, 0.9);
                overflow: hidden;
            }
            .stat_bar_fill_ { height: 100%; border-radius: inherit; }
            .status_tag_ {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 80px;
                margin-top: 14px;
                padding: 7px 10px;
                border-radius: 999px;
                color: #08111f;
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }
            .status_tag_.is_healthy_ { background: var(--healthy); }
            .status_tag_.is_warning_ { background: var(--warning); }
            .status_tag_.is_critical_ { background: var(--critical); }
            .meta_list_ { display: grid; gap: 12px; margin-top: 18px; }
            .meta_item_ {
                display: grid;
                grid-template-columns: minmax(120px, 150px) minmax(0, 1fr);
                gap: 12px;
                padding-bottom: 12px;
                border-bottom: 1px solid rgba(51, 65, 85, 0.82);
            }
            .meta_key_ { color: var(--muted); font-size: 13px; text-transform: uppercase; letter-spacing: 0.06em; }
            .meta_value_ { color: var(--text); word-break: break-word; }
            .refresh_panel_ {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 12px;
                margin-top: 18px;
                color: var(--muted);
                font-size: 13px;
            }
            .refresh_control_ {
                display: inline-flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px;
            }
            .refresh_input_ {
                width: 120px;
                padding: 8px 10px;
                border: 1px solid rgba(71, 85, 105, 0.9);
                border-radius: 10px;
                color: var(--text);
                background: rgba(15, 23, 42, 0.92);
                font: inherit;
            }
            .refresh_button_ {
                padding: 8px 14px;
                border: 0;
                border-radius: 10px;
                color: #08111f;
                background: #38bdf8;
                font: inherit;
                font-weight: 700;
                cursor: pointer;
            }
            .footer_ { margin-top: 22px; color: var(--muted); font-size: 13px; }
            @media (max-width: 1160px) {
                .section_grid_, .stats_grid_, .hero_meta_ { grid-template-columns: 1fr; }
            }
            @media (max-width: 760px) {
                .page_wrap_ { padding: 18px 12px 40px; }
                .hero_panel_, .panel_, .stat_card_ { padding: 18px; }
                .chart_wrap_ { min-height: 280px; }
                .chart_canvas_ { height: 280px; }
                .meta_item_ { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <main class="page_wrap_">
            <section class="hero_panel_">
                <div class="hero_eyebrow_">Multiple Node / Tool Page</div>
                <h1 class="hero_title_">{{ $page_config_->Page_Title_ }}</h1>
                <p class="hero_copy_">{{ $page_config_->Page_Subtitle_ }}</p>

                <div class="hero_meta_">
                    <article class="hero_meta_card_">
                        <div class="hero_meta_label_">Host</div>
                        <div class="hero_meta_value_" data-host-name_>{{ $initial_snapshot_['system']['host_name'] }}</div>
                    </article>
                    <article class="hero_meta_card_">
                        <div class="hero_meta_label_">OS</div>
                        <div class="hero_meta_value_" data-os-name_>{{ $initial_snapshot_['system']['os'] }}</div>
                    </article>
                    <article class="hero_meta_card_">
                        <div class="hero_meta_label_">Updated At</div>
                        <div class="hero_meta_value_" data-generated-at_>{{ $initial_snapshot_['generated_at'] }}</div>
                    </article>
                    <article class="hero_meta_card_">
                        <div class="hero_meta_label_">Refresh</div>
                        <div class="hero_meta_value_" data-refresh-display_>{{ $page_config_->Refresh_Interval_Milliseconds_ }} ms / {{ $page_config_->Chart_Max_Points_ }} points</div>
                    </article>
                </div>
            </section>

            <section class="stats_grid_" data-metric-cards_>
                @foreach ($initial_snapshot_['metrics'] as $metric_key_ => $metric_)
                    <article class="stat_card_" data-metric-card_="{{ $metric_key_ }}">
                        <div class="stat_label_">{{ $metric_['label'] }}</div>
                        <div class="stat_value_" data-metric-value_>{{ $metric_['display'] }}</div>
                        <div class="stat_desc_" data-metric-description_>{{ $metric_['description'] }}</div>
                        <div class="stat_bar_">
                            <div
                                class="stat_bar_fill_"
                                data-metric-bar_
                                style="width: {{ max(0, min(100, $metric_['value'])) }}%; background: {{ $page_config_->Metric_Options_[$metric_key_]['color'] ?? '#38bdf8' }};"
                            ></div>
                        </div>
                        <div class="status_tag_ is_{{ $metric_['status'] }}_" data-metric-status_>{{ $metric_['status'] }}</div>
                    </article>
                @endforeach
            </section>

            <section class="section_grid_">
                <article class="panel_">
                    <h2 class="panel_title_">Realtime Usage Chart</h2>
                    <p class="panel_subtitle_" data-refresh-subtitle_>每 {{ $page_config_->Refresh_Interval_Milliseconds_ }} ms 輪詢一次 metrics，保留最近 {{ $page_config_->Chart_Max_Points_ }} 筆資料，追蹤 Total CPU / CPU Core / Memory / Disk 變化。</p>
                    <div class="chart_wrap_">
                        <canvas class="chart_canvas_" data-chart-grid-canvas_></canvas>
                        <canvas class="chart_canvas_" data-chart-data-canvas_></canvas>
                    </div>
                    <div class="chart_legend_" data-chart-legend_>
                        @foreach ($page_config_->Metric_Options_ as $metric_option_)
                            <div class="chart_legend_item_">
                                <span class="chart_dot_" style="background: {{ $metric_option_['color'] }};"></span>
                                <span>{{ $metric_option_['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="refresh_panel_">
                        <div>Route: `{{ $page_config_->Route_Name_ }}`</div>
                        <div>Metrics: `{{ $page_config_->Metrics_Route_Name_ }}`</div>
                        <div class="refresh_control_">
                            <label for="refresh_interval_input_">Refresh (ms)</label>
                            <input id="refresh_interval_input_" class="refresh_input_" type="number" min="30" max="60000" step="10" value="{{ $page_config_->Refresh_Interval_Milliseconds_ }}" data-refresh-input_>
                            <button type="button" class="refresh_button_" data-refresh-apply_>Apply</button>
                        </div>
                        <div data-refresh-state_>ready</div>
                    </div>
                </article>

                <article class="panel_">
                    <h2 class="panel_title_">Runtime Detail</h2>
                    <p class="panel_subtitle_">優先讀取 `tool_c#/hardware-info.json`，同時列出 Laravel 執行環境、JSON 更新時間與實際讀取來源。</p>
                    <div class="meta_list_" data-detail-list_>
                        <div class="meta_item_"><div class="meta_key_">Hardware Source</div><div class="meta_value_" data-detail-hardware-source_>{{ $initial_snapshot_['system']['hardware_source'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Hardware Updated</div><div class="meta_value_" data-detail-hardware-updated_>{{ $initial_snapshot_['system']['hardware_updated_at'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Hardware JSON Path</div><div class="meta_value_" data-detail-hardware-path_>{{ $initial_snapshot_['details']['app']['hardware_info_path'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">PHP</div><div class="meta_value_" data-detail-php_>{{ $initial_snapshot_['system']['php_version'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Laravel Env</div><div class="meta_value_" data-detail-env_>{{ $initial_snapshot_['system']['laravel_env'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Cache Driver</div><div class="meta_value_" data-detail-cache_>{{ $initial_snapshot_['system']['cache_driver'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Queue Driver</div><div class="meta_value_" data-detail-queue_>{{ $initial_snapshot_['system']['queue_driver'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Timezone</div><div class="meta_value_" data-detail-timezone_>{{ $initial_snapshot_['system']['timezone'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Request Memory</div><div class="meta_value_" data-detail-request-memory_>{{ $initial_snapshot_['details']['app']['current_request_memory'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Peak Memory</div><div class="meta_value_" data-detail-peak-memory_>{{ $initial_snapshot_['details']['app']['peak_request_memory'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Memory Limit</div><div class="meta_value_" data-detail-memory-limit_>{{ $initial_snapshot_['details']['app']['memory_limit'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Base Path</div><div class="meta_value_" data-detail-base-path_>{{ $initial_snapshot_['details']['app']['base_path'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">CPU Source</div><div class="meta_value_" data-detail-cpu-source_>{{ $initial_snapshot_['details']['cpu']['source'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Memory Source</div><div class="meta_value_" data-detail-memory-source_>{{ $initial_snapshot_['details']['memory']['source'] }}</div></div>
                        <div class="meta_item_"><div class="meta_key_">Disk Mount</div><div class="meta_value_" data-detail-disk-mount_>{{ $initial_snapshot_['details']['disk']['mount_path'] }}</div></div>
                    </div>
                </article>
            </section>

            <div class="footer_">
                即時資料優先來自 `tool_c#/hardware-info.json`；若檔案不存在或格式無法解析，才會退回伺服器端 PHP snapshot / 系統命令 fallback。
            </div>
        </main>

        <script id="system_resource_monitor_bootstrap_" type="application/json">{!! $frontend_bootstrap_ !!}</script>
        <script>
            (function () {
                const bootstrapElement = document.getElementById('system_resource_monitor_bootstrap_');
                if (!(bootstrapElement instanceof HTMLScriptElement)) {
                    return;
                }

                let bootstrap = null;
                try {
                    bootstrap = JSON.parse(bootstrapElement.textContent || '{}');
                } catch (error_) {
                    return;
                }

                const state = {
                    chartMaxPoints: Number(bootstrap.page?.chartMaxPoints || 24),
                    refreshIntervalMilliseconds: Number(bootstrap.page?.refreshIntervalMilliseconds || 5000),
                    metricOptions: bootstrap.page?.metricOptions || {},
                    metricsEndpoint: String(bootstrap.page?.endpoints?.metrics || ''),
                    history: [],
                    refreshTimerId: 0,
                    isFetching: false,
                    chartFrameId: 0,
                    lastRenderedCoreCount: -1,
                    lastGridSignature: '',
                    devicePixelRatio: Math.min(Math.max(window.devicePixelRatio || 1, 1), 2),
                };

                const refreshStateElement = document.querySelector('[data-refresh-state_]');
                const generatedAtElement = document.querySelector('[data-generated-at_]');
                const hostNameElement = document.querySelector('[data-host-name_]');
                const osNameElement = document.querySelector('[data-os-name_]');
                const refreshDisplayElement = document.querySelector('[data-refresh-display_]');
                const refreshSubtitleElement = document.querySelector('[data-refresh-subtitle_]');
                const refreshInputElement = document.querySelector('[data-refresh-input_]');
                const refreshApplyElement = document.querySelector('[data-refresh-apply_]');
                const gridCanvas = document.querySelector('[data-chart-grid-canvas_]');
                const dataCanvas = document.querySelector('[data-chart-data-canvas_]');
                const chartLegendElement = document.querySelector('[data-chart-legend_]');
                if (!(gridCanvas instanceof HTMLCanvasElement) || !(dataCanvas instanceof HTMLCanvasElement)) {
                    return;
                }

                const detailMap = {
                    hardwareSource: document.querySelector('[data-detail-hardware-source_]'),
                    hardwareUpdated: document.querySelector('[data-detail-hardware-updated_]'),
                    hardwarePath: document.querySelector('[data-detail-hardware-path_]'),
                    php: document.querySelector('[data-detail-php_]'),
                    env: document.querySelector('[data-detail-env_]'),
                    cache: document.querySelector('[data-detail-cache_]'),
                    queue: document.querySelector('[data-detail-queue_]'),
                    timezone: document.querySelector('[data-detail-timezone_]'),
                    requestMemory: document.querySelector('[data-detail-request-memory_]'),
                    peakMemory: document.querySelector('[data-detail-peak-memory_]'),
                    memoryLimit: document.querySelector('[data-detail-memory-limit_]'),
                    basePath: document.querySelector('[data-detail-base-path_]'),
                    cpuSource: document.querySelector('[data-detail-cpu-source_]'),
                    memorySource: document.querySelector('[data-detail-memory-source_]'),
                    diskMount: document.querySelector('[data-detail-disk-mount_]'),
                };

                const metricCardMap = {};
                document.querySelectorAll('[data-metric-card_]').forEach((element) => {
                    if (!(element instanceof HTMLElement)) {
                        return;
                    }

                    const key = element.dataset.metricCard_ || '';
                    metricCardMap[key] = {
                        value: element.querySelector('[data-metric-value_]'),
                        description: element.querySelector('[data-metric-description_]'),
                        bar: element.querySelector('[data-metric-bar_]'),
                        status: element.querySelector('[data-metric-status_]'),
                    };
                });

                const pushSnapshot = (snapshot) => {
                    if (!snapshot || typeof snapshot !== 'object') {
                        return;
                    }

                    const perCorePercentages = Array.isArray(snapshot.details?.cpu?.per_core_percentages)
                        ? snapshot.details.cpu.per_core_percentages.map((value) => Number(value || 0))
                        : [];

                    state.history.push({
                        label: String(snapshot.sample_label || ''),
                        cpu: Number(snapshot.metrics?.cpu?.value || 0),
                        memory: Number(snapshot.metrics?.memory?.value || 0),
                        disk: Number(snapshot.metrics?.disk?.value || 0),
                        cpuPerCore: perCorePercentages,
                    });

                    while (state.history.length > state.chartMaxPoints) {
                        state.history.shift();
                    }
                };

                const statusTextResolve = (status) => {
                    if (status === 'critical') {
                        return 'critical';
                    }

                    if (status === 'warning') {
                        return 'warning';
                    }

                    return 'healthy';
                };

                const metricColorResolve = (metricKey) => {
                    return String(state.metricOptions?.[metricKey]?.color || '#38bdf8');
                };

                const cpuCoreCountResolve = () => {
                    return state.history.reduce((maxCount, sample) => {
                        return Math.max(maxCount, Array.isArray(sample.cpuPerCore) ? sample.cpuPerCore.length : 0);
                    }, 0);
                };

                const refreshIntervalMillisecondsNormalize = (value) => {
                    return Math.max(30, Math.min(60000, Number(value || 0) || 5000));
                };

                const refreshUiRender = () => {
                    const refreshText = state.refreshIntervalMilliseconds + ' ms / ' + state.chartMaxPoints + ' points';
                    if (refreshDisplayElement instanceof HTMLElement) {
                        refreshDisplayElement.textContent = refreshText;
                    }

                    if (refreshSubtitleElement instanceof HTMLElement) {
                        refreshSubtitleElement.textContent = '每 ' + state.refreshIntervalMilliseconds + ' ms 輪詢一次 metrics，保留最近 ' + state.chartMaxPoints + ' 筆資料，追蹤 Total CPU / CPU Core / Memory / Disk 變化。';
                    }

                    if (refreshInputElement instanceof HTMLInputElement) {
                        const nextValue = String(state.refreshIntervalMilliseconds);
                        if (document.activeElement !== refreshInputElement && refreshInputElement.value !== nextValue) {
                            refreshInputElement.value = nextValue;
                        }
                    }
                };

                const cpuCoreColorResolve = (coreIndex) => {
                    const hue = (coreIndex * 37) % 360;

                    return 'hsl(' + hue + ' 88% 64%)';
                };

                const cpuCoreSeriesResolve = () => {
                    const coreCount = cpuCoreCountResolve();

                    return Array.from({ length: coreCount }, (_, coreIndex) => ({
                        key: 'cpuCore' + coreIndex,
                        label: 'CPU Core ' + (coreIndex + 1),
                        color: cpuCoreColorResolve(coreIndex),
                        valueResolve: (sample) => Number(sample.cpuPerCore?.[coreIndex] || 0),
                    }));
                };

                const renderLegend = () => {
                    if (!(chartLegendElement instanceof HTMLElement)) {
                        return;
                    }

                    const coreCount = cpuCoreCountResolve();
                    if (state.lastRenderedCoreCount === coreCount) {
                        return;
                    }

                    const legendItems = [
                        {
                            label: String(state.metricOptions?.cpu?.label || 'CPU'),
                            color: metricColorResolve('cpu'),
                            dotClassName: 'chart_dot_',
                        },
                        {
                            label: String(state.metricOptions?.memory?.label || 'Memory'),
                            color: metricColorResolve('memory'),
                            dotClassName: 'chart_dot_',
                        },
                        {
                            label: String(state.metricOptions?.disk?.label || 'Disk'),
                            color: metricColorResolve('disk'),
                            dotClassName: 'chart_dot_',
                        },
                        ...cpuCoreSeriesResolve().map((series) => ({
                            label: series.label,
                            color: series.color,
                            dotClassName: 'chart_dot_line_',
                        })),
                    ];

                    chartLegendElement.innerHTML = legendItems.map((legendItem) => {
                        return '<div class="chart_legend_item_"><span class="' + legendItem.dotClassName + '" style="background: ' + legendItem.color + ';"></span><span>' + legendItem.label + '</span></div>';
                    }).join('');
                    state.lastRenderedCoreCount = coreCount;
                };

                const renderMetricCards = (snapshot) => {
                    ['cpu', 'memory', 'disk'].forEach((metricKey) => {
                        const metric = snapshot.metrics?.[metricKey];
                        const card = metricCardMap[metricKey];
                        if (!metric || !card) {
                            return;
                        }

                        if (card.value instanceof HTMLElement) {
                            card.value.textContent = String(metric.display || '0.0%');
                        }

                        if (card.description instanceof HTMLElement) {
                            card.description.textContent = String(metric.description || '');
                        }

                        if (card.bar instanceof HTMLElement) {
                            const widthValue = Math.max(0, Math.min(100, Number(metric.value || 0)));
                            card.bar.style.width = widthValue + '%';
                            card.bar.style.background = metricColorResolve(metricKey);
                        }

                        if (card.status instanceof HTMLElement) {
                            const normalizedStatus = statusTextResolve(String(metric.status || 'healthy'));
                            card.status.textContent = normalizedStatus;
                            card.status.className = 'status_tag_ is_' + normalizedStatus + '_';
                        }
                    });
                };

                const renderDetail = (snapshot) => {
                    if (generatedAtElement instanceof HTMLElement) {
                        generatedAtElement.textContent = String(snapshot.generated_at || '-');
                    }

                    if (hostNameElement instanceof HTMLElement) {
                        hostNameElement.textContent = String(snapshot.system?.host_name || '-');
                    }

                    if (osNameElement instanceof HTMLElement) {
                        osNameElement.textContent = String(snapshot.system?.os || '-');
                    }

                    const detailAssignments = {
                        hardwareSource: snapshot.system?.hardware_source,
                        hardwareUpdated: snapshot.system?.hardware_updated_at,
                        hardwarePath: snapshot.details?.app?.hardware_info_path,
                        php: snapshot.system?.php_version,
                        env: snapshot.system?.laravel_env,
                        cache: snapshot.system?.cache_driver,
                        queue: snapshot.system?.queue_driver,
                        timezone: snapshot.system?.timezone,
                        requestMemory: snapshot.details?.app?.current_request_memory,
                        peakMemory: snapshot.details?.app?.peak_request_memory,
                        memoryLimit: snapshot.details?.app?.memory_limit,
                        basePath: snapshot.details?.app?.base_path,
                        cpuSource: snapshot.details?.cpu?.source,
                        memorySource: snapshot.details?.memory?.source,
                        diskMount: snapshot.details?.disk?.mount_path,
                    };

                    Object.entries(detailAssignments).forEach(([key, value]) => {
                        const element = detailMap[key];
                        if (element instanceof HTMLElement) {
                            element.textContent = String(value || '-');
                        }
                    });
                };

                const chartDimensionsResolve = () => {
                    const drawWidth = dataCanvas.clientWidth;
                    const drawHeight = dataCanvas.clientHeight;
                    const padding = { top: 24, right: 18, bottom: 36, left: 42 };
                    const chartWidth = drawWidth - padding.left - padding.right;
                    const chartHeight = drawHeight - padding.top - padding.bottom;
                    if (chartWidth <= 0 || chartHeight <= 0) {
                        return null;
                    }

                    return {
                        drawWidth,
                        drawHeight,
                        padding,
                        chartWidth,
                        chartHeight,
                    };
                };

                const canvasContextPrepare = (targetCanvas) => {
                    const context = targetCanvas.getContext('2d');
                    if (!context) {
                        return null;
                    }

                    const devicePixelRatio = Math.min(Math.max(window.devicePixelRatio || 1, 1), 2);
                    state.devicePixelRatio = devicePixelRatio;
                    const width = Math.round(targetCanvas.clientWidth * devicePixelRatio);
                    const height = Math.round(targetCanvas.clientHeight * devicePixelRatio);
                    if (targetCanvas.width !== width || targetCanvas.height !== height) {
                        targetCanvas.width = width;
                        targetCanvas.height = height;
                    }

                    context.setTransform(1, 0, 0, 1, 0, 0);
                    context.scale(devicePixelRatio, devicePixelRatio);

                    return context;
                };

                const renderChartGrid = () => {
                    const context = canvasContextPrepare(gridCanvas);
                    const dimensions = chartDimensionsResolve();
                    if (!context || !dimensions) {
                        return;
                    }

                    const gridSignature = [
                        dimensions.drawWidth,
                        dimensions.drawHeight,
                        state.devicePixelRatio,
                    ].join(':');
                    if (state.lastGridSignature === gridSignature) {
                        return;
                    }

                    state.lastGridSignature = gridSignature;
                    context.clearRect(0, 0, dimensions.drawWidth, dimensions.drawHeight);
                    context.strokeStyle = 'rgba(71, 85, 105, 0.64)';
                    context.lineWidth = 1;
                    context.font = '12px Segoe UI';
                    context.fillStyle = '#93a4bb';

                    for (let tick = 0; tick <= 4; tick++) {
                        const percent = tick * 25;
                        const y = dimensions.padding.top + dimensions.chartHeight - (percent / 100) * dimensions.chartHeight;
                        context.beginPath();
                        context.moveTo(dimensions.padding.left, y);
                        context.lineTo(dimensions.padding.left + dimensions.chartWidth, y);
                        context.stroke();
                        context.fillText(percent + '%', 6, y + 4);
                    }
                };

                const renderChartData = () => {
                    const context = canvasContextPrepare(dataCanvas);
                    const dimensions = chartDimensionsResolve();
                    if (!context || !dimensions) {
                        return;
                    }

                    context.clearRect(0, 0, dimensions.drawWidth, dimensions.drawHeight);

                    const samples = state.history;
                    if (samples.length === 0) {
                        return;
                    }

                    const xPositionResolve = (index) => {
                        if (samples.length === 1) {
                            return dimensions.padding.left + dimensions.chartWidth;
                        }

                        return dimensions.padding.left + (dimensions.chartWidth * index / (samples.length - 1));
                    };

                    const yPositionResolve = (value) => {
                        return dimensions.padding.top + dimensions.chartHeight - (Math.max(0, Math.min(100, value)) / 100) * dimensions.chartHeight;
                    };

                    const chartSeries = [
                        ...cpuCoreSeriesResolve(),
                        {
                            key: 'cpu',
                            color: metricColorResolve('cpu'),
                            valueResolve: (sample) => Number(sample.cpu || 0),
                        },
                        {
                            key: 'memory',
                            color: metricColorResolve('memory'),
                            valueResolve: (sample) => Number(sample.memory || 0),
                        },
                        {
                            key: 'disk',
                            color: metricColorResolve('disk'),
                            valueResolve: (sample) => Number(sample.disk || 0),
                        },
                    ];

                    chartSeries.forEach((series) => {
                        context.beginPath();
                        context.strokeStyle = series.color;
                        context.lineWidth = series.key === 'cpu' || series.key === 'memory' || series.key === 'disk' ? 3 : 2;

                        samples.forEach((sample, index) => {
                            const x = xPositionResolve(index);
                            const y = yPositionResolve(series.valueResolve(sample));
                            if (index === 0) {
                                context.moveTo(x, y);
                            } else {
                                context.lineTo(x, y);
                            }
                        });

                        context.stroke();

                        const latestSample = samples[samples.length - 1];
                        const latestX = xPositionResolve(samples.length - 1);
                        const latestY = yPositionResolve(series.valueResolve(latestSample));
                        context.beginPath();
                        context.fillStyle = series.color;
                        context.arc(latestX, latestY, series.key === 'cpu' || series.key === 'memory' || series.key === 'disk' ? 4 : 3, 0, Math.PI * 2);
                        context.fill();
                    });

                    context.fillStyle = '#93a4bb';
                    context.font = '12px Segoe UI';
                    context.textAlign = 'center';
                    const labelStep = Math.max(1, Math.ceil(samples.length / 6));
                    samples.forEach((sample, index) => {
                        if (index % labelStep !== 0 && index !== samples.length - 1) {
                            return;
                        }

                        context.fillText(String(sample.label || ''), xPositionResolve(index), dimensions.drawHeight - 10);
                    });
                    context.textAlign = 'start';
                };

                const renderChartSchedule = () => {
                    if (state.chartFrameId) {
                        return;
                    }

                    state.chartFrameId = window.requestAnimationFrame(() => {
                        state.chartFrameId = 0;
                        renderChartGrid();
                        renderChartData();
                    });
                };

                const renderSnapshot = (snapshot) => {
                    pushSnapshot(snapshot);
                    renderMetricCards(snapshot);
                    renderDetail(snapshot);
                    refreshUiRender();
                    renderLegend();
                    renderChartSchedule();
                };

                const fetchSnapshot = async () => {
                    if (state.metricsEndpoint === '' || state.isFetching) {
                        return;
                    }

                    state.isFetching = true;
                    if (refreshStateElement instanceof HTMLElement) {
                        refreshStateElement.textContent = 'refreshing';
                    }

                    try {
                        const response = await fetch(state.metricsEndpoint, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            cache: 'no-store',
                        });

                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status);
                        }

                        const snapshot = await response.json();
                        renderSnapshot(snapshot);

                        if (refreshStateElement instanceof HTMLElement) {
                            refreshStateElement.textContent = 'updated at ' + String(snapshot.generated_at || '-');
                        }
                    } catch (error_) {
                        if (refreshStateElement instanceof HTMLElement) {
                            refreshStateElement.textContent = 'refresh failed';
                        }
                    } finally {
                        state.isFetching = false;
                    }
                };

                const refreshTimerRestart = () => {
                    if (state.refreshTimerId) {
                        window.clearInterval(state.refreshTimerId);
                    }

                    state.refreshTimerId = window.setInterval(fetchSnapshot, state.refreshIntervalMilliseconds);
                };

                const refreshIntervalApply = () => {
                    const nextValue = refreshIntervalMillisecondsNormalize(refreshInputElement instanceof HTMLInputElement ? refreshInputElement.value : state.refreshIntervalMilliseconds);
                    state.refreshIntervalMilliseconds = nextValue;
                    refreshUiRender();
                    refreshTimerRestart();

                    const url = new URL(window.location.href);
                    url.searchParams.set('refresh_ms', String(nextValue));
                    window.history.replaceState({}, '', url.toString());

                    if (refreshStateElement instanceof HTMLElement) {
                        refreshStateElement.textContent = 'refresh interval set to ' + nextValue + ' ms';
                    }
                };

                renderSnapshot(bootstrap.snapshot || {});
                refreshTimerRestart();
                window.addEventListener('resize', () => {
                    state.lastGridSignature = '';
                    renderChartSchedule();
                });
                if (refreshApplyElement instanceof HTMLButtonElement) {
                    refreshApplyElement.addEventListener('click', refreshIntervalApply);
                }

                if (refreshInputElement instanceof HTMLInputElement) {
                    refreshInputElement.addEventListener('keydown', (event_) => {
                        if (event_.key === 'Enter') {
                            refreshIntervalApply();
                        }
                    });
                }
            }());
        </script>
    </body>
</html>
