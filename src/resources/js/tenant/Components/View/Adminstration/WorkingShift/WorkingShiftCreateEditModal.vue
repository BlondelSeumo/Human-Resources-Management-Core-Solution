<template>
    <modal id="working-shift-modal"
           size="large"
           v-model="showModal"
           :title="this.$fieldTitle(this.selectedUrl ?
           (checkDisable ? 'view': 'edit') : 'add', 'work_shift', true)"
           @submit="submit"
           :hide-submit-button="checkDisable"
           :hide-cancel-button="makeDefault"
           :close-button="false"
           :modal-backdrop="!makeDefault"
           :cancel-btn-label="checkDisable ? $t('close') : $t('cancel')"
           :loading="loading"
           :preloader="preloader">

        <form :data-url='selectedUrl ? selectedUrl : apiUrl.WORKING_SHIFTS'
              :class="{'disabled-section' : checkDisable}"
              method="POST"
              ref="form">
            <app-note v-if="this.selectedUrl && !viewOnly"
                      class="mb-4"
                      :title="$t('note')"
                      :notes="(checkDisable && $can('update_working_shifts')) ?
                      $t('this_workshift_is_read_only_due_to_attendance_history') :
                      $t('working_shift_update_note')"
            />
            <app-form-group
                :label="$t('name')"
                type="text"
                v-model="formData.name"
                :placeholder="$placeholder('name', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />
            <div class="row" v-if="!makeDefault && parseInt(formData.is_default) !== 1">
                <div class="col-md-6">
                    <app-form-group
                        :disabled="!!formData.is_default"
                        :label="$t('start_date')"
                        type="date"
                        v-model="formData.start_date"
                        :placeholder="$placeholder('start_date', '')"
                        :required="true"
                        :error-message="$errorMessage(errors, 'start_date')"
                    />
                </div>
                <div class="col-md-6">
                    <app-form-group
                        :disabled="!!formData.is_default"
                        :label="$t('end_date')"
                        type="date"
                        v-model="formData.end_date"
                        :placeholder="$placeholder('end_date', '')"
                        :error-message="$errorMessage(errors, 'end_date')"
                    />
                </div>
            </div>

            <div class="form-group d-flex flex-column flex-md-row align-items-md-center my-primary">
                <label class="mr-md-3 mb-md-0" v-if="checkDisable">{{ $fieldTitle('working_shift_type', '') }}</label>
                <label class="mr-md-3 mb-md-0" v-else>{{ $fieldTitle('choose_a_working_shift_type', '') }}</label>
                <div>
                    <app-input
                        type="radio"
                        :list="[
                            {id: 'regular', value: $t('regular')},
                            {id: 'scheduled', value:  $t('scheduled')}
                        ]"
                        v-model="formData.type"
                        :error-message="$errorMessage(errors, 'type')"
                    />
                </div>
            </div>

            <!--work shift type == regular -->
            <div v-if="formData.type === 'regular'">
                <div class="mt-4">
                    <label v-if="checkDisable">{{ $fieldTitle('regular_week', '') }}</label>
                    <label v-else>{{ $fieldTitle('set_regular_week', '') }}</label>
                    <small class="text-muted">
                        ({{ $fieldTitle('set_week_with_fixed_time', '') }})
                    </small>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ $fieldTitle('start_time', '') }}</label>
                            <app-input
                                :id="'start_at'"
                                type="time"
                                v-model="formData.start_at"
                                :placeholder="$placeholder('start_time', '')"
                                name="start_time"
                                :error-message="$errorMessage(errors, 'start_at')"
                            />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ $fieldTitle('end_time', '') }}</label>
                            <app-input
                                :id="'end-at'"
                                type="time"
                                v-model="formData.end_at"
                                :placeholder="$placeholder('end_time', '')"
                                :error-message="$errorMessage(errors, 'end_at')"
                            />
                        </div>
                    </div>
                </div>

                <div class="my-3">
                    <h6 v-if="checkDisable" class="text-danger">{{ $fieldTitle('weekend_days', '') }}</h6>
                    <h6 v-else class="text-danger">{{ $fieldTitle('select_your_weekend_days', '') }}</h6>
                </div>
                <div class="mb-primary">
                    <app-input
                        type="checkbox"
                        :list="weekdays"
                        v-model="formData.weekdays"
                        radio-checkbox-wrapper="row"
                        custom-checkbox-type="checkbox-default col-md-3"
                        :error-message="$errorMessage(errors, 'weekdays')"
                    />
                </div>
            </div>

            <div v-else>
                <div class="mt-4">
                    <label v-if="checkDisable">{{ $fieldTitle('scheduled_week', '') }}</label>
                    <label v-else>{{ $fieldTitle('set_scheduled_week', '') }}</label>
                    <small class="text-muted">
                        ({{
                            $fieldTitle('set_week_with_customized_time_and_without_time_and_without_time_it_will_be_weekend', '')
                        }})
                    </small>
                </div>
                <div class="row">
                    <div v-for="(item, index) in formData.details" class="col-md-6">
                        <label>
                            {{ $t(item.weekday) }}
                            <span class="text-muted">
                                {{ !(item.start_at && item.end_at) ? `(${$t('weekend')})` : '' }}
                            </span>
                        </label>
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <app-input
                                        :id="'start-at-'+index"
                                        type="time"
                                        v-model="item.start_at"
                                        :placeholder="$t('start_time')"
                                        :error-message="errorMessageForArray(
                                            errors,
                                        `details.${index}.start_at`,
                                        `details.${index}.end at`
                                        )"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <app-input
                                        :id="'end-at-'+index"
                                        type="time"
                                        v-model="item.end_at"
                                        :placeholder="$t('end_time')"
                                        :error-message="errorMessageForArray(
                                            errors,
                                            `details.${index}.end_at`,
                                            `details.${index}.start at`
                                        )"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <app-form-group
                type="textarea"
                :label="$fieldLabel('description', '')"
                :placeholder="`${checkDisable ? '' : $textAreaPlaceHolder('description')}`"
                v-model="formData.description"
                :required="true"
                :error-message="$errorMessage(errors, 'description')"
            />

            <template v-if="parseInt(formData.is_default) !== 1 && !viewOnly && !makeDefault">
                <app-form-group-selectable
                    :label="$fieldLabel('department', '')"
                    type="multi-select"
                    v-model="formData.departments"
                    list-value-field="name"
                    :error-message="$errorMessage(errors, 'departments')"
                    :fetch-url="apiUrl.SELECTABLE_WORK_SHIFT_DEPARTMENTS"
                />

                <app-form-group-selectable
                    type="multi-select"
                    :label="$t('employee')"
                    list-value-field="full_name"
                    v-model="formData.users"
                    form-group-class="mb-0"
                    :error-message="$errorMessage(errors, 'users')"
                    :fetch-url="`${apiUrl.SELECTABLE_WORK_SHIFT_USERS}?without=admin&excluded_departments=${formData.departments}&employee=only`"
                />
            </template>
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {localToUtc, formatDateForServer, formatUtcToLocal} from "../../../../../common/Helper/Support/DateTimeHelper";
import {cloneDeep} from 'lodash'
import {errorMessageForArray} from '../../../../../common/Helper/Support/FormHelper'

export default {
    name: "WorkingShiftCreateEditModal",
    mixins: [FormHelperMixins, ModalMixin],
    props:{
      viewOnly:{
          type: Boolean,
          default: false,
      },
      makeDefault:{
          type: Boolean,
          default: false,
      }
    },
    data() {
        return {
            errorMessageForArray,
            weekdays: [
                {id: "sun", value: this.$t("sunday")},
                {id: "mon", value: this.$t("monday")},
                {id: "tue", value: this.$t("tuesday")},
                {id: "wed", value: this.$t("wednesday")},
                {id: "thu", value: this.$t("thursday")},
                {id: "fri", value: this.$t("friday")},
                {id: "sat", value: this.$t("saturday")},
            ],
            formData: {
                name: '',
                type: 'regular',
                departments: [],
                weekdays: [],
                users: [],
                attendances_count: 0,
                details: [
                    {
                        weekday: 'sun',
                        start_at: '',
                        end_at: '',
                        is_weekend: 0
                    },
                    {
                        weekday: 'mon',
                        start_at: '',
                        end_at: '',
                        is_weekend: 0
                    },
                    {
                        weekday: 'tue',
                        start_at: '',
                        end_at: '',
                        is_weekend: 0
                    },
                    {
                        weekday: 'wed',
                        start_at: '',
                        end_at: '',
                        is_weekend: 0
                    },
                    {
                        weekday: 'thu',
                        start_at: '',
                        end_at: '',
                        is_weekend: 0
                    },
                    {
                        weekday: 'fri',
                        start_at: '',
                        end_at: '',
                        is_weekend: 0
                    },
                    {
                        weekday: 'sat',
                        start_at: '',
                        end_at: '',
                        is_weekend: 0
                    }
                ],
            },
        }
    },
    mounted() {
        if(this.makeDefault){
            this.formData.name = this.$t('regular_work_shift');
            this.formData.start_date = new Date()
        }
    },
    computed:{
      checkDisable () {
          return this.selectedUrl ? (this.formData.attendances_count > 0 || !this.$can('update_working_shifts') || this.viewOnly ):
              !this.$can('create_working_shifts')
        }
    },
    methods: {
        submit() {
            this.loading = true;
            this.message = '';
            this.errors = {};

            let formData = {};
            if (this.formData.type === 'regular') {
                formData = this.prepareRegularType(this.formData);
            } else {
                formData = this.prepareSchedulerType(this.formData);
            }

            formData.start_date = formatDateForServer(formData.start_date);
            formData.end_date = formatDateForServer(formData.end_date);
            this.makeDefault ? formData.is_default = 1 : null;

            this.save(formData);
        },
        afterSuccess({data}) {
            this.formData = {};
            $('#working-shift-modal').modal('hide')
            this.toastAndReload(data.message, 'working-shift-table');
        },
        afterSuccessFromGetEditData({data}) {
            this.formData = data;
            this.formData.start_date = data.start_date ? new Date(data.start_date) : '';
            this.formData.end_date = data.end_date ? new Date(data.end_date) : '';
            this.formData.start_at = formatUtcToLocal(data.start_at);
            this.formData.end_at = formatUtcToLocal(data.end_at);
            this.formData.weekdays = data.details.filter(week => {
                return parseInt(week.is_weekend);
            }).map(week => {
                return week.weekday;
            });

            this.formData.details = data.details.map(details => {
                return {
                    ...details,
                    start_at: formatUtcToLocal(details.start_at),
                    end_at: formatUtcToLocal(details.end_at),
                }
            });

            this.formData.departments = this.collection(data.departments).pluck()
                .concat(this.collection(data.upcoming_departments).pluck())
            this.formData.users = this.collection(data.users).pluck()
                .concat(this.collection(data.upcoming_users).pluck())
            this.preloader = false;
        },
        prepareRegularType(formData) {
            let data = cloneDeep(formData);

            if (!(data.start_at && data.end_at)) {
                return data;
            }

            data.details.map(day => {
                if (data.weekdays.includes(day.weekday)) {
                    day.is_weekend = 1;
                    return day;
                }

                day.end_at = localToUtc(data.end_at);
                day.start_at = localToUtc(data.start_at);
                day.is_weekend = data.weekdays.includes(day.weekday) ? 1 : 0
                return day;
            });

            return data;
        },
        prepareSchedulerType(formData) {
            let data = cloneDeep(formData);
            data.details.map(day => {
                day.start_at = localToUtc(day.start_at);
                day.end_at = localToUtc(day.end_at);
                day.is_weekend = !!(day.start_at && day.end_at) ? 0 : 1
                return day;
            })
            return data;
        }
    },
}
</script>