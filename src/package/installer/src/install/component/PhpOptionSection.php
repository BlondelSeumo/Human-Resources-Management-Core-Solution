<?php
$php_option_result = checkPhpOptions();
$_SESSION["php_option_result"] = $php_option_result;
?>

<div class="card card-with-shadow border-0 mb-primary">
    <div class="card-header bg-transparent d-flex align-items-center justify-content-between p-primary">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-bars text-primary mr-2"></i>Php Options
        </h5>
        <a class="text-muted hover-underline" target="_blank" href="<?php echo $config->link->php_options ?>">Need help?</a>
    </div>
    <div class="card-body">
        <div class="row mt-2">
            <div class="col-4 text-size-18">Option Name</div>
            <div class="col-4 text-size-18">Status</div>
        </div>
        <hr/>

        <?php

        foreach ($php_option_result['php_options']['php_options'] as $key => $value) {
            ?>
            <div class=" <?php echo $value['isSet'] ? 'text-success' : 'text-danger' ?> row mb-3">
                <div
                    class="col-4 text-size-16"><?php echo $value['option'] ?></div>
                <div class="col-4 text-size-26">
                    <i class="<?php echo $value['isSet'] ? 'text-success fas fa-check-circle' : 'text-danger far fa-times-circle' ?>"></i>
                </div>
            </div>
        <?php } ?>
    </div>
</div>