<template>
    <modal id="beneficiary-badge-create-edit-modal"
           size="large"
           v-model="showModal"
           :title="this.$t('payslip')"
           :cancel-btn-label="$t('close')"
           :hide-submit-button="true"
           @submit=""
           :preloader="preloader">

        <div class="d-flex justify-content-center mb-primary">
            <div class="text-center">
                <img :src="logo" alt="logo" class="radius-5 mb-2" style="max-height: 100px; max-width: 150px;" />
                <h5 class="font-weight-bold">{{ payrunData.title ? payrunData.title : generalSettings.tenant_name }}</h5>
                <div v-if="payrunData.address">
                    <p class="m-0">{{ payrunData.address }}</p>
                </div>
                <div v-else>
                    <p class="m-0">
                        {{ generalSettings.address ? `${generalSettings.address}, ` : '' }}
                        {{ generalSettings.area ? `${generalSettings.area}, ` : '' }}
                        {{ generalSettings.city ? `${generalSettings.city}, ` : '' }}</p>
                    <p class="m-0">
                        {{ generalSettings.zip_code ? `Zip-${generalSettings.zip_code}, ` : '' }}
                        {{ generalSettings.country ? `${generalSettings.country}, ` : '' }}</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-primary py-primary">
            <div class="">
                <app-user-media
                    :row-data="user"
                    :value="user.profile_picture"
                />
            </div>
            <div>
                <p class="m-0">{{ this.$t('payslip_for') }} : <span class="text-info">{{ getDateDifferenceString(payslip.start_date, payslip.end_date) }}</span></p>
                <p class="m-0">{{ this.$t('created_at') }} : {{ customDateFormat(payslip.created_at) }}</p>
            </div>
            <div>
                <p class="m-0">{{ this.$t('designation') }} : {{ user.designation ? user.designation.name : 'None' }}</p>
                <p class="m-0">{{ this.$t('department') }} : {{ user.department ? user.department.name : 'None' }}</p>
            </div>
        </div>

        <div class="d-flex justify-content-between border-bottom">
            <h6>{{ this.$t('basic_salary') }}</h6>
            <h6>{{ formatCurrency(numberFormatter(Number(basicSalary))) }}</h6>
        </div>

        <div v-if="payslip.consider_type !== 'none'">
            <div v-if="earnedSalary > basicSalary" class="d-flex justify-content-between border-bottom mt-2">
                <h6>{{ this.$t('overtime_earning') }}</h6>
                <h6>{{ formatCurrency(numberFormatter(earnedSalary - basicSalary)) }}</h6>
            </div>
            <div v-if="earnedSalary < basicSalary" class="d-flex justify-content-between border-bottom mt-2">
                <h6>{{ this.$t('unpaid_leave_deduction') }}</h6>
                <h6>{{ formatCurrency(numberFormatter(basicSalary - earnedSalary)) }}</h6>
            </div>

            <div class="d-flex justify-content-between border-bottom mt-2">
                <h6>
                    {{ this.$t('total_earning') }}
                    <small>
                        ({{ `${this.$t('based_on')} ${this.$t(payslip.consider_type)}` }}{{ earnedSalary >= basicSalary ? ` , ${parseInt(payslip.consider_overtime) ? this.$t('included_overtime') : this.$t('excluded_overtime')}` : '' }})
                    </small>
                </h6>
                <h6>{{ formatCurrency(numberFormatter(earnedSalary)) }}</h6>
            </div>
        </div>

        <div class="mb-primary mt-primary">
            <template v-if="beneficiaries.length">
                <div class="border-bottom mb-2">
                    <h6>{{ this.$t('beneficiary') }}</h6>
                </div>
                <div class="mb-3 text-muted">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex justify-content-between border-bottom mb-2">
                                <h6>{{ this.$t('allowances') }}</h6>
                            </div>
                            <template v-for="beneficiary in beneficiaries">
                                <div class="mb-2 d-flex justify-content-between"
                                     v-if="beneficiary.beneficiary.type === 'allowance'">
                                    <p class="m-0 text-size-12">
                                        {{
                                            parseInt(beneficiary.is_percentage) ? `${beneficiary.beneficiary.name}(${beneficiary.amount}%)` : beneficiary.beneficiary.name
                                        }}
                                    </p>
                                    <p class="m-0 text-size-12">
                                        {{
                                            formatCurrency(numberFormatter(parseInt(beneficiary.is_percentage) ? getPercentageOfSalary(beneficiary.amount) : Number(beneficiary.amount)))
                                        }}
                                    </p>
                                </div>
                            </template>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between border-bottom mb-2">
                                <h6>{{ this.$t('deductions') }}</h6>
                            </div>
                            <template v-for="beneficiary in beneficiaries">
                                <div class="mb-2 d-flex justify-content-between"
                                     v-if="beneficiary.beneficiary.type === 'deduction'">
                                    <p class="m-0 text-size-12">
                                        {{
                                            parseInt(beneficiary.is_percentage) ? `${beneficiary.beneficiary.name}(${beneficiary.amount}%)` : beneficiary.beneficiary.name
                                        }}
                                    </p>
                                    <p class="m-0 text-size-12">
                                        {{
                                            formatCurrency(numberFormatter(parseInt(beneficiary.is_percentage) ? getPercentageOfSalary(beneficiary.amount) : Number(beneficiary.amount)))
                                        }}
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <p class="m-0">{{ this.$t('total_allowance') }}</p>
                                <p class="m-0">{{ formatCurrency(numberFormatter(totalAllowances)) }}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <p class="m-0">{{ this.$t('total_deduction') }}</p>
                                <p class="m-0">{{ formatCurrency(numberFormatter(totalDeductions)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between border-bottom">
                    <h6>{{ this.$t('beneficiary_amount') }}</h6>
                    <h6>{{ formatCurrency(numberFormatter(totalBeneficiary)) }}</h6>
                </div>
            </template>
        </div>

        <div class="d-flex justify-content-between border-bottom mb-primary">
            <h6>{{ this.$t('net_payable_salary') }}</h6>
            <h6>{{ formatCurrency(numberFormatter(Number(payslip.net_salary))) }}</h6>
        </div>

        <p v-if="payrunData.note" class="border-top font-italic"><b>Note: </b>{{payrunData.note}}</p>

    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import {mapState} from "vuex";
import {customDateFormat, getDateDifferenceString} from "../../../../../common/Helper/Support/DateTimeHelper";
import {formatCurrency, numberFormatter} from "../../../../../common/Helper/Support/SettingsHelper";

export default {
    name: "PayslipViewModal",
    mixins: [ModalMixin],
    props: {
        payslip: {},
        settings: {}
    },
    data() {
        return {
            urlGenerator,
            formatCurrency,
            customDateFormat,
            getDateDifferenceString,
            numberFormatter
        }
    },
    methods: {
        getPercentageOfSalary(amount) {
            return ((this.basicSalary / 100) * Number(amount));
        },
    },
    computed: {
        ...mapState({
            generalSettings: state => state.settings
        }),
        logo() {
            if (this.payrunData.logo){
                return urlGenerator(this.payrunData.logo)
            }
            return this.$store.state.settings.tenant_logo
                ? urlGenerator(this.$store.state.settings.tenant_logo)
                : urlGenerator('/images/logo/default-logo.png')
        },
        payrunData() {
            return JSON.parse(this.payslip.payrun.data);
        },
        user() {
            return this.payslip.user;
        },
        basicSalary(){
            return Number(this.payslip.basic_salary);
        },
        earnedSalary(){
            return Number(this.payslip.net_salary) - this.totalBeneficiary;
        },
        beneficiaries(){
            return this.payslip.beneficiaries.length
                ? this.payslip.beneficiaries
                : (parseInt(this.payslip.without_beneficiary) ? [] : this.payslip.payrun.beneficiaries);

        },
        totalBeneficiary() {
            if (!this.beneficiaries.length) {
                return 0;
            }
            return Number(this.totalAllowances - this.totalDeductions);
        },
        totalAllowances() {
            return this.beneficiaries.reduce((allowance, beneficiary) => {
                if (beneficiary.beneficiary.type === 'allowance') {
                    return parseInt(beneficiary.is_percentage)
                        ? ((this.basicSalary / 100) * Number(beneficiary.amount)) + allowance
                        : Number(beneficiary.amount) + allowance;
                } else {
                    return allowance;
                }
            }, 0);
        },
        totalDeductions() {
            return this.beneficiaries.reduce((deduction, beneficiary) => {
                if (beneficiary.beneficiary.type === 'deduction') {
                    return parseInt(beneficiary.is_percentage)
                        ? ((this.basicSalary / 100) * Number(beneficiary.amount)) + deduction
                        : Number(beneficiary.amount) + deduction;
                } else {
                    return deduction;
                }
            }, 0);
        }
    }
}
</script>
