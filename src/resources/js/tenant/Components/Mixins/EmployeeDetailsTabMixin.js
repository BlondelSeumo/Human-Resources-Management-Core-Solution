export default {
    data(){
        return{
            jobDeskTabs: [
                {
                    'name': this.$t('leave_allowance'),
                    'title': this.$t('leave_allowance'),
                    'component': 'app-employee-allowance',
                    'props': {}
                },
                {
                    'name': this.$t('attendance'),
                    'title': this.$t('attendance'),
                    'component': 'app-employee-attendance',
                    'permission': !!this.$can('view_employees') &&
                        (this.$can('view_attendance_summary') || window.user.id === this.employeeId),
                    'props': {}
                },
                {
                    'name': this.$t('leave'),
                    'title': this.$t('leave'),
                    'component': 'app-employee-leave',
                    'permission': !!this.$can('view_employees'),
                    'props': {
                        'managerDept': this.managerDept
                    }
                },
                {
                    'name': this.$t('documents'),
                    'title': this.$t('documents'),
                    'component': 'app-employee-documents',
                    'permission': true,
                    'headerButton': {
                        'label': this.$t('add_new'),
                    },
                    'props': {
                        id: this.employeeId
                    }
                },
                {
                    'name': this.$t('assets'),
                    'title': this.$t('assets'),
                    'component': 'app-employee-company-asset',
                    'props': {
                        id: this.employeeId,
                    }
                },
                {
                    'name': this.$t('job_history'),
                    'title': this.$t('job_history'),
                    'component': 'app-employee-job-history',
                    'props': {
                        id: this.employeeId
                    }
                },
                {
                    'name': this.$t('salary_overview'),
                    'title': this.$t('salary_overview'),
                    'component': 'app-employee-salary-reviews',
                    'permission': !!this.$can('view_salary'),
                    'props': {
                        id: this.employeeId
                    }
                },
                {
                    'name': this.$t('payrun_and_badge'),
                    'title': this.$t('payrun_and_badge'),
                    'component': 'app-employee-payrun-and-badge',
                    'permission': window.user.id === this.employeeId || !!this.$can('view_payslips'),
                    'props': {
                        id: this.employeeId
                    }
                },
                {
                    'name': this.$t('payslip'),
                    'title': this.$t('payslip'),
                    'component': 'app-employee-payslip',
                    'permission': window.user.id === this.employeeId || !!this.$can('view_payroll_summery'),
                    'props': {
                        'managerDept': this.managerDept
                    }
                },
                {
                    'name': this.$t('address_details'),
                    'title': this.$t('address_details'),
                    'component': 'app-employee-address-details',
                    'props': {
                        id: this.employeeId
                    }
                },
                {
                    'name': this.$t('emergency_contacts'),
                    'title': this.$t('emergency_contacts'),
                    'component': 'app-employee-emergency-contact',
                    'props': {
                        id: this.employeeId
                    }
                },
                {
                    'name': this.$t('social_links'),
                    'title': this.$t('social_links'),
                    'component': 'app-employee-social-link',
                    'props': {
                        id: this.employeeId
                    }
                },
            ],
            employeeDetailsTabs: [
                {
                    'name': this.$t('personal_details'),
                    'title': this.$t('personal_details'),
                    'component': 'app-employee-personal-details',
                    'permission': true,
                    'props': {}
                },
                {
                    'name': this.$fieldTitle('password', 'change', true),
                    'title': this.$fieldTitle('password', 'change', true),
                    'component': 'app-employee-password-change',
                    'permission': window.user.id === this.employeeId,
                    'props': {}
                },
                {
                    'name': this.$t('activity_log'),
                    'title': this.$t('activity_log'),
                    'component': 'app-activity',
                    'permission': true,
                    'props': {
                        id: this.employeeId
                    }
                },
            ]
        }
    }
}