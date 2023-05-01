<template>
    <modal id="employee-allowance-modal"
           size="large"
           v-model="showModal"
           :title="this.$t(`edit_badge`)"
           @submit="submit"
           :scrollable="false"
           :preloader="preloader">

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


        <template slot="footer-space" v-if="employeeBeneficiary">
            <button type="button"
                    class="btn btn-success mr-3"
                    @click="restoreBeneficiary">
                {{ this.$t('restore_to_default') }}
            </button>
        </template>
    </modal>
</template>

<script>
import ModalMixin from "../../../../../../../common/Mixin/Global/ModalMixin";
import {axiosGet, axiosPost} from "../../../../../../../common/Helper/AxiosHelper";

export default {
    name: "EmployeeBeneficiaryModal",
    mixins: [ModalMixin],
    props: {
        userBeneficiaries: {},
        employeeId: {},
        beneficiaryType: {},
        employeeBeneficiary: {},
    },
    data() {
        return {
            beneficiaries: [],
            prevAllowances: this.userBeneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.beneficiary_id)),
            prevAllowancePercentages: this.userBeneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.is_percentage)),
            prevDeductions: this.userBeneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.beneficiary_id)),
            prevDeductionPercentages: this.userBeneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.is_percentage)),
            formData: {
                allowances: this.userBeneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.beneficiary_id)),
                allowanceValues: this.userBeneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.amount)),
                allowancePercentages: this.userBeneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.is_percentage)),
                deductions: this.userBeneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.beneficiary_id)),
                deductionValues: this.userBeneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.amount)),
                deductionPercentages: this.userBeneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.is_percentage)),
            },
            errors: {}
        }
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
            axiosPost(`${this.apiUrl.EMPLOYEES}/${this.employeeId}/payrun-setting/update-beneficiary`, this.formData)
                .then(response => {
                    $('#employee-allowance-modal').modal('hide');
                    this.$toastr.s('', response.data.message);
                    this.$emit('reload')
                }).catch((error) => {
                this.errors = error.response.data.errors;
            }).finally(() => {
                this.preloader = false;
            })
        },
        restoreBeneficiary() {
            this.preloader = true;
            axiosGet(`${this.apiUrl.EMPLOYEES}/${this.employeeId}/payrun-setting/restore?type=beneficiaries`)
                .then(response => {
                    $('#employee-allowance-modal').modal('hide');
                    this.$toastr.s('', response.data.message);
                    this.$emit('reload')
                }).finally(() => {
                this.preloader = false;
            })
        }
    },
    computed: {
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
    created() {
        this.getBeneficiaries();
    },
    // watch: {
    //     formData: {
    //         handler: function (user) {
    //             console.log(user)
    //         },
    //         deep: true
    //     }
    // }
}
</script>

<style scoped>

</style>