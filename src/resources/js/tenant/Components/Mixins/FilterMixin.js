// Department Filter
import {collection} from "../../../common/Helper/helpers";
import {axiosGet} from "../../../common/Helper/AxiosHelper";

export const DepartmentFilterMixin = {
    computed: {
        departments() {
            return this.$store.state.departments.selectable;
        }
    },

    watch: {
        'departments.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'departments') option.push(...this.departments)
                })
            },
            immediate: true
        }
    },

    created() {
        let filter = collection(this.options.filters?.filter(filter => filter.key === 'departments')).first();
        if (this.$can('view_departments')) {
            this.$store.dispatch('getSelectableDepartments', filter.manager ? filter.manager : false)
        }
    }
}

// Designation Filter
export const DesignationFilterMixin = {
    computed: {
        designations() {
            return this.$store.state.designations.selectable;
        }
    },

    watch: {
        'designations.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'designations') option.push(...this.designations)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_designations')) {
            this.$store.dispatch('getSelectableDesignations')
        }
    }
}

// WorkingShift Filter
export const WorkingShiftFilterMixin = {
    computed: {
        workingShifts() {
            return this.$store.state.working_shifts.selectable;
        }
    },

    watch: {
        'workingShifts.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'working_shifts') option.push(...this.workingShifts)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_working_shifts')) {
            this.$store.dispatch('getSelectableWorkingShifts')
        }
    }
}

//Roles Filter
export const RoleFilterMixin = {
    computed: {
        roles() {
            return this.$store.state.roles.selectable;
        }
    },

    watch: {
        'roles.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'roles') option.push(...this.roles)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_roles')) {
            this.$store.dispatch('getSelectableRoles', {alias: 'tenant'})
        }
    }
}

//Employment Status Filter
export const EmploymentStatusFilterMixin = {
    computed: {
        employmentStatuses() {
            return this.$store.state.employment_statuses.selectable;
        }
    },

    watch: {
        'employmentStatuses.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'employment_statuses') option.push(...this.employmentStatuses)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_employment_statuses')) {
            this.$store.dispatch('getSelectableEmploymentStatuses')
        }
    }
}


//Users Filter
export const UserFilterMixin = {
    methods:{
        getUsers(){
            axiosGet(this.apiUrl.TENANT_SELECTABLE_USER).then(({data})=>{
                this.options.filters.find(fl => fl.key === 'users' && fl.type === 'multi-select-filter').option = data;
            })
        }
    },
    created() {
        this.options.filters.find(fl => fl.key === 'users' && fl.type === 'multi-select-filter').permission ?
        this.getUsers() : null;
    }
}