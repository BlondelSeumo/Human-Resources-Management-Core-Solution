<?php
$mysql_result = checkMysqlRequirement();
$_SESSION["mysql_result"] = $mysql_result;
?>

<div class="card card-with-shadow border-0 mb-primary">
    <div class="card-header bg-transparent d-flex align-items-center justify-content-between p-primary">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fab fa-php text-primary mr-2"></i>MySql Version
        </h5>
        <a class="text-muted hover-underline" target="_blank" href="<?php echo $config->link->mysql_version ?>">Need help?</a>
    </div>
    <div class="card-body">
        <div class="row mt-2">
            <div class="col-4 text-size-18">Minimum Required</div>
            <div class="col-4 text-size-18">Your Version</div>
            <div class="col-4 text-size-18">Status</div>
        </div>
        <hr/>

        <div class="row mb-3">

            <div
                class="<?php echo $mysql_result['mysql_requirement']['mysql_version']['is_supported'] ? 'text-success' : 'text-danger' ?> col-4">
                    <span
                        class="text-size-16"><?php echo $mysql_result['mysql_requirement']['mysql_version']['minimum'] ?></span>
            </div>

            <div
                class="<?php echo $mysql_result['mysql_requirement']['mysql_version']['is_supported'] ? 'text-success' : 'text-danger' ?>  col-4 text-size-16"><?php echo $mysql_result['mysql_requirement']['mysql_version']['current'] ?></div>
            <div class="col-4 text-size-26">
                <i class="<?php echo $mysql_result['mysql_requirement']['mysql_version']['is_supported'] ? 'text-success fas fa-check-circle' : 'text-danger far fa-times-circle' ?>"></i>
            </div>
        </div>
    </div>
</div>