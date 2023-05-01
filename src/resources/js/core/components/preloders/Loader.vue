<template>
    <span class="spinner-bounce" :class="loaderColor">
        <span class="bounce1 bg-brand-color mr-1"/>
        <span class="bounce2 bg-brand-color mr-1"/>
        <span class="bounce3 bg-brand-color mr-1"/>
    </span>
</template>

<script>
    export default {
        name: "Loader",
        props: {
            loaderColor: {
                type: String,
                default: 'brand-color'
            }
        }
    }
</script>
