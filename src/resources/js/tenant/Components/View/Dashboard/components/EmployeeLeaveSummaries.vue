<template>
    <div class="position-relative">
        <app-overlay-loader v-if="preloader"/>
        <div class="row" :class="{'loading-opacity': preloader}">
            <div class="col-12 col-md-6">
                <div class="card card-with-shadow border-0 mb-primary">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">{{ numberFormatter(summaries.total) }}</h5>
                            <p class="text-muted text-size-14 mb-0">{{ $t('total_leave_allowance') }}</p>
                        </div>
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="width-13 height-13 radius-2 bg-brand-color margin-right-5"/>
                                <p class="text-size-13 mb-0">{{ $t('paid') }} - {{ numberFormatter(summaries.paidTotal) }}</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="width-13 height-13 radius-2 bg-success margin-right-5"/>
                                <p class="text-size-13 mb-0">{{ $t('un_paid') }} - {{ numberFormatter(summaries.total - summaries.paidTotal) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card card-with-shadow border-0 mb-primary">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">{{ numberFormatter(summaries.taken) }}</h5>
                            <p class="text-muted text-size-14 mb-0">{{ $t('total_leave_taken') }}</p>
                        </div>
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="width-13 height-13 radius-2 bg-brand-color margin-right-5"/>
                                <p class="text-size-13 mb-0">{{ $t('paid') }} - {{ numberFormatter(summaries.paidTaken) }}</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="width-13 height-13 radius-2 bg-success margin-right-5"/>
                                <p class="text-size-13 mb-0">{{ $t('un_paid') }} - {{ numberFormatter(summaries.taken - summaries.paidTaken) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-4 mb-md-0">
                <div class="card card-with-shadow border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">{{ numberFormatter(summaries.available) }}</h5>
                            <p class="text-muted text-size-14 mb-0">{{ $t('total_leave_available') }}</p>
                        </div>
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="width-13 height-13 radius-2 bg-brand-color margin-right-5"/>
                                <p class="text-size-13 mb-0">{{ $t('paid') }} - {{ numberFormatter(summaries.paidTotal - summaries.paidTaken) }}</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="width-13 height-13 radius-2 bg-success margin-right-5"/>
                                <p class="text-size-13 mb-0">{{ $t('un_paid') }} - {{ numberFormatter(summaries.available - (summaries.paidTotal - summaries.paidTaken)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card card-with-shadow border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">{{ numberFormatter(summaries.pending) }}</h5>
                            <p class="text-muted text-size-14 mb-0">{{ $t('leave_request_pending') }}</p>
                        </div>
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="width-13 height-13 radius-2 bg-brand-color margin-right-5"/>
                                <p class="text-size-13 mb-0">{{ $t('paid') }} - {{ numberFormatter(summaries.paidPending) }}</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="width-13 height-13 radius-2 bg-success margin-right-5"/>
                                <p class="text-size-13 mb-0">{{ $t('un_paid') }} - {{ numberFormatter(summaries.pending -  summaries.paidPending) }}</p>
                            </div>
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
import {numberFormatter} from "../../../../../common/Helper/Support/SettingsHelper";

export default {
    name: "EmployeeLeaveSummaries",
    data() {
        return {
            numberFormatter,
            preloader: false,
            summaries: {},
        }
    },
    created() {
        this.getMonthlyLog();
    },
    methods: {
        getMonthlyLog() {
            this.preloader = true;
            axiosGet(`${APP_DASHBOARD}/employee/leave-summaries`).then(({data}) => {
                this.summaries = data;
            }).finally(() => {
                this.preloader = false;
            })
        }
    },
}
</script>

<style scoped>

</style>