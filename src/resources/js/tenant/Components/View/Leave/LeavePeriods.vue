<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('leave_period')" icon="briefcase">
            <app-default-button
                v-if="$can('create_leave_periods')"
                :title="$addLabel('leave_period')"
                @click="openDepartmentModal()"
            />
        </app-page-top-section>

        <app-table
            id="leave-period-table"
            :options="options"
            @action="triggerActions"
        />

        <app-leave-period-create-edit
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('leave-period-table')"
            @cancelled="cancelled"
        />
    </div>
</template>

<script>
import HelperMixin from "../../../../common/Mixin/Global/HelperMixin";
import DeleteMixin from "../../../../common/Mixin/Global/DeleteMixin";
import TriggerActionMixin from "../../../../common/Mixin/Global/TriggerActionMixin";
import LeavePeriodsMixins from "../../Mixins/LeavePeriodsMixins";

export default {
    name: "LeavePeriods",
    mixins: [HelperMixin, DeleteMixin, TriggerActionMixin, LeavePeriodsMixins],
    data(){
        return {
            isModalActive: false,
            selectedUrl: ''
        }
    },
    methods: {
        openDepartmentModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
        },
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${this.apiUrl.LEAVE_PERIODS}/${row.id}`;
                this.isModalActive = true;
            } else {
                this.getAction(row, action, active)
            }
        }
    }
}
</script>
