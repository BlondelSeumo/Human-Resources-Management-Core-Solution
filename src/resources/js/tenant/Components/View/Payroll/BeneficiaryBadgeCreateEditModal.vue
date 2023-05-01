<template>
    <modal id="beneficiary-badge-create-edit-modal"
           size="large"
           v-model="showModal"
           :title="generateModalTitle('badge')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader">
        <form ref="form"
              :data-url='selectedUrl ? selectedUrl : apiUrl.BENEFICIARY_BADGE'
              @submit.prevent="submitData">

            <app-form-group
                :label="$t('name')"
                type="text"
                v-model="formData.name"
                :placeholder="$placeholder('name', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />
            <app-form-group
                type="radio"
                page="page"
                class="my-4"
                :label="$t('beneficiary_type')"
                radio-checkbox-name="beneficiary_type"
                v-model="formData.type"
                :error-message="$errorMessage(errors, 'type')"
                :list="[
                {id:'allowance', value:this.$t('allowance')},
                {id:'deduction', value:this.$t('deduction')}
             ]"
            />
            <app-form-group
                type="textarea"
                :label="$fieldLabel('description', '')"
                :placeholder="$textAreaPlaceHolder('description')"
                v-model="formData.description"
                :required="true"
                :error-message="$errorMessage(errors, 'description')"
            />

            <app-form-group
                :label="$t('active')"
                v-model="formData.is_active"
                page="page"
                type="switch"
            />

        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";

export default {
    name: "BeneficiaryBadgeCreateEditModal",
    mixins: [FormHelperMixins, ModalMixin],
    data(){
        return{
            formData:{
                is_active: true,
            }
        }
    },
    methods: {
        afterSuccess({data}) {
            this.formData = {};
            $('#beneficiary-badge-create-edit-modal').modal('hide');
            this.toastAndReload(data.message, 'beneficiary-badge-table')
            this.$emit('input', false);
        },
        afterSuccessFromGetEditData({data}){
            this.formData = data;
            this.formData.is_active = parseInt(data.is_active);
            this.preloader = false;
        }
    }
}
</script>

<style scoped>

</style>