<template>
    <div>
        <div class="bulk-floating-action-wrapper">
            <div class="actions">
                <div class="p-2">
                    <button class="btn btn-success" @click="openConfirmationModal('approve')">
                        {{ $t('approve') }}
                    </button>
                    <button class="btn btn-danger" @click="openConfirmationModal('reject')">
                        {{ $t('reject') }}
                    </button>
                </div>
            </div>
        </div>

        <app-confirmation-modal
            v-if="confirmationModalActive"
            :loading="loading"
            :message="promptMessage"
            :title="promptTitle"
            :firstButtonName="$t('yes')"
            :modal-class="modalClass"
            :icon="modalIcon"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed"
            @cancelled="cancelled"
            :self-close="false"
        />
    </div>
</template>

<script>
import {axiosPost} from "../../../../../../common/Helper/AxiosHelper";

export default {
    name: "AttendanceRequestContextMenu",
    props: {
        requests: {},
        allSelected: {},
    },
    data() {
        return {
            confirmationModalActive: false,
            loading: false,
            status: '',
            promptTitle: '',
            promptMessage: '',
            modalIcon: '',
            modalClass: '',
        }
    },
    methods: {
        openConfirmationModal(status) {
            this.confirmationModalActive = true;
            this.status = status;
            if (status == 'approve') {
                this.promptTitle = this.$t('approve_all_attendance_request');
                this.promptMessage = this.$t('all_attendance_request_approve_message');
                this.modalIcon = 'check-circle';
                this.modalClass = 'success';
            } else {
                this.promptTitle = this.$t('reject_all_attendance_request');
                this.promptMessage = this.$t('all_attendance_request_reject_message');
                this.modalIcon = '';
                this.modalClass = 'danger';
            }
        },
        cancelled() {
            this.confirmationModalActive = false;
            this.status = '';
            this.promptTitle = '';
            this.promptMessage = '';
            this.modalIcon = '';
            this.modalClass = '';
        },
        confirmed() {
            this.loading = true;
            let formData = {
                status: this.status,
                requests: this.requests.map(r => r.id),
                all_selected: this.allSelected
            }
            axiosPost(`${this.apiUrl.ATTENDANCE_REQUESTS}/update`, formData).then(({data}) => {
                this.loading = false;
                this.$toastr.s('', data.message);
                this.confirmationModalActive = false;
                $("#app-confirmation-modal").modal('hide');
                this.$emit(`reload`)
            }).catch(({response}) => {
                this.loading = false;
                this.$toastr.e('', response.data.message);
            })
            console.log(formData)
        },

    }
}
</script>

<style scoped>

</style>