<template>
    <button :class="'btn btn-primary text-center '+btnClass"
            :type="type"
            @click.prevent="$emit('click')"
            :disabled="loading">
        <span class="w-100">
            <loader v-if="loading" :loader-color="'white-color'"/>
            <template v-if="!loading">
                {{ label }}
            </template>
        </span>
    </button>
</template>

<script>
    import Loader from "../preloders/Loader";

    export default {
        name: "SubmitButton",
        components: {
            'loader': Loader
        },
        props: {
            btnClass: {
                required: false
            },
            label: {
                required: false,
                default: function () {
                    return this.$t('save');
                }
            },
            loading: {
                required: false,
                default: function () {
                    return false;
                }
            },
            type: {
                required: false,
                default: function () {
                    return 'submit'
                }
            },
            icon: {
                required: false,
                default: function () {
                    return 'fa fa-spin fa-spinner';
                }
            }
        },

    }
</script>
