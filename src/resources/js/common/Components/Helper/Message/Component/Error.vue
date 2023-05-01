<template>
    <div class="p-0">
        <small class="text-danger">{{ message }}</small>
    </div>
</template>

<script>
export default {
    name: "Error",
    props: {
        message: {
            required: true,
            type: String
        }
    }
}
</script>

<style scoped>

</style>
