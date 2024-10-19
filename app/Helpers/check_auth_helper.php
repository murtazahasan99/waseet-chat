<?php

if (!function_exists('checkAuth')) {

    function checkAuth($role = "isLoggedIn") 
    {
        $session = session();
        if (isset($session->$role) && isset($session->user_id) && isset($session->username)) {
            return true;
        }else{
            return false;  
        }
    }
}
