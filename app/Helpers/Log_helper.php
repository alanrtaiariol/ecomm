<?php 
// app/Helpers/Log_helper.php
if(! function_exists('log_event')) {
    function log_event($event, $message) {
        $path = WRITEPATH . 'logs/products/events.log';

        $logMessage = date('Y-m-d H:i:s') . " - {$event}: {$message}" . PHP_EOL;

        file_put_contents($path, $logMessage, FILE_APPEND);
    }
}