<template>
    <div class="px-primary">
        <div class="row justify-content-end">
            <div class="col-12 col-md-11 col-lg-10">
                <div class="py-5 px-5 px-md-0">
                    <div class="d-flex align-items-center mb-5">
                        <div class="mr-3">
                            <app-icon name="layers" class="size-35 text-muted"/>
                        </div>
                        <div>
                            <p class="mb-0">{{ $t('employees_today_entries', {length: data.details.length}) }}</p>
                            <p class="mb-0 text-muted">
                                <span class="text-muted">{{ $t('total_approved_hours') }}</span>
                                {{ countTotalHours() }}
                            </p>
                        </div>
                    </div>
                    <div v-for="(details, index) in data.details">
                        <div class="row align-items-center mb-5">
                            <div class="col-8 col-md-5 mb-4 mb-md-0">
                                <div class="d-flex align-items-center">
                                    <div class="text-warning d-flex align-items-center mr-3">
                                        <app-icon name="corner-down-right" class="mr-1"/>
                                        {{ ordinal_suffix_of(index, data.details.length) }}
                                    </div>
                                    <div>
                                        <div class="mb-1">
                                            <span class="text-muted">{{ $t('in_time') }}</span>
                                            {{ dateTimeFormat(details.in_time) }}
                                            <app-punch-geolocation-data
                                                v-if="details.in_ip_data"
                                                :details="details"
                                            />
                                            <app-note-editor
                                                v-if="Object.keys(getComment(details, 'punch-in')).length"
                                                :id="getComment(details, 'punch-in').id"
                                                :row-data="getComment(details, 'punch-in')"
                                                :note-title="$t(getComment(details, 'punch-in').type)"
                                                :note-description="getComment(details, 'punch-in').comment"
                                                :url="`${apiUrl.ATTENDANCE_NOTES}/${getComment(details, 'punch-in').id}`"
                                                :edit-permission="$can('update_attendance_notes') && Number(user.id ) === Number(getComment(details, 'punch-in').user_id)"
                                            />
                                        </div>
                                        <p class="mb-0">
                                            <span class="text-muted">{{ $t('out_time') }}</span>
                                            {{ dateTimeFormat(details.out_time) || $t('not_yet') }}
                                            <app-punch-geolocation-data
                                                v-if="details.out_ip_data"
                                                :details="details"
                                                key-data="out_ip_data"
                                            />
                                            <app-note-editor
                                                v-if="Object.keys(getComment(details, 'punch-out')).length"
                                                :id="getComment(details, 'punch-out').id"
                                                :row-data="getComment(details, 'punch-out')"
                                                :note-title="$t(getComment(details, 'punch-out').type)"
                                                :note-description="getComment(details, 'punch-out').comment"
                                                :url="`${apiUrl.ATTENDANCE_NOTES}/${getComment(details, 'punch-in').id}`"
                                                :edit-permission="$can('update_attendance_notes') && Number(user.id ) === Number(getComment(details, 'punch-out').user_id)"
                                            />
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-md-3 mb-4 mb-md-0">
                                <p class="text-right text-md-left mb-1">
                                    <span class="text-muted">{{ $t('type') }}</span>
                                    {{
                                        details.review_by ||
                                        (details.status && details.status.name === 'status_pending')
                                            ? $t('manual') : $t('auto')
                                    }}
                                    <app-attendance-type
                                        :hide-type="true"
                                        :value="[details]"
                                        :table-id="tableId"
                                    />
                                </p>
                                <p class="text-right text-md-left mb-0">
                                    <span class="text-muted">{{ $t('total_hours') }}</span>
                                    {{ countHours(details) }}
                                </p>
                            </div>
                            <div class="col-8 col-md-2" v-if="tableId === 'attendance-request-table'">
                            <span class="badge badge-lg rounded-pill"
                                  :class="['badge-' + details.status.class]">
                                {{ (details.status.translated_name) }}
                            </span>
                            </div>
                            <div class="col-4 col-md-2 text-right">
                                <dropdown-action
                                    v-if="tableId === 'attendance-request-table'"
                                    :key="`dropdown${details.id}`"
                                    :actions="actions"
                                    :unique-key="details.id"
                                    :row-data="details"
                                    @action="triggerActions"
                                />

                                <default-action
                                    v-else
                                    :key="`default${details.id}`"
                                    :actions="actions"
                                    :unique-key="details.id"
                                    :row-data="details"
                                    @action="triggerActions"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        </div>
    </div>
</template>

<script>
import {convertSecondToHourMinutes, dateTimeFormat} from "../../../../../../common/Helper/Support/DateTimeHelper";
import AttendanceHelperMixin from "../../../../Mixins/AttendanceHelperMixin";
import DropdownAction from "../../../../../../core/components/datatable/DropdownAction";
import DefaultAction from "../../../../../../core/components/datatable/DefaultAction";
import DateTimeWithNote from "./DateTimeWithNote";
import MemoizeMixins from "../../../../../../common/Helper/Support/MemoizeMixins";
import AttendanceRequestActionMixin from "../../../../Mixins/AttendanceRequestActionMixin";
import {filterLastPendingData} from "../../Helper/Helper";

export default {
    name: 'ExpandableColumn',
    components: {DateTimeWithNote, DefaultAction, DropdownAction},
    mixins: [AttendanceHelperMixin, MemoizeMixins, AttendanceRequestActionMixin],
    props: {
        data: {},
        tableId: {}
    },
    data() {
        return {
            dateTimeFormat
        }
    },
    computed: {
        dailyLogActions() {
            return [
                {
                    name: 'edit',
                    title: this.$t('edit'),
                    icon: 'edit',
                    modifier: (row) => row.status?.name !== 'status_pending' && row.status?.name !== 'status_reject' &&
                        (this.$can('update_attendances') || this.$can('send_attendance_request'))
                },
                {
                    name: 'change-log',
                    title: this.$t(this.tableId === 'attendance-request-table' ? 'change_log' : 'log'),
                    icon: 'clock'
                },
                {
                    name: 'cancel',
                    title: this.$t('cancel'),
                    icon: 'trash',
                    modalClass: 'warning',
                    modalSubtitle: this.$t('you_are_going_to_cancel_a_attendance'),
                    modalIcon: 'slash',
                    modifier: (row) => ((this.$can('update_attendance_status') &&
                        (this.data.user_id !== window.user.id || this.$isAdmin()) &&
                        row.status?.name !== 'status_pending') ||
                        (row.status?.name === 'status_pending' && this.data.user_id == window.user.id)) &&
                        row.status?.name !== 'status_reject'
                }
            ];
        },
        requestActions() {
            return [
                ...this.dailyLogActions,
                {
                    name: 'approve',
                    title: this.$t('approve'),
                    icon: 'approve',
                    modalClass: 'success',
                    modalSubtitle: this.$t('you_are_going_to_approve_this_attendance'),
                    modalIcon: 'check-circle',
                    modifier: (row) => {
                        let conditions = this.$can('update_attendance_status') &&
                            row.status.name === 'status_pending' && (this.data.user_id !== window.user.id || this.$isAdmin())

                        return conditions && (!this.$isOnlyDepartmentManager() ||
                            this.$isOnlyDepartmentManager() && this.data.user_id !== window.user.id)
                    }
                },
                {
                    name: 'reject',
                    title: this.$t('reject'),
                    icon: 'reject',
                    modalClass: 'danger',
                    modalSubtitle: this.$t('you_are_going_to_reject_a_attendance'),
                    modalIcon: 'x-circle',
                    modifier: (row) => {
                        let conditions = this.$can('update_attendance_status') &&
                            row.status.name === 'status_pending' &&
                            (this.data.user_id !== window.user.id || this.$isAdmin())

                        return conditions && (!this.$isOnlyDepartmentManager() ||
                            this.$isOnlyDepartmentManager() && this.data.user_id === window.user.id)
                    }
                },
            ];
        },
        actions() {
            return {
                'daily-log': this.dailyLogActions,
                'attendance-request-table': this.requestActions,
                'attendance-summery-table': this.dailyLogActions
            }[this.tableId]
        },
        user() {
            return window.user;
        }
    },
    methods: {
        ordinal_suffix_of(i, a_length) {
            let index = a_length - i;
            let j = index % 10,
                k = index % 100;
            if (i === 0) {
                return 'Last';
            }
            if (j === 1 && k !== 11) {
                return index + "st";
            }
            if (j === 2 && k !== 12) {
                return index + "nd";
            }
            if (j === 3 && k !== 13) {
                return index + "rd";
            }
            return index + "th";
        },
        countTotalHours() {
            return convertSecondToHourMinutes(this.getTotalWorkedDuration(
                this.data.details.filter(value => (!value.status || value.status.name === 'status_approve'))
            ).asSeconds());
        },
        getComment(details, type) {
            return this.memoize(`details-${details.id}-${type}`, () => {
                const field = {'punch-in': 'in-note', 'punch-out': 'out-note'}[type];

                return this.collection(details.comments.filter(comment => comment.type === field)).first();
            })
        },
        countHours(details) {
            return convertSecondToHourMinutes(this.getTotalWorkedDuration([details]).asSeconds())
        },
    },
}
</script>