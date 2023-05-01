<template>
    <p class="mb-0" v-if="value && value[identifier]">
        {{ value[identifier] }}
    </p>
</template>

<script>
export default {
    name: "AddressValue",
    props: {
        value: {},
        identifier: {
            type: String
        }
    }
}
</script>