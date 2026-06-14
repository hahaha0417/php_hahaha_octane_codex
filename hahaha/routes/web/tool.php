<?php

use hahaha\tool\page\log_viewer\hahaha_route_log_viewer;
use hahaha\tool\page\queue_viewer\hahaha_route_queue_viewer;

// http://127.0.0.1:10001/tool/page/log/viewer
hahaha_route_log_viewer::Instance()->Register();
// http://127.0.0.1:10001/tool/page/queue/viewer
hahaha_route_queue_viewer::Instance()->Register();
