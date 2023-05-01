<template>
    <modal
        id="import-loading-modal"
        size="small"
        alignModal="center"
        hideHeader="false"
        v-model="showModal"
        :btn-label="$t('save')"
        :hide-footer="true"
        :preloader="preloader">

        <div v-if="response">
            <div class="text-center m-5">
                <app-icon
                    :name="error?'alert-triangle':'check-circle'"
                    :class="error?'text-danger':'text-success'"
                    class="size-40"/>
            </div>

            <p :class="error?'text-danger text-center':'text-success text-center'">{{ response }}</p>

            <div v-if="!error && totalAttendance != attendanceComplete">
                <p>Total Attendance: {{ totalAttendance }}</p>
                <app-pre-loader class="mb-2"/>
                <p>Complete Import: {{ attendanceComplete }}</p>
                <p>Please wait until attendance import is complete.</p>
            </div>

            <div v-else class="text-center">
                <app-button class="btn-primary" label="reload" @submit="modalClose" :is-disabled="false"/>
            </div>

        </div>

        <div v-else>
            <div class="m-5">
                <app-pre-loader/>
            </div>

            <div><h5 class="text-center">Please wait few minutes....</h5></div>
            <p class="text-danger text-center">Attention: Don't reload the page while import is processing</p>
        </div>

    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";

export default {
    name: "ImportLoadingModal",
    mixins: [ModalMixin],
    props: {
        error: {},
        response: {},
        totalAttendance: {},
        attendanceComplete: {}
    },
    created() {

    },
    mounted() {

    },
    methods: {
        modalClose(){
            location.reload();
        }
    }
}
</script>

<style scoped>

</style>