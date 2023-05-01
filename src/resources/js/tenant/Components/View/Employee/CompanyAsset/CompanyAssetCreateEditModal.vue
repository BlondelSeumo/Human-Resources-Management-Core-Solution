<template>
    <modal id="company-asset-create-edit-modal"
           size="large"
           v-model="showModal"
           :title="$t(this.selectedUrl ? (editable ? 'edit_asset' : 'preview_asset') : 'add_asset')"
           @submit="submit"
           :cancel-btn-label="checkDisable ? $t('close') : $t('cancel')"
           :hide-footer="!editable"
           :loading="loading"
           :preloader="preloader">

        <form :data-url='selectedUrl ? selectedUrl : apiUrl.COMPANY_ASSETS'
              :class="{'disabled-section' : checkDisable}"
              method="POST"
              ref="form"
              @submit.prevent="submit"
        >
            <app-form-group
                :label="$t('asset_name')"
                type="text"
                v-model="formData.name"
                :placeholder="$placeholder('asset_name', '')"
                :required="true"
                :disabled="!editable"
                :error-message="$errorMessage(errors, 'name')"
            />
            <app-form-group-selectable
                type="search-select"
                :label="$t('asset_type')"
                list-value-field="name"
                :placeholder="$t('select_asset_type')"
                v-model="formData.type_id"
                :required="true"
                :disabled="!editable"
                :error-message="$errorMessage(errors, 'type_id')"
                :fetch-url="`${apiUrl.SELECTABLE_COMPANY_ASSET_TYPES}`"
            />
            <app-form-group
                :label="$t('is_working')"
                type="radio"
                :list="[
                {id:'yes',value: $t('yes')},
                {id:'no', value:  $t('no')},
                {id:'maintenance', value:  $t('maintenance')}
            ]"
                v-model="formData.is_working"
                :disabled="!editable"
                :error-message="$errorMessage(errors, 'is_working')"

            />
            <app-form-group
                :label="$t('asset_code')"
                type="text"
                v-model="formData.code"
                :placeholder="$placeholder('asset_code', '')"
                :required="true"
                :disabled="!editable"
                :error-message="$errorMessage(errors, 'code')"
            />
            <app-form-group
                :label="$t('asset_serial_number')"
                type="text"
                v-model="formData.serial_number"
                :placeholder="$placeholder('asset_serial_number', '')"
                :required="true"
                :disabled="!editable"
                :error-message="$errorMessage(errors, 'serial_number')"
            />
            <div class="row">
                <div class="col-md-12">
                    <app-form-group
                        :label="$t('date')"
                        type="date"
                        v-model="formData.date"
                        :placeholder="$placeholder('date', '')"
                        :required="true"
                        :disabled="!editable"
                        :error-message="$errorMessage(errors, 'date')"
                    />
                </div>
            </div>
            <app-form-group-selectable
                type="search-select"
                :label="$t('assigned_employee')"
                list-value-field="full_name"
                :placeholder="$t('select_assigned_employee')"
                v-model="formData.user_id"
                :required="true"
                :disabled="!editable"
                :error-message="$errorMessage(errors, 'user_id')"
                :fetch-url="`${apiUrl.TENANT_SELECTABLE_USER}?without=admin&employee=only&with_auth=yes`"
            />
            <div class="form-group">
                <label>{{ $t('note') }}</label>
                <app-input
                    type="textarea"
                    v-model="formData.note"
                    id="text-editor-for-note"
                    :placeholder="$textAreaPlaceHolder('note')"
                    row="4"
                    :disabled="!editable"
                    :error-message="$errorMessage(errors, 'note')"
                />
            </div>
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {errorMessageForArray} from '../../../../../common/Helper/Support/FormHelper'
import {formatDateForServer} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "CompanyAssetCreateEditModal",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        editable: {
            required: false,
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            errorMessageForArray,

        }
    },
    computed: {
        checkDisable() {
            return this.selectedUrl ? (!this.$can('update_company_assets')) :
                !this.$can('create_company_assets')
        },
    },
    methods: {
        submit() {
            let formData = {...this.formData};
            formData.date = formatDateForServer(this.formData.date);
            this.loading = true;
            this.message = '';
            this.errors = {};
            this.save(formData);
        },
        afterSuccess({data}) {
            this.formData = {};
            $('#company-asset-create-edit-modal').modal('hide');
            this.$emit('input', false);
            this.toastAndReload(data.message, 'company-asset-table');
        },
        afterSuccessFromGetEditData({data}) {
            this.preloader = false;
            this.formData = data;
            this.formData.date = new Date(data.date);
        },
    },
}
</script>