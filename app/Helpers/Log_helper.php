<?php 
// app/Helpers/Log_helper.php
if(! function_exists('log_crud_action')){
    function log_crud_action($action, $description) {
        $logger = \Config\Services::logger();

        $message = date('Y-m-d H:i:s') . ' - ' . strtoupper($action) . ': ' . strtoupper($description);
        $logger->info($message,['channel' => 'products']);
    }
}