<template>
    <div class="d-flex align-items-center">
        <app-pre-loader v-if="preloader"/>
        <button v-else
                class="btn"
                :class="!punch ? 'btn-success' : 'btn-warning'"
                @click.prevent="$emit('open-modal', punch)">

            {{ !punch ? $t('punch_in') : $t('punch_out') }}
        </button>
    </div>
</template>

<script>
    import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
    import {CHECK_PUNCH_IN} from "../../../../Config/ApiUrl";

    export default {
        name: "AppPunchInOut",
        data() {
            return {
                preloader: false,
                punch: false
            }
        },
        mounted() {
            this.checkPunchIn();

            this.$hub.$on('reload-punch-in-out-button', () => this.checkPunchIn())
        },
        methods: {
            checkPunchIn() {
                this.preloader = true;
                axiosGet(CHECK_PUNCH_IN).then(({data}) => {
                    this.punch = !!(data);
                }).finally(() => {
                    this.preloader = false;
                })
            }
        }
    }
</script>