<template>
    <div>
        <app-overlay-loader v-if="preloader"/>
        <form v-else @submit.prevent="submitData" ref="form" :data-url="`${apiUrl.PAYROLL_SETTINGS}/beneficiaries`">

            <app-note
                class="mb-primary"
                :title="$t('how_badge_value_work')"
                content-type="html"
                :notes="`<ol>
                        <li>${$t('create_badge_for_allowance_or_deduction_from_beneficiary_badge_module',{
                            beneficiary_badge : `<a href='${urlGenerator(apiUrl.BENEFICIARY_BADGE_FRONTEND)}'>${$t('beneficiary_badge')}</a>`
                        })}</li>
                        <li>${$t('select_badge_and_assign_a_value_which_will_applicable_for_all_employees_while_execute_payrun')}</li>
                        <li>${$t('you_can_set_beneficiary_individually_over_the_default_from_the_employees_details',{
                            employees : `<a href='${urlGenerator(apiUrl.EMPLOYEES_FRONTEND)}'>${$t('employees')}</a>`
                        })}</li>
                        <li>${$t('you_can_also_update_beneficiaries_in_payslip_generated_for_every_employee',{
                            payslip : `<a href='${urlGenerator(apiUrl.PAYSLIP_FRONTEND)}'>${$t('payslip')}</a>`
                        })}</li>
                     </ol>`"
            />

            <app-form-group
                type="multi-select"
                page="page"
                :label="$t('allowance')"
                list-value-field="name"
                v-model="formData.allowances"
                :error-message="$errorMessage(errors, 'allowances')"
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
                :error-message="$errorMessage(errors, 'deductions')"
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

            <div class="row mt-4">
                <div class="col-3"></div>
                <div class="col-9">
                    <app-submit-button :label="$t('save')" :loading="loading" @click="submitData"/>
                </div>
            </div>
        </form>
        <app-confirmation-modal
            v-if="warningModalActive"
            :message="this.$t('add_default_payrun_setting_first')"
            :title="this.$t('warning')"
            firstButtonName="Ok"
            secondButtonName="Later"
            modal-class="warning"
            icon="alert-circle"
            modal-id="app-confirmation-modal"
            @confirmed="redirectDefaultPayrunSetting"
            @cancelled="warningModalActive = false"
        />
    </div>
</template>

<script>
import FormHelperMixins from "../../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet, urlGenerator} from "../../../../../../common/Helper/AxiosHelper";
import {PAYROLL_SETTINGS_FRONTEND} from "../../../../../Config/ApiUrl";

export default {
    name: "BadgeValueSetting",
    mixins: [FormHelperMixins],
    data() {
        return {
            warningModalActive: false,
            beneficiaries: [],
            prevAllowances: [],
            prevDeductions: [],
            prevAllowancePercentages: [],
            prevDeductionPercentages: [],
            formData: {
                allowances: [],
                allowanceValues: [],
                deductions: [],
                deductionValues: [],
                allowancePercentages: [],
                deductionPercentages: [],
            }
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

        afterSuccess(response) {
            this.toastAndReload(response.data.message)
        },
        getSetting() {
            this.preloader = true;
            axiosGet(this.apiUrl.PAYROLL_SETTINGS).then(({data}) => {
                if (!parseInt(data.is_default)) {
                    this.warningModalActive = true;
                }
                this.formData = {
                    allowances: data.beneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.beneficiary_id)),
                    allowanceValues: data.beneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.amount)),
                    allowancePercentages: data.beneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.is_percentage)),
                    deductions: data.beneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.beneficiary_id)),
                    deductionValues: data.beneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.amount)),
                    deductionPercentages: data.beneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.is_percentage)),
                }
                this.prevAllowances = data.beneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.beneficiary_id))
                this.prevAllowancePercentages = data.beneficiaries.filter(b => b.beneficiary?.type === 'allowance').map(b => Number(b.is_percentage))
                this.prevDeductions = data.beneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.beneficiary_id))
                this.prevDeductionPercentages = data.beneficiaries.filter(b => b.beneficiary?.type === 'deduction').map(b => Number(b.is_percentage))
            }).finally(() => {
                this.getBeneficiaries();
            })
        },
        getBeneficiaries() {
            axiosGet(this.apiUrl.SELECTABLE_BENEFICIARY_BADGE).then(({data}) => {
                this.beneficiaries = data
            }).finally(() => {
                this.preloader = false;
            })
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
        submitData() {
            if (this.formData.eligible_audience === 'all') {
                this.formData = {
                    ...this.formData,
                    users: [],
                    departments: [],
                }
            }
            this.setBasicFormActionData()
                .save(this.formData);
        },
        redirectDefaultPayrunSetting() {
            window.location = urlGenerator(this.apiUrl.PAYROLL_SETTINGS_FRONTEND)
        }
    },
    computed: {
        allowances() {
            return this.beneficiaries.filter(benefit => benefit.type === 'allowance');
        },
        deductions() {
            return this.beneficiaries.filter(benefit => benefit.type === 'deduction');
        },
        selectedAllowances() {
            let allowances = this.allowances.filter(benefit => this.formData.allowances.includes(benefit.id));
            let result = [];
            this.formData.allowances.forEach(item => {
                result.push(allowances.find(d => d.id == item))
            })
            return result;
        },
        selectedDeductions() {
            let deductions = this.deductions.filter(benefit => this.formData.deductions.includes(benefit.id));
            let result = [];
            this.formData.deductions.forEach(item => {
                result.push(deductions.find(d => d.id == item))
            })
            return result;
        }
    },
    created() {
        // this.getBeneficiaries();
        this.getSetting();
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