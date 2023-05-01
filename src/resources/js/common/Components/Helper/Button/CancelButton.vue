<template>
    <button @click="$emit('click')"
            type="button"
            :class="`btn btn-dark `+btnClass">
        {{ $t(label) }}
    </button>
</template>

<script>
export default {
    name: "CancelButton",
    props: {
        label: {
            default: function () {
                return 'cancel';
            }
        },
        btnClass: {}
    }
}
</script>

<style scoped>

</style>
