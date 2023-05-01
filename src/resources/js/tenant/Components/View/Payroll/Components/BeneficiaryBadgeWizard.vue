<template>
    <div>
        <div style="min-height: 200px">
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
            <div class="pb-2" v-if="formData.allowances.length && beneficiaries.length">
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
                                <app-message type="error" :message="$errorMessage(errors, 'allowanceValues.'+index)"/>
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
                <div class="row">
                    <div class="col-3"></div>
                    <div class="col-9">
                        <app-message type="error" :message="$errorMessage(errors, 'allowanceValues')"/>
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
            <div class="pb-2" v-if="formData.deductions.length && beneficiaries.length">
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
                                <app-message type="error" :message="$errorMessage(errors, 'deductionValues.'+index)"/>
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
                <div class="row">
                    <div class="col-3"></div>
                    <div class="col-9">
                        <app-message type="error" :message="$errorMessage(errors, 'deductionValues')"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-12">
                    <button type="button" @click.prevent="goBack" class="btn btn-secondary mr-2">{{ this.$t('back') }}</button>

                    <app-submit-button
                        btn-class="btn btn-primary"
                        @click="startPayrun"
                        :label="this.$t('confirm_and_run')"
                        :loading="submitLoader"
                    />

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {axiosGet, axiosPatch, axiosPost} from "../../../../../common/Helper/AxiosHelper";
import ManualPayrunErrorMixin from "../../../Mixins/ManualPayrunErrorMixin";

export default {
    name: "BeneficiaryBadgeWizard",
    mixins: [ManualPayrunErrorMixin],
    data() {
        return {
            submitLoader: false,
            beneficiaries: [],
            prevAllowances: [],
            prevDeductions: [],
            prevAllowancePercentages: [],
            prevDeductionPercentages: [],
            errors: {},
            formData: {
                allowances: [],
                allowanceValues: [],
                allowancePercentages: [],
                deductions: [],
                deductionValues: [],
                deductionPercentages: [],
            }
        }
    },
    props: {
        props: {
            default: ''
        }
    },
    methods: {
        goBack() {
            this.$emit('back', true);
        },
        getBeneficiaries() {
            this.preloader = true;
            axiosGet(this.apiUrl.SELECTABLE_BENEFICIARY_BADGE).then(({data}) => {
                this.beneficiaries = data
            }).finally(() => {
                this.preloader = false;
            })
        },
        removeSelectedAllowance(id, index) {
            this.formData.allowances = this.formData.allowances.filter(item => item !== id);
            this.formData.allowanceValues.splice(index, 1);
            this.prevAllowances.splice(index, 1);
            this.formData.allowancePercentages.splice(index, 1);
            this.prevAllowancePercentages.splice(index, 1);
        },
        removeSelectedDeduction(id, index) {
            this.formData.deductions = this.formData.deductions.filter(item => item !== id);
            this.formData.deductionValues.splice(index, 1);
            this.prevDeductions.splice(index, 1);
            this.formData.deductionPercentages.splice(index, 1);
            this.prevDeductionPercentages.splice(index, 1);
        },
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
        startPayrun() {
            this.$store.dispatch('setPayrunData', this.formData);
            this.submitLoader = true;
            !!this.formData.id ? this.update() : this.save()

        },
        save(){
            axiosPost(this.apiUrl.MANUAL_PAYRUN,
                this.$store.state.payrun.payrunData
            ).then(({data}) => {
                this.afterSuccess(data)
            }).catch(({response}) => {
                this.afterError(response);
            });
        },
        update(){
            axiosPatch(`${this.apiUrl.PAYRUNS}/${this.formData.id}`,
                this.$store.state.payrun.payrunData
            ).then(({data}) => {
                this.afterSuccess(data)
            })
        },
        afterSuccess(data){
            this.submitLoader = false;
            this.$hub.$emit('reload-payrun-table');
            this.$toastr.s('', data.message);
        },
        afterError(response){
            this.$toastr.e('', response.data.message);
            this.submitLoader = false;
            this.errors = response.data.errors;
            let pageOne = ['departments', 'users'],
                pageTwo = ['consider_type', 'payrun_period', 'executable_month', 'end_date', 'start_date']

            if (Object.keys(this.errors).some(r=> pageOne.includes(r))){
                this.$emit('back', true);
                this.$emit('back', true);
            }

            if (Object.keys(this.errors).some(r=> pageTwo.includes(r))){
                this.$emit('back', true);
            }

            this.$store.dispatch('setErrors', this.errors);
        },
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
                result.push(allowances.find(d => d.id === item))
            })
            return result;
        },
        selectedDeductions() {
            let deductions = this.deductions.filter(benefit => this.formData.deductions.includes(benefit.id));
            let result = [];
            this.formData.deductions.forEach(item => {
                result.push(deductions.find(d => d.id === item))
            })
            return result;
        },
        statePayrunData(){
            return this.$store.state.payrun.payrunData
        }
    },

    created() {
        this.getBeneficiaries();
    },

    watch: {
        statePayrunData: {
            handler: function (data){
                this.formData = data;
                this.formData.allowances = this.formData.allowances || []
                this.formData.allowancePercentages = this.formData.allowancePercentages || []
                this.formData.allowanceValues = this.formData.allowanceValues || []
                this.formData.deductions = this.formData.deductions || []
                this.formData.deductionValues = this.formData.deductionValues || []
                this.formData.deductionPercentages = this.formData.deductionPercentages || []
            },
            immediate: true
        }
    }

}
</script>
