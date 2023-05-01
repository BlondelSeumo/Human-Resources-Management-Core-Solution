import {LEAVES} from "../../Config/ApiUrl";
import {leaveDurations, leaveRequestStatusBuild} from "../View/Leave/Helper/Helper";

export default {
    data() {
        return {
            adminRequestId: '',
            tableId: 'leave-summery-table',
            isLeaveModalActive: false,
            isResponseLogModalActive: false,
            options: {
                name: '',
                url: '',
                datatableWrapper: true,
                showHeader: true,
                responsive: true,
                tablePaddingClass: "px-0",
                showSearch: false,
                showFilter: false,
                tableShadow: false,
                tableData: null,
                afterRequestSuccess: ({data}) => {
                    this.tableData = data.data;
                },
                columns: [
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
                        type: 'object',
                        key: 'type',
                        modifier: (value) => {
                            return value.name
                        }
                    },
                    {
                        title: this.$t('attachments'),
                        type: 'component',
                        componentName: 'app-attachments-column',
                        key: 'attachments',
                    },
                    {
                        title: this.$t('status'),
                        type: 'custom-html',
                        key: 'status',
                        modifier: (value, row) => {
                            let status = value;
                            if(value.name === 'status_pending') {
                                status = leaveRequestStatusBuild(this.managerDept, row.last_review) || value
                            }
                            return `<span class="badge badge-pill badge-${status.class}">
                                ${status.translated_name}
                            </span>`
                        }
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
                        modifier: (row) => ((row.user_id !== window.user.id || this.$isAdmin()) &&
                            this.$can('assign_leaves') && row.status.name === 'status_approved') ||
                            (row.status.name === 'status_pending' && row.user_id == window.user.id)
                    },
                ],
                rowLimit: 10,
                paginationType: "pagination"
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
    }
}