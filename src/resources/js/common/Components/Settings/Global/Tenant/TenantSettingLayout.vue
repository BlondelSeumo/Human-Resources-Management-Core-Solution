<template>
    <div class="content-wrapper">
        <app-page-top-section
                :title="$fieldTitle('tenant', 'settings', true)"
                :directory="$t('settings')"
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
        name: "SettingLayout",
        props: {
            settingsPermissions: {
                required: true,
            }
        },
        data() {
            const permissions = JSON.parse(this.settingsPermissions);
            return {
                tabs: [
                    {
                        name: this.$fieldTitle('custom', 'fields', true),
                        title: this.$t('custom_fields'),
                        component: "tenant-custom-field-settings",
                        permission: permissions.custom_fields,
                        headerButton: {
                            label: this.$fieldTitle('add', 'custom_field', true),
                            class: 'btn btn-primary',
                        }
                    },
                    {
                        name: this.$t('notification'),
                        title: this.$t('notification'),
                        component: "app-notification-settings",
                        props: {alias: 'tenant'},
                        permission: permissions.notification
                    },
                ]
            }
        }
    }
</script>
