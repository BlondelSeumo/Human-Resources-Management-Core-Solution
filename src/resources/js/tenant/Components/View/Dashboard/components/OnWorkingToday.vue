<template>

    <div class="card card-with-shadow border-0 h-100 min-height-350">
        <div class="card-header bg-transparent p-primary d-flex justify-content-between align-items-center">
            <h5 class="card-title text-capitalize mb-0">{{ $t('total_attendance_today') }} - {{
                    numberFormatter(attendance.total)
                }}</h5>
        </div>
        <div
            class="card-body d-flex flex-column justify-content-center align-items-center position-relative p-primary"
            :class="{'loading-opacity': preloader}">
            <app-overlay-loader v-if="preloader"/>
            <template v-else>
                <app-chart
                    class="mb-primary"
                    type="dough-chart"
                    :height="230"
                    :labels="todayOverviewLabels"
                    :data-sets="todayOverviewDataSet"
                />
                <div class="chart-data-list">
                    <div class="row">
                        <div class="col-12"
                             v-for="(item, index) in todayOverviewChartDataList"
                             :key="index">
                            <div class="data-group-item px-0">
                                <span class="square" :style="`background-color: ${item.color}`"/>
                                <span class="value">{{ item.name }} - {{ item.value }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

</template>

<script>
import {numberFormatter} from "../../../../../common/Helper/Support/SettingsHelper";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {APP_DASHBOARD} from "../../../../Config/ApiUrl";

export default {
    name: "OnWorkingToday",
    data() {
        return {
            numberFormatter,
            preloader: false,
            cardSummaries: {},
            attendance: {},

            todayOverviewLabels: [],
            colors: {
                early: '#ea833a',
                late: '#df4736',
                regular: '#5cde64',
            },
            todayOverviewDataSet: [
                {
                    borderWidth: 0,
                    backgroundColor: ['#ea833a', '#df4736', '#5cde64'],
                    data: [0, 0, 0]
                }
            ],
            todayOverviewChartDataList: [
                {name: this.$t('early'), value: 0, color: '#ea833a'},
                {name: this.$t('late'), value: 0, color: '#df4736'},
                {name: this.$t('regular'), value: 0, color: '#5cde64'},
            ]
        }
    },
    created() {
        this.getSummeryAndAttendanceData();
    },
    methods: {
        getSummeryAndAttendanceData() {
            this.preloader = true;
            axiosGet(`${APP_DASHBOARD}/on-working`).then(({data}) => {
                this.attendance = data;
                this.insertDataToChart(data.behaviour)
            })
        },
        insertDataToChart(behaviour) {
            this.todayOverviewLabels = Object.keys(behaviour).map(behave => this.$t(behave));
            this.todayOverviewDataSet[0].data = Object.values(behaviour);
            this.todayOverviewChartDataList = Object.entries(behaviour).map((behave, key) => {
                return {
                    name: this.$t(behave[0]),
                    value: behave[1],
                    color: this.colors[behave[0]]
                }
            });
            this.preloader = false;
        }
    },
}
</script>
