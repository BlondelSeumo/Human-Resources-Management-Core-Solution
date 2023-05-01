<template>
    <div class="card card-with-shadow border-0 h-100">
        <div class="card-header bg-transparent p-3">
            <h4 class="card-title text-capitalize mb-0">{{ $t('time_log') }}</h4>
        </div>
        <app-overlay-loader v-if="preloader"/>
        <div class="card-body" v-else>
            <div class="mb-primary">
                <h5 class="border-bottom mb-3">{{ $t('today') }}</h5>
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">{{ todayScheduled }}</h6>
                        <p class="text-muted text-size-12 mb-0">{{ $t('scheduled') }}</p>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ todayWorked }}</h6>
                        <p class="text-muted text-size-12 mb-0">{{ $t('worked') }}</p>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ todayBalance }}</h6>
                        <p class="text-muted text-size-12 mb-0">{{ $t('balance') }}</p>
                    </div>
                </div>
            </div>
            <div>
                <h5 class="border-bottom mb-3">{{ $t('this_month') }}</h5>
                <div class="d-flex align-items-center mb-4">
                    <div>
                        <div
                            class="width-55 height-55 bg-brand-color d-flex align-items-center justify-content-center rounded mr-3">
                            <app-icon name="clock" class="text-white size-22"/>
                        </div>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ totalScheduled }}</h5>
                        <p class="text-muted text-size-12 mb-0">{{ $t('total_schedule_time') }}</p>
                    </div>
                </div>
                <div>
                    <div class="mb-3">
                        <p class="text-size-12 mb-1">{{ $t('worked_time') }} - {{ totalWorked }}</p>
                        <div class="progress animate-progress height-10 default-base-color radius-20">
                            <div class="progress-bar bg-brand-color"
                                 role="progressbar"
                                 :style="`width: ${workedPercentage}%`"/>
                        </div>
                    </div>
                    <div class="mb-3">
                        <p class="text-size-12 mb-1">{{ $t('shortage_time') }} - {{ totalShortage }}</p>
                        <div class="progress animate-progress height-10 default-base-color radius-20">
                            <div class="progress-bar bg-brand-color"
                                 role="progressbar"
                                 :style="`width: ${shortagePercentage}%`"/>
                        </div>
                    </div>
                    <div>
                        <p class="text-size-12 mb-1">{{ $t('over_time') }} - {{ overTime }}</p>
                        <div class="progress animate-progress height-10 default-base-color radius-20">
                            <div class="progress-bar bg-brand-color"
                                 role="progressbar"
                                 :style="`width: ${overtimePercentage}%`"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {APP_DASHBOARD} from "../../../../Config/ApiUrl";
import {
    convertSecondToHourMinutes,
    differentInTime,
    getDifferentTillNow,
    getHoursAndMinutesInString
} from "../../../../../common/Helper/Support/DateTimeHelper";
import moment from "moment";
import {collection} from "../../../../../common/Helper/helpers";

export default {
    name: "EmployeeMonthlyTimeLog",
    data() {
        return {
            preloader: false,
            logs: {},
            todayWorked: convertSecondToHourMinutes(0, true),
            todayBalance: convertSecondToHourMinutes(0, true),
            interval: null,
        }
    },
    created() {
        this.getMonthlyLog();
    },
    mounted() {
        this.$hub.$on('reload-dashboard', () => this.getMonthlyLog())
    },
    methods: {
        getMonthlyLog() {
            this.preloader = true;
            axiosGet(`${APP_DASHBOARD}/employee/attendance-log`).then(({data}) => {
                this.logs = data;
                if (data.today_attendance) {
                    this.setTodayTimeLog();
                    this.interval = setInterval(this.setTodayTimeLog, 30 * 1000);
                }
            }).finally(() => {
                this.preloader = false;
            })
        },
        setTodayTimeLog() {
            let worked = this.getTotalWorkedDuration().asSeconds();
            this.todayWorked = convertSecondToHourMinutes(worked);
            this.todayBalance = convertSecondToHourMinutes((worked - this.logs.today_scheduled));

            if (collection(this.logs.today_attendance.details).first().out_time) {
                clearInterval(this.interval);
            }
        },
        getTotalWorkedDuration() {
            return this.logs.today_attendance.details.reduce((carry, details) => {
                if (details.out_time) {
                    return carry.add(moment.duration(differentInTime(details.in_time, details.out_time, true)))
                }
                return carry.add(moment.duration(getDifferentTillNow(details.in_time)))
            }, moment.duration(0));
        }
    },
    computed: {
        totalScheduled() {
            return getHoursAndMinutesInString(this.logs.total_scheduled, true);
        },
        totalWorked() {
            return getHoursAndMinutesInString(this.logs.total_worked, true);
        },
        overTime() {
            return getHoursAndMinutesInString(this.logs.over_time, true);
        },
        totalShortage() {
            return getHoursAndMinutesInString(this.logs.shortage, true);
        },
        workedPercentage() {
            return (this.logs.total_worked / this.logs.total_scheduled) * 100;
        },
        shortagePercentage() {
            return (this.logs.shortage / this.logs.total_scheduled) * 100;
        },
        overtimePercentage() {
            return (this.logs.over_time / this.logs.total_scheduled) * 100;
        },
        todayScheduled() {
            return convertSecondToHourMinutes(this.logs.today_scheduled);
        },
    }
}
</script>

<style scoped>

</style>