<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('attendance_settings')"/>
        <app-tab
            :tabs="tabs"
            icon="settings"
        />
    </div>
</template>

<script>
export default {
    name: "AttendanceSettingLayout",
    data() {
        return {
            tabs: [
                {
                    name: this.$t('preference'),
                    title: this.$t('preference'),
                    component: "app-attendance-preference-settings",
                    permission: this.$can('view_attendance_settings')
                },
                {
                    name: this.$t('definitions'),
                    title: this.$t('definitions'),
                    component: "app-attendance-definition-settings",
                    props: {alias: 'app'},
                    permission: this.$can('view_attendance_settings')
                },
                {
                    name: this.$t('geolocation_and_ip'),
                    title: this.$t('geolocation_and_ip'),
                    component: "app-attendance-geolocation-settings",
                    props: {alias: 'app'},
                    permission: this.$can('view_attendance_settings')
                }
            ]
        }
    }
}
</script>

<style scoped>

</style>