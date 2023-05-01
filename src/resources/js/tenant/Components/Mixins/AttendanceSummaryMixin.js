import {ATTENDANCES} from "../../Config/ApiUrl";
import {convertSecondToHourMinutes, formatDateToLocal} from "../../../common/Helper/Support/DateTimeHelper";
import {attendanceBehavior} from "../View/Attendance/Helper/Helper";

export default {
    data() {
        return {
            tableOptions: {
                name: '',
                url: `${ATTENDANCES}/${this.user.id}/summaries-data-table`,
                datatableWrapper: false,
                showHeader: true,
                responsive: true,
                tablePaddingClass: "px-0",
                showSearch: false,
                showFilter: false,
                tableShadow: false,
                afterRequestSuccess: ({data}) => {
                    this.$emit('checkTableData', (data.data.length))
                },
                columns: [
                    {
                        title: this.$t('date'),
                        type: 'custom-html',
                        key: 'in_date',
                        modifier: (in_date) => formatDateToLocal(in_date)
                    },
                    {
                        title: this.$t('punched_in'),
                        type: 'component',
                        key: 'details',
                        componentName: 'app-punch-in-date-time',
                    },
                    {
                        title: this.$t('in_geolocation'),
                        type: 'component',
                        key: 'details',
                        componentName: 'app-punch-in-geolocation',
                    },
                    {
                        title: this.$t('punched_out'),
                        type: 'component',
                        key: 'details',
                        componentName: 'app-punch-out-date-time',
                    },
                    {
                        title: this.$t('out_geolocation'),
                        type: 'component',
                        key: 'details',
                        componentName: 'app-punch-out-geolocation',
                    },
                    {
                        title: this.$t('behavior'),
                        type: 'custom-html',
                        key: 'behavior',
                        modifier: attendanceBehavior
                    },
                    {
                        title: this.$t('type'),
                        type: 'component',
                        componentName: 'app-attendance-type',
                        key: 'details',
                    },
                    {
                        title: this.$t('total_hours'),
                        type: 'custom-html',
                        key: 'details',
                        modifier: (details, attendance) => convertSecondToHourMinutes(this.getTotalWorked(attendance).asSeconds())
                    },
                    {
                        title: this.$t('entry'),
                        type: 'expandable-column',
                        key: 'details',
                        isVisible: true,
                        componentName: 'app-attendance-expandable-column',
                        modifier: (details) => {
                            return {
                                title: details.length > 1 ? this.$t('multi') : this.$t('single'),
                                expandable: details.length > 1,
                                className: details.length > 1 ? 'warning' : 'success'
                            };
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        isVisible: true
                    }
                ],
                filters: [],
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'dailyLogTableRow',
                        name: 'edit',
                        modifier: row =>  (this.$can('update_attendances') || this.$can('send_attendance_request')) && row?.details?.length < 2
                    },
                    {
                        title: this.$t('change_log'),
                        name: 'change-log',
                        icon: 'trash-2',
                        type: 'dailyLogTableRow',
                        modifier: row => row?.details?.length < 2
                    },
                    {
                        title: this.$t('cancel'),
                        name: 'cancel',
                        icon: 'trash-2',
                        type: 'dailyLogTableRow',
                        modalClass: 'warning',
                        modalSubtitle: this.$t('you_are_going_to_cancel_a_attendance'),
                        modalIcon: 'slash',
                        modifier: row => this.$can('update_attendances') &&
                            !!this.collection(row.details).first()?.added_by && row?.details?.length < 2 &&
                            (row.user_id != window.user.id || this.$isAdmin())
                    },
                ],
                rowLimit: 10,
                paginationType: "pagination"
            },
        }
    }

}