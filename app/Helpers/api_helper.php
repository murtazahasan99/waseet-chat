<?php

if (!function_exists('responsBuilder')) {

    function responsBuilder($msg = '', $status = true, $data = []) : array
    {
        return [
            'status' => $status,
            'msg' => $msg,
            'data' => $data
        ];
    }
}
