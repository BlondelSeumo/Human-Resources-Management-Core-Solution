<template>
    <div class="d-flex align-items-center justify-content-center">
        <div class="spinner-border" :class="spinnerClass" role="status">
            <span class="sr-only"></span>
        </div>
    </div>
</template>

<script>
    export default {
        name: "AppPreLoader",
        props: {
            spinnerClass: {
                type: String,
                default: ''
            }
        }
    }
</script>
