<template>
    <div class="d-flex">
        <div class="icon-box mr-2 flex-shrink-1">
            <app-icon name="calendar" class="text-muted"/>
        </div>
        <app-overlay-loader v-if="!payruns || !payrun"/>
        <div v-else class="ml-3 w-100">
            <div>
                <h6 class="d-inline">{{ payrunTitle(payrun.time_range) }}</h6>
                <span class="font-size-80 ml-2">
                    {{ $t('includes_employee', {'count': payrun.count_employee}) }}
                </span>
            </div>
            <div>
                <span class="font-size-90">
                    {{ $t(payrun.period) }} -
                    {{ $t(payrun.consider_type) }} {{ $t('based') }}
                    {{  payrun.consider_type !== 'none' && payrun.consider_type !== 'None' ?
                    `(${considerOvertimeTitle(payrun.consider_overtime)} ${$t('overtime')})` : '' }}
                </span>
                <span class="text-muted">|</span>
                <span class="font-size-90"><span class="text-info">{{ $t('Default') }}</span> - {{ $t('followed_by', {'setting' : $t(badgeType)}) }}</span>
            </div>
            <div class="py-2">
                <a href="" @click.prevent="defaultBadgeShow">
                    {{ isDefaultBadgeActive ? $t('hide') : $t('show') }} {{ $t('details') }}
                </a>
            </div>
            <div v-if="isDefaultBadgeActive">
                <beneficiary-badge-show
                    v-if="badgeType === 'default'"
                    :earnings="collect(payrun.beneficiaries, 'allowance')"
                    :deductions="collect(payrun.beneficiaries, 'deduction')"
                />
                <template v-else>
                    <app-pre-loader v-if="badgePreloader"/>
                    <div v-else >
                        <div class="py-2" v-for="employee in employees">
                            <div class="py-2">
                                <span class="list-bullet text-primary">{{ employee.full_name }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="mr-1">{{ payrunTitle(makeRanges(employee)) }}</span>
                                <span class="font-size-90">
                                    {{ employeePayrunSettings(employee) }}</span>
                            </div>
                            <beneficiary-badge-show
                                :earnings="collect(employeePayrunBeneficiaries(employee), 'allowance')"
                                :deductions="collect(employeePayrunBeneficiaries(employee), 'deduction')"
                            />
                        </div>
                        <div class="text-center mt-4">
                            <app-load-more
                                v-if="showLoadMoreButton"
                                :preloader="loadMorePreloader"
                                @submit="loadMoreEmployees"/>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import BeneficiaryBadgeShow from "./BeneficiaryBadgeShow";
import {makePayrunRanges, payrunTitleByDateRange} from "../Helper/helper";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {getConsiderOvertimeTitle} from "../Helper/helper";

export default {
    name: "DefaultPayrunModalMedia",
    components: {BeneficiaryBadgeShow},
    props: {
        payruns: {},
        badgeType: {
            required: false,
            default: 'default'
        },
    },
    data(){
        return {
            isDefaultBadgeActive: false,
            employeeBadgeShow: false,
            badgePreloader: false,
            page: 1,
            employees: {},
            loadMorePreloader: false,
            showLoadMoreButton: false
        }
    },
    methods: {
        considerOvertimeTitle(consider_overtime){
            return getConsiderOvertimeTitle(consider_overtime)
        },
        collect(badges, type){
            return badges.map( badge => {
                    return badge.beneficiary?.type === type ? badge : false
                }
            ).filter((badge) => badge)
        },
        payrunTitle(dateRange){
            if(!dateRange || Object.keys(dateRange).length === 0) return this.$t('multiple_time_period')
            return payrunTitleByDateRange(dateRange);
        },
        makeRanges(employee){
            let period = employee.payrun_setting ?
                employee.payrun_setting.payrun_period :
                this.defaultPayrun.period
            return makePayrunRanges(period);
        },
        getEmployeesFollowedByEmployeePayrun(){
            this.badgePreloader = true;
            axiosGet(`${this.apiUrl.EMPLOYEES_FOLLOWED_BY_EMPLOYEE_PAYRUN}?page=${this.page}`).then(({data}) => {
                if (Object.keys(this.employees).length > 0){
                    this.employees.concat(data.data)
                }else{
                    this.employees = data.data;
                }

                this.showLoadMoreButton = data.to < data.total;
            }).finally(() => {
                this.badgePreloader = false;
                this.loadMorePreloader = false;
            });
        },
        defaultBadgeShow() {
            this.isDefaultBadgeActive = !this.isDefaultBadgeActive;

            if (this.badgeType === 'employee' && this.isDefaultBadgeActive){
                this.getEmployeesFollowedByEmployeePayrun();
            }

            if (this.badgeType === 'employee' && !this.isDefaultBadgeActive){
                this.page = 1;
                this.employees = {}
            }
        },
        loadMoreEmployees(){
            this.page++
            this.loadMorePreloader = true;
            this.getEmployeesFollowedByEmployeePayrun()
        },
        employeePayrunSettings(employee){
            let period = employee.payrun_setting ?
                employee.payrun_setting.payrun_period :
                this.defaultPayrun.period

            let consider_type =  employee.payrun_setting ?
                employee.payrun_setting.consider_type :
                this.defaultPayrun.consider_type

            let consider_overtime =  employee.payrun_setting ?
                employee.payrun_setting.consider_overtime :
                this.defaultPayrun.consider_overtime

            return `${this.$t(period)} - ${this.$t(consider_type)} ${this.$t('based')} ${consider_type !== 'none' && consider_type !== 'None' ?
                `(${this.considerOvertimeTitle(consider_overtime)} ${this.$t('overtime')})` : ''}`;
        },

        employeePayrunBeneficiaries(employee){
            return Object.keys(employee.payrun_beneficiaries).length > 0 ?
                employee.payrun_beneficiaries : this.payruns?.employee?.restricted_badge_user.includes(employee.id) ? []
                : this.defaultPayrun.beneficiaries
        }
    },
    computed: {
        payrun(){
            return this.badgeType === 'employee' ? this.payruns.employee : this.payruns.default
        },
        defaultPayrun(){
            return this.payruns.default
        }
    }
}
</script>

<style scoped>

</style>