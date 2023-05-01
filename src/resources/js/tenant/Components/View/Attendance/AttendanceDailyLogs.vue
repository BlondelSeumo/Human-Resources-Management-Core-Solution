<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('daily_log')">
            <div class="d-flex d-inline">
                <app-attendance-top-buttons :tableId="tableId"/>

                <div v-if="$can('import_attendances') || $can('export_attendance_daily_log')"
                     class="btn-group dropdown ml-2">
                    <button class="btn btn-primary rounded-right px-3" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        {{ $t('actions') }} <i class="fas fa-chevron-down fa-sm"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a v-if="$can('import_attendances')" class="dropdown-item"
                           :href="urlGenerator(`${apiUrl.IMPORT_FRONT_END}?tab=Attendance`)">
                            <app-icon name="upload" class="size-16 mr-1"/>
                            {{ $t('import_attendance') }}
                        </a>
                        <a v-if="$can('export_attendance_daily_log')" class="dropdown-item" href="#"
                           @click="viewConfirmationModal">
                            <app-icon name="download" class="size-16 mr-1"/>
                            {{ $t('export_attendance') }}
                        </a>
                    </div>
                </div>
            </div>
        </app-page-top-section>
        <app-table
            :id="tableId"
            :options="options"
            @action="triggerActions"
            @getFilteredValues="setQueryObject"
        />

        <app-attendance-create-edit-modal
            v-if="isAttendanceModalActive"
            v-model="isAttendanceModalActive"
        />

        <app-attendance-edit-request-modal
            v-if="isEditModalActive"
            v-model="isEditModalActive"
            :selectedUrl="selectedUrl"
            :tableId="tableId"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            :message="modalSubtitle"
            :modal-class="modalClass"
            :icon="modalIcon"
            modal-id="app-confirmation-modal"
            @confirmed="updateStatus()"
            @cancelled="cancelled"
        />

        <app-attendance-log-modal
            v-if="isAttendanceLogModalActive"
            v-model="isAttendanceLogModalActive"
            :url="changeLogUrl"
            :tableId="tableId"
        />

        <app-confirmation-modal
            v-if="exportConfirmationModal"
            :title="$t('attendance_export_title')"
            :message="$t('attendance_export_message')"
            modal-id="app-confirmation-modal"
            modal-class="primary"
            icon="download"
            :first-button-name="$t('export')"
            :second-button-name="$t('cancel')"
            @confirmed="exportFilteredAttendance()"
            @cancelled="exportConfirmationModal = false"
            :self-close="false"
        />

        <app-confirmation-modal
            v-if="noDataFoundModal"
            :message="this.$t('please_filter_the_attendance_data_first')"
            :title="this.$t('no_attendance_data_found')"
            :second-button-name="$t('okay')"
            :hide-first-button="true"
            modal-class="warning"
            icon="filter"
            modal-id="app-confirmation-modal"
            @cancelled="noDataFoundModal = false"
            :self-close="false"
        />

    </div>
</template>

<script>
import AttendanceDailyLogMixin from "../../Mixins/AttendanceDailyLogMixin";
import AppAttendanceTopButtons from "./Component/AppAttendanceTopButtons";
import AttendanceRequestActionMixin from "../../Mixins/AttendanceRequestActionMixin";
import {urlGenerator} from "../../../../common/Helper/AxiosHelper";
import {formatDateForServer, localTimeZone} from "../../../../common/Helper/Support/DateTimeHelper";
import {objectToQueryString} from "../../../../common/Helper/Support/TextHelper";

export default {
    name: "DailyLog",
    mixins: [AttendanceDailyLogMixin, AttendanceRequestActionMixin],
    components: {AppAttendanceTopButtons},
    data() {
        return {
            urlGenerator,
            tableId: 'daily-log',
            isAttendanceModalActive: false,
            selectedUrl: '',
            exportConfirmationModal: false,
            noDataFoundModal: false,
            queryObject: {}
        }
    },
    methods: {
        openAttendanceModal() {
            this.isAttendanceModalActive = true;
        },
        viewConfirmationModal() {
            if (this.tableData < 1) {
                this.noDataFoundModal = true;
            } else {
                this.exportConfirmationModal = true;
            }
        },
        exportFilteredAttendance() {
            let query = _.isEmpty(this.queryObject) ? `date=${formatDateForServer(new Date())}` : objectToQueryString(this.queryObject);
            window.location = urlGenerator(`${this.apiUrl.EXPORT}/attendance/daily-log?${query}&timeZone=${localTimeZone}`);
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.exportConfirmationModal = false;
        },
        setQueryObject(obj) {
            this.queryObject = obj;
        }
    }
}
</script>