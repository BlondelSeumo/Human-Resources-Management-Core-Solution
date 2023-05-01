<template>
    <div>
        <app-overlay-loader v-if="loading"/>
        <div v-else class="d-flex timeline timeline-change-log mb-4">
            <div class="text-right timeline-title">
                <p class="mb-0">
                    {{ this.$t('salary') }}
                </p>

                <a href="#"
                   v-if="salaryUpdatePermission"
                   class="font-size-80 primary-text-color"
                   @click.prevent="getEmployeeJobHistory">
                    {{ $t('increment') }}
                </a>

            </div>
            <div class="d-flex">
                <div class="timeline-icon mx-3">
                    <div class="svg-wrapper">
                        <app-icon name="dollar-sign"/>
                    </div>
                </div>
                <div class="timeline-content">
                    <div v-if="!salaries.length" class="single-record mb-primary">
                        {{ $t('not_added_yet') }}
                    </div>
                    <div v-for="(salary, index) in salaries" class="single-record mb-primary">
                        <div>
                            <p class="mb-0">{{ formatCurrency(numberFormatter(Number(salary.amount))) }}</p>
                            <p class="mb-0 font-size-90" v-if="salaries[index+1]">
                                <template v-if="!salary.end_at">
                                    <template  v-if="isAfterNow(salary.start_at)">
                                        <span class="font-size-90 text-warning">Will be effective from {{ formatDateToLocal(salary.start_at) }}</span>
                                    </template>
                                    <template v-else>
                                        <span class="font-size-90 text-muted"> {{ formatDateToLocal(salary.start_at) }} - </span>
                                        <span class="font-size-90 primary-text-color">Present</span>
                                    </template>
                                </template>
                                <span v-if="salary.end_at && isAfterNow(salary.end_at)"
                                      class="font-size-90 primary-text-color">Present</span>
                                <span v-else class="font-size-90 text-muted">{{ formatDateToLocal(salary.end_at) }}</span>
                            </p>
                            <p class="mb-0" v-else>
                                <template v-if="salary.end_at">
                                    <span v-if="isAfterNow(salary.end_at)" class="font-size-80 primary-text-color">Present</span>
                                    <span v-else class="font-size-80 text-muted">till - {{ formatDateToLocal(salary.end_at) }}</span>
                                </template>
                                <template v-else>
                                    <span v-if="isAfterNow(salary.start_at)"
                                          class="font-size-90 text-warning">Upcoming</span>
                                    <span v-else class="font-size-90 primary-text-color">Present</span>
                                </template>
                            </p>
                            <p class="mb-0 font-size-90 text-muted" v-if="salaries[index+1]">
                                <template v-if="Number(salary.amount) > Number(salaries[index+1].amount)">
                                    <span class="font-size-90 primary-text-color">{{ salary.added_by.full_name }}</span>
                                    has awarded a salary increment.
                                </template>
                                <template v-else>
                                    <span class="font-size-90 primary-text-color">{{ salary.added_by.full_name }}</span>
                                    has punished a salary decrement.
                                </template>
                            </p>
                            <p class="mb-0 font-size-90 text-muted" v-if="salaries[index+1]">
                                from <span class="font-size-90 primary-text-color">{{ formatCurrency(numberFormatter(Number(salaries[index + 1].amount))) }}</span>
                                to <span class="font-size-90 primary-text-color">{{ formatCurrency(numberFormatter(Number(salary.amount))) }}</span>
                                on <span class="font-size-90">{{ formatDateToLocal(salary.created_at) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <job-history-edit-modal
            v-if="isJobHistoryEditModalActive"
            v-model="isJobHistoryEditModalActive"
            modalType="salary"
            :employee="employee"
            @reload="reload"
        />
    </div>
</template>

<script>
import {axiosGet} from "../../../../../../common/Helper/AxiosHelper";
import {formatDateToLocal, isAfterNow} from "../../../../../../common/Helper/Support/DateTimeHelper";
import JobHistoryEditModal from "../JobHistory/components/JobHistoryEditModal";
import {formatCurrency, numberFormatter} from "../../../../../../common/Helper/Support/SettingsHelper";

export default {
    name: 'SalaryReviews',
    components: {JobHistoryEditModal},
    props: ['props'],
    data() {
        return {
            numberFormatter,
            formatCurrency,
            formatDateToLocal,
            isAfterNow,
            salaries: [],
            loading: false,
            isJobHistoryEditModalActive: false,
            employee: {}
        }
    },
    created() {
        this.getSalaries();
    },
    methods: {
        getSalaries() {
            this.loading = true;
            axiosGet(`${this.apiUrl.EMPLOYEES}/${this.props.id}/salaries`).then(({data}) => {
                this.salaries = data;
            }).finally(() => {
                this.loading = false;
            })
        },
        getEmployeeJobHistory() {
            this.loading = true;
            axiosGet(`${this.apiUrl.EMPLOYEES}/${this.props.id}`).then(({data}) => {
                this.employee = data;
                this.isJobHistoryEditModalActive = true;
            }).finally(() => {
                this.loading = false;
            })
        },
        reload() {
            this.getSalaries()
            this.$store.dispatch("getEmployeeDetails", this.employee.id);
        }
    },
    computed:{
        salaryUpdatePermission(){
            return (this.$can('update_salary') && this.props.id !== window.user.id) || this.$isAdmin();
        },
    },
    mounted() {
        this.$hub.$on('employeeDetailsActionHappened', (value) => {
            this.getSalaries();
        });
    }
}
</script>