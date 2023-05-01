<template>
    <modal
        id="job-history-edit-modal"
        :scrollable="false"
        v-model="showModal"
        :title="modalTitle"
        :btnLabel="buttonLabel"
        :submitBtnClass="modalType === 'terminate' || modalType === 'remove-employee' ? 'btn-danger' : 'btn-primary'"
        @submit="submit"

        :loading="loading"
        :preloader="preloader">

        <form
            ref="form"
            @submit.prevent="submitData">

            <app-form-group
                v-if="modalType === 'joining_date'"
                type="date"
                :label="$t('joining_date')"
                :placeholder="$placeholder('joining_date')"
                v-model="formData.joining_date"
                :required="true"
                :error-message="$errorMessage(errors, 'joining_date')"
            />

            <app-input
                v-if="modalType === 'terminate'"
                type="textarea"
                v-model="formData.description"
                :text-area-rows="4"
                :error-message="$errorMessage(errors, 'description')"
                :placeholder="$t('enter_employee_termination_reason')"
            />

            <app-note v-if="modalType === 'remove-employee'"
                      class="mb-4"
                      :title="$t('note')"
                      :notes="$t('all_selected_employee_will_be_removed_from_employee_list')"
            />
            <template v-if="modalType === 'rejoining'">
                <app-form-group-selectable
                    formGroupClass="mb-0"
                    type="select"
                    :label="$t('employment_status')"
                    list-value-field="name"
                    v-model="formData.employment_status_id"
                    :chooseLabel="$t('employment_status')"
                    :error-message="$errorMessage(errors, 'employment_status_id')"
                    :fetch-url="`${apiUrl.SELECTABLE_EMPLOYMENT_STATUS}?excluded=terminated`"
                />
                <app-input
                    type="textarea"
                    class="mt-primary"
                    v-model="formData.description"
                    :text-area-rows="4"
                    :error-message="$errorMessage(errors, 'description')"
                    :placeholder="$t('enter_employee_rejoining_reason')"
                />
            </template>
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../../common/Mixin/Global/ModalMixin";
import {formatDateForServer} from "../../../../../../common/Helper/Support/DateTimeHelper";
import {axiosPost} from "../../../../../../common/Helper/AxiosHelper";

export default {
    name: "EmployeeContextActionModal",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        modalType: {},
        employees: {},
    },
    data() {
        return {}
    },
    methods: {
        submit() {
            this.loading = true;
            let actionType = {
                'joining_date': 'joining-date',
                'terminate': 'terminate',
                'remove-employee': 'remove-employee',
                'rejoining': 'rejoining',
            }[this.modalType]
            this.formData.users = this.employees.map(employee => employee.id)
            if (actionType === 'joining-date') {
                this.formData.joining_date = formatDateForServer(this.formData.joining_date);
                this.updateEmployees(actionType)
            }else if (actionType === 'terminate') {
                this.updateEmployees(actionType)
            } else if (actionType === 'remove-employee') {
                this.updateEmployees(actionType)
            } else if (actionType === 'rejoining') {
                this.updateEmployees(actionType)
            }
        },

        updateEmployees(actionType) {
            axiosPost(
                `${this.apiUrl.EMPLOYEES}/${actionType}/update`,
                this.formData
            ).then(({data}) => {
                this.loading = false;
                $('#job-history-edit-modal').modal('hide');
                this.$toastr.s('', data.message);
                this.showModal = false;
                this.$hub.$emit(`reload-employee-table`)
            }).catch(({response}) => {
                this.loading = false;
                this.$toastr.e('', response.data.message);
            })
        }
    },
    computed: {
        buttonLabel() {
            if (this.modalType === 'terminate') {
                return this.$t('terminate');
            } else if (this.modalType === 'remove-employee') {
                return this.$t('remove');
            } else if (this.modalType === 'rejoining') {
                return this.$t('rejoin');
            }
            return 'save';
        },
        modalTitle() {
            if (this.modalType === 'terminate') {
                return this.$t('confirm_employee_terminate')
            } else if (this.modalType === 'remove-employee') {
                return this.$t('confirm_employee_remove');
            } else if (this.modalType === 'rejoining') {
                return this.$t('rejoin_employee');
            }
            return this.$addLabel(this.modalType)
        }
    }
}
</script>