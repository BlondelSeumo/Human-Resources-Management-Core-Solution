<?php

/*
 * ATTENTION
 * Before you executing this request, please keep in mind the following:
 * 1. There must be folder named "original" inside the public_html
 * 2. All files/folders will be removed except the "original" once you execute this
 * 3. All files/folders will be copied to public_html from original folder
 * 4. All files/folders will be re-structured to work with cPanel
 * 5. Finally the https://www.yourdomain.com/install-demo-data url will be called to install demo data.
 * */


//Check if valid request or not.
//Set the key DEPLOYMENT_KEY .env in deployHQ.
//Set the urls in according to DEPLOYMENT_KEY in deployHQ


function response($httpStatusCode, $httpStatusMsg)
{
    echo $httpStatusMsg;
    $phpSapiName = substr(php_sapi_name(), 0, 3);
    if ($phpSapiName == 'cgi' || $phpSapiName == 'fpm') {
        header('Status: ' . $httpStatusCode . ' ' . $httpStatusMsg);
    } else {
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
        header($protocol . ' ' . $httpStatusCode . ' ' . $httpStatusMsg);
    }
    exit();
}

// check key has been set and its alphanumeric
if (!isset($_GET["key"]) || !ctype_alnum($_GET["key"])) {
    response(403, "Not provided any proper key or key does not exist!");
}

// sanitize key
$key = htmlspecialchars(strip_tags(trim($_GET["key"])));

// validate key
if (!check_valid_env("DEPLOYMENT_KEY", $key)) {
    response(401, "Not authorized!");
}

if (isset($_GET["action"])) {
    $action = htmlspecialchars(trim($_GET["action"]));

    if ($action === 'deploying') {
        setDeployingMode($action);
        response(200, "Action:deploying...");
    }
    if ($action === 'unzip') {
        unzip('src');
        response(200, "Action:unzip done.");
    }
    if ($action === 'deployed') {
        change_path_in_index();
        installDemoData();
        response(200, "Action:deployed is done.");
    }
    if ($action === 'seed_demo_data') {
        installDemoData();
        response(200, "Action:Demo data seeded successfully.");
    }
    if ($action === 'prepare_release') {
        prepareMarketplaceVersion();
        response(200, "Action:Prepare marketplace_version folder for release is done");
    }
}

//So now, the we've verified the request. Go ahead!

set_time_limit(500); //set execution time = 5 minutes. Since it could take time.


//In cPanel, we have to re-structure some folder and files.


function getPublicFolder()
{
    return __DIR__ . '/public';
}

function getRootFolder()
{
    return dirname(__FILE__, 2);
}

function getFilesAndFolderNeedToSkip()
{
    return [];
}

function get_original_folder_name()
{
    return basename(__DIR__);
}

function prepareMarketplaceVersion()
{
    if (check_valid_env("PREPARE_RELEASE_FILES", "true")) {
        copy_root_release();
    }
}

function installDemoData()
{
    if (check_valid_env("INSTALL_DEMO_DATA", "true")) {
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $actual_link = str_replace(get_original_folder_name() . "/", "", $actual_link);
        echo file_get_contents($actual_link . "/install-demo-data") == "1" ? "Demo data seeded successfully \r\n" : exit("Error while seeding demo data \r\n");
    }
}


function custom_copy($src, $dst)
{
    if (is_dir($src)) {
        $dir = opendir($src);

        // Make the destination directory if not exist
        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }

        // Loop through the files in source directory
        foreach (scandir($src) as $file) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    // Recursively calling custom copy function for sub directory
                    custom_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);

    } else if ($src) {
        copy($src, $dst);  //copy file
    }
}


function change_path_in_index()
{
    $index = file_get_contents(get_absolute_path("/src/public/index.php"));
    $index = str_replace("/../vendor/autoload.php", "/src/vendor/autoload.php", $index);

    $index = str_replace("/../bootstrap/app.php", "/src/bootstrap/app.php", $index);

    file_put_contents(get_absolute_path("/src/public/index.php"), $index);

    moveEveryThingFromOneDirToAnotherDir(getPublicFolder(), getRootFolder(), ['mix-manifest.json']);
}

function setDeployingMode($action)
{
    // root clean up
    $dirNeedToBeClean = getRootFolder();
    $allowedFiles = [".htaccess", "index.php"];
    $allowedDirs = ['src', 'images'];
    $allowedLinks = [];
    cleanDir($dirNeedToBeClean, $allowedDirs, $allowedFiles, $allowedLinks);
    // Done root clean up

    // set message of maintanance
    $maintenanceFilePath = get_absolute_path("/src/resources/views/custom_errors/maintenance.blade.php");
    if (!file_exists($maintenanceFilePath)) {
        response(404, "Not found $maintenanceFilePath");
    }

    $deployinMessage = file_get_contents($maintenanceFilePath);
    $rootIndexFilePath = get_absolute_path("index.php");

    if (!file_exists($rootIndexFilePath)) {
        response(404, "Not found $rootIndexFilePath");
    }
    file_put_contents($rootIndexFilePath, $deployinMessage);
    // Done message setting
}

function unzip($zipFileName)
{
    $zipFile = $zipFileName . ".zip";
    $dir = getRootFolder();
    $allowedFiles = [".htaccess", $zipFile, "index.php"];
    $allowedDirs = [];
    $allowedLinks = [];
    cleanDir($dir, $allowedDirs, $allowedFiles, $allowedLinks);

    $zip = new ZipArchive;
    if ($zip->open(get_absolute_path($zipFile)) === TRUE) {
        $zip->extractTo(get_absolute_path('src'));
        $zip->close();
        echo "$zipFile extracted  \r\n";
        if (unlink(get_absolute_path($zipFile))) {
            echo "$zipFile deleted  \r\n";
        }
    } else {
        unlink(get_absolute_path($zipFile));
        echo 'failed!';
    }
}

function cleanDir($dirNeedToBeClean, $allowedDirs, $allowedFiles, $allowedLinks)
{
    $listOfDirs = scandir($dirNeedToBeClean);

    foreach ($listOfDirs as $dir) {
        if ($dir !== '.' && $dir !== '..') {
            $absolutePath = get_absolute_path($dir);
            if (is_dir($absolutePath) && !in_array($dir, $allowedDirs) && !is_link($absolutePath)) {
                $rdir = new RecursiveDirectoryIterator($absolutePath, RecursiveDirectoryIterator::SKIP_DOTS);
                $ri = new RecursiveIteratorIterator($rdir, RecursiveIteratorIterator::CHILD_FIRST);

                foreach ($ri as $file) {

                    if ($file->isLink() || $file->isFile()) {
                        unlink($file);
                    } else {
                        rmdir($file->getRealPath());
                    }
                }
                rmdir($absolutePath);
            } else {
                if (is_file(get_absolute_path($dir)) && !in_array($dir, $allowedFiles)) {

                    unlink(get_absolute_path($dir));
                } else if (is_link(get_absolute_path($dir)) && !in_array($dir, $allowedLinks)) {
                    unlink(get_absolute_path($dir));
                }
            }
        }
    }
}

function moveEveryThingFromOneDirToAnotherDir($from, $to, $skipList = [])
{
    $files = scandir($from);
    foreach ($files as $fname) {
        if ($fname != '.' && $fname != '..' && !in_array($fname, $skipList, true)) {
            rename($from . DIRECTORY_SEPARATOR . $fname, $to . DIRECTORY_SEPARATOR . $fname);
        }
    }
}

function check_valid_env($key, $value)
{

    $env_content = file_get_contents(".env");

    if (!$env_content) {
        $env_content = file_get_contents("/.env");
    }

    return strpos($env_content, "$key=$value") || strpos($env_content, "$key=$value");
}

function get_absolute_path($file_path = "", $show_original_folder = false)
{
    $root = dirname(__FILE__);

    if (!$show_original_folder) {
        $root = str_replace("/" . get_original_folder_name(), "", $root);
    }

    if ($file_path) return $root . "/" . $file_path;
    else return $root;
}

function copy_root_release()
{
    $release_folder = "marketplace_version";
    $doc_folder = "documentation";
    $dont_copy = array($release_folder,
        $doc_folder,
        'deploy.php',
        '.env',
        "template",
        "error_log",
        ".DS_Store",
        "deploy.php",
        ".github",
        ".gitattributes",
        ".gitignore",
        "FETCH_HEAD"
    );

    $src = get_absolute_path("", true);
    $src = str_replace(get_original_folder_name(), "", $src);

    $dst = $src . "/" . $release_folder . "/upload";

    mkdir("$dst", 0755, true);

    // Loop through the files in source directory
    foreach (scandir($src) as $file) {

        if (array_search($file, $dont_copy) === false && ($file != '.') && ($file != '..')) {

            if (is_dir($src . '/' . $file)) {
                // Recursively calling custom copy function for sub directory
                custom_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        } else if ($file == $doc_folder && is_dir($src . '/' . $file)) {
            custom_copy($src . '/' . $file, $src . "/" . $release_folder . '/' . $file);
        }
    }
}
