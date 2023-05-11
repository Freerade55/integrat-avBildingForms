<?php
//
////// логи
//
function logs($webhook_data)

{


    if (file_exists(ROOT . "/logs/log.json")) {
        $log = file_get_contents(ROOT . "/logs/log.json");
        $log = json_decode($log, true);
    } else {
        $log = [];
    }

    $t = explode(" ",microtime());
    $log[date("Y-m-d H:i:s", $t[1]).substr((string)$t[0],1,4)] = $webhook_data;
    $log = json_encode($log, JSON_UNESCAPED_UNICODE);
    file_put_contents(ROOT . "/logs/log.json", $log);
}


