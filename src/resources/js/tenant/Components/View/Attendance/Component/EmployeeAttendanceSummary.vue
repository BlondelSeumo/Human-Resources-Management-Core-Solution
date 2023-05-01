<template>
    <div class="row my-5">
        <div class="col-lg-4 mb-4 mb-lg-0" v-if="isChartVisible">
            <div class="row align-items-center justify-content-center">
                <div class="col-6 col-md-5 col-lg-5">
                    <app-chart
                        type="dough-chart"
                        :height="170"
                        :labels="labels"
                        :data-sets="dataSet"
                    />
                </div>
                <div class="col-6 col-md-5 col-lg-7">
                    <div class="chart-data-list">
                        <div class="data-group-item" style="color: #5cde64">
                            <span class="square" style="background-color: #5cde64"/>
                            {{ $t('regular') }}
                            <span class="value">{{ summaries.regular || 0 }} {{ $t('days') }}</span>
                        </div>
                        <div class="data-group-item" style="color: #ea833a">
                            <span class="square" style="background-color: #ea833a"/>
                            {{ $t('early') }}
                            <span class="value">{{ summaries.early || 0 }} {{ $t('days') }}</span>
                        </div>
                        <div class="data-group-item" style="color: #df4736">
                            <span class="square" style="background-color: #df4736"/>
                            {{ $t('late') }}
                            <span class="value">{{ summaries.late || 0 }} {{ $t('days') }}</span>
                        </div>
                        <div class="data-group-item" style="color: #ee00ff">
                            <span class="square" style="background-color: #ee00ff"/>
                            {{ $t('on_leave') }}
                            <span class="value">{{ numberFormatter(summaries.on_leave) }} {{ $t('days') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div :class="`${isChartVisible ? 'col-lg-8' : 'col-lg-12'}`">
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="default-base-color text-center rounded p-3">
                        <h5 title="HH:MM:SS">{{ summaries.scheduled }}</h5>
                        <p class="text-muted mb-0">{{ $t('total_schedule_hour') }}</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="default-base-color text-center rounded p-3">
                        <h5 title="HH:MM:SS">{{ summaries.paid_leave }}</h5>
                        <p class="text-muted mb-0">Leave hour (paid)</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="default-base-color text-center rounded p-3">
                        <h5 :class="`text-${summaries.availability_behavior_class}`">{{ summaries.percentage }}%</h5>
                        <p class="text-muted mb-0">{{ $t('total_work_availability') }}</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3 mb-lg-0">
                    <div class="default-base-color text-center rounded p-3">
                        <h5 title="HH:MM:SS">{{ summaries.worked }}</h5>
                        <h6 :title="$t('in_hours')">({{ workedMinutePercentage(summaries.worked) }} {{ $t('hour_short_form') }})</h6>
                        <p class="text-muted mb-0">
                            {{ $t('total_active_hour') }}
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3 mb-lg-0">
                    <div class="default-base-color text-center rounded p-3">
                        <h5 title="HH:MM:SS" :class="balanceClass">{{ summaries.balance }}</h5>
                        <h6 :title="$t('in_hours')" :class="balanceClass">({{ workedMinutePercentage(summaries.balance) }} {{ $t('hour_short_form') }})</h6>
                        <p class="mb-0">{{ $t('balance') }} ({{ summaries.balance_behavior }})</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="default-base-color text-center rounded p-4">
                        <h5 :class="`text-${summaries.average_class}`">{{ $t(summaries.average) }}</h5>
                        <p class="text-muted mb-0">
                            {{ $t('average_behavior') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {numberFormatter} from "../../../../../common/Helper/Support/SettingsHelper";
import {getSecondFromTime} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "EmployeeAttendanceSummary",
    props: {
        summaries: {
            default: function () {
                return {};
            }
        },
        data: {
            default: function () {
                return [0, 0, 0, 0]
            }
        }
    },
    data() {
        return {
            numberFormatter,
            dataSet: [
                {
                    borderWidth: 0,
                    backgroundColor: [
                        '#5cde64',
                        '#ea833a',
                        '#df4736',
                        '#ee00ff'
                    ],
                    data: this.data
                },
            ],
            labels: [this.$t('regular'), this.$t('early'), this.$t('late'), this.$t('on_leave')],
        }
    },
    computed: {
        isChartVisible() {
            return this.data.reduce((sum, current) => parseInt(sum) + parseInt(current))
        },
        balanceClass(){
            //check for '-'
            return this.summaries.balance.includes('-') ? 'text-danger' : 'text-success';
        }
    },
    methods:{
        workedMinutePercentage(time) {
            // console.log(time,getSecondFromTime(time))
            let sec = getSecondFromTime(time);
            return (sec/3600).toFixed(2);
        }
    }
}
</script>
