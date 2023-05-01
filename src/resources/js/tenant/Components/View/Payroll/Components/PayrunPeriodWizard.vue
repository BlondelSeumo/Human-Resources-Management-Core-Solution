<template>
    <div>
        <div style="min-height: 200px">
            <app-form-group
                type="select"
                page="page"
                :label="$t('payrun_period')"
                v-model="formData.payrun_period"
                :error-message="$errorMessage(errors, 'payrun_period')"
                :list="[
                {id:'', value:this.$fieldLabel('choose', 'payrun_period'), disabled: true},
                {id:'monthly', value:this.$t('monthly')},
                {id:'customized', value:this.$t('customized')}
             ]"
            />
            <template v-if="formData.payrun_period === 'customized'">
                <app-form-group
                    page="page"
                    :label="$t('start_date')"
                    type="date"
                    v-model="formData.start_date"
                    :placeholder="$placeholder('start_date', '')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'start_date')"
                />
                <app-form-group
                    page="page"
                    :label="$t('end_date')"
                    type="date"
                    v-model="formData.end_date"
                    :min-date="formData.start_date"
                    :max-date="lastOfTheMonth(formData.start_date)"
                    :placeholder="$placeholder('end_date', '')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'end_date')"
                />
            </template>
            <div v-if="formData.payrun_period === 'monthly'">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-xl-3 col-md-3 col-sm-12 col-xs-12">
                        {{ $t('executable_month_year') }}
                    </div>
                    <div class="col-lg-8 col-xl-8 col-md-8 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <app-input
                                    type="select"
                                    v-model="formData.executable_month"
                                    :list="months"
                                    :error-message="$errorMessage(errors, 'executable_month')"
                                />
                            </div>
                            <div class="col-sm-6">
                                <app-input
                                    type="select"
                                    v-model="formData.executable_year"
                                    :list="yearsList"
                                    :error-message="$errorMessage(errors, 'executable_year')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <app-form-group
                type="radio"
                class="mt-primary"
                page="page"
                :label="$t('payrun_generating_type')"
                radio-checkbox-name="consider_type"
                v-model="formData.consider_type"
                :error-message="$errorMessage(errors, 'consider_type')"
                :list="[
                {id:'hour', value:this.$t('hour')},
                {id:'daily_log', value:this.$t('daily_log')},
                {id:'none', value:this.$t('none')}
             ]"
            />
            <div v-if="formData.consider_type === 'hour' || formData.consider_type === 'daily_log'" class="mt-primary">
                <app-form-group
                    label-alignment=""
                    type="switch"
                    page="page"
                    :label="this.$t('consider_overtime')"
                    v-model="formData.consider_overtime"
                >
                    <template slot="suggestion" v-if="formData.consider_overtime">
                        <small class="mt-2 font-italic">
                            ({{ $t('overtime_will_be_calculated_after_the_end_of_the_total_schedule_time') }})
                        </small>
                    </template>
                </app-form-group>
            </div>
        </div>

        <div class="form-group mt-4">
            <div class="row">
                <div class="col-12">
                    <button type="button" @click.prevent="goBack" class="btn btn-secondary mr-2">{{ this.$t('back') }}</button>
                    <button type="button" @click.prevent="goNext" class="btn btn-primary">{{ this.$t('next') }}</button>
                </div>
            </div>
        </div>

        <app-confirmation-modal
            v-if="customizedDateWarningActive"
            :title="$t('attention_please')"
            :message="$t('customized_date_multi_month_warning_message')"
            modal-class="danger"
            icon="alert-circle"
            second-button-name="close"
            :hide-first-button="true"
            modal-id="app-warning-modal"
            @cancelled="closeWarning"
        />
    </div>

</template>

<script>
import moment from "moment";
import ManualPayrunErrorMixin from "../../../Mixins/ManualPayrunErrorMixin";
import {formatDateForServer} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "PayrunPeriodWizard",
    mixins: [ManualPayrunErrorMixin],
    data() {
        return {
            formData: {

            },
            customizedDateWarningActive: false
        }
    },
    props: {
        props: {
            default: ''
        }
    },
    mounted() {
        this.formData = this.$store.state.payrun.payrunData
    },
    methods: {
        closeWarning(){
            this.customizedDateWarningActive = false;
        },
        firstOfTheMonth(date) {
            return moment(date).startOf('month').format('YYYY-MM-DD');
        },
        lastOfTheMonth(date) {
            return moment(date).endOf('month').format('YYYY-MM-DD');
        },
        goBack() {
            this.$emit('back', true);
        },
        goNext() {
            if(moment(this.formData.start_date).isSame(moment(this.formData.end_date), 'month')){
                this.$store.dispatch('setPayrunData', this.formData);

                if (this.formData.start_date) {
                    this.$store.dispatch('setPayrunData', {
                        start_date: formatDateForServer(this.formData.start_date)
                    });
                }

                if (this.formData.end_date) {
                    this.$store.dispatch('setPayrunData', {
                        end_date: formatDateForServer(this.formData.end_date)
                    });
                }
                this.$emit('next', true);
            }else {
                this.customizedDateWarningActive = true;
            }
        }
    },
    computed: {
        months() {
            return Array.apply(0, Array(12)).map((_, i) => {
                return {
                    id: moment().month(i).format('MMM'),
                    value: moment().locale(window.appLanguage).month(i).format('MMMM')
                };
            });
        },
        yearsList() {
            let year = new Date().getFullYear()
            let i = 0, arr = []

            while (i < 50){
                arr.push({
                    id: year,
                    value: year
                })
                i++;
                year--
            }
            return arr
        },
        statePayrunData(){
            return this.$store.state.payrun.payrunData
        },
        executableMonth(){
            return this.formData.executable_month
        }
    },

    watch: {
        statePayrunData: {
            handler: function (data){
                this.formData = data;
            },
            immediate: true
        },
        executableMonth: {
            handler: function (data){
                if (!data) {
                    this.formData.executable_month = moment().subtract(1, "month").format('MMM')
                    this.formData.executable_year = this.formData.executable_month === 'Dec' ? moment().subtract(1, "year").format('YYYY') : moment().format('YYYY')
                    this.formData.consider_overtime = true;
                }
            },
            immediate: true
        },
    }
}
</script>

