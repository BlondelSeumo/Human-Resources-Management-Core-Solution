<template>
    <modal
        id="employee-list-modal"
        size="large"
        v-model="showModal"
        :title="from === 'user' ? generateModalTitle('user') : generateModalTitle('Employee')"
        @submit="submit"
        :loading="loading"
        :btn-label="selectedUrl ? $t('save') : $t('invite')"
        :preloader="preloader">

        <form
            v-if="isMailSettingExist || selectedUrl"
            ref="form"
            :data-url='selectedUrl ? selectedUrl : EMPLOYEE_INVITE'
            @submit.prevent="submitData"
        >
            <app-form-group
                :label="$t('email')"
                :placeholder="$placeholder('email')"
                v-model="formData.email"
                :required="true"
                :error-message="$errorMessage(errors, 'email')"
            />
            <app-form-group
                :label="$t('gender')"
                type="radio"
                :list="[
                {id:'male',value: $t('male')},
                {id:'female', value:  $t('female')},
                {id:'other', value:  $t('other')}
            ]"
                v-model="formData.gender"
                :error-message="$errorMessage(errors, 'gender')"
            />
            <div class="row">
                <div class="col-md-6">
                    <app-form-group
                        :label="$t('employee_id')"
                        :placeholder="$placeholder('employee_id')"
                        v-model="formData.employee_id"
                        :required="true"
                        :error-message="$errorMessage(errors, 'employee_id',true,true)"
                    />

                </div>
                <div class="col-md-6">
                    <app-form-group-selectable
                        type="select"
                        :label="$t('department')"
                        list-value-field="name"
                        v-model="formData.department_id"
                        :chooseLabel="$t('department')"
                        :error-message="$errorMessage(errors, 'department_id')"
                        :fetch-url="SELECTABLE_DEPARTMENT"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <app-form-group-selectable
                        type="select"
                        :label="$t('designation')"
                        list-value-field="name"
                        v-model="formData.designation_id"
                        :chooseLabel="$t('designation')"
                        :error-message="$errorMessage(errors, 'designation_id')"
                        :fetch-url="SELECTABLE_DESIGNATION"
                    />
                </div>
                <div class="col-md-6">
                    <app-form-group-selectable
                        type="select"
                        :label="$t('employment_status')"
                        list-value-field="name"
                        v-model="formData.employment_status_id"
                        :chooseLabel="$t('employment_status')"
                        :error-message="$errorMessage(errors, 'employment_status_id')"
                        :fetch-url="`${SELECTABLE_EMPLOYMENT_STATUS}?excluded=terminated`"
                    />
                </div>
            </div>
            <app-form-group-selectable
                type="multi-select"
                v-if="$can('attach_users_to_roles')"
                :label="$t('role')"
                list-value-field="name"
                v-model="formData.roles"
                :chooseLabel="$t('role')"
                :error-message="$errorMessage(errors, 'roles')"
                :fetch-url="SELECTABLE_ROLE"
            />
            <app-form-group
                v-if="!selectedUrl"
                type="number"
                :label="$t('salary')"
                :placeholder="$placeholder('salary')"
                v-model="formData.salary"
                :required="true"
                :error-message="$errorMessage(errors, 'salary')"
            />
            <app-form-group
                type="date"
                :label="$t('joining_date')"
                :placeholder="$placeholder('joining_date')"
                v-model="formData.joining_date"
                :required="true"
                :error-message="$errorMessage(errors, 'joining_date')"
            />
            <app-form-group
                v-if="from === 'user'"
                class="mb-primary"
                type="single-checkbox"
                label=""
                :list-value-field="$t('do_not_show_in_employee_list')"
                v-model="formData.dont_show_in_employee"
            />
        </form>
        <app-note v-else
                  :title="$t('no_delivery_settings_found')"
                  :notes="$optional(currentTenant, 'is_single') ?
                  `<ol>
                     <li>${$t('cron_job_settings_warning',{
                      documentation: `<a href='https://payday.gainhq.com/documentation/important-settings.html#scheduler-queue' target='_blank'>${$t('documentation')}</a>`
                  })}</li>
                     <li>${$t('no_delivery_settings_warning', {
                      location: `<a href='${urlGenerator(TENANT_EMAIL_SETUP_SETTING)}'>${$t('here')}</a>`
                  })}</li>
                  </ol>`: [$t('no_delivery_settings_warning_none')]"
                  content-type="html"
        />
        <template v-if="!isMailSettingExist && !selectedUrl" slot="footer">
            <button type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">
                {{ $t('close') }}
            </button>
        </template>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
import {
    EMPLOYEES_LIST,
    SELECTABLE_DEPARTMENT,
    SELECTABLE_ROLE,
    SELECTABLE_DESIGNATION,
    SELECTABLE_EMPLOYMENT_STATUS,
    EMPLOYEE_INVITE, AUTO_EMPLOYEE_ID,
} from "../../../Config/ApiUrl";
import {
    TENANT_MAIL_CHECK_URL,
    TENANT_EMAIL_SETUP_SETTING
} from "../../../../common/Config/apiUrl";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";
import {formatDateForServer} from "../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "EmployeeListCreateEditModel",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        from: {
            type: String,
            default: 'employee'
        }
    },
    data() {
        return {
            isMailSettingExist: true,
            formData: {
                employee_id: '',
                department: {},
                roles: [],
                designation: {},
                employment_status: {},
                is_in_employee: false,
            },
            EMPLOYEES_LIST,
            SELECTABLE_DEPARTMENT,
            SELECTABLE_ROLE,
            SELECTABLE_DESIGNATION,
            SELECTABLE_EMPLOYMENT_STATUS,
            EMPLOYEE_INVITE,
            TENANT_EMAIL_SETUP_SETTING
        }
    },
    methods: {
        submit() {
            this.fieldStatus.isSubmit = true;
            this.loading = true;
            this.message = '';
            this.errors = {};
            this.formData.is_in_employee = this.formData.dont_show_in_employee ? 0 : 1;
            this.formData.joining_date = formatDateForServer(this.formData.joining_date);
            this.submitData(this.formData);
        },
        afterSuccess({data}) {
            this.formData = {};
            $('#employee-list-modal').modal('hide');
            this.$emit('input', false);
            this.toastAndReload(data.message, this.from === 'employee' ? 'employee-table' : 'user-table');
            if (this.from === 'user') {
                this.$hub.$emit(`reload-role-table`)
            }
        },
        afterSuccessFromGetEditData({data}) {
            this.preloader = false;
            this.formData = data;
            this.formData.employee_id = data.profile?.employee_id;
            this.formData.roles = this.collection(data.roles).pluck();
            this.formData.designation_id = data.designation?.id;
            this.formData.department_id = data.department?.id;
            this.formData.employment_status_id = data.employment_status?.id;
            this.formData.dont_show_in_employee = parseInt(this.formData.is_in_employee) ? 0 : 1;
            this.formData.joining_date = data.profile?.joining_date ? new Date(data.profile?.joining_date) : null;
            this.formData.gender = data.profile?.gender;
        },
        checkMailSettings() {
            this.preloader = true;
            axiosGet(TENANT_MAIL_CHECK_URL).then(response => {
                this.isMailSettingExist = !!response.data;
            }).finally(() => {
                this.preloader = false;
            });
        },
        generateModalTitle(subject) {
            return this.$fieldTitle(this.selectedUrl ? 'edit' : 'invite', subject, true);
        },
        checkEmployeeID() {
            axiosGet(AUTO_EMPLOYEE_ID).then(response => {
                this.formData.employee_id = response.data;
            }).finally(() => {
                this.preloader = false;
            });
        }
    },
    created() {
        this.checkMailSettings();
        if (!this.selectedUrl) {
            this.checkEmployeeID();
        }
    }
}
</script>

