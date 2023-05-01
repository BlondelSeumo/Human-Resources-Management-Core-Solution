<template>
    <modal id="employee-address-details-modal"
           v-model="showModal"
           size="large"
           :title="$fieldTitle(actionType , title === 'present_address' ? 'current_address' : title, true)"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
           :scrollable="false"
    >
        <form ref="form"
              :data-url="url"
              @submit.prevent="submitData">
            <app-form-group
                :label="$t('address_details')"
                type="textarea"
                :placeholder="$textAreaPlaceHolder('address_details')"
                v-model="formData.details"
                :required="true"
                :error-message="$errorMessage(errors, 'details')"
            />

            <div class="row">
                <div class="col-md-6">
                    <app-form-group
                        :label="$t('area')"
                        :placeholder="$placeholder('area')"
                        v-model="formData.area"
                        :error-message="$errorMessage(errors, 'area')"
                    />
                </div>
                <div class="col-md-6">
                    <app-form-group
                        :label="$t('city')"
                        :placeholder="$placeholder('city')"
                        v-model="formData.city"
                        :error-message="$errorMessage(errors, 'city')"
                    />
                </div>
                <div class="col-md-6">
                    <app-form-group
                        :label="$t('state')"
                        :placeholder="$placeholder('state')"
                        v-model="formData.state"
                        :error-message="$errorMessage(errors, 'state')"
                    />
                </div>
                <div class="col-md-6">
                    <app-form-group
                        :label="$t('zip_code')"
                        :placeholder="$placeholder('zip_code')"
                        v-model="formData.zip_code"
                        :error-message="$errorMessage(errors, 'zip_code')"
                    />
                </div>
                <div class="col-md-6">
                    <app-form-group
                        type="select"
                        :label="$t('country')"
                        :chooseLabel="$t('country')"
                        v-model="formData.country"
                        :list="countries"
                        form-group-class="mb-md-0"
                        :error-message="$errorMessage(errors, 'country')"
                    />
                </div>
                <div class="col-md-6">
                    <app-form-group
                        type="tel-input"
                        :label="$t('phone_number')"
                        :placeholder="$placeholder('phone_number')"
                        v-model="formData.phone_number"
                        form-group-class="mb-0"
                        :error-message="$errorMessage(errors, 'phone_number')"
                    />
                </div>
            </div>
        </form>
    </modal>
</template>
<script>

import ModalMixin from "../../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../../common/Mixin/Global/FormHelperMixins";
import {addChooseInSelectArray} from "../../../../../../common/Helper/Support/FormHelper";
import {flatObjectWithKey} from "../../../../../../common/Helper/ObjectHelper";
import countries from "../../Helper/countries";

export default {
    name: "EmployeeAddressDetailsEditModal",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        title: {},
        address: {},
        url: {}
    },
    data() {
        return {
            actionType: '',
            formData: {
                __method: 'PATCH',
                type: this.title
            }
        }
    },
    methods: {
        afterSuccess({data}) {
            this.loading = false;
            $('#employee-address-details-modal').modal('hide');
            this.$toastr.s('', data.message);
            this.$emit('reload')
        },
    },
    computed: {
        countries() {
            return addChooseInSelectArray(countries, 'value', this.$t('country'))
        }
    },
    created() {
        this.actionType = this.address.value ? 'edit' : 'add';
        this.formData = flatObjectWithKey(this.address, 'value');
        this.formData._method = 'PATCH';
        this.formData.type = this.title;
    }
}
</script>