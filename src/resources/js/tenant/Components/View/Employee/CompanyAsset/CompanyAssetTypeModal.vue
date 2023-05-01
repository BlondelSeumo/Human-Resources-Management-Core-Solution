<template>
    <modal id="company-asset-type-modal"
           size="default"
           v-model="showModal"
           :title="$t(this.selectedUrl ? 'edit_asset_type' : 'add_asset_type')"
           @submit="submit"
           :cancel-btn-label="checkDisable ? $t('close') : $t('cancel')"
           :loading="loading"
           :preloader="preloader">

        <form :data-url='selectedUrl ? selectedUrl : apiUrl.COMPANY_ASSET_TYPES'
              :class="{'disabled-section' : checkDisable}"
              method="POST"
              ref="form"
              @submit.prevent="submit"
        >
            <app-form-group
                :label="$t('name')"
                type="text"
                v-model="formData.name"
                :placeholder="$placeholder('name', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />
        </form>
    </modal>
</template>

<script>
import {COMPANY_ASSET_TYPES} from "../../../../Config/ApiUrl";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";

export default {
    name: "CompanyAssetTypeModal",
    mixins: [FormHelperMixins, ModalMixin],
    data() {
        return {
            COMPANY_ASSET_TYPES
        }
    },
    computed: {
        checkDisable() {
            return this.selectedUrl ? (!this.$can('update_company_asset_types')) :
                !this.$can('create_company_asset_types')
        },
    },
    methods: {
        submit() {
            let formData = {...this.formData};
            this.loading = true;
            this.message = '';
            this.errors = {};
            this.save(formData);
        },
        afterSuccess({data}) {
            this.formData = {};
            $('#company-asset-type-modal').modal('hide');
            this.$emit('input', false);
            this.toastAndReload(data.message, 'company-asset-type-table');
        },
    }
}
</script>