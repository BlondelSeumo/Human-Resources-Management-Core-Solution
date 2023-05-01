<template>
    <div v-if="value && value[identifier]"
         class="d-flex align-items-center">
        <div class="mr-2">
            <app-icon
                :name="icon"
                class="primary-text-color"
                width="16"
                height="16"
            />
        </div>
        <div>
            <p class="mb-0">{{ value[identifier] }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "ContactDetails",
    props: {
        value: {},
        identifier: {
            type: String
        },
        icon: {}
    }
}
</script>