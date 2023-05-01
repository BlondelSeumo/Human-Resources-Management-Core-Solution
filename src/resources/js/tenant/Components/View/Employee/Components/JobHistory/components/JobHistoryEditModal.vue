<template>
    <modal
        id="job-history-edit-modal"
        :scrollable="false"
        v-model="showModal"
        :title="formValue ? $editLabel(modalType) : $addLabel(modalType)"
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

            <app-form-group-selectable
                v-if="modalType === 'department'"
                type="select"
                :label="$t('department')"
                list-value-field="name"
                v-model="formData.department_id"
                :chooseLabel="$t('department')"
                :error-message="$errorMessage(errors, 'department_id')"
                :fetch-url="apiUrl.SELECTABLE_DEPARTMENT"/>

            <app-form-group-selectable
                v-if="modalType === 'designation'"
                type="select"
                :label="$t('designation')"
                list-value-field="name"
                v-model="formData.designation_id"
                :chooseLabel="$t('designation')"
                :error-message="$errorMessage(errors, 'designation_id')"
                :fetch-url="apiUrl.SELECTABLE_DESIGNATION"/>

            <app-form-group-selectable
                v-if="modalType === 'employment_status'"
                type="select"
                :label="$t('employment_status')"
                list-value-field="name"
                v-model="formData.employment_status_id"
                :chooseLabel="$t('employment_status')"
                :error-message="$errorMessage(errors, 'employment_status_id')"
                :fetch-url="apiUrl.SELECTABLE_EMPLOYMENT_STATUS"/>

            <app-form-group-selectable
                v-if="modalType === 'work_shift'"
                type="select"
                :label="$t('work_shift')"
                list-value-field="name"
                v-model="formData.work_shift_id"
                :chooseLabel="$t('work_shift')"
                :error-message="$errorMessage(errors, 'work_shift_id')"
                :fetch-url="`${apiUrl.SELECTABLE_WORKING_SHIFT}`"/>

            <app-form-group-selectable
                v-if="modalType === 'role'"
                type="multi-select"
                :label="$t('role')"
                list-value-field="name"
                v-model="formData.roles"
                :chooseLabel="$t('role')"
                :error-message="$errorMessage(errors, 'roles')"
                :fetch-url="apiUrl.SELECTABLE_ROLE"/>

            <app-note v-if="hasDeptManagerRole && modalType === 'role'"
                      :title="$t('note')"
                      :notes="[$t('department_manager_role_note', {
                      edit: `<a href='${urlGenerator(TENANT_DEPARTMENTS_URL)}'>${$t('edit')}</a>`
                      })]"
                      content-type="html"
            />

            <app-form-group
                v-if="modalType === 'employment_status'"
                type="textarea"
                :label="$t('note')"
                v-model="formData.description"
                :placeholder="$textAreaPlaceHolder('note')"
                :error-message="$errorMessage(errors, 'description')"
            />

            <app-form-group
                v-if="modalType === 'salary'"
                type="number"
                :label="$t('amount')"
                :placeholder="$placeholder('amount')"
                v-model="formData.amount"
                :required="true"
                :error-message="$errorMessage(errors, 'amount')"
            />

            <app-form-group
                v-if="modalType === 'salary'"
                type="date"
                :label="$t('effective_date')"
                :placeholder="$placeholder('effective_date')"
                v-model="formData.start_at"
                :required="true"
                :error-message="$errorMessage(errors, 'start_at')"
            />

        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../../../common/Mixin/Global/ModalMixin";
import {axiosPatch} from "../../../../../../../common/Helper/AxiosHelper";
import {formatDateForServer} from "../../../../../../../common/Helper/Support/DateTimeHelper";
const {TENANT_DEPARTMENTS_URL} = require('../../../../../../../common/Config/apiUrl');

export default {
    name: "JobHistoryEditModal",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        modalType: {},
        employee: {},
    },
    data() {
        return {
            formData: {},
            TENANT_DEPARTMENTS_URL
        }
    },
    created() {
        this.formData.roles = this.collection(this.employee.roles).pluck()
        this.formData.designation_id = this.collectPresentValues(this.employee.designations).id;
        this.formData.department_id = this.collectPresentValues(this.employee.departments).id;
        this.formData.employment_status_id = this.collectPresentValues(this.employee.employment_statuses).id;
        this.formData.work_shift_id = this.collectPresentValues(this.employee.working_shifts).id;
        this.formData.joining_date = this.employee?.profile?.joining_date
        this.formData.description = this.collectPresentValues(this.employee.employment_statuses)?.pivot?.description;
        this.formData.amount = this.employee.updated_salary ? this.employee.updated_salary.amount : null;
        this.formData.start_at = this.employee.updated_salary ? new Date(this.employee.updated_salary.start_at) : null;
    },
    methods: {
        submit() {
            this.loading = true;

            let actionType = {
                'department': 'department',
                'designation': 'designation',
                'work_shift': 'workshift',
                'employment_status': 'employment-status',
                'joining_date': 'joining-date',
                'role': 'roles',
                'salary': 'salary'
            }[this.modalType]

            if (actionType == 'joining-date') {
                this.formData.joining_date = formatDateForServer(this.formData.joining_date);
            }

            if (actionType == 'salary') {
                this.formData.start_at = formatDateForServer(this.formData.start_at);
            }
            axiosPatch(
                `${this.apiUrl.EMPLOYEES}/${this.employee.id}/${actionType}/update`,
                this.formData
            ).then(({data}) => {
                this.loading = false;
                $('#job-history-edit-modal').modal('hide');
                this.$toastr.s('', data.message);
                this.showModal = false;
                this.$emit('reload')
            }).catch(({response}) => {
                this.loading = false;
                this.errors = response.data.errors
                this.$toastr.e('', response.data.message);
            })
        },
        collectPresentValues(data) {
            if (!data) {
                return '';
            }
            return this.collection(data.filter(data => data.pivot.end_date == null ||
                data.pivot.end_date === '')).first()
        }
    },
    computed: {
        formValue() {
            let formKey = {
                'department': 'department_id',
                'designation': 'designation_id',
                'work_shift': 'work_shift_id',
                'employment_status': 'employment_status_id',
                'joining_date': 'joining_date',
                'role': 'roles',
                'salary': 'amount'
            }[this.modalType];
            return this.formData[formKey];
        },
        hasDeptManagerRole() {
            return this.employee.roles.find((role) => role.alias === 'department_manager')
        }
    }
}
</script>

<style scoped>

</style>