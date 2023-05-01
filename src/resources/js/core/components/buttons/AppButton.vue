<template>
    <button type="button" class="btn" @click.prevent="submit" :class="className" :disabled="isDisabled">{{ label }}
    </button>
</template>

<script>
    export default {
        name: "AppButton",
        props: {
            label: {
                required: true,
                type: String,
            },
            className: {
                type: String
            },
            isDisabled: {
                required: true,
                type: Boolean,
                default: false
            }
        },
        methods: {

            submit() {
                this.$emit('submit');
            },
        }
    }
</script>
