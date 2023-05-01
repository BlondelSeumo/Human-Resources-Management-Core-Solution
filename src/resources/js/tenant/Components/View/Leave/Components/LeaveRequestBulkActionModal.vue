<template>
    <modal
        id="leave-request-action-modal"
        :scrollable="false"
        v-model="showModal"
        :title="modalTitle"
        :btnLabel="buttonLabel"
        :submitBtnClass="modalType === 'rejected' ? 'btn-danger' : 'btn-success'"
        @submit="submit"
        :loading="loading"
    >
        <form
            ref="form"
            @submit.prevent="submitData">

            <app-form-group
                type="textarea"
                :label="$t('response_note')"
                :placeholder="$placeholder('note')"
                v-model="formData.note"
                :required="true"
                :error-message="$errorMessage(errors, 'note')"
            />
        </form>

        <app-note
            class="mb-4"
            :title="$t('note')"
            :notes="$t('leave_bulk_action_message')"
        />

    </modal>
</template>

<script>


import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosPost} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "LeaveRequestBulkActionModal",
    mixins: [ModalMixin, FormHelperMixins],
    props: {
        modalType: {},
        requests: {},
        allSelected: {}
    },
    methods: {
        submit() {
            this.loading = true;
            this.formData.status = this.modalType;
            this.formData.requests = this.requests.map(r => r.id);
            this.formData.all_selected = this.allSelected;

            axiosPost(`${this.apiUrl.LEAVE_REQUESTS}/update`, this.formData).then(({data}) => {
                this.loading = false;
                this.$toastr.s('', data.message);
                this.showModal = false;
                $('#leave-request-action-modal').modal('hide');
                this.$emit(`leaveRequestUpdated`)
            }).catch(({response}) => {
                this.afterError(response);
            })
        },
    },
    computed: {
        buttonLabel() {
            if (this.modalType === 'approved') {
                return this.$t('approve');
            }
            return this.$t('rejected');
        },
        modalTitle() {
            if (this.modalType === 'approved') {
                return this.$t('approve_all_leave_request')
            }
            return this.$t('reject_all_leave_request');
        }
    }

}
</script>
