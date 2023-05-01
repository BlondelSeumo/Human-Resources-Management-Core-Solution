<template>
    <modal id="leave-create-edit-modal"
           v-model="showModal"
           size="large"
           :title="modalTitle"
           @submit="submit"
           :loading="loading"
           :preloader="preloader">
        <form
            ref="form"
            enctype="multipart/form-data"
            @submit.prevent="submitData">

            <app-form-group-selectable
                v-if="assignPermissions"
                type="search-select"
                v-model="formData.employee_id"
                :label="$t('employee')"
                list-value-field="full_name"
                :required="true"
                :error-message="$errorMessage(errors, 'employee_id')"
                :placeholder="$t('search_and_select_an_employee')"
                :fetch-url="`${apiUrl.TENANT_SELECTABLE_USER}?without=admin&employee=only&with_auth=yes`"
            />
            <app-overlay-loader v-if="modalPreloader"/>
            <div :class="{'loading-opacity': modalPreloader}">
                <p v-if="formData.employee_id">
                    {{ $t('leave_availability') }}
                    <a class="ml-2"
                       data-toggle="collapse"
                       href="#leaveAvailability"
                       aria-expanded="false"
                       @click="ariaExpanded = !ariaExpanded"
                       aria-controls="leaveAvailability">
                        {{ ariaExpanded ? $t('hide') : $t('show') }}
                    </a>
                </p>

                <div class="collapse" id="leaveAvailability">
                    <div class="note note-warning rounded p-4 mb-3">
                        <div class="row">
                            <div v-for="availability in availabilities" class="col-md-6">
                                <p class="default-font-color mb-0">
                                    <span class="text-muted">{{ availability.leave_type.name }}:</span>
                                    {{ numberFormatter(parseFloat(availability.amount) - parseFloat(availability.taken)) }}
                                </p>
                            </div>
                            <div v-if="availabilities.length === 0">{{ $t('no_available_leave') }}</div>
                        </div>
                    </div>
                </div>

            <app-form-group
                type="select"
                v-model="formData.leave_type_id"
                :label="$t('leave_type')"
                list-value-field="name"
                :required="true"
                :error-message="$errorMessage(errors, 'leave_type_id')"
                :list="selectableLeaveType"
            />

            <div class="row align-items-center my-primary">
                <div class="col-md-3">
                    <label>
                        {{ $t('age') }} <span class="text-muted">({{ $t('leave_duration') }})</span>
                    </label>
                </div>
                <div class="col-md-9">
                    <app-input
                        type="radio"
                        v-model="formData.leave_duration"
                        :list="leaveDuration"
                        :error-message="$errorMessage(errors, 'leave_duration')"
                        list-value-field="name"/>
                </div>

            </div>

            <template v-if="formData.leave_duration === 'single_day'">
                <app-form-group
                    type="date"
                    v-model="formData.date"
                    :label="$t('date')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'date')"
                    :placeholder="$t('enter_date')"
                />
            </template>

            <template v-if="formData.leave_duration === 'multi_day'">
                <div class="row">
                    <div class="col-md-6">
                        <app-form-group
                            type="date"
                            v-model="formData.start_date"
                            :label="$t('start_date')"
                            :required="true"
                            :error-message="$errorMessage(errors, 'start_date')"
                            :placeholder="$t('enter_start_date')"
                        />
                    </div>

                    <div class="col-md-6">
                        <app-form-group
                            type="date"
                            v-model="formData.end_date"
                            :label="$t('end_date')"
                            :required="true"
                            :error-message="$errorMessage(errors, 'end_date')"
                            :placeholder="$t('enter_end_date')"
                        />
                    </div>
                </div>
            </template>

            <template v-if="formData.leave_duration === 'half_day'">
                <label>{{ $t('date') }}</label>
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <app-input
                            type="date"
                            v-model="formData.date"
                            :required="true"
                            :error-message="$errorMessage(errors, 'date')"
                            :placeholder="$t('enter_date')"
                        />
                    </div>
                    <div class="col-md-6">
                        <app-input
                            type="radio"
                            v-model="formData.day_type"
                            :list="dayHalf"
                            list-value-field="name"
                        />
                    </div>
                </div>
            </template>

            <template v-if="formData.leave_duration === 'hours'">
                <app-form-group
                    type="date"
                    v-model="formData.date"
                    :label="$t('date')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'date')"
                    :placeholder="$t('enter_date')"
                />
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <app-form-group
                            type="time"
                            v-model="formData.start_time"
                            :label="$t('start_time')"
                            :required="true"
                            :error-message="$errorMessage(errors, 'start_time')"
                            :placeholder="$t('enter_start_time')"
                        />
                    </div>
                    <div class="col-md-6">
                        <app-form-group
                            type="time"
                            v-model="formData.end_time"
                            :label="$t('end_time')"
                            :required="true"
                            :error-message="$errorMessage(errors, 'end_time')"
                            :placeholder="$t('enter_end_time')"
                        />
                    </div>
                </div>
            </template>

            <app-form-group
                type="textarea"
                :text-area-rows="4"
                v-model="formData.note"
                :label="$t('reason_note')"
                :error-message="$errorMessage(errors, 'note')"
                :placeholder="$t('add_reason_note_here')"
            />

            <div class="form-group mb-0">
                <label>
                    {{ $t('attachments') }}
                </label>
                <app-input
                    type="dropzone"
                    v-model="attachments"
                    :error-message="errorMessageInArray(errors, 'attachments.0')"
                />
                <small class="text-muted">{{ $t('leave_attachment_allowed_file_types') }}</small>
            </div>
            </div>
        </form>
    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet, axiosPost} from "../../../../../common/Helper/AxiosHelper";
import {addChooseInSelectArray, formDataAssigner} from "../../../../../common/Helper/Support/FormHelper";
import {
    formatDateForServer,
    formatTimeForServer,
    formatDateTimeForServer,
    serverDateTimeFormat
} from "../../../../../common/Helper/Support/DateTimeHelper";
import {errorMessageInArray} from "../../../../../common/Helper/Support/FormHelper";
import moment from "moment";
import {numberFormatter} from "../../../../../common/Helper/Support/SettingsHelper";

export default {
    name: "LeaveCreateEditModal",
    mixins: [ModalMixin, FormHelperMixins],
    props: {
        tableId: {},
        employee: {},
        specificId: {},
    },
    data() {
        return {
            numberFormatter,
            errorMessageInArray,
            modalPreloader: false,
            ariaExpanded: false,
            formData: {
                leave_duration: 'single_day',
                day_type: 'first_half',
                leave_type_id: '',
            },
            attachments: [],
            availabilities: [],
            leaveDuration: [
                {id: 'single_day', name: 'Single Day'},
                {id: 'multi_day', name: 'Multi Day'},
                {id: 'half_day', name: 'Half Day'},
                {id: 'hours', name: 'Hours'}
            ],
            dayHalf: [
                {id: 'first_half', name: 'First half'},
                {id: 'last_half', name: 'Last half'}
            ]
        }
    },
    methods: {
        submit() {
            this.loading = true;
            let formData = {...this.formData}

            if (formData.leave_duration === 'multi_day') {
                formData.start_date = formatDateForServer(formData.start_date);
                formData.end_date = formatDateForServer(formData.end_date);
            } else {
                formData.date = formatDateForServer(formData.date);
            }

            if (formData.leave_duration === 'hours') {
                formData.start_time = formatDateTimeForServer(moment(`${formData.date} ${formatTimeForServer(formData.start_time)}`,serverDateTimeFormat))
                formData.end_time = formatDateTimeForServer(moment(`${formData.date} ${formatTimeForServer(formData.end_time)}`,serverDateTimeFormat))
            }

            if (formData.leave_duration === 'half_day') {
                formData.leave_duration = formData.day_type
            }

            formData = formDataAssigner(new FormData, formData);

            this.attachments.forEach(file => {
                formData.append('attachments[]', file)
            })

            axiosPost(this.apiUrl.LEAVE_ASSIGN, formData).then(({data}) => {
                this.loading = false;
                this.$toastr.s('', data.message);
                $('#leave-create-edit-modal').modal('hide');
                this.showModal = false;
                if (this.tableId) {
                    this.$hub.$emit(`reload-${this.tableId}`);
                } else {
                    this.$emit('reload')
                }
            }).catch(({response}) => {
                this.loading = false;
                this.message = '';
                this.errors = response.data.errors || {};
                this.fieldStatus.isSubmit = true
                if (response.status != 422)
                    this.$toastr.e(response.data.message || response.statusText)
            })
        },
        getEmployeeExistingLeaves() {
            this.modalPreloader = true;
            axiosGet(`${this.apiUrl.LEAVES}/${this.formData.employee_id}/allowances`).then(response => {
                this.availabilities = response.data.allowances;
            }).finally(() => this.modalPreloader=false)
        }
    },
    created() {
        if (!this.employee && !this.assignPermissions && !this.specificId) {
            this.formData.employee_id = window.user.id
            this.getEmployeeExistingLeaves()
        }

        if (this.specificId){
            this.formData.employee_id = this.specificId
            this.getEmployeeExistingLeaves()
        }
    },
    computed: {
        assignPermissions() {
            if (this.specificId && this.$can('assign_leaves')){
                return false;
            }

            if (this.employee && this.$can('assign_leaves') ){
                this.formData.employee_id = this.employee.id
                this.getEmployeeExistingLeaves();
                return false;
            }
            return this.$can('assign_leaves');
        },
        employeeId() {
            return this.formData.employee_id;
        },
        selectableLeaveType() {
            let leave_type = [];
            leave_type = addChooseInSelectArray(leave_type, 'name', this.$t('leave_type'));
            if (this.availabilities.length !== 0) {
                this.availabilities.map((availability) => leave_type.push(availability.leave_type))
            }
            return leave_type;
        },
        modalTitle(){
            return this.$can('assign_leaves') ? this.$t('assign_leave') : this.$t('request_leave') ;
        }
    },
    watch: {
        employeeId: {
            handler: function (employee_id) {
                if (employee_id && this.assignPermissions) {
                    this.formData.leave_type_id = '';
                    this.getEmployeeExistingLeaves()
                }
            }
        }
    }
}
</script>