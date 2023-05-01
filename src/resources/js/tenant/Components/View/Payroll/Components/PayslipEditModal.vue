<template>
    <modal id="payslip-edit-modal"
           size="large"
           v-model="showModal"
           :title="`Edit ${this.$t('payslip')}`"
           @submit="submit"
           :preloader="preloader">

        <div class="d-flex justify-content-center mb-primary">
            <div class="text-center">
                <img :src="logo" alt="logo" class="radius-5 mb-2" style="max-height: 100px; max-width: 150px;"/>
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
                <p class="m-0">{{ this.$t('payslip_for') }} : <span
                    class="text-info">{{ getDateDifferenceString(payslip.start_date, payslip.end_date) }}</span></p>
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
            <div class="d-flex justify-content-between border-bottom mb-2">
                <h6>{{ this.$t('beneficiary') }}</h6>
            </div>

            <div class="text-muted">
                <app-form-group
                    type="multi-select"
                    page="page"
                    :label="$t('allowance')"
                    list-value-field="name"
                    v-model="formData.allowances"
                    :list="allowances"
                    @deletedId="removeAllowanceId"
                    @addedId="addAllowanceId"
                />
                <div v-if="formData.allowances.length && beneficiaries.length">
                    <div class="row" v-for="(allowance, index) in selectedAllowances" :key="index">
                        <div class="col-3"></div>
                        <div class="col-4 form-group">
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-xl-6 col-md-6 col-sm-12 col-xs-12">
                                    <label class="text-left d-block mb-lg-0">
                                        {{ allowance.name }}
                                    </label>
                                </div>
                                <div class="col-lg-6 col-xl-6 col-md-6 col-sm-12 col-xs-12">
                                    <app-input
                                        type="number"
                                        v-model="formData.allowanceValues[index]"
                                        :placeholder="'value'"
                                        :required="true"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <app-input
                                :id="`amount-${allowance.id}`"
                                type="single-checkbox"
                                class="mt-2"
                                v-model="formData.allowancePercentages[index]"
                                list-value-field="In Percent(%)"
                            />
                        </div>
                        <div class="col-1 pl-0 ml-0">
                            <a href="#" @click.prevent="removeSelectedAllowance(allowance.id, index)">
                                <app-icon name="trash" class="text-primary mt-2" width="22"/>
                            </a>
                        </div>
                    </div>
                    <div class="row mb-primary" v-if="errors.allowanceValues">
                        <div class="col-3"></div>
                        <div class="col-9">
                            <small class="text-warning">{{ errors.allowanceValues[0] }}</small>
                        </div>
                    </div>
                </div>

                <app-form-group
                    type="multi-select"
                    page="page"
                    :label="$t('deduction')"
                    list-value-field="name"
                    v-model="formData.deductions"
                    :list="deductions"
                    @deletedId="removeDeductionId"
                    @addedId="addDeductionId"
                />
                <div v-if="formData.deductions.length && beneficiaries.length">
                    <div class="row" v-for="(deduction, index) in selectedDeductions" :key="index">
                        <div class="col-3"></div>
                        <div class="col-4 form-group">
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-xl-6 col-md-6 col-sm-12 col-xs-12">
                                    <label class="text-left d-block mb-lg-0">
                                        {{ deduction.name }}
                                    </label>
                                </div>
                                <div class="col-lg-6 col-xl-6 col-md-6 col-sm-12 col-xs-12">
                                    <app-input
                                        type="number"
                                        v-model="formData.deductionValues[index]"
                                        :placeholder="'value'"
                                        :required="true"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <app-input
                                :id="`amount-${deduction.id}`"
                                type="single-checkbox"
                                class="mt-2"
                                v-model="formData.deductionPercentages[index]"
                                list-value-field="In Percent(%)"
                            />
                        </div>
                        <div class="col-1 pl-0 ml-0">
                            <a href="#" @click.prevent="removeSelectedDeduction(deduction.id, index)">
                                <app-icon name="trash" class="text-primary mt-2" width="22"/>
                            </a>
                        </div>
                    </div>
                    <div class="row" v-if="errors.deductionValues">
                        <div class="col-3"></div>
                        <div class="col-9">
                            <small class="text-warning">{{ errors.deductionValues[0] }}</small>
                        </div>
                    </div>
                </div>
            </div>


            <div class="d-flex justify-content-between border-bottom">
                <p class="m-0">{{ this.$t('total_allowance') }}</p>
                <p class="m-0">{{ formatCurrency(numberFormatter(totalAllowances)) }}</p>
            </div>


            <div class="d-flex justify-content-between border-bottom mt-2">
                <p class="m-0">{{ this.$t('total_deduction') }}</p>
                <p class="m-0">{{ formatCurrency(numberFormatter(totalDeductions)) }}</p>
            </div>


            <div class="d-flex justify-content-between border-bottom mt-3">
                <h6>{{ this.$t('beneficiary_amount') }}</h6>
                <h6>{{ formatCurrency(numberFormatter(totalBeneficiary)) }}</h6>
            </div>
        </div>

        <div class="d-flex justify-content-between border-bottom mb-primary">
            <h6>{{ this.$t('net_payable_salary') }}</h6>
            <h6>{{ formatCurrency(numberFormatter(formData.net_salary)) }}</h6>
        </div>

        <p v-if="payrunData.note" class="border-top font-italic"><b>Note: </b>{{payrunData.note}}</p>

    </modal>
</template>

<script>
import {
    customDateFormat,
    getDateDifferenceString
} from "../../../../../common/Helper/Support/DateTimeHelper";
import {mapState} from "vuex";
import {axiosGet, axiosPatch, urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {FormMixin} from "../../../../../core/mixins/form/FormMixin";
import {formatCurrency, numberFormatter} from "../../../../../common/Helper/Support/SettingsHelper";

export default {
    name: "PayslipEditModal",
    mixins: [ModalMixin, FormMixin],
    props: {
        payslip: {},
        settings: {}
    },
    data() {
        return {
            formatCurrency,
            customDateFormat,
            getDateDifferenceString,
            numberFormatter,
            beneficiaries: [],
            prevAllowances: [],
            prevAllowancePercentages: [],
            prevDeductions: [],
            prevDeductionPercentages: [],
            formData: {
                allowances: [],
                allowanceValues: [],
                allowancePercentages: [],
                deductions: [],
                deductionValues: [],
                deductionPercentages: [],
                net_salary: this.payslip.net_salary,
            },
            errors: {},
            earnedSalary: 0,
        }
    },
    created() {
        this.getBeneficiaries();
        this.prevAllowances = this.appliedBeneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.beneficiary_id));
        this.prevAllowancePercentages = this.appliedBeneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.is_percentage));
        this.prevDeductions = this.appliedBeneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.beneficiary_id));
        this.prevDeductionPercentages = this.appliedBeneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.is_percentage));

        this.formData.allowances = this.appliedBeneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.beneficiary_id));
        this.formData.allowanceValues = this.appliedBeneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.amount));
        this.formData.allowancePercentages = this.appliedBeneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.is_percentage));
        this.formData.deductions = this.appliedBeneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.beneficiary_id));
        this.formData.deductionValues = this.appliedBeneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.amount));
        this.formData.deductionPercentages = this.appliedBeneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.is_percentage));
        this.earnedSalary = Number(this.payslip.net_salary) - this.totalBeneficiary;
        this.formData.net_salary = this.earnedSalary + this.totalBeneficiary;
    },
    methods: {
        addAllowanceId(value) {
            this.prevAllowances.push(value)
            this.formData.allowancePercentages.push(false)
            this.prevAllowancePercentages.push(false)
        },
        removeAllowanceId(value) {
            let index = this.prevAllowances.indexOf(value)
            this.prevAllowances.splice(index, 1);
            this.formData.allowanceValues.splice(index, 1);
            this.prevAllowancePercentages.splice(index, 1);
            this.formData.allowancePercentages.splice(index, 1);
        },
        addDeductionId(value) {
            this.prevDeductions.push(value)
            this.formData.deductionPercentages.push(false)
            this.prevDeductionPercentages.push(false)
        },
        removeDeductionId(value) {
            let index = this.prevDeductions.indexOf(value)
            this.prevDeductions.splice(index, 1);
            this.formData.deductionValues.splice(index, 1);
            this.prevDeductionPercentages.splice(index, 1);
            this.formData.deductionPercentages.splice(index, 1);
        },
        removeSelectedAllowance(id, index) {
            this.formData.allowances = this.formData.allowances.filter(item => item != id);
            this.formData.allowanceValues.splice(index, 1);
            this.prevAllowances.splice(index, 1);
            this.formData.allowancePercentages.splice(index, 1);
            this.prevAllowancePercentages.splice(index, 1);
        },
        removeSelectedDeduction(id, index) {
            this.formData.deductions = this.formData.deductions.filter(item => item != id);
            this.formData.deductionValues.splice(index, 1);
            this.prevDeductions.splice(index, 1);
            this.formData.deductionPercentages.splice(index, 1);
            this.prevDeductionPercentages.splice(index, 1);
        },
        getPercentageOfSalary(amount) {
            return ((this.basicSalary / 100) * Number(amount));
        },
        getBeneficiaries() {
            this.preloader = true;
            axiosGet(this.apiUrl.SELECTABLE_BENEFICIARY_BADGE).then(({data}) => {
                this.beneficiaries = data
            }).finally(() => {
                this.preloader = false;
            })
        },
        submit() {
            this.preloader = true;
            axiosPatch(`${this.apiUrl.PAYSLIP}/${this.payslip.id}/update`, this.formData).then(({data}) => {
                this.$emit('updated')
                this.$toastr.s(data.message);
            }).catch((error) => {
                this.errors = error.response.data.errors;
                this.$toastr.e(error.response.data.message);
            }).finally(() => {
                this.preloader = false;
            })
        }
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
        basicSalary() {
            return Number(this.payslip.basic_salary);
        },
        appliedBeneficiaries() {
            return this.payslip.beneficiaries.length
                ? this.payslip.beneficiaries
                : (parseInt(this.payslip.without_beneficiary) ? [] : this.payslip.payrun.beneficiaries);
        },
        totalBeneficiary() {
            return Number(this.totalAllowances - this.totalDeductions);
        },
        totalAllowances() {
            let total = 0;
            this.formData.allowances.forEach((item, index) => {
                let allowanceValue = this.formData.allowanceValues[index] ? Number(this.formData.allowanceValues[index]) : 0;
                this.formData.allowancePercentages[index]
                    ? total += this.getPercentageOfSalary(allowanceValue)
                    : total += allowanceValue;
            })
            return total;
        },
        totalDeductions() {
            let total = 0;
            this.formData.deductions.forEach((item, index) => {
                let deductionValue = this.formData.deductionValues[index] ? Number(this.formData.deductionValues[index]) : 0;
                this.formData.deductionPercentages[index]
                    ? total += this.getPercentageOfSalary(deductionValue)
                    : total += deductionValue;
            })
            return total;
        },
        allowances() {
            return this.beneficiaries.filter(benefit => benefit.type === 'allowance');
        },
        selectedAllowances() {
            let allowances = this.allowances.filter(benefit => this.formData.allowances.includes(benefit.id));
            let result = [];
            this.formData.allowances.forEach(item => {
                result.push(allowances.find(d => d.id == item))
            })
            return result;
        },
        deductions() {
            return this.beneficiaries.filter(benefit => benefit.type === 'deduction');
        },
        selectedDeductions() {
            let deductions = this.deductions.filter(benefit => this.formData.deductions.includes(benefit.id));
            let result = [];
            this.formData.deductions.forEach(item => {
                result.push(deductions.find(d => d.id == item))
            })
            return result;
        },
        submitButtonClass() {
            return (this.formData.allowances.length !== this.formData.allowanceValues.length)
            || (this.formData.deductions.length !== this.formData.deductionValues.length)
                ? 'btn-primary disabled' : 'btn-primary';
        }
    },
    watch: {
        totalBeneficiary: {
            handler: function (totalBeneficiary) {
                this.formData.net_salary = this.earnedSalary + totalBeneficiary
            },
        }
    }
}
</script>