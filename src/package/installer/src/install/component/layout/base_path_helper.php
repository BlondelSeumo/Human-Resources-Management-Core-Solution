<?php
	function baseUrl()
	{
        $sub_folder = str_replace('index.php', '', $_SERVER['PHP_SELF']);
        $sub_folder = str_replace('install', '', $sub_folder);
        $base_url = rtrim($sub_folder, '/');
        $base_url = $base_url ? $base_url.'/' : '/';
        return $base_url;
	}