<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('payroll_settings')"/>
        <app-tab
            :tabs="tabs"
            icon="settings"
        />
    </div>
</template>

<script>
export default {
    name: "PayrollSettingLayout",
    data() {
        return {
            tabs: [
                {
                    name: this.$t('default_payrun'),
                    title: this.$t('default_payrun'),
                    component: "app-default-payrun-setting",
                    permission: this.$can('view_payroll_settings')
                },
                {
                    name: this.$t('badge_value'),
                    title: this.$t('badge_value'),
                    component: "app-badge-value-setting",
                    permission: this.$can('view_payroll_settings')
                },
                {
                    name: this.$t('manage_audience'),
                    title: this.$t('manage_audience'),
                    component: "app-payrun-audience-setting",
                    permission: this.$can('view_payroll_settings')
                },
                {
                    name: this.$t('payslip'),
                    title: this.$t('payslip'),
                    component: "app-payslip-setting",
                    permission: this.$can('view_payroll_settings')
                },
            ]
        }
    }
}
</script>

<style scoped>

</style>