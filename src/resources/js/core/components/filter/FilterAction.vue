<template>
    <div class="dropdown-item">
        <div class="row filter-action-button-wrapper">
            <div class="col-12 d-flex justify-content-between">
                <button type="button" class="btn btn-clear pl-sm-0" :disabled="isClearDisabled" @click.prevent="clear($event)">
                    {{$t('clear')}}
                </button>
                <button type="submit" class="btn btn-primary" @click.prevent="apply">
                    {{$t('apply')}}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            isClearDisabled: {
                default: false
            },
            isApplyDisabled: {
                default: false
            }
        },
        name: "FilterAction",
        methods: {
            apply() {
                this.$emit('apply');
            },
            clear(event) {
                this.$emit('clear', event);
            }
        }
    }
</script>
