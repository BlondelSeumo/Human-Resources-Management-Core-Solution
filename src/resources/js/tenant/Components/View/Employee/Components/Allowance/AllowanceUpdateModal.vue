<template>
    <modal id="user-leave-allowance-modal"
           v-model="showModal"
           :title="$t('user_leave')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader">
        <form ref="form" :data-url='this.selectedUrl ? this.selectedUrl : apiUrl.LEAVE_TYPES'>
            <app-form-group
                :label="$t('amount_in_days')"
                type="number"
                v-model="formData.amount"
                :placeholder="$placeholder('amount', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'amount')"
            />

        </form>
    </modal>
</template>

<script>
import ModalMixin from "../../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../../common/Mixin/Global/FormHelperMixins";

export default {
    name: "AllowanceUpdateModal",
    mixins: [ModalMixin, FormHelperMixins],

    data(){
        return {}
    },
    methods:{
        afterSuccess({data}) {
            this.formData = {};
            $('#user-leave-allowance-modal').modal('hide');
            this.$toastr.s('', data.message);
            this.$emit('input', false);
        }
    }

}
</script>
