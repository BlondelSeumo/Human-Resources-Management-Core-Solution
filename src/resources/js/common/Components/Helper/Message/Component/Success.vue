<template>
    <div v-if="message">
        <span class="text-success">{{ message }}</span>
    </div>
</template>

<script>
export default {
    name: "Success",
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
