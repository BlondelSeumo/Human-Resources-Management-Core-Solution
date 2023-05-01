<template>
    <modal id="leave-type-modal"
           v-model="showModal"
           :title="generateModalTitle('leave_type')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader">
        <form ref="form" :data-url='this.selectedUrl ? this.selectedUrl : apiUrl.LEAVE_TYPES'>
            <app-form-group
                :label="$t('name')"
                v-model="formData.name"
                :placeholder="$placeholder('name', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />

            <app-form-group
                :label="$t('type')"
                type="select"
                v-model="formData.type"
                :list="getLeaveTypes"
                :placeholder="$placeholder('type', '')"
                :error-message="$errorMessage(errors, 'type')"
            />

            <app-form-group
                v-if="formData.type === 'special'"
                :label="$t('special_percentage')"
                type="decimal"
                v-model="formData.special_percentage"
                :placeholder="$placeholder('special_percentage', '')"
                :error-message="$errorMessage(errors, 'special_percentage')"
            />

            <app-form-group
                :label="$t('amount')"
                type="decimal"
                v-model="formData.amount"
                :placeholder="$placeholder('amount', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'amount')"
            />
            <div class="d-flex justify-content-between">
                <app-form-group
                    :label="$t('enabled')"
                    type="radio"
                    :radio-checkbox-name="'enabled'"
                    v-model="formData.is_enabled"
                    :required="true"
                    :list="[{'id': 1, 'value': 'Yes'}, {'id': 0, 'value': 'No'}]"
                    :error-message="$errorMessage(errors, 'is_enabled')"
                />
                <app-form-group
                    :label="$t('is_earning_enabled')"
                    type="radio"
                    :radio-checkbox-name="'is_earning_enabled'"
                    v-model="formData.is_earning_enabled"
                    :required="true"
                    :list="[{'id': 1, 'value': 'Yes'}, {'id': 0, 'value': 'No'}]"
                    :error-message="$errorMessage(errors, 'is_earning_enabled')"
                />
            </div>
        </form>
    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {addSelectInSelectArray} from "../../../../../common/Helper/Support/FormHelper";

export default {
    name: "LeaveTypeCreateEditModal",
    mixins: [ModalMixin, FormHelperMixins],
    data() {
        return {
            formData: {
                is_enabled: true,
                is_earning_enabled: true,
            }
        }
    },
    methods: {
        afterSuccess({data}) {
            this.toastAndReload(data.message, 'leave-types-table');
            this.formData = {};
            $('#leave-type-modal').modal('hide');
            this.$emit('input', false);
        }
    },
    computed: {
        getLeaveTypes() {
            return addSelectInSelectArray([
                {id: 'paid', value: this.$t('paid')},
                {id: 'unpaid', value: this.$t('unpaid')},
            ], 'value', 'type');
        }
    }
}
</script>

<style scoped>

</style>
