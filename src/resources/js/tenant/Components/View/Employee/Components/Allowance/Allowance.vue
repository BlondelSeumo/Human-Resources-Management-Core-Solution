<template>
    <div>
        <app-overlay-loader v-if="preloader || loader"/>
        <template v-else>
            <app-note
                class="mb-primary"
                :title="$t('allowance_policy')"
                content-type="html"
                :notes="`1. ${$t('leave_year_start_from')} ${policyMonth}
                        <br>2. ${$t('any_type_of_change_will_be_effected_from_next_day')}`"
            />

            <div class="row">
                <div v-for="allowance in allowances" class="col-sm-6 col-md-6 col-xl-3 mb-primary">
                    <div class="allowance-card">
                        <div
                            class="default-base-color radius-top-8 d-flex align-items-center justify-content-between border-bottom p-4">
                            <p class="mb-0">{{ allowance.leave_type.name }}</p>
                            <div class="dropdown options-dropdown"  v-if="$can('update_employee_leave_amount')">
                                <button type="button"
                                        class="btn-option btn d-flex align-items-center justify-content-center"
                                        data-toggle="dropdown">
                                    <app-icon name="more-horizontal"/>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right radius-8 py-2 mt-1">
                                    <a class="dropdown-item px-4 py-2"
                                       href="#"
                                       @click.prevent="updateItem(allowance)">
                                        {{ $t('edit') }}
                                    </a>
<!--                                    <a class="dropdown-item px-4 py-2"-->
<!--                                       href="#"-->
<!--                                       @click.prevent="deleteItem(allowance)">-->
<!--                                        {{ $t('remove') }}-->
<!--                                    </a>-->
                                </div>
                            </div>
                        </div>
                        <div class="default-base-color radius-bottom-8 border-top p-4">
                            <p class="mb-1">
                                <span class="text-muted">{{ $t('type') }}:</span> {{ $t(allowance.leave_type.type) }}
                            </p>
                            <template v-if="parseInt(allowance.leave_type.is_earning_enabled)">
                                <p class="mb-1">
                                    <span class="text-muted">{{ $t('earning_rate') }}:</span>
                                    {{ Number(allowance.leave_type.amount / 12).toFixed(2) + ' /' + $t('month') }}
                                </p>
                            </template>
                            <p class="mb-1">
                                <span class="text-muted">{{ $t('allowance') }}:</span>
                                {{ Number(allowance.leave_type.amount).toFixed(2) }}
                            </p>
                            <p class="mb-1">
                                <span class="text-muted">{{ $t('earned') }}:</span>
                                {{ Number(allowance.amount).toFixed(2) }}
                            </p>
                            <p class="mb-1">
                                <span class="text-muted">{{ $t('taken') }}:</span>
                                {{ Number(allowance.taken).toFixed(2) }}
                            </p>
                            <p class="mb-0">
                                <span class="text-muted">{{ $t('availability') }}:</span>
                                {{ Number(Number(allowance.amount) - Number(allowance.taken)).toFixed(2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="deleteAllowance"
            @cancelled="cancelled"
        />
        <app-allowance-update-modal
            v-if="allowanceUpdateModalActive"
            v-model="allowanceUpdateModalActive"
            :selected-url="selectedUrl"
            @input="updateAllowance"
        />

    </div>
</template>

<script>
import {axiosDelete, axiosGet, axiosPatch} from "../../../../../../common/Helper/AxiosHelper";
import {mapState} from "vuex";
import moment from "moment";

export default {
    name: "Allowance",
    data() {
        return {
            allowances: [],
            preloader: false,
            policyMonth: '',
            confirmationModalActive: false,
            allowanceDataForAction: null,
            allowanceUpdateModalActive: false,
            selectedUrl: '',
        }
    },
    methods: {
        getAllowances() {
            this.preloader = true;
            axiosGet(`${this.apiUrl.LEAVES}/${this.employeeDetails.id}/allowances`).then(response => {
                this.allowances = response.data.allowances;

                this.policyMonth = moment().month(moment().locale('en').month(response.data.start_month).format('M') - 1 ).format('MMMM')

            }).finally(() => {
                this.preloader = false;
            });
        },
        cancelled() {
            this.confirmationModalActive = false;
            this.allowanceDataForAction = null;
        },
        deleteItem(item) {
            this.allowanceDataForAction = item;
            this.confirmationModalActive = true;
        },
        updateItem(item) {
            this.allowanceDataForAction = item;
            this.allowanceUpdateModalActive = true;
            this.selectedUrl = `${this.apiUrl.LEAVES}/${item.id}/leave-type`;
        },
        deleteAllowance() {
            this.preloader = true;
            axiosDelete(`${this.apiUrl.LEAVES}/${this.allowanceDataForAction.id}/leave-type`).then(response => {
                this.$toastr.s(response.data.message);
                this.allowances = this.allowances.filter((allowance) => allowance.id !== this.allowanceDataForAction.id);
                this.allowanceDataForAction = null;
            }).catch(({response}) => {
                this.$toastr.e(response.data.message);
                this.allowanceDataForAction = null;
            }).finally(() => {
                this.preloader = false;
                this.confirmationModalActive = false;
            });
        },
        updateAllowance() {
            this.getAllowances();
        }
    },
    computed: {
        ...mapState({
            loader: state => state.loading,
            employeeDetails: state => state.employees.employee,
        }),
    },
    watch: {
        'employeeDetails.id': {
            handler: function (id) {
                if (id) {
                    this.getAllowances();
                }
            },
            immediate: true
        }
    }
}
</script>