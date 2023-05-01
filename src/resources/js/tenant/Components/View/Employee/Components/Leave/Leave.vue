<template>
    <app-overlay-loader v-if="loader" />
    <app-leave-summary
        v-else
        :first-user="JSON.stringify(employee)"
        :specific="true"
        :manager-dept="this.props.managerDept"
        :from-employee-details = "true"
    />
</template>

<script>
    import {mapState} from "vuex";

    export default {
        name: 'EmployeeLeave',
        props: ['props'],
        data() {
            return {

            }
        },
        computed: {
            ...mapState({
                employee: state => state.employees.employee,
                loader: state => state.loading,
            }),
        }
    }
</script>