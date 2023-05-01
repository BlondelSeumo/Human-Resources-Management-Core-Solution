<?php

include_once 'Requirement.php';
include_once 'PermissionHelper.php';
include_once 'PhpOption.php';
include_once 'MysqlRequirement.php';

function checkPhpVersion()
{
    $your_php = phpversion();
    $minimum_php = '8.0 or 8.1';
    $isSupported = (floatval($your_php) == floatval('8.0')) || (floatval($your_php) == floatval('8.1'));

    $result = [
        'php_version' => [
            'current' => $your_php,
            'minimum' => $minimum_php,
            'is_supported' => $isSupported
        ],
        'errors' => !$isSupported
    ];
    return ['php_version' => $result];
}

function checkFolderPermission()
{
    $config = json_decode(file_get_contents('./config.json'));
    $permissions = new PermissionHelper($config);
    $permission_result = $permissions->check();

    return ['permissions' => $permission_result];
}

function checkPhpOptions()
{
    $config = json_decode(file_get_contents('./config.json'));
    $option = new PhpOption($config);
    $php_options_result = $option->checkPhpOptions();

    return ['php_options' => $php_options_result];
}

function checkPhpRequirement()
{
    if(function_exists('json_decode')){
        $isJsonEnabled = true;
    }else{
        $isJsonEnabled = false;
    }

    if ($isJsonEnabled){
        $config = json_decode(file_get_contents('./config.json'));
        $requirement = new Requirement($config);
        $requirement_result = $requirement->check();

    }else{
        $requirement_result['requirements'] = [
            'php' => [
                'JSON' => false
            ]
        ];
        $requirement_result['errors'] = true;
    }
    return ['php_requirement' => $requirement_result];

}

function checkMysqlRequirement()
{
    $config = json_decode(file_get_contents('./config.json'));
    $mysql_requirement = new MysqlRequirement($config);
    $mysql_requirement_result = $mysql_requirement->isMySqlSupported();

    return ['mysql_requirement' => $mysql_requirement_result];
}

function isSupportedForInstallation()
{

    $checkPhpVersionResult = $_SESSION["check_php_version"];
    $phpRequirementResult = $_SESSION["php_requirement_result"];

    if (!($_SESSION["php_requirement_result"]["php_requirement"]["errors"])) {

        $phpOptionResult = $_SESSION["php_option_result"];
        $mysqlResult = $_SESSION["mysql_result"];
        $folderPermission = $_SESSION["folder_permission"];

        $result = array_merge($checkPhpVersionResult, $mysqlResult, $folderPermission, $phpRequirementResult, $phpOptionResult);

        $logic = !$checkPhpVersionResult['php_version']['errors'] &&
            !$mysqlResult['mysql_requirement']['errors'] &&
            !$folderPermission['permissions']['errors'] &&
            !$phpOptionResult['php_options']['errors'] &&
            !$phpRequirementResult['php_requirement']['errors'];
    } else {
        $result = array_merge($checkPhpVersionResult, $phpRequirementResult);;
        $logic = !$checkPhpVersionResult['php_version']['errors'] &&
            !$phpRequirementResult['php_requirement']['errors'];;
    }

    $_SESSION["result"] = $result;
    return $logic;
}
