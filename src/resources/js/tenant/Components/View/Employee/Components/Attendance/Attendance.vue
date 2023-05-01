<template>
    <app-overlay-loader v-if="loader" />
    <app-attendance-summery
        v-else
        :first-user="JSON.stringify(employee)"
        :specific="true"
        :from-employee-details="true"
    />
</template>
<script>
    import {mapState} from "vuex";

    export default {
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