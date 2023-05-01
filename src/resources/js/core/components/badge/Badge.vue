<template>
    <span class="badge" :class="className">{{ label }}</span>
</template>

<script>
    export default {
        name: "AppBadge",
        props: {
            className: {
                type: String,
                require: true
            },
            label: {
                type: String,
                require: true
            }
        }
    }
</script>
