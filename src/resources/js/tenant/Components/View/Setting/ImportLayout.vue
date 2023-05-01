<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('import')"/>
        <app-tab
            :tabs="tabs"
            icon="settings"
        />
    </div>
</template>

<script>
export default {
    name: "ImportLayout",
    data() {
        return {
            tabs: [
                {
                    name: this.$t('employees'),
                    title: this.$t('import_employee'),
                    component: "app-import-employees",
                    permission: !!this.$can('import_employees')
                },
                {
                    name: this.$t('attendance'),
                    title: this.$t('import_attendance'),
                    component: "app-import-attendances",
                    permission: this.$can('import_attendances')
                },
            ]
        }
    }
}
</script>

<style scoped>

</style>