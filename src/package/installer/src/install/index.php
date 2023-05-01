<?php
session_unset(); // New added
ob_start();
include_once './component/layout/head.php';
include_once "./InstallerService/GetResult.php";
$config = json_decode(file_get_contents("config.json"));

?>
<div id="app">
    <div class="container">
        <h2 class="text-center text-capitalize mb-0 p-primary">
            Required Environment
        </h2>

        <!--Need to check php7.4 before any other check-->
        <?php $is_supported = false;
        include_once './component/PhpVersionSection.php';
        ?>

        <?php
        if ($_SESSION["check_php_version"]["php_version"]["php_version"]["is_supported"]) {

            include_once './component/RequiredPhpEnvironmentSection.php';
            if (!$_SESSION["php_requirement_result"]["php_requirement"]["errors"]) {
                include_once './component/PhpOptionSection.php';
                include_once './component/MysqlRequirementSection.php';
                include_once './component/FolderPermissionSection.php';
            }

            $is_supported = isSupportedForInstallation();
            if ($is_supported) {
                $sub_folder = str_replace('index.php', '', $_SERVER['PHP_SELF']);
                $sub_folder = str_replace('install', '', $sub_folder);
                $base_url = rtrim($sub_folder, '/');
                $base_url = $base_url ? $base_url . '/' : '/';
                $_SESSION["base_url"] = $base_url;
            }
        }
        ?>

        <?php if ($is_supported) { ?>
            <button
                id="next-btn"
                type="button"
                class="btn btn-primary btn-block text-center mb-primary text-size-20 py-2">
                Next
            </button>
        <?php } else { ?>
            <button
                id="retry-btn"
                type="button"
                class="btn btn-primary btn-block text-center mb-primary text-size-20 py-2">
                Retry
            </button>
        <?php } ?>
    </div>
</div>
<?php include_once 'component/layout/footer.php'; ?>
