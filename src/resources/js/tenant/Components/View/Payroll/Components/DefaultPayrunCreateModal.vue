<template>
    <div>
        <modal id="default-payrun-modal"
               v-model="showModal"
               :title="`${$t('add_payrun')} (${$t('default')})`"
               @submit="submitData"
               :loading="loading">
            <app-overlay-loader v-if="preloader"/>
            <div v-if="!preloader && !errorStatus">
                <div>
                    <span class="d-block text-warning">{{ $t('all_good_just_have_quick_read') }}</span>
                    <span class="d-block">{{ $t('default_payrun_start_notice') }}</span>
                    <span class="d-block">{{ $t('have_look_generated_default_payrun') }}</span>
                </div>

                <div>
                    <div class="pt-primary" v-if="haveEmployeesFollowedBySettings">
                        <default-payrun-modal-media :payruns="this.payruns" badge-type="default"/>
                    </div>
                    <div class="pt-primary" v-if="haveEmployeesFollowedByEmployee">
                        <default-payrun-modal-media :payruns="this.payruns" badge-type="employee"/>
                    </div>
                </div>

                <div class="mt-3">
                    <app-form-group
                        type="textarea"
                        :text-area-rows="2"
                        v-model="formData.note"
                        :label="`${$t('payslip_note')} (${$t('optional')})`"
                        :placeholder="$t('add_payslip_note_here')"
                    />
                </div>

                <div class="pt-3">
                    <span>{{ $t('make_your_own') }} <a :href="urlGenerator(apiUrl.PAYROLL_SETTINGS_FRONTEND)">settings.</a> {{ $t('also_can') }} <a class="text-primary cursor-pointer" @click.prevent="openManualPayrunModal">Add manual payrun.</a> {{ $t('otherwise_run_default_payrun') }}.</span>
                </div>
            </div>

            <app-note v-if="errorStatus"
                      :title="$t('no_payrun_settings_found')"
                      :notes="[$t('no_payrun_settings_warning', {
                      location: `<a href='${urlGenerator(apiUrl.PAYROLL_SETTINGS_FRONTEND)}'>${$t('here')}</a>`
                  })]"
                      content-type="html"
            />

            <template slot="footer">
                <button type="button"
                        class="btn btn-secondary  mr-2"
                        data-dismiss="modal">
                    {{ $t('cancel') }}
                </button>
                <app-submit-button
                    v-if="!errorStatus"
                    btn-class="btn btn-primary"
                    @click="runPayrun()"
                    :label="$t('run')"
                    :loading="runButtonLoader"
                />
            </template>

        </modal>
    </div>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import DefaultPayrunModalMedia from "./DefaultPayrunModalMedia";
import {axiosGet, axiosPost} from "../../../../../common/Helper/AxiosHelper";
import {DEFAULT_PAYRUN} from "../../../../Config/ApiUrl";

export default {
    name: "DefaultPayrunCreateModal",
    mixins: [ModalMixin, FormHelperMixins],
    components: {DefaultPayrunModalMedia},
    data(){
        return {
            runButtonLoader: false,
            payruns: {},
            errorStatus: false,
        }
    },
    methods: {
        runPayrun(){
            this.runButtonLoader = true;
            axiosPost(
                `${this.apiUrl.DEFAULT_PAYRUN}`, this.formData
            ).then(({data}) => {
                this.formData = {};
                this.runButtonLoader = false;
                this.$toastr.s('', data.message);
                $('#default-payrun-modal').modal('hide');
                this.showModal = false;
                this.$emit('reload');
            }).catch(({response}) => {
                this.runButtonLoader = false;
                this.$toastr.e('', response.data.message);
            })
        },
        getDefaultPayrun(){
            axiosGet(`${this.apiUrl.DEFAULT_PAYRUN}`).then(({data}) => {
                this.payruns = data
                this.preloader = false;
            }).catch(({response}) => {
                this.errorStatus = true
                this.$toastr.e('', response.data.message);
            });
        },
        openManualPayrunModal(){
            this.$emit('openManualPayrun')
            $('#default-payrun-modal').modal('hide');
            this.showModal = false;
        }
    },
    computed: {
        haveEmployeesFollowedByEmployee(){
            return !!this.payruns?.employee?.count_employee;
        },
        haveEmployeesFollowedBySettings(){
            return !!this.payruns?.default?.count_employee;
        }
    },
    mounted() {
        this.getDefaultPayrun();
    }
}
</script>

<style scoped>

</style>