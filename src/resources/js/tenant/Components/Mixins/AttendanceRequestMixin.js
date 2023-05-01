import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {ATTENDANCE_REQUESTS} from "../../Config/ApiUrl";
import {filterLastPendingData} from "../View/Attendance/Helper/Helper";
import {
    DepartmentFilterMixin, UserFilterMixin,
    WorkingShiftFilterMixin
} from "./FilterMixin";
import AttendanceHelperMixin from "./AttendanceHelperMixin";
import {convertSecondToHourMinutes} from "../../../common/Helper/Support/DateTimeHelper";
import {axiosGet} from "../../../common/Helper/AxiosHelper";

export default {
    mixins: [
        AttendanceHelperMixin,
        DatatableHelperMixin,
        DepartmentFilterMixin,
        WorkingShiftFilterMixin,
        UserFilterMixin
    ],
    data() {
        return {
            options: {
                name: this.$t('attendance_request'),
                url: ATTENDANCE_REQUESTS,
                showHeader: true,
                enableRowSelect: this.$can('approve_attendance') && this.$can('reject_attendance'),
                showCount: true,
                showClearFilter: true,
                columns: [
                    {
                        title: this.$t('profile'),
                        type: 'component',
                        className: 'd-inline-flex',
                        componentName: 'app-attendance-employee-media-object',
                        key: 'user',
                    },
                    {
                        title: this.$t('punched_in'),
                        type: 'component',
                        componentName: 'app-punch-in-date-time',
                        key: 'details',
                    },
                    {
                        title: this.$t('punched_out'),
                        type: 'component',
                        componentName: 'app-punch-out-date-time',
                        key: 'details',
                    },
                    {
                        title: this.$t('request_type'),
                        type: 'component',
                        componentName: 'app-attendance-request-type',
                        key: 'details',
                    },
                    {
                        title: this.$t('total_hours'),
                        type: 'custom-html',
                        key: 'details',
                        isVisible: true,
                        modifier: (value) => {
                            return convertSecondToHourMinutes(
                                this.getTotalWorkedDuration(
                                    [filterLastPendingData(value)]
                                ).asSeconds()
                            )
                        }
                    },
                    {
                        title: this.$t('status'),
                        type: 'expandable-column',
                        key: 'details',
                        isVisible: true,
                        componentName: 'app-attendance-expandable-column',
                        modifier: (details) => {
                            return {
                                title: this.$t(filterLastPendingData(details)?.status.name.replace('status_', '')),
                                expandable: details.length > 1,
                                className: filterLastPendingData(details)?.status.class,
                            };
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
                    },
                ],
                filters: [
                    {
                        title: this.$t('entry_date'),
                        type: "range-picker",
                        key: "entry_date",
                        option: ["today", "last7Days", "thisMonth", "lastMonth", "thisYear"]
                    },
                    {
                        title: this.$t('request_date'),
                        type: "range-picker",
                        key: "request_date",
                        option: ["today", "last7Days", "thisMonth", "lastMonth", "thisYear"]
                    },
                    this.$can('view_departments') ?
                        {
                            title: this.$t('department'),
                            type: "multi-select-filter",
                            key: "departments",
                            option: [],
                            listValueField: 'name'
                        } : {},
                    this.$can('view_working_shifts') ?
                        {
                            title: this.$t('work_shift'),
                            type: "multi-select-filter",
                            key: "working_shifts",
                            option: [],
                            listValueField: 'name'
                        } : {},
                    {
                        title: this.$t('see_rejected'),
                        type: 'toggle-filter',
                        key: 'rejected',
                        buttonLabel: {
                            active: 'Yes',
                            inactive: 'No'
                        },
                        header: {
                            title: this.$t('show_rejected_attendances'),
                            description: this.$t('filter_data_which_are_rejected')
                        }
                    },
                    {
                        title: this.$t('request_type'),
                        type: "drop-down-filter",
                        key: "request_type",
                        option: [
                            {id: 'new', name: this.$t('new')},
                            {id: 'change', name: this.$t('change')},
                        ],
                        listValueField: 'name'
                    },
                    {
                        title: this.$t('users'),
                        type: "multi-select-filter",
                        key: "users",
                        option: [],
                        listValueField: 'full_name',
                        permission: !!this.$can('view_all_attendance') && !!this.$can('view_users')
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('change_log'),
                        name: 'change-log',
                        type: 'requestTableRow',
                    },
                    {
                        title: this.$t('approve'),
                        name: 'approve',
                        type: 'requestTableRow',
                        modalClass: 'success',
                        modalSubtitle: this.$t('you_are_going_to_approve_this_attendance'),
                        modalIcon: 'check-circle',
                        modifier: (row) => {
                            let conditions = this.$can('update_attendance_status') &&
                                filterLastPendingData(row.details)?.status.name !== 'status_reject' &&
                                (row.user_id !== window.user.id || this.$isAdmin())

                            return conditions && (!this.$isOnlyDepartmentManager() ||
                                this.$isOnlyDepartmentManager() && row.user_id != window.user.id)
                        }
                    },
                    {
                        title: this.$t('reject'),
                        name: 'reject',
                        type: 'requestTableRow',
                        modalClass: 'danger',
                        modalSubtitle: this.$t('you_are_going_to_reject_a_attendance'),
                        modalIcon: 'x-circle',
                        modifier: (row) => {
                            let conditions = this.$can('update_attendance_status') &&
                                filterLastPendingData(row.details)?.status.name !== 'status_reject' &&
                                (row.user_id !== window.user.id || this.$isAdmin())

                            return conditions && (!this.$isOnlyDepartmentManager() ||
                                this.$isOnlyDepartmentManager() && row.user_id != window.user.id)
                        }
                    },
                    {
                        title: this.$t('cancel'),
                        name: 'cancel',
                        type: 'requestTableRow',
                        modalClass: 'warning',
                        modalSubtitle: this.$t('you_are_going_to_cancel_a_attendance'),
                        modalIcon: 'slash',
                        modifier: (row) => filterLastPendingData(row.details)?.status.name !== 'status_reject' &&
                            row.user_id == window.user.id
                    },
                ],
            },
            isContextMenuOpen: false,
            allSelected: false,
            selectedRequests: [],
            filterValue: {},
        }
    },
    methods: {
        getSelectedRows(data, allSelected) {
            if (this.filterValue.rejected) {
                this.isContextMenuOpen = false;
                return;
            }
            this.allSelected = allSelected;
            this.selectedRequests = data;
            this.isContextMenuOpen = data.length;
        },
        afterBulkAction() {
            this.isContextMenuOpen = false;
            this.$hub.$emit('reload-attendance-request-table')
        },
        getFilterValues(value) {
            this.filterValue = value;
        },
    },
}
