<?php

function phpInfoArray()
{
    ob_start();
    phpinfo();
    $info_arr = array();
    $info_lines = explode("\n", strip_tags(ob_get_clean(), "<tr><td><h2>"));
    $cat = "General";
    foreach ($info_lines as $line) {
        // new cat?
        preg_match("~<h2>(.*)</h2>~", $line, $title) ? $cat = $title[1] : null;
        if (preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
            $info_arr[$cat][$val[1]] = $val[2];
        } elseif (preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
            $info_arr[$cat][$val[1]] = array("local" => $val[2], "master" => $val[3]);
        }
    }

    $newArray = [];
    foreach ($info_arr as $key => $value) {
        $newKey = strtolower(str_replace(" ", "_", str_replace_last(' ', '', $key)));
        foreach ($value as $k => $v) {
            $test = strtolower(str_replace(" ", "_", str_replace_last(' ', '', $k)));
            $newArray[$newKey][$test] = $v;
        }
    }
    return $newArray;
}

function str_replace_last($search, $replace, $str)
{
    if (($pos = strrpos($str, $search)) !== false) {
        $search_length = strlen($search);
        $str = substr_replace($str, $replace, $pos, $search_length);
    }
    return $str;
}