<template>
    <modal id="leave-period-modal"
           v-model="showModal"
           :title="generateModalTitle('leave_type')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader">
        <form ref="form" :data-url='this.selectedUrl ? this.selectedUrl : apiUrl.LEAVE_PERIODS'>
            <app-form-group
                :label="$t('start_date')"
                type="date"
                v-model="formData.start_date"
                :placeholder="$placeholder('start_date', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'start_date')"
            />

            <app-form-group
                :label="$t('end_date')"
                type="date"
                v-model="formData.end_date"
                :placeholder="$placeholder('end_date', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'end_date')"
            />
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";

export default {
    name: "LeavePeriodCreateEditModal",
    mixins: [FormHelperMixins, ModalMixin],
    methods: {
        afterSuccess({data}) {
            this.formData = {};
            $('#leave-period-modal').modal('hide');
            this.$emit('input', false);
            this.toastAndReload(data.message, 'leave-period-table')
        },
        afterSuccessFromGetEditData({data}) {
            this.formData.start_date = new Date(data.start_date);
            this.formData.end_date = new Date(data.end_date);
            this.preloader = false;
        }
    }
}
</script>

<style scoped>

</style>
