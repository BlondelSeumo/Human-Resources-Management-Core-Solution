<template>
    <div>
        <app-table
            class="remove-datatable-x-padding"
            id="attendance-summery-table"
            :options="tableOptions"
            :filtered-data="filter"
            @action="triggerActions"
        />

        <app-attendance-create-edit-modal
            v-if="isAttendanceModalActive"
            v-model="isAttendanceModalActive"
            tableId="summary"
        />

        <app-attendance-edit-request-modal
            v-if="isEditModalActive"
            v-model="isEditModalActive"
            :selectedUrl="selectedUrl"
            tableId="summary"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            :message="modalSubtitle"
            :modal-class="modalClass"
            :icon="modalIcon"
            modal-id="app-confirmation-modal"
            @confirmed="updateStatus"
            @cancelled="cancelled"
        />

        <app-attendance-log-modal
            v-if="isAttendanceLogModalActive"
            v-model="isAttendanceLogModalActive"
            :url="changeLogUrl"
            tableId="summary"
        />
    </div>
</template>

<script>
import AttendanceRequestActionMixin from "../../../Mixins/AttendanceRequestActionMixin";
import AttendanceSummaryMixin from "../../../Mixins/AttendanceSummaryMixin";
import AttendanceHelperMixin from "../../../Mixins/AttendanceHelperMixin";

export default {
    name: "AttendanceSummaryTable",
    props: {
        specificEmployee: {},
        detailsId: {},
        attendanceId: {},
        user: {
            default: function () {
                return {}
            }
        }
    },
    mixins: [AttendanceRequestActionMixin, AttendanceSummaryMixin, AttendanceHelperMixin],
    data() {
        return {
            isAttendanceModalActive: false,
            selectedUrl: '',
            tableId: 'summary'
        }
    },
    created() {
        if (this.detailsId) {
            this.changeLogUrl = `${this.apiUrl.ATTENDANCES}/${this.detailsId}/log`;
            this.isAttendanceLogModalActive = true;
        }
        if(this.specificEmployee){
            this.tableOptions.paginationBlockClass = 'mt-primary px-0';
        }
    },
    computed: {
        filter() {
            const filter = this.$store.state.calendar.filter;
            return Object.keys(filter).reduce((object, key) => {
                if (filter[key]) {
                    object[key] = filter[key]
                }
                return object;
            }, {});
        }
    }
}
</script>

<style scoped>

</style>