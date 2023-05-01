<?php
sleep(1);
include_once 'base_path_helper.php';
?>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <link rel="stylesheet" type="text/css" href="<?php echo baseUrl()?>css/core.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo baseUrl()?>css/fontawesome.css"/>

    <!--
    if we use ./images/icon.png then it missing in cpanel
    -->
    <link rel="shortcut icon" href="<?php echo baseUrl()?>images/icon.png"/>
    <link rel="apple-touch-icon" href="<?php echo baseUrl()?>images/icon.png"/>
    <link rel="apple-touch-icon-precomposed" href="<?php echo baseUrl()?>images/icon.png"/>

    <title>Install</title>
</head>
<body>
<!doctype html>