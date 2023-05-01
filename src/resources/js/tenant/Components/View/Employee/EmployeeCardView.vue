<template>
    <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-primary" v-if="data.length > 0"
             v-for="(employee, rowIndex) in data" :key="'row'+rowIndex">
            <app-employee-preview-card
                :id="'employee'"
                :employee="employee"
                :actions="actions"
                @action-employee="triggerActions"
            />
        </div>
    </div>
</template>

<script>

export default {
    name: "EmployeeCardView",
    props: {
        data: {
            type: Array,
            required: true
        },
        actions: {
            type: Array
        }
    },
    methods: {
        triggerActions(row, action, active) {
            this.$emit('getCardAction', row, action, active);
        }
    }
}
</script>
