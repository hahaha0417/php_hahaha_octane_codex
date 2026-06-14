<?php

namespace hahaha\tool\page\log_viewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewType;

class hahaha_controller_log_viewer extends Controller
{
    public function Index(Request $request): ViewType
    {
        $log_directory_path_ = trim((string) $request->query('log_directory', ''));
        $log_file_ = trim((string) $request->query('log_file', ''));
        $keyword_ = trim((string) $request->query('keyword', ''));
        $severity_filter_ = trim((string) $request->query('severity_filter', 'all'));
        $block_limit_ = (int) $request->query('block_limit', 99);
        $page_config_ = hahaha_config_log_viewer_base::Instance()->Clear()->Initial($log_directory_path_, $log_file_, $keyword_, $severity_filter_, $block_limit_);
        $frontend_bootstrap_ = json_encode([
            'state' => $page_config_->Frontend_State_Resolve(),
            'endpoints' => [
                'page' => route('page.demo.log_viewer'),
                'files' => route('page.demo.log_viewer.files'),
                'content' => route('page.demo.log_viewer.content'),
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return View::file(base_path('tool/page/log_viewer/view/hahaha_view_log_viewer.blade.php'), [
            'page_config_' => $page_config_,
            'frontend_bootstrap_' => $frontend_bootstrap_,
        ]);
    }

    public function Files(Request $request): JsonResponse
    {
        $log_directory_path_ = trim((string) $request->query('log_directory', ''));
        $log_file_ = trim((string) $request->query('log_file', ''));
        $keyword_ = trim((string) $request->query('keyword', ''));
        $severity_filter_ = trim((string) $request->query('severity_filter', 'all'));
        $block_limit_ = (int) $request->query('block_limit', 99);
        $page_config_ = hahaha_config_log_viewer_base::Instance()->Clear()->Initial($log_directory_path_, $log_file_, $keyword_, $severity_filter_, $block_limit_);

        return response()->json($page_config_->Frontend_State_Resolve());
    }

    public function Content(Request $request): HttpResponse
    {
        $log_directory_path_ = trim((string) $request->query('log_directory', ''));
        $log_file_ = trim((string) $request->query('log_file', ''));
        $page_config_ = hahaha_config_log_viewer_base::Instance()->Clear()->Initial($log_directory_path_, $log_file_);

        if ($page_config_->Log_Directory_Path_ === '') {
            return response('找不到指定的 log 資料夾。', 404, [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]);
        }

        $selected_log_file_path_ = $page_config_->Selected_Log_File_Path_Resolve(
            $page_config_->Log_Directory_Path_,
            $log_file_ !== '' ? $log_file_ : $page_config_->Selected_Log_File_,
        );

        if ($selected_log_file_path_ === '' || ! is_file($selected_log_file_path_) || ! is_readable($selected_log_file_path_)) {
            return response('找不到可讀取的 log 檔案。', 404, [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]);
        }

        $log_file_last_updated_ = filemtime($selected_log_file_path_) ?: time();
        $log_file_size_ = filesize($selected_log_file_path_) ?: 0;
        $entity_tag_ = '"'.sha1(implode('|', [
            $selected_log_file_path_,
            $log_file_last_updated_,
            $log_file_size_,
        ])).'"';
        $last_modified_header_ = gmdate('D, d M Y H:i:s', $log_file_last_updated_).' GMT';
        $request_entity_tag_ = trim((string) $request->headers->get('If-None-Match', ''));
        $request_last_modified_ = trim((string) $request->headers->get('If-Modified-Since', ''));
        $response_headers_ = [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'ETag' => $entity_tag_,
            'Last-Modified' => $last_modified_header_,
        ];

        if ($request_entity_tag_ === $entity_tag_) {
            return response('', 304, $response_headers_);
        }

        if ($request_last_modified_ !== '' && strtotime($request_last_modified_) >= $log_file_last_updated_) {
            return response('', 304, $response_headers_);
        }

        return response(file_get_contents($selected_log_file_path_) ?: '', 200, $response_headers_);
    }
}
