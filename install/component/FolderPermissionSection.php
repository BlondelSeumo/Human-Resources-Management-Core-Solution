<?php
$folder_permission = checkFolderPermission();
$_SESSION["folder_permission"] = $folder_permission;
?>

<div class="card card-with-shadow border-0 mb-primary">
    <div class="card-header bg-transparent d-flex align-items-center justify-content-between p-primary">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="far fa-folder-open text-primary mr-2"></i>
            Folder Permissions</h5>
        <i data-feather="check-circle"></i>
        <a class="text-muted hover-underline" target="_blank" href="<?php echo $config->link->folder_permissions ?>">Need help?</a>
    </div>
    <div class="card-body">
        <div class="row mt-2">
            <div class="col-4 text-size-18">Folder</div>
            <div class="col-4 text-size-18">Minimum Required Permission</div>
            <div class="col-4 text-size-18">Status</div>
        </div>
        <hr/>
        <?php
        foreach ($folder_permission['permissions']['permissions'] as $permission) {
            ?>
            <div class="row mb-3">
                <div
                    class="<?php echo $permission['isSet'] ? 'text-success' : 'text-danger' ?> col-4 text-size-16"><?php echo $permission['folder'] ?></div>
                <div
                    class="<?php echo $permission['isSet'] ? 'text-success' : 'text-danger' ?>  col-4 text-size-16"><?php echo $permission['permission'] ?></div>
                <div class="col-4 text-size-26">
                    <i class="<?php echo $permission['isSet'] ? 'text-success fas fa-check-circle' : 'text-danger far fa-times-circle' ?>"></i>
                </div>
            </div>
        <?php } ?>

    </div>
</div>

