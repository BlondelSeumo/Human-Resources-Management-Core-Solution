<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('attendance_details')">
            <app-attendance-top-buttons/>
        </app-page-top-section>
        <app-filter-with-search
            :options="options"
            @filteredValue="setFilterValues"
            @searchValue="setSearchValue"
        />

        <div class="card card-with-shadow border-0" style="min-height: 400px;">
            <div class="card-body">
                <div class="summery-calendar">
                    <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between mb-primary">
                        <app-month-calendar :show-date-range-calendar="true"/>

                        <app-period-calendar/>
                    </div>
                    <app-overlay-loader v-if="loading"/>

                    <div class="d-flex" v-else-if="$optional(attendances, 'data', 'length')">
                        <div class="profile-column">
                            <div class="column-header">{{ $t('profile') }}</div>
                            <template v-for="employee in attendances.data">
                                <app-employee-profile-card :employee="employee"/>
                            </template>
                        </div>
                        <div class="total-hour-column">
                            <div class="column-header">{{ $t('total') }}</div>
                            <app-employee-work-hour-type v-for="employee in attendances.data"
                                                         :attendance-summery="getAttendanceSummaries(employee)"
                                                         :key="employee.id"
                            />
                        </div>
                        <div class="date-column custom-scrollbar custom-scrollbar-with-mouse-move">
                            <div class="d-flex">
                                <div class="date-value-wrapper" v-for="range in ranges">
                                    <div class="date">
                                        {{ formatTitleRangeDate(range) }}
                                    </div>
                                    <div class="date-hour-wrapper"
                                         v-for="employee in attendances.data"
                                         v-html="formatEmployeeAttendances(employee, range)"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <app-empty-data-block v-else :message="$t('nothing_to_show_here')"/>
                    <app-pagination
                        v-if="attendances.total && attendances.last_page > 1"
                        :rowLimit="attendances.per_page"
                        :totalRow="attendances.total"
                        @submit="setPagination"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AppEmployeeProfileCard from "./Component/AppEmployeeProfileCard";
import AppEmployeeWorkHourType from "./Component/AppEmployeeWorkHourType";
import AppEmployeeWorkingDate from "./Component/AppEmployeeWorkingDate";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";
import {ATTENDANCES} from "../../../Config/ApiUrl";
import {
  DepartmentFilterMixin,
  DesignationFilterMixin,
  EmploymentStatusFilterMixin,
  WorkingShiftFilterMixin
} from '../../Mixins/FilterMixin';
import AppAttendanceTopButtons from "./Component/AppAttendanceTopButtons";
import moment from 'moment'
import {
    convertSecondToHourMinutes,
    differentInTime, serverDateFormat,
    serverDateTimeFormat, serverTimeFormat
} from "../../../../common/Helper/Support/DateTimeHelper";
import optional from "../../../../common/Helper/Support/Optional";
import MemoizeMixins from "../../../../common/Helper/Support/MemoizeMixins";
import AttendanceHelperMixin from "../../Mixins/AttendanceHelperMixin";
import TemplateHelper from "../../../../core/helpers/template-helper/TemplateHelper";
import AttendanceSummaryDetailsMixin from "../../Mixins/AttendanceSummaryDetailsMixin";

export default {
    name: "AttendanceDetails",
    components: {
        AppEmployeeWorkingDate,
        AppEmployeeWorkHourType,
        AppEmployeeProfileCard,
        AppAttendanceTopButtons
    },
    mixins: [DepartmentFilterMixin, DesignationFilterMixin, EmploymentStatusFilterMixin, WorkingShiftFilterMixin, MemoizeMixins, AttendanceHelperMixin, AttendanceSummaryDetailsMixin],
    methods: {
        getAttendances(queryString) {
            this.loading = true;
            axiosGet(`${ATTENDANCES}/details?${queryString}`).then(response => {
                this.attendances = response.data.attendances;
                this.ranges = response.data.range.map(range => moment(range, serverDateTimeFormat));
                this.defaultWorkingShift = response.data.default;
            }).finally(() => {
                this.loading = false;
                TemplateHelper.moveHorizontalScrollbarWithMouse();
            })
        },
        formatEmployeeAttendances(employee, date) {
            const attendance = this.getAttendance(employee, date);

            const workShiftDetails = this.getDetails(
                this.getEmployeeWorkingShiftFromDate(
                    employee.working_shifts,
                    date,
                    this.defaultWorkingShift
                ),
                date,
            );

            const holiday = this.isHoliday(date, employee.holidays);

            const scheduled = holiday ? moment.duration('00:00') : this.getScheduled(workShiftDetails);

            const paid_leave = Number(this.getTotalPaidLeave(employee, workShiftDetails, date, holiday));

            if (!attendance) {
                return `<div class="date-hour">${scheduled.asSeconds() ? convertSecondToHourMinutes(scheduled.asSeconds()) : ''}</div>
                        <div class="date-hour">${scheduled.asSeconds() ? '00:00' : ''}</div>
                        <div class="date-hour">${paid_leave ? convertSecondToHourMinutes(paid_leave) : ''}</div>
                        <div class="date-hour">${scheduled.asSeconds() ? convertSecondToHourMinutes(this.getBalance(scheduled.asSeconds(), 0, paid_leave)) : ''}</div>`
            }

            const worked = this.getTotalWorked(attendance);

            const balance = this.getBalance(scheduled.asSeconds(), worked.asSeconds(), paid_leave);

            if (holiday) {
                return `<div class="date-hour">00:00</div>
                        <div class="date-hour">${worked.asSeconds() ? convertSecondToHourMinutes(worked.asSeconds()) : ''}</div>
                        <div class="date-hour">${paid_leave ? convertSecondToHourMinutes(paid_leave) : ''}</div>
                        <div class="date-hour">${(scheduled.asSeconds() || balance) ? convertSecondToHourMinutes(balance) : ''}</div>`
            }

            return `<div class="date-hour">${scheduled.asSeconds() ? convertSecondToHourMinutes(scheduled.asSeconds()) : ''}</div>
                    <div class="date-hour">${worked.asSeconds() ? convertSecondToHourMinutes(worked.asSeconds()) : ''}</div>
                    <div class="date-hour">${paid_leave ? convertSecondToHourMinutes(paid_leave) : ''}</div>
                    <div class="date-hour">${(scheduled.asSeconds() || balance) ? convertSecondToHourMinutes(balance) : ''}</div>`
        },
        getAttendanceSummaries(employee) {
            const {scheduled, worked, paid_leave, balance} = this.ranges.map(date => {
                const workShiftDetails = this.getDetails(
                    this.getEmployeeWorkingShiftFromDate(employee.working_shifts, date, this.defaultWorkingShift),
                    date
                );
                const holiday = this.isHoliday(date, employee.holidays);
                const attendance = this.getAttendance(employee, date);
                const scheduled = holiday ? 0 : this.getScheduled(workShiftDetails).asSeconds();
                const worked = attendance ? this.getTotalWorked(attendance).asSeconds() : 0;
                const paid_leave = this.getTotalPaidLeave(employee, workShiftDetails, date, holiday);
                const balance = this.getBalance(scheduled, worked, paid_leave);
                return {
                    scheduled,
                    worked,
                    paid_leave,
                    balance
                };
            }).reduce((prev, attendance) => {
                prev.scheduled += attendance.scheduled;
                prev.worked += attendance.worked;
                prev.paid_leave += attendance.paid_leave;
                prev.balance += attendance.balance
                return prev;
            }, {
                scheduled: 0,
                worked: 0,
                paid_leave: 0,
                balance: 0
            });

            return {
                scheduled: convertSecondToHourMinutes(scheduled),
                worked: convertSecondToHourMinutes(worked),
                paid_leave: convertSecondToHourMinutes(paid_leave),
                balance: convertSecondToHourMinutes(balance),
            }

        },
        getTotalPaidLeave(employee, workingShiftDetails, date, holiday) {
            if (parseInt(workingShiftDetails.is_weekend) === 1 || !!holiday){
                return moment.duration(0);
            }
            let leave = employee.leaves.find(leave => {
                return moment(date.format(serverDateFormat))
                    .isBetween(moment(leave.start_at).format(serverDateFormat), moment(leave.end_at).format(serverDateFormat),'days', '[]')
            })
            if(leave && (leave.duration_type === 'first_half' || leave.duration_type === 'last_half')){
                return (moment.duration(differentInTime(workingShiftDetails.start_at, workingShiftDetails.end_at)).asSeconds()) / 2;
            }else if (leave && leave.duration_type === 'hours'){
                return moment.duration(differentInTime(moment(leave.start_at).format(serverTimeFormat), moment(leave.end_at).format(serverTimeFormat))).asSeconds();
            }
            return leave ? moment.duration(differentInTime(workingShiftDetails.start_at, workingShiftDetails.end_at)).asSeconds() : moment.duration(0).asSeconds();
        },

        getAttendance(employee, date) {
            return this.memoize(`attendance-${employee.id}-${date.format(serverDateFormat)}`, () => {
                return employee.attendances.find(attendance => {
                    return attendance.in_date === date.format(serverDateFormat);
                });
            });
        },
        getScheduled(details) {
            if (!details) {
                return moment.duration("00:00");
            }
            return this.memoize(`work-shift-details-${details.id}`, () => {
                if (parseInt(optional(details, 'is_weekend'))) {
                    return moment.duration("00:00");
                }
                return differentInTime(details.start_at, details.end_at);
            });
        },
        getBalance(scheduled, worked, paid_leave) {
          if (paid_leave){
            return (worked + paid_leave) - scheduled ;
          }
            return worked - scheduled;
        },
        isHoliday(date, holidays){
            return holidays.find( holiday => date.isSame(holiday, 'day'))
        },
        formatTitleRangeDate(range){
            let date = range.locale(window.appLanguage)
            return date.format('MMM D')
        }
    },
    watch: {
        queryString: {
            handler: function (queryString) {
                if (!queryString) {
                    return;
                }
                this.getAttendances(queryString)
            },
            immediate: true
        }
    }
}
</script>