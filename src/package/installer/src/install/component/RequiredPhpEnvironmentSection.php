<?php
$php_requirement_result = checkPhpRequirement();
$_SESSION["php_requirement_result"] = $php_requirement_result;
?>

<div class="card card-with-shadow border-0 mb-primary">
    <div class="card-header bg-transparent d-flex align-items-center justify-content-between p-primary">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fab fa-codepen text-primary mr-2"></i>Php Environment
        </h5>
        <a class="text-muted hover-underline" target="_blank" href="<?php echo $config->link->php_environment ?>">Need help?</a>
    </div>

    <div class="card-body">
        <div class="row mt-2">
            <div class="col-4 text-size-18">Library</div>
            <div class="col-4 text-size-18">Status</div>
        </div>
        <hr/>
        <?php

        foreach ($php_requirement_result['php_requirement']['requirements']['php'] as $key => $value) {
            ?>
            <div class=" <?php echo $value ? 'text-success' : 'text-danger' ?> row mb-3">
                <div
                    class="col-4 text-size-16"><?php echo ucfirst($key) ?></div>
                <div class="col-4 text-size-26">
                    <i class="<?php echo $value ? 'text-success fas fa-check-circle' : 'text-danger far fa-times-circle' ?>"></i>
                </div>
            </div>

        <?php } ?>
    </div>
</div>

<?php if (array_key_exists('apache', $php_requirement_result['php_requirement']['requirements'])) { ?>
    <div class="card card-with-shadow border-0 mb-primary">
        <div class="card-header bg-transparent d-flex align-items-center p-primary">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-feather-alt text-primary mr-2"></i>Apache Environment</h5>
        </div>

        <div class="card-body">
            <div class="row mt-2">
                <div class="col-4 text-size-18">Library</div>
                <div class="col-4 text-size-18">Status</div>
            </div>
            <hr/>
            <?php

            foreach ($php_requirement_result['php_requirement']['requirements']['apache'] as $key => $value) {
                ?>
                <div class=" <?php echo $value ? 'text-success' : 'text-danger' ?> row mb-3">
                    <div
                        class="col-4 text-size-16"><?php echo ucfirst($key) ?></div>
                    <div class="col-4 text-size-26">
                        <i class="<?php echo $value ? 'text-success fas fa-check-circle' : 'text-danger far fa-times-circle' ?>"></i>
                    </div>
                </div>

            <?php } ?>
        </div>
    </div>
<?php } ?>
