<template>
    <modal
        id="employee-termination-reason-modal"
        size="small"
        alignModal="center"
        hideHeader="false"
        v-model="showModal"
        @submit="submitData"
        :loading="loading"
        :btn-label="$t('save')"
        :hide-footer="true"
        :preloader="preloader">

        <form class="position-relative mb-0"
              :class="{'loading-opacity': loading}"
              ref="form"
              :data-url="`${apiUrl.EMPLOYEES}/${id}/update-termination-note`">
            <app-overlay-loader v-if="loading"/>
            <app-input
                type="textarea"
                v-model="formData.description"
                :text-area-rows="4"
                :required="true"
                :error-message="$errorMessage(errors, 'description')"
                :placeholder="$t('enter_employee_termination_reason')"
            />

            <div class="text-right mt-5">
                <a href="#"
                   class="btn btn-secondary mr-1"
                   :class="{'disabled': loading}"
                   data-dismiss="modal"
                   @click="$emit('input', false)">
                    {{ $t('cancel') }}
                </a>
                <a href="#"
                   class="btn btn-primary mr-1"
                   :class="{'disabled': loading}"
                   @click.prevent="submitData">
                    {{ $t('save') }}
                </a>
            </div>
        </form>
    </modal>
</template>

<script>
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";

export default {
    name: "EmployeeTerminationReasonModal",
    mixins: [ModalMixin, FormHelperMixins],
    props: {
        id: {}
    },
    data() {
        return {
            formData: {
                _method: 'PATCH'
            }
        }
    },
    methods: {
        afterSuccess({data}) {
            this.loading = false;
            $('#employee-termination-reason-modal').modal('hide');
            $(".modal-backdrop").remove();
            this.toastAndReload(data.message, 'employee-table');
        }
    }
}
</script>