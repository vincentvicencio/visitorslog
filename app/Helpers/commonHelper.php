<?php

//PAGE NAME
if (!function_exists("page_name")) {
    function page_name($name)
    {
        $path = explode('/', Request::path());
        $menu_name = strtolower($path[0]);
        $main = str_replace("_", " ", $menu_name);
        $sub  = isset($path[1]) && $path[1] ? strtolower($path[1]) : '';

        $menu = [
            'menu' => $menu_name,
            'main' => $main,
            'sub' => $sub,
            'page_title' => $main
        ];

        return $menu[$name];
    }
}

// CURRENT PAGE
if (!function_exists("current_page")) {
    function current_page()
    {
        $link  = Request::segments();
        return end($link);
    }
}