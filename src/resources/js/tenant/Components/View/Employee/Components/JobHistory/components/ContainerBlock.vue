<template>
    <div class="d-flex timeline mx-lg-5 mb-5rem">
        <div class="text-right timeline-title">
            <p class="mb-0">{{ title }}</p>
            <span v-if="updatePermission" @click="$emit('openModal', name)" class="font-size-90 primary-text-color cursor-pointer">{{ $t('change')}}</span>
        </div>
        <div class="d-flex">
            <div class="timeline-icon mx-3">
                <div class="svg-wrapper">
                    <app-icon :name="icon"/>
                </div>
            </div>

            <div v-if="name === 'joining_date' || name === 'role'" class="timeline-content">
                <block-content :data="values" :name="name"/>
            </div>

            <div v-else class="timeline-content">
                <template v-for="value in values">
                    <block-content
                        :data="value"
                        :name="name"/>
                </template>
                <block-content
                    v-if="(typeof this.data === 'object' && Object.keys(this.data).length === 0)"
                    :data="values"
                    :name="name"/>
            </div>
        </div>

    </div>
</template>

<script>

import BlockContent from "./BlockContent";

export default {
    name: "JobHistoryBlock",
    components: {BlockContent},
    props: {
        title: {},
        data: {},
        name: {},
        icon: {},
        employeeId: {}
    },
    computed: {
        values(){
            return this.data;
        },
        updatePermission(){
            let permission = {
                'department': 'move_department_employees',
                'work_shift': 'view_working_shifts',
                'designation': 'view_designations',
                'employment_status': 'view_employment_statuses',
                'role': 'attach_roles_users',
            }[this.name]

            return this.$can('update_employee_job_history') && ((this.$can(permission) || this.name === 'joining_date') &&
                (this.employeeId !== window.user.id || this.$isAdmin()))
        }
    }
}
</script>

<style scoped>

</style>