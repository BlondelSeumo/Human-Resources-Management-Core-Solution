<template>
    <button @click="$emit('back')"
            type="button"
            class="btn btn-dark">
        {{ $t(label) }}
    </button>
</template>

<script>
export default {
    name: "BackButton",
    props: {
        label: {
            default: function () {
                return 'back';
            }
        }
    }
}
</script>

<style scoped>

</style>
