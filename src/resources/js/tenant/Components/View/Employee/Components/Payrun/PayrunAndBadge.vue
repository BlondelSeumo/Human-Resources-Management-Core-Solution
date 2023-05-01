<template>
    <app-overlay-loader v-if="loading"/>

    <div v-else>
        <app-note
            v-if="this.$can('update_employee_beneficiary')"
            class="mb-primary"
            :title="$t('why_payrun_and_badge')"
            content-type="html"
            :notes="`<ol>
                        <li>${$t('by_default_all_payrun_and_beneficiary_badges_is_set_from_default_setting',{
                            setting :  `<a href='${urlGenerator(apiUrl.PAYROLL_SETTINGS_FRONTEND)}'>${$t('setting')}</a>`
                        })}</li>
                        <li>${$t('you_can_individually_update_or_change_these_values_from_the_edit_option')}</li>
                     </ol>`"
        />
        <div class="row">
            <div class="col-4">
                <div class="d-flex align-items-center">
                    <div
                        class="width-55 height-55 default-base-color rounded-circle d-inline-flex flex-shrink-0 align-items-center justify-content-center">
                        <app-icon name="dollar-sign" class="size-24 primary-text-color"/>
                    </div>
                    <div class="ml-2">
                        <h6 class="mb-0">{{ this.$t('payrun_period') }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-6 mt-3">
                <template v-if="employee.payrun_setting">
                    <p class="m-0">{{ this.$t(employee.payrun_setting.payrun_period) }}</p>
                    <p class="m-0">{{ `${this.$t('consider_type')} - ${this.$t(employee.payrun_setting.consider_type)}` }}</p>
                    <small class="m-0" v-if="employee.payrun_setting.consider_type !== 'none'">
                        ({{ `${parseInt(employee.payrun_setting.consider_overtime) ? this.$t('overtime_included') : this.$t('overtime_excluded')}` }})
                    </small>
                </template>
                <template v-else-if="!employee.payrun_restriction && defaultPayrun.setting">
                    <p class="m-0">{{ this.$t(defaultPayrun.setting.payrun_period) }}</p>
                    <p class="m-0">{{ `${this.$t('consider_type')} - ${this.$t(defaultPayrun.setting.consider_type)}` }}</p>
                    <small class="m-0" v-if="defaultPayrun.setting.consider_type !== 'none'">
                        ({{ `${parseInt(defaultPayrun.setting.consider_overtime) ? this.$t('overtime_included') : this.$t('overtime_excluded')}` }})
                    </small>
                </template>
            </div>
            <div class="col-2 mt-3" v-if="this.$can('update_employee_payrun')">
                <a href="#" @click.prevent="employeePayrunPeriodModal = true">
                    <app-icon name="edit" class="size-24"/>
                </a>
            </div>
        </div>

        <div class="row mt-primary">
            <div class="col-4">
                <div class="d-flex align-items-center">
                    <div
                        class="width-55 height-55 default-base-color rounded-circle d-inline-flex flex-shrink-0 align-items-center justify-content-center">
                        <app-icon name="plus" class="size-24 primary-text-color"/>
                    </div>
                    <div class="ml-2">
                        <h6 class="mb-0">{{ this.$t('allowance') }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-6 mt-3">
                    <div v-for="beneficiary in userBeneficiaries">
                        <div class="row mb-1" v-if="beneficiary.beneficiary.type === 'allowance'">
                            <div class="col-7">
                                <p class="m-0">{{ beneficiary.beneficiary.name }}</p>
                            </div>
                            <div class="col-5">
                                <p class="m-0">
                                    <template v-if="parseInt(beneficiary.is_percentage)">{{ beneficiary.amount }} %
                                    </template>
                                    <template v-else>{{ formatCurrency(numberFormatter(beneficiary.amount)) }}</template>
                                </p>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="col-2 mt-3" v-if="this.$can('update_employee_beneficiary')">
                <a href="#" @click.prevent="openBeneficiaryModal('allowance')">
                    <app-icon name="edit" class="size-24"/>
                </a>
            </div>
        </div>

        <div class="row mt-primary">
            <div class="col-4">
                <div class="d-flex align-items-center">
                    <div
                        class="width-55 height-55 default-base-color rounded-circle d-inline-flex flex-shrink-0 align-items-center justify-content-center">
                        <app-icon name="minus" class="size-24 primary-text-color"/>
                    </div>
                    <div class="ml-2">
                        <h6 class="mb-0">{{ this.$t('deduction') }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-6 mt-3">

                <div v-for="(beneficiary,index) in userBeneficiaries">
                    <div class="row mb-1" v-if="beneficiary.beneficiary.type === 'deduction'">
                        <div class="col-7">
                            <p class="m-0">{{ beneficiary.beneficiary.name }}</p>
                        </div>
                        <div class="col-5">
                            <template v-if="parseInt(beneficiary.is_percentage)">{{ beneficiary.amount }} %
                            </template>
                            <template v-else>{{ formatCurrency(numberFormatter(beneficiary.amount)) }}</template>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-2 mt-3" v-if="this.$can('update_employee_beneficiary')">
                <a href="#" @click.prevent="openBeneficiaryModal('deduction')">
                    <app-icon name="edit" class="size-24"/>
                </a>
            </div>
        </div>

        <app-employee-payrun-period-modal
            v-if="employeePayrunPeriodModal"
            v-model="employeePayrunPeriodModal"
            :user-payrun="userPayrun"
            :employee-id="employee.id"
            :employee-payrun="!!employee.payrun_setting"
            @reload="reloadPage"
        />

        <app-employee-beneficiary-modal
            v-if="employeeBeneficiaryModal"
            v-model="employeeBeneficiaryModal"
            :user-beneficiaries="userBeneficiaries"
            :employee-id="employee.id"
            :beneficiary-type="beneficiaryType"
            :employee-beneficiary="!!employee.payrun_beneficiaries.length"
            @reload="reloadPage"
        />

    </div>
</template>

<script>
import {axiosGet, urlGenerator} from "../../../../../../common/Helper/AxiosHelper";
import {formatCurrency, numberFormatter} from "../../../../../../common/Helper/Support/SettingsHelper";

export default {
    name: "PayrunAndBadge",
    props: ['props'],
    data() {
        return {
            formatCurrency,
            numberFormatter,
            urlGenerator,
            employeePayrunPeriodModal: false,
            employeeBeneficiaryModal: false,
            loading: false,
            beneficiaryType: '',
            defaultPayrun: {},
            employee: {}
        }
    },
    created() {
        this.getPayrunSetting();
    },
    methods: {
        getPayrunSetting() {
            this.loading = true;
            axiosGet(`${this.apiUrl.EMPLOYEES}/${this.props.id}/payrun-setting`).then(({data}) => {
                this.employee = data;
                this.defaultPayrun = data.default_payrun ? data.default_payrun : {};
            }).finally(() => {
                this.loading = false;
            })
        },
        openBeneficiaryModal(type) {
            this.employeeBeneficiaryModal = true;
            this.beneficiaryType = type;
        },
        reloadPage() {
            this.getPayrunSetting();
            this.employeePayrunPeriodModal = false;
            this.employeeBeneficiaryModal = false;
            this.beneficiaryType = '';
        }
    },
    computed: {
        currencySymbol() {
            return window.settings.currency_symbol;
        },
        userPayrun() {
            return this.employee.payrun_setting ?
                this.employee.payrun_setting :
                (this.employee.payrun_restriction ? null : this.defaultPayrun.setting);
        },
        userBeneficiaries() {
            if (this.employee.payrun_beneficiaries.length) {
                return this.employee.payrun_beneficiaries;
            } else {
                return (this.employee.beneficiary_restriction ? []
                    : (this.employee.default_payrun ? this.employee.default_payrun.beneficiaries : []));
            }

        }
    }
}
</script>

<style scoped>

</style>