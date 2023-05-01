<script src="<?php echo baseUrl()?>js/vendor.js"></script>
<script type="text/javascript">
    let retryBtn = document.getElementById('retry-btn');
    if (retryBtn){
        retryBtn.addEventListener('click', () => {
            retryBtn.innerHTML = `<div class="spinner-border text-white" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>`;
            location.reload();
        })
    }

</script>
<script type="text/javascript">
    let php_var = "<?php echo  $_SESSION["base_url"] . 'setup/additional-requirement'; ?>";
    let nextBtn = document.getElementById('next-btn');
    if(nextBtn){
        nextBtn.addEventListener('click', () => {
            nextBtn.innerHTML = `<div class="spinner-border text-white" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>`;
            location.href = php_var
        })
    }
</script>
</body>
</html>

