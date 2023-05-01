<template>
    <button :class="['btn ', preloader ? 'disabled' : '', className]" :type="type" @click.prevent="submit">
        <span v-if="preloader" class="spinner-bounce">
            <span class="bounce1 mr-1"/>
            <span class="bounce2 mr-1"/>
            <span class="bounce3 mr-1"/>
        </span>
        <span v-else>
            {{ btnLabel }}
        </span>
    </button>
</template>

<script>
    export default {
        name: "AppLoadMore",
        props: {
            label: {
                type: String,
                default: ''
            },
            preloader: {
                type: Boolean,
                default: false
            },
            className: {
                type: String,
                default: 'btn-load-more-data'
            },
            type: {
                type: String,
                default: 'button'
            }
        },
        computed: {
            btnLabel() {
                return this.label === '' ? this.$t('load_more') : this.label;
            }
        },
        methods: {
            submit() {
                this.$emit('submit');
            }
        }
    }
</script>
