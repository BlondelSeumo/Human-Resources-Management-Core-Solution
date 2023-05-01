import HelperMixin from "../../../common/Mixin/Global/HelperMixin";
import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import AppFunction from "../../../core/helpers/app/AppFunction";
import {EMPLOYEES, EMPLOYEES_PROFILE} from "../../Config/ApiUrl";
import {
    DepartmentFilterMixin,
    DesignationFilterMixin,
    WorkingShiftFilterMixin,
    RoleFilterMixin,
    EmploymentStatusFilterMixin
} from "./FilterMixin"

import {axiosDelete, axiosPatch, urlGenerator} from "../../../common/Helper/AxiosHelper";
import {TENANT_USERS} from "../../../common/Config/apiUrl";
import {calenderTime} from "../../../common/Helper/Support/DateTimeHelper";

export default {
    mixins: [HelperMixin, DatatableHelperMixin, DepartmentFilterMixin, DesignationFilterMixin, WorkingShiftFilterMixin, RoleFilterMixin, EmploymentStatusFilterMixin],
    data() {
        return {
            isModalActive: false,
            isEmployeeCreateOpenModalActive: false,
            employmentStatusModalActive: false,
            isTerminationReasonModalActive: false,
            promptIcon: '',
            promptTitle: '',
            promptMessage: '',
            modalClass: '',
            confirmationModalActive: false,
            promptAction: '',
            tenant: '',
            selectedUrl: '',
            employeeId: '',
            loading: false,
            attendanceModalActive: false,
            leaveModalActive: false,
            isJobHistoryEditModalActive: false,
            modalAction: '',
            options: {
                name: this.$allLabel('employee'),
                url: EMPLOYEES,
                showCount: true,
                showClearFilter: true,
                responsive: true,
                cardViewComponent: 'app-employee-card-view',
                enableRowSelect: true,
                columns: [
                    {
                        title: this.$t('profile'),
                        type: 'component',
                        componentName: 'app-employee-media-object',
                        key: 'image',
                        modifier: (value, row) => {
                            return row.icon ? `${AppFunction.getBaseUrl()}/${row.icon.value.split('/').filter(d => d).join('/')}` : '';
                        }
                    },
                    {
                        title: this.$t('employee_id'),
                        type: 'object',
                        key: 'profile',
                        isVisible: true,
                        modifier: (value, row) => {
                            return value ? value.employee_id : '-';
                        }
                    },
                    {
                        title: this.$t('employment_status'),
                        type: 'component',
                        componentName: 'app-employee-status',
                        key: 'employment_status',
                        isVisible: true,
                    },
                    {
                        title: this.$t('department'),
                        type: 'custom-html',
                        key: 'department',
                        isVisible: true,
                        modifier: (department) => {
                            return department ? department.name ? department.name : '-' : '-';
                        }
                    },
                    {
                        title: this.$t('work_shift'),
                        type: 'object',
                        key: 'working_shift',
                        isVisible: true,
                        modifier: (work_shift, row) => {
                            return work_shift ? work_shift.name ? work_shift.name : '-' : '-';
                        }
                    },
                    {
                        title: this.$t('joining_date'),
                        type: 'object',
                        key: 'profile',
                        isVisible: true,
                        modifier: (value, row) => {
                            return value && value.joining_date ? calenderTime(value.joining_date, false) : this.$t('not_yet_joined');
                        }
                    },
                    {
                        title: this.$t('salary'),
                        type: 'object',
                        key: 'salary',
                        isVisible: true,
                        modifier: (salary, row) => {
                            return salary && salary.amount ? salary.amount : '-';
                        }
                    },
                    {
                        title: this.$t('role'),
                        type: 'object',
                        key: 'roles',
                        isVisible: true,
                        modifier: (roles) => {
                            let roleName = '';
                            roles.forEach((element, index) => {
                                roleName += index > 0 ? ', ' + element.name : element.name;
                            });
                            roleName = roleName ? roleName : '-';
                            return roleName;
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action'
                    }
                ],
                filters: [
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('joining_date'),
                        key: "joining_date",
                        type: "range-picker",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('designation'),
                        type: "multi-select-filter",
                        key: "designations",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_designations')
                    },
                    {
                        title: this.$t('employment_status'),
                        type: "multi-select-filter",
                        key: "employment_statuses",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_employment_statuses')
                    },
                    {
                        title: this.$t('department'),
                        type: "multi-select-filter",
                        key: "departments",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_departments')
                    },
                    {
                        title: this.$t('work_shift'),
                        type: "multi-select-filter",
                        key: "working_shifts",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_working_shifts')
                    },
                    {
                        title: this.$t('role'),
                        type: "multi-select-filter",
                        key: "roles",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_roles')
                    },
                    {
                        title: this.$t('gender'),
                        type: "checkbox",
                        key: "gender",
                        option: [
                            {id: 'male', value: this.$t('male')},
                            {id: 'female', value: this.$t('female')},
                            {id: 'other', value: this.$t('other')}
                        ],
                        permission: !!this.$can('view_employees')
                    },
                    {
                        title: this.$t("salary"),
                        type: "range-filter",
                        key: "salary",
                        minTitle: this.$t("minimum_salary"),
                        maxTitle: this.$t("maximum_salary"),
                        maxRange: 100,
                        minRange: 0
                    }
                ],
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('view_details'),
                        key: "view_details",
                    },
                    {
                        title: this.$t('edit'),
                        url: EMPLOYEES,
                        key: "edit",
                        modifier: row => {
                            if (row.employment_status && row.employment_status.alias === 'terminated') {
                                return false;
                            }

                            return this.$can('update_employees') && (row.id != window.user.id || this.$isAdmin());
                        },
                    },
                    {
                        title: this.$t('add_attendance'),
                        key: "add_attendance",
                        modifier: (row) => this.$can('update_attendance_status')
                            && row.employment_status?.alias !== 'terminated'
                            && row.status.name !== 'status_inactive'
                    },
                    {
                        title: this.$t('assign_leave'),
                        key: "assign_leave",
                        modifier: (row) => this.$can('assign_leaves')
                            && row.employment_status?.alias !== 'terminated'
                            && row.status.name !== 'status_inactive'
                    },
                    {
                        title: this.$t('cancel_invitation'),
                        key: "cancel_invitation",
                        icon: 'x-circle',
                        modalClass: 'danger',
                        modifier: row => {
                            if (row.status) {
                                return row.status.name === 'status_invited';
                            }
                        },
                    },
                    {
                        title: this.$t('add_salary'),
                        key: 'add_or_edit_salary',
                        modifier: row => this.$can('add_salary') && !row.updated_salary &&
                            (row.employment_status && row.employment_status.alias !== 'terminated'),
                    },
                    {
                        title: this.$t('edit_salary'),
                        key: 'add_or_edit_salary',
                        modifier: row => this.$can('edit_salary') && row.updated_salary &&
                            (row.employment_status && row.employment_status.alias !== 'terminated'),
                    },
                    {
                        title: this.$t('add_joining_date'),
                        key: 'add_joining_date',
                        modifier: row => this.$can('update_employee_job_history') && !row.profile?.joining_date &&
                            (row.employment_status && row.employment_status.alias !== 'terminated'),
                    },
                    {
                        title: this.$t('edit_joining_date'),
                        key: 'add_joining_date',
                        modifier: row => this.$can('update_employee_job_history') && row.profile?.joining_date &&
                            (row.employment_status && row.employment_status.alias !== 'terminated'),
                    },
                    {
                        title: this.$t('terminate'),
                        key: "terminate",
                        icon: 'log-out',
                        modalClass: 'danger',
                        modifier: row => {
                            if (row.employment_status) {
                                return row.status.name !== 'status_invited' &&
                                    row.employment_status.alias !== 'terminated' && row.id != window.user.id;
                            }
                        },
                    },
                    {
                        title: this.$t('rejoining'),
                        key: "rejoining",
                        icon: 'log-in',
                        modalClass: 'primary',
                        modifier: row => {
                            if (row.employment_status) {
                                return row.employment_status.alias === 'terminated';
                            }
                        },
                    },
                    {
                        title: this.$t('remove_from_employee_list'),
                        key: 'remove_from_employee',
                        modifier: row => parseInt(row.is_in_employee) && !this.$isOnlyDepartmentManager() &&
                            (row.id !== window.user.id || this.$isAdmin())
                    }
                ],
                rowLimit: 10,
                paginationType: "pagination"
            },
            selectedEmployees: [],
            isContextMenuOpen: false,
            employee: {},
        }
    },
    watch: {
        isModalActive: function (value) {
            if (!value) {
                this.selectedUrl = '';
            }
        }
    },
    methods: {
        getSelectedRows(data, isSelectedAll) {
            this.selectedEmployees = data;
            this.isContextMenuOpen = data.length;
        },
        triggerActions(row, action, active) {
            this.promptIcon = action.icon;
            this.employeeId = row.id;
            this.modalClass = action.modalClass;

            if (action.key === 'view_details') {
                window.location = urlGenerator(`${EMPLOYEES_PROFILE}/${this.employeeId}/profile`);
            }

            if (action.key === 'edit') {
                this.selectedUrl = `${action.url}/${row.id}`;
                this.isModalActive = true;
            }

            if (action.key === 'cancel_invitation') {
                this.confirmationModalActive = true;
                this.promptAction = action.key;
                this.promptTitle = this.$t('are_you_sure');
                this.promptMessage = this.$t('you_are_going_to_cancel_an_invitation');
            }

            if (action.key === 'terminate') {
                this.confirmationModalActive = true;
                this.promptAction = action.key;
                this.promptTitle = this.$t('are_you_sure');
                this.promptMessage = this.$t('you_are_going_to_terminate_an_employee');
            }

            if (action.key === 'rejoining') {
                this.confirmationModalActive = true;
                this.promptAction = action.key;
                this.promptTitle = this.$t('are_you_sure');
                this.promptMessage = this.$t('you_are_permitting_an_employee_for_re_joining');
            }

            if (action.key === 'remove_from_employee') {
                this.confirmationModalActive = true;
                this.promptAction = action.key;
                this.promptTitle = this.$t('are_you_sure');
                this.promptMessage = this.$t('you_are_going_to_remove_an_employee_from_list');
            }

            if (action.key === 'assign_leave') {
                this.leaveModalActive = true;
                this.employee = row;
            }

            if (action.key === 'add_attendance') {
                this.attendanceModalActive = true;
                this.employee = row;
            }

            if (action.key === 'add_joining_date') {
                this.isJobHistoryEditModalActive = true;
                this.employee = row;
                this.modalAction = 'joining_date';
            }

            if (action.key === 'add_or_edit_salary') {
                this.isJobHistoryEditModalActive = true;
                this.employee = row;
                this.modalAction = 'salary';
            }
        },
        triggerConfirm() {
            if (this.promptAction === 'cancel_invitation') {
                this.cancelInvitation();
            }
            if (this.promptAction === 'terminate') {
                this.terminate();
            }
            if (this.promptAction === 'rejoining') {
                this.rejoining();
            }
            if (this.promptAction === 'remove_from_employee') {
                this.removeEmployee();
            }
        },
        closeConfirmation() {
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.loading = false;
            this.confirmationModalActive = false;
        },
        cancelInvitation() {
            this.loading = true;
            axiosDelete(`${EMPLOYEES}/${this.employeeId}/cancel-invitation`).then(({data}) => {
                this.confirmationModalActive = false;
                this.toastAndReload(data.message, 'employee-table');
            }).catch(({data}) => {
                this.confirmationModalActive = false;
                this.toastAndReload(data.message, 'employee-table');
            }).finally(() => this.closeConfirmation());
        },
        terminate() {
            this.loading = true;
            axiosPatch(`${EMPLOYEES}/${this.employeeId}/terminate`).then(({data}) => {
                this.confirmationModalActive = false;
                this.toastAndReload(data.message, 'employee-table');
                setTimeout(() => {
                    this.isTerminationReasonModalActive = true;
                })
            }).catch(({data}) => {
                this.confirmationModalActive = false;
                this.toastAndReload(data.message, 'employee-table');
            }).finally(() => this.closeConfirmation());
        },
        rejoining() {
            this.confirmationModalActive = false;
            this.employmentStatusModalActive = true;
        },
        reloadEmployeeTable() {
            this.$hub.$emit('reload-employee-table');
        },
        removeEmployee() {
            this.loading = true;
            axiosPatch(`${TENANT_USERS}/${this.employeeId}/remove-from-employee`)
                .then(({data}) => {
                    this.confirmationModalActive = false;
                    this.toastAndReload(data.message, 'employee-table')
                }).catch(({response}) => {
                this.toastException(response.data)
                this.confirmationModalActive = false;
            }).finally(() => this.closeConfirmation());
        }
    }
}
