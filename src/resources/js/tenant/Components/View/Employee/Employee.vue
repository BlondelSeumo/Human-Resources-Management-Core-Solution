<template>
    <div class="content-wrapper">
        <div class="row align-items-center">
            <div class="col-md-12 col-lg-6">
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb p-0 d-flex align-items-center mb-primary">
                            <li class="breadcrumb-item page-header d-flex align-items-center">
                                <h4 class="mb-0" v-if="ownProfile">{{ $t('job_desk') }}</h4>
                                <h4 class="mb-0" v-else>{{ $t('employee_details') }}</h4>
                            </li>
                            <template v-if="this.$can('view_employees') && !ownProfile">
                                <li class="ml-2">|</li>
                                <li>
                                    <a :href="urlGenerator(apiUrl.ALL_EMPLOYEE_URL_FRONT_END)"
                                       class="btn btn-link text-primary pl-2">{{ $t('back_to_all_employees') }}</a>
                                </li>
                            </template>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-md-12 col-lg-6" v-if="!isActionButtonHide">
                <div class="d-flex justify-content-lg-end mb-primary">
                    <div class="dropdown">
                        <button type="button"
                                v-if="actionButtonReady"
                                class="btn btn-success dropdown-toggle"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            {{ $t('action') }}
                            <app-icon name="chevron-down"/>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item"
                               v-if="attendanceActionPermission"
                               @click="openAttendanceModal">
                                {{ attendanceActionTitle }}
                            </a>
                            <a href="#" class="dropdown-item"
                               v-if="leaveActionPermission"
                               @click="openLeaveModal">
                                {{ leaveActionTitle }}
                            </a>
                            <a href="#" class="dropdown-item"
                               v-if="terminatePermission"
                               @click="triggerTerminate">
                                {{ $t('terminate') }}
                            </a>
                            <a href="#" class="dropdown-item"
                               v-if="salaryPermission"
                               @click="employeeAction('salary')">
                                {{ salaryTitle }}
                            </a>
                            <a href="#" class="dropdown-item"
                               v-if="joiningPermission"
                               @click="employeeAction('joining_date')">
                                {{ joiningDateTitle }}
                            </a>
                            <a href="#" class="dropdown-item"
                               v-if="rejoinPermission"
                               @click="triggerRejoin">
                                {{ $t('rejoining') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="user-profile mb-primary">
            <div class="card card-with-shadow py-5 border-0" style="min-height: 220px;">
                <app-overlay-loader v-if="loader"/>
                <div v-else class="row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-5">
                        <div class="media border-right px-5 pr-xl-5 pl-xl-0 user-header-media align-items-center">
                            <div class="profile-pic-wrapper">
                                <div class="custom-image-upload-wrapper circle mx-xl-auto">
                                    <div class="image-area d-flex">
                                        <img id="imageResult"
                                             :src="profile_picture_link"
                                             alt=""
                                             class="img-fluid mx-auto my-auto">
                                    </div>
                                    <div class="input-area">
                                        <label id="upload-label" for="upload">{{ $t('change') }}</label>
                                        <input id="upload"
                                               @change="readURL"
                                               type="file"
                                               ref="changeProfileImage"
                                               class="form-control d-none">
                                    </div>
                                </div>
                            </div>
                            <div class="media-body user-info-header">
                                <h4>
                                    {{ employee.full_name }}
                                </h4>
                                <span class="badge badge-pill badge-success user-status"
                                      v-if="employee.profile && employee.employment_status"
                                      :class="['badge', 'badge-pill', 'badge-'+employee.employment_status.class, 'user-status']">
                                    {{ employee.employment_status.name }}
                                </span>
                                <p class="text-muted mt-2 mb-0" v-if="employee.profile && employee.designation">
                                    {{ employee.designation.name }}
                                </p>
                                <p class="text-muted mb-0" v-if="employee.profile">
                                    {{ employee.profile.employee_id }}
                                </p>
                                <p class="text-muted" v-if="employee.roles">
                                    {{ rolesNames }}
                                </p>
                                <div
                                    class="d-flex align-items-center justify-content-center justify-content-lg-start position-relative">
                                    <app-overlay-loader v-if="socialLinkLoader"/>
                                    <template v-else v-for="link in socialLinks">
                                        <a v-if="Object.values(link)[0]" :href="Object.values(link)[0]" target="_blank"
                                           class="d-flex align-items-center mr-3 user-social-link">
                                            <app-icon :name="Object.keys(link)[0]" width="17" height="17"/>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-7">
                        <div
                            class="user-details px-5 px-sm-5 px-md-5 px-lg-0 px-xl-0 mt-5 mt-sm-5 mt-md-0 mt-lg-0 mt-xl-0">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6">
                                    <div class="border-right custom">
                                        <div class="media mb-4 mb-xl-5">
                                            <div class="align-self-center mr-3">
                                                <app-icon name="briefcase"/>
                                            </div>
                                            <div class="media-body">
                                                <p class="text-muted mb-0">
                                                    {{ $t('department') }}
                                                </p>
                                                <p class="mb-0" v-if="employee.department">
                                                    {{ employee.department.name }}
                                                </p>
                                                <p class="mb-0" v-else>
                                                    {{ $t('not_added_yet') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="media mb-4 mb-xl-0">
                                            <div class="align-self-center mr-3">
                                                <app-icon name="clock"/>
                                            </div>
                                            <div class="media-body">
                                                <p class="text-muted mb-0">
                                                    {{ $t('work_shift') }}
                                                    <button type="button"
                                                            class="text-size-12 btn btn-sm btn-outline-primary rounded-pill px-2 padding-y-1 mb-1 ml-2"
                                                            @click.prevent="viewWorkShift">
                                                        {{ this.$t('view') }}
                                                    </button>
                                                </p>
                                                <p class="mb-0" v-if="employee.working_shift">
                                                    {{ employee.working_shift.name }}
                                                </p>
                                                <p class="mb-0" v-else>
                                                    {{ $t('not_added_yet') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6">
                                    <div class="media mb-4 mb-xl-5">
                                        <div class="align-self-center mr-3">
                                            <app-icon name="dollar-sign"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-muted mb-0">
                                                {{ $t('salary') }}
                                            </p>
                                            <p class="mb-0" v-if="!employee.updated_salary && !employee.salary">
                                                {{ $t('not_added_yet') }}
                                            </p>
                                            <template v-else>
                                                <p class="mb-0" v-if="employee.salary">
                                                    {{ formatCurrency(numberFormatter(Number(employee.salary.amount))) }}
                                                    <span class="text-primary font-size-80">(current)</span>
                                                </p>
                                                <p class="mb-0"
                                                   v-if="employee.updated_salary && isAfterNow(employee.updated_salary.start_at)">
                                                    {{ formatCurrency(numberFormatter(Number(employee.updated_salary.amount))) }}
                                                    <span class="font-size-80">(Increment from {{ formatDateToLocal(employee.updated_salary.start_at) }})</span>
                                                </p>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="media mb-0 mb-xl-0">
                                        <div class="align-self-center mr-3">
                                            <app-icon name="calendar"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-muted mb-0">
                                                {{ $t('joining_date') }}
                                            </p>
                                            <p class="mb-0" v-if="employee.profile && employee.profile.joining_date">
                                                {{ formatDateToLocal(employee.profile.joining_date) }}</p>
                                            <p class="mb-0" v-else>
                                                {{ $t('not_added_yet') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <app-tab :tabs="tabs" :icon="tabIcon"/>

        <app-attendance-create-edit-modal
            v-if="attendanceModalActive"
            v-model="attendanceModalActive"
            :specific-id="employeeId"
            @reload="employeeActionHappened"
        />

        <app-leave-create-edit-modal
            v-if="leaveModalActive"
            v-model="leaveModalActive"
            :specific-id="employeeId"
            @reload="employeeActionHappened"
        />

        <app-confirmation-modal
            :title="promptTitle"
            :message="promptMessage"
            :modal-class="modalClass"
            :icon="icon"
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="triggerConfirm"
            @cancelled="cancelled"
            :loading="loading"
            :self-close="false"
        />

        <app-employment-status-modal
            v-if="employmentStatusModalActive"
            v-model="employmentStatusModalActive"
            :id="employeeId"
            @update_employment_statuses="refreshStatus"
        />

        <app-employee-termination-reason-modal
            v-if="isTerminationReasonModalActive"
            v-model="isTerminationReasonModalActive"
            :id="employeeId"
        />

        <job-history-edit-modal
            v-if="isJobHistoryEditModalActive"
            v-model="isJobHistoryEditModalActive"
            :modalType="modalAction"
            :employee="employee"
            @reload="reloadPage"
        />

        <app-working-shift-modal
            v-if="viewWorkShiftModal"
            v-model="viewWorkShiftModal"
            :selected-url="workShiftUrl"
            :view-only="true"
            @close="viewWorkShiftModal = false"
        />

    </div>
</template>

<script>
import {axiosPost, urlGenerator} from '../../../../common/Helper/AxiosHelper';
import {FormMixin} from "../../../../core/mixins/form/FormMixin";
import {formatDateToLocal, isAfterNow} from "../../../../common/Helper/Support/DateTimeHelper";
import {mapState} from "vuex";
import EmployeeActionMixin from "../../Mixins/EmployeeActionMixin";
import JobHistoryEditModal from "./Components/JobHistory/components/JobHistoryEditModal";
import {WORKING_SHIFTS} from "../../../Config/ApiUrl";
import EmployeeDetailsTabMixin from "../../Mixins/EmployeeDetailsTabMixin";
import {formatCurrency, numberFormatter} from "../../../../common/Helper/Support/SettingsHelper";

export default {
    name: "EmployeeDetails",
    mixins: [FormMixin, EmployeeActionMixin, EmployeeDetailsTabMixin],
    components: {JobHistoryEditModal},
    props: {
        employeeId: {},
        managerDept: {}
    },
    data() {
        return {
            numberFormatter,
            formatCurrency,
            urlGenerator,
            files: [],
            profile_picture: '',
            tabIcon: 'user',
            viewWorkShiftModal: false,
            workShiftUrl: '',
            employee: {
                status: {},
                employment_status: {}
            },
            tabs: [],
            formatDateToLocal,
            isAfterNow,
        }
    },
    created() {
        this.tabs = this.ownProfile ? this.jobDeskTabs : this.employeeDetailsTabs.concat(this.jobDeskTabs);
    },
    mounted() {
        this.$store.dispatch("getEmployeeDetails", this.employeeId);
        this.$store.dispatch('getEmployeeSocialLinks', this.employeeId)

        $(function () {
            $('#upload').on('change', function () {
                this.readURL();
            });
        });
    },
    methods: {
        readURL() {
            this.files = this.$refs.changeProfileImage.files;
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#imageResult').attr('src', e.target.result);
                };
                let image = reader.readAsDataURL(this.files[0]);

                let formData = new FormData;

                formData.append('profile_picture', this.files[0]);
                formData.append('user_id', this.employeeId);

                axiosPost(`admin/auth/users/profile-picture`, formData).then(response => {
                    this.$store.dispatch("getEmployeeDetails", this.employeeId);
                    this.$toastr.s(response.data ? response.data.message : '');
                }).catch(error => {
                    this.$store.dispatch("getEmployeeDetails", this.employeeId);
                    this.$toastr.e(error.response.data ? error.response.data.errors.profile_picture[0] : '');
                });

            }
        },
        reloadPage() {
            this.$store.dispatch("getEmployeeDetails", this.employeeId);
            this.$hub.$emit('employeeDetailsActionHappened', this.employeeId);
        },
        employeeActionHappened() {
            this.$hub.$emit('employeeDetailsActionHappened', this.employeeId);
        },
        viewWorkShift() {
            this.viewWorkShiftModal = true;
            this.workShiftUrl = `${WORKING_SHIFTS}/${this.employee.working_shift.id}`;
        }
    },
    computed: {
        ...mapState({
            employeeDetails: state => state.employees.employee,
            loader: state => state.loading,
            socialLinkLoader: state => state.employees.socialLinkLoader,
            socialLinks: state => state.employees.socialLinks
        }),
        actionButtonReady() {
            return !!this.employee.full_name
        },
        profile_picture_link() {
            if (this.employee.profile_picture) {
                return this.$optional(this.employee, 'profile_picture', 'full_url');
            }
            return urlGenerator('/images/avatar.png')
        },
        attendanceActionTitle() {
            return this.$can('update_attendance_status') ? this.$t('add_attendance') : this.$t('request_attendance');
        },
        leaveActionTitle() {
            return this.$can('assign_leaves') ? this.$t('assign_leave') : this.$t('request_leave');
        },
        ownProfile() {
            return window.user.id === this.employeeId;
        },
        joiningDateTitle() {
            return this.employee.profile?.joining_date ? this.$t('edit_joining_date') : this.$t('add_joining_date');
        },
        salaryTitle() {
            return this.employee.updated_salary ? this.$t('edit_salary') : this.$t('add_salary');
        },
        rolesNames(){
            let title = '';
            if (!this.employee.roles.length) return ''
            this.employee?.roles.map((role) => {
                title += ` ${role.name},`
            })
            return title.replace(/,+$/, '');
        },
        isActionButtonHide(){
            return !this.attendanceActionPermission &&
                !this.leaveActionPermission &&
                !this.terminatePermission &&
                !this.terminatePermission &&
                !this.joiningPermission &&
                !this.rejoinPermission;
        },
        attendanceActionPermission(){
            return (this.employee.employment_status && this.employee.employment_status.alias !== 'terminated') && this.employee.status.name !== 'status_inactive'
        },
        leaveActionPermission(){
            return (this.employee.employment_status && this.employee.employment_status.alias !== 'terminated') && this.employee.status.name !== 'status_inactive'
        },
        terminatePermission(){
            return this.employee.employment_status && this.employee.status.name !== 'status_invited' && this.employee.employment_status.alias !== 'terminated' && this.$can('terminate_employees')
        },
        salaryPermission(){
            return this.$can('update_employee_job_history') && (this.employee.employment_status && this.employee.employment_status.alias !== 'terminated')
        },
        joiningPermission(){
            return this.$can('update_employee_job_history') && (this.employee.employment_status && this.employee.employment_status.alias !== 'terminated')
        },
        rejoinPermission(){
            return this.employee.employment_status && this.employee.employment_status.alias === 'terminated' && this.$can('rejoin_employees')
        }
    },
    watch: {
        employeeDetails: {
            handler: function (employee) {
                this.employee = employee;
                if (employee.full_name) {
                    let title = document.title.split('-');
                    title[0] = employee.full_name;
                    document.title = title.join(' - ');
                }
            },
            deep: true
        }
    }
}
</script>

<style scoped lang="scss">
.user-social-link {
    svg {
        width: 17px;
        height: 17px;
    }

    i {
        font-size: 17px;
    }
}
</style>
