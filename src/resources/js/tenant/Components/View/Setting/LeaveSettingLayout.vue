<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('leave_settings')"/>
        <app-tab
            :tabs="tabs"
            icon="settings"
        />
    </div>
</template>

<script>
export default {
    name: "LeaveSettingLayout",
    data() {
        return {
            tabs: [
                {
                    headerButton: this.$can('create_leave_types') && {
                        "label": this.$fieldTitle('add', 'leave_type', true),
                    },
                    name: this.$t('leave_type'),
                    title: this.$t('leave_type'),
                    component: "app-leave-types-setting",
                    permission: this.$can('view_leave_settings')
                },
                {
                    name: this.$t('allowance_policy'),
                    title: this.$t('allowance_policy'),
                    component: "app-leave-allowance-policy",
                    permission: this.$can('manage_leave_allowance_policy')
                },
                {
                    name: this.$t('approval'),
                    title: this.$t('approval'),
                    component: "app-leave-approval-setting",
                    permission: this.$can('manage_leave_approval')
                },
            ]
        }
    }
}
</script>

<style scoped>

</style>