<template>
    <div class="content-wrapper">
        <app-page-top-section
            :title="$t('app_settings')"
            icon="settings"
        />

        <app-tab
            :tabs="tabs"
            icon="settings"
        />
    </div>
</template>

<script>
export default {
    name: "TenantSettingLayout",
    data() {
        return {
            tabs: [
                {
                    name: this.$t('general'),
                    title: this.$t('general'),
                    component: "app-tenant-general-settings",
                    permission: this.$can('view_settings')
                },
                {
                    name: this.$t('modules'),
                    title: this.$t('modules'),
                    component: "app-modules-settings",
                    permission: this.$can('view_settings')
                },
                {
                    name: this.$t('cron_job'),
                    title: this.$t('cron_job'),
                    component: "app-cron-job-settings",
                    props: {alias: 'tenant'},
                },
                {
                    name: this.$t('email_setup'),
                    title: this.$t('email_setup'),
                    component: "app-delivery-settings",
                    props: {alias: 'app'},
                    permission: Boolean(this.$can('view_delivery_settings') && Boolean(tenant.is_single))
                },
                // {
                //     name: this.$fieldTitle('custom', 'fields', true),
                //     title: this.$fieldTitle('custom', 'fields', true),
                //     component: "tenant-custom-field-settings",
                //     permission: this.$can('view_custom_fields'),
                //     props: { alias: Boolean(tenant.is_single) ? 'app' : 'tenant' },
                //     headerButton: {
                //         label: this.$fieldTitle('add', 'custom_field', true),
                //         class: 'btn btn-primary',
                //     }
                // },
                {
                    name: this.$t('notification'),
                    title: this.$t('notification'),
                    component: "app-tenant-notification-settings",
                    permission: this.$can('view_notification_settings'),
                    props: { specific: !Boolean(tenant.is_single), alias: 'tenant' }
                },
                // {
                //     name: this.$t('import'),
                //     title: this.$t('import_from_database'),
                //     component: "app-import-database",
                //     props: {alias: 'tenant'},
                //     permission: this.$isAdmin(),
                //     headerButton: {
                //         label: this.$fieldTitle('start', 'import', true),
                //         class: 'btn btn-primary',
                //     }
                // },
                {
                    name: this.$t('update'),
                    title: this.$t('update'),
                    component: "app-update",
                    permission: this.$isAdmin(),
                },
            ]
        }
    }
}
</script>

<style scoped>

</style>
