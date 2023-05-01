<template>
    <app-overlay-loader v-if="loader"/>
    <app-payroll-summery
        v-else
        :first-user="JSON.stringify(employee)"
        :from-employee-details = "true"
    />
</template>

<script>
import {mapState} from "vuex";

export default {
    name: "EmployeePayslip",
    props: ['props'],
    computed: {
        ...mapState({
            employee: state => state.employees.employee,
            loader: state => state.loading,
        }),
    }
}
</script>

<style scoped>

</style>