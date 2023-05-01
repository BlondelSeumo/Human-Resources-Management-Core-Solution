import {LEAVES} from "../../Config/ApiUrl";
import {DepartmentFilterMixin, UserFilterMixin, WorkingShiftFilterMixin} from "./FilterMixin";
import {leaveDurations} from "../View/Leave/Helper/Helper";

export default {
    mixins: [DepartmentFilterMixin, WorkingShiftFilterMixin, UserFilterMixin],
    data() {
        return {
            adminRequestId: '',
            isLeaveModalActive: false,
            isResponseLogModalActive: false,
            options: {
                showSearch: true,
                showFilter: true,
                filters: [
                    // {
                    //     title: this.$t('date_range'),
                    //     type: "range-picker",
                    //     key: "date_range",
                    //     option: ["today", "thisWeek", "next7Days", "nextMonth", "last7Days", "lastMonth", "thisYear"]
                    // },
                    {
                        title: this.$t('department'),
                        type: "multi-select-filter",
                        key: "departments",
                        manager: true,
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_departments')
                    },
                    {
                        title: this.$t('work_shift'),
                        type: "drop-down-filter",
                        key: "working_shifts",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_working_shifts')
                    },
                    {
                        title: this.$t('leave_duration'),
                        type: 'drop-down-filter',
                        key: 'leave_duration',
                        option: [
                            {
                                id: 'hours',
                                name: this.$t('hours')
                            },
                            {
                                id: 'single_day',
                                name: this.$t('single_day')
                            },
                            {
                                id: 'multi_day',
                                name: this.$t('multi_day')
                            },
                            {
                                id: 'first_half',
                                name: this.$t('first_half')
                            },
                            {
                                id: 'last_half',
                                name: this.$t('last_half')
                            },

                        ],
                        listValueField: 'name'
                    },
                    {
                        title: this.$t('users'),
                        type: "multi-select-filter",
                        key: "users",
                        option: [],
                        listValueField: 'full_name',
                        permission: !!this.$can('view_all_leaves')  && !!this.$can('view_users')
                    },
                ],
            },
            tableId: 'leave-status-table',
            tableOptions: {
                name: '',
                url: '',
                datatableWrapper: false,
                showHeader: true,
                responsive: true,
                tablePaddingClass: "px-0",
                showSearch: false,
                showFilter: false,
                tableShadow: false,
                columns: [
                    {
                        title: this.$t('profile'),
                        type: 'component',
                        className: 'd-inline-flex',
                        componentName: 'app-attendance-employee-media-object',
                        key: 'user',
                    },
                    {
                        title: this.$t('date_and_time'),
                        type: 'component',
                        componentName: 'app-leave-date-time',
                        key: 'start_at',
                    },
                    {
                        title: this.$t('leave_duration'),
                        type: 'custom-html',
                        key: 'start_at',
                        modifier: (value, row) => leaveDurations(row)
                    },
                    {
                        title: this.$t('leave_type'),
                        type: 'custom-html',
                        key: 'type',
                        modifier: (type) => type.name
                    },
                    {
                        title: this.$t('attachments'),
                        type: 'component',
                        componentName: 'app-attachments-column',
                        key: 'attachments',
                    },
                    {
                        title: this.$t('activity'),
                        type: 'component',
                        componentName: 'app-activity-column',
                        key: 'comments',
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action'
                    }
                ],
                filters: [],
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('response_log'),
                        type: 'modal',
                        component: 'app-leave-response-log-modal',
                        modalId: 'response-log-modal',
                        url: '',
                        name: 'response-log'
                    },
                    {
                        title: this.$t('cancel'),
                        name: 'canceled',
                        type: 'requestTableRow',
                        modalClass: 'warning',
                        modalSubtitle: this.$t('you_are_going_to_cancel_this_leave_request'),
                        modalIcon: 'check-circle',
                        modifier: (row) => (row.user_id != window.user.id || this.$isAdmin()) &&
                            this.$can('assign_leaves') && row.status.name === 'status_approved'
                    },
                ],
                rowLimit: 10,
                paginationType: "pagination",
            },
        }
    },
    methods: {
        openLeaveModal(adminRequest = false) {
            this.adminRequestId = adminRequest ? window.user.id : '';
            this.isLeaveModalActive = true;
        },
        openResponseLogModal() {
            this.isResponseLogModalActive = true;
        },
        setFilterValues(filterValue) {
            if (filterValue.date_range && typeof filterValue.date_range != 'string') {
                filterValue.date_range = JSON.stringify(filterValue.date_range)
            }
            this.$store.dispatch('updateFilterObject', filterValue)
        },
        setSearchValue(search) {
            this.$store.dispatch('updateFilterObject', {search: search})
        },
        triggerActions(row, action, active) {
            if (action.name === 'response-log') {
                this.logUrl = `${this.apiUrl.LEAVES}/${row.id}/log`
                this.isResponseLogModalActive = true;
            } else {
                this.getAction(row, action, active)
            }
        },
    },
}
