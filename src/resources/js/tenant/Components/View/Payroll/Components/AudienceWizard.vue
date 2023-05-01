<template>
    <div>
        <div style="min-height: 200px">
            <div class="row mt-6">
                <div class="col-3">
                    <label>{{ $t('who_are_allowed_for_payrun') }}</label><br/>
                    <small class="text-muted font-italic">{{ $t('manual_payrun_audience_message') }}</small>
                    <app-message type="error" :message="$errorMessage(errors, 'departments') || $errorMessage(errors, 'users')"/>
                </div>
                <div class="col-9">
                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('by_department')"
                        list-value-field="name"
                        v-model="formData.departments"
                        :fetch-url="apiUrl.SELECTABLE_PAYRUN_DEPARTMENTS"
                        :chooseAll="false"
                    />

                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('by_user')"
                        list-value-field="full_name"
                        v-model="formData.users"
                        :fetch-url="`${apiUrl.TENANT_SELECTABLE_PAYRUN_USER}?except-invited-only=true`"
                        :chooseAll="false"
                    />
                </div>
            </div>
        </div>

        <div class="form-group mt-4">
            <div class="row">
                <div class="col-12">
                    <button type="button" @click.prevent="openPayrun" class="btn btn-secondary mr-2">{{ this.$t('back') }}</button>
                    <button type="button" @click.prevent="goNext" class="btn btn-primary">{{ this.$t('next') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ManualPayrunErrorMixin from "../../../Mixins/ManualPayrunErrorMixin";

export default {
    name: "AudienceWizard",
    mixins: [ManualPayrunErrorMixin],
    data() {
        return {
            formData: {},
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
        goNext() {
            this.$store.dispatch('setPayrunData', this.formData);
            this.$emit('next', true);
        },
        openPayrun() {
            this.$hub.$emit('open-payrun-table');
        }
    },
    computed: {
        statePayrunData(){
            return this.$store.state.payrun.payrunData
        }
    },
    watch: {
        statePayrunData: {
            handler: function (data){
                this.formData = data;
            },
            immediate: true
        }
    }
}
</script>

