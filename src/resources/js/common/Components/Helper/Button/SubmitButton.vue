<template>
    <button :class="'btn text-center '+btnClass"
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
import Loader from "../../../../core/components/preloders/Loader";

export default {
    name: "SubmitButton",
    components: {
        'loader': Loader
    },
    props: {
        btnClass: {
            required: false,
            default: 'btn-primary'
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

<style scoped>

</style>
