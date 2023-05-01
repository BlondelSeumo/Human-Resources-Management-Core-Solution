import FormHelperMixins from "../../../common/Mixin/Global/FormHelperMixins";
import {DepartmentFilterMixin, UserFilterMixin, WorkingShiftFilterMixin} from "./FilterMixin";

export default {
    mixins: [FormHelperMixins, DepartmentFilterMixin, WorkingShiftFilterMixin, UserFilterMixin],
    data() {
        return {
            adminRequestId: '',
            isLeaveModalActive: false,
            isResponseLogModalActive: false,
            logUrl : '',
            options: {
                showSearch: true,
                showFilter: true,
                filters: [
                    {
                        title: this.$t('department'),
                        type: "multi-select-filter",
                        key: "departments",
                        option: [],
                        manager: true,
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
        }
    },
    methods: {
        openLeaveModal(adminRequest = false) {
            this.adminRequestId = adminRequest ? window.user.id : '';
            this.isLeaveModalActive = true;
        },
        openResponseLogModal(leaveId) {
            this.isResponseLogModalActive = true;
            this.logUrl = `${this.apiUrl.LEAVES}/${leaveId}/log`
        },
        triggerActions(row, action, active) {
            if (action.name === 'response-log') {
                this.isResponseLogModalActive = true;
            } else {
                this.getAction(row, action, active)
            }
        },
    }
}
