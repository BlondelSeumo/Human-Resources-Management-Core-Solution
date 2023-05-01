<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('dashboard')">
            <template v-if="isTenantExist || tenant.is_single">
                <div class="d-flex">
                    <app-punch-in-out @open-modal="openPunchInModal"/>
                    <button class="btn btn-outline-primary ml-3"
                            v-if="adminSummaryPermissions || employeeStatisticsPermissions || attendancePermissions"
                            @click="isAdmin = !isAdmin">
                        {{ dashboardButtonLabel }}
                    </button>
                </div>
            </template>
        </app-page-top-section>

        <app-employee-dashboard v-if="!isAdmin"/>

        <app-admin-dashboard v-if="isAdmin"/>

        <punch-in-out-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :punch="punchStatus"
            @close="isModalActive = false"/>

    </div>
</template>

<script>

import AppPunchInOut from "../Attendance/Component/AppPunchInOut";
import PunchInOutModal from "../Attendance/Component/PunchInOutModal";

export default {
    name: "Dashboard",
    components: {PunchInOutModal, AppPunchInOut},
    data() {
        return {
            punchStatus: '',
            isModalActive: false,
            isAdmin: this.$isAdmin(),
        }
    },
    methods: {
        openPunchInModal(value) {
            this.punchStatus = value;
            this.isModalActive = true;
        },
    },
    computed: {
        isTenantExist() {
            return !!window.tenant && !window.tenant.is_single;
        },
        tenant() {
            return window.tenant || {};
        },
        dashboardButtonLabel() {
            return this.isAdmin ? this.$t('view_as_employee')
                : (this.$isAdmin() ? this.$t('view_as_admin') : this.$t('view_as_manager'));
        },
        adminSummaryPermissions() {
            return this.$can('view_employees') ||
                this.$can('view_departments') || this.$can('view_all_leaves')
        },
        employeeStatisticsPermissions() {
            return this.$can('view_employment_statuses') ||
                this.$can('view_designations') || this.$can('view_departments')
        },
        attendancePermissions() {
            return this.$can('view_all_attendance')
        }
    }
}
</script>

