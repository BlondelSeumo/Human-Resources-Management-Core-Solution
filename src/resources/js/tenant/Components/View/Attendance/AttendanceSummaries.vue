<template>
    <div :class="!fromEmployeeDetails?'content-wrapper':''">
        <app-page-top-section v-if="!fromEmployeeDetails" :title="$t('summery')">
            <div class="d-flex d-inline">
                <app-default-button
                    btn-class="btn btn-success mr-2"
                    :title="$t('export_all')"
                    v-if="exportable && $can('export_attendance_summery')"
                    @click="viewConfirmationModal(true)"
                />
                <app-default-button
                    btn-class="btn btn-success mr-2"
                    :title="$t('export')"
                    v-if="$can('export_attendance_summery')"
                    @click="viewConfirmationModal()"
                />
                <app-attendance-top-buttons
                    :specific-id="specificId"
                    :tableId="tableId"
                    :setting-button="false"
                />
            </div>
        </app-page-top-section>

        <app-filter-with-search
            :options="tableOptions"
            @filteredValue="setUserId"
            v-if="!specificEmployee"
        />

        <div :class="fromEmployeeDetails?'': 'card card-with-shadow border-0'"
             style="min-height: 400px;">

            <div
                :class="!fromEmployeeDetails?'p-primary':'px-0 pt-0 pb-primary'"
                class="card-header d-flex align-items-center justify-content-between primary-card-color">
                <app-month-calendar/>

                <app-period-calendar/>
            </div>

            <app-overlay-loader v-if="preloader"/>

            <div :class="!fromEmployeeDetails?'p-primary':'p-0'" class="card-body" v-else>
                <div class="d-flex justify-content-between" v-if="!specificEmployee">
                    <div class="media align-items-center">
                        <app-avatar
                            :title="employee.full_name"
                            avatar-class="avatars-w-50"
                            :status="$optional(employee, 'status', 'class')"
                            :img="$optional(employee, 'profile_picture', 'full_url')"
                        />

                        <div class="media-body ml-3">
                            <a :href="profileUrl">{{ employee.full_name }}</a>
                            <p class="text-muted font-size-90 mb-0">
                                {{ employee.department.name }}, {{employee.profile.employee_id}}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="#" @click="changeUser('previous')" :class="{'disabled text-muted' : isPreviousNull}">
                            {{ $t('previous') }}
                        </a>
                        <span class="text-muted px-2">|</span>
                        <a href="#" @click="changeUser('next')" :class="{'disabled text-muted' : isNextNull}">
                            {{ $t('next') }}
                        </a>
                    </div>
                </div>

                <employee-attendance-summary
                    :summaries="summaries"
                    :data="dataSet"
                />

                <app-attendance-summary-table
                    :specific-employee="fromEmployeeDetails"
                    :details-id="detailsId"
                    :attendance-id="attendanceId"
                    :user="employee"
                    @checkTableData="setTableDataNumber"
                />

            </div>
        </div>

        <app-confirmation-modal
            v-if="exportConfirmationModal"
            :title="modalSubtitle"
            :message="modalMessage"
            modal-id="app-confirmation-modal"
            modal-class="primary"
            icon="download"
            :first-button-name="$t('export')"
            :second-button-name="$t('cancel')"
            @confirmed="exportFilteredAttendance()"
            @cancelled="exportConfirmationModal = false"
            :self-close="false"
        />

        <app-confirmation-modal
            v-if="noDataFoundModal"
            :message="this.$t('please_filter_the_attendance_data_first')"
            :title="this.$t('no_attendance_data_found')"
            :second-button-name="$t('okay')"
            :hide-first-button="true"
            modal-class="warning"
            icon="filter"
            modal-id="app-confirmation-modal"
            @cancelled="noDataFoundModal = false"
            :self-close="false"
        />
    </div>
</template>

<script>
import AppAttendanceTopButtons from "./Component/AppAttendanceTopButtons";
import MemoizeMixins from "../../../../common/Helper/Support/MemoizeMixins";
import AttendanceHelperMixin from "../../Mixins/AttendanceHelperMixin";
import AttendanceSummaryDetailsMixin from "../../Mixins/AttendanceSummaryDetailsMixin";
import EmployeeAttendanceSummary from "./Component/EmployeeAttendanceSummary";
import {axiosGet, urlGenerator} from "../../../../common/Helper/AxiosHelper";
import {collection} from "../../../../common/Helper/helpers";
import {localTimeZone} from "../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "AttendanceSummery",
    components: {
        EmployeeAttendanceSummary,
        AppAttendanceTopButtons
    },
    props: {
        detailsId: {},
        attendanceId: {},
        firstUser: {
            default: function () {
                return {}
            }
        },
        specific: {},
        fromEmployeeDetails: {}
    },
    mixins: [
        MemoizeMixins,
        AttendanceHelperMixin,
        AttendanceSummaryDetailsMixin,
    ],
    data() {
        return {
            tableId: 'summary',
            preloader: true,
            summaries: {},
            dataSet: [],
            isChanging: false,
            nextUser: {},
            filterLoaded: true,
            exportConfirmationModal: false,
            noDataFoundModal: false,
            tableData: 0,
            modalMessage: '',
            modalSubtitle: '',
            exportAllEmployee: false,
        }
    },
    methods: {
        getAttendanceSummary(userId = false) {
            this.preloader = true;
            let employeeId = userId || this.user.id
            if (employeeId) {
                axiosGet(`${this.apiUrl.ATTENDANCES}/${employeeId}/summaries?${this.queryString}`).then(response => {
                    this.summaries = response.data;
                    const {regular, early, late, on_leave} = this.summaries;
                    this.dataSet = [regular, early, late, on_leave];
                }).finally(() => {
                    this.preloader = false;
                });
            }
        },
        changeUser(type) {
            this.isChanging = true;
            axiosGet(`${this.apiUrl.APP_SELECTABLE}/${this.employee.id || this.user.id}/next-user/${type}`)
                .then(response => {
                    this.nextUser = response.data;
                    this.setActiveUserToAvatarFilter(this.nextUser.id);
                    this.setActiveUserToDropdownFilter(this.nextUser.id);
                    this.getAttendanceSummary();
                })
        },
        setUserId(filter) {
            this.getAttendanceSummary(filter.user);
            this.setActiveUserToAvatarFilter(filter.user)
            this.setActiveUserToDropdownFilter(filter.user);
        },
        setTableDataNumber(numberOfData){
            this.tableData = numberOfData;
        },
        viewConfirmationModal(allEmployee = false) {
            if (allEmployee) {
                this.exportAllEmployee = true;
                this.modalSubtitle = this.$t('all_employees_attendance_export_title');
                this.modalMessage = this.$t('all_employees_attendance_export_message');
            } else {
                this.exportAllEmployee = false;
                this.modalSubtitle = this.$t('attendance_export_title');
                this.modalMessage = this.$t('attendance_export_message');
            }
            if (this.tableData < 1) {
                this.noDataFoundModal = true;
            } else {
                this.exportConfirmationModal = true;
            }
        },
        exportFilteredAttendance() {
            let url = urlGenerator(`${this.apiUrl.EXPORT}/${this.employee.id || this.user.id}/attendance?${this.queryString}&timeZone=${localTimeZone}`);
            if (this.exportAllEmployee) {
                url = urlGenerator(`${this.apiUrl.EXPORT}/attendance/all?${this.queryString}&timeZone=${localTimeZone}`);
            }
            window.location = url;
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.exportConfirmationModal = false;
        }
    },
    computed: {
        specificId() {
            if (this.specific) {
                return JSON.parse(this.firstUser)?.id
            }
            return '';
        },
        isPreviousNull() {
            return collection(this.getAllUsers).first().id === this.employee.id
        },
        getAllUsers() {
            return this.tableOptions.filters.find(data => data.key === 'user').option
        },
        isNextNull() {
            return collection(this.getAllUsers).last().id === this.employee.id
        },
        user() {
            if (this.$can('view_attendance_summary') && !this.$can('view_all_attendance')) {
                return window.user;
            }
            return this.isChanging ? this.nextUser : JSON.parse(this.firstUser);
        },
        queryString() {
            return this.$store.getters.getFilterUrls;
        },
        employee() {
            return this.summaries.employee
        },
        profileUrl() {
            return urlGenerator(`${this.apiUrl.EMPLOYEES_PROFILE}/${this.employee.id}/profile`)
        },
        specificEmployee() {
            return (this.$can('view_attendance_summary') && !this.$can('view_all_attendance')) ||
                this.specific
        }
    },
    mounted() {
        this.$hub.$on('reload-summary', () => {
            this.getAttendanceSummary(this.employee?.id);
        })
        this.$hub.$on('employeeDetailsActionHappened', (value) => {
            this.getAttendanceSummary(this.employee?.id);
        });
    },
    watch: {
        queryString: {
            handler: function (queryString) {
                if (!queryString) {
                    return;
                }
                this.exportable = this.filterQuery.within !== 'thisYear';
                this.getAttendanceSummary(this.employee?.id);
            },
            immediate: true
        }
    },
}
</script>