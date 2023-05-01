<template>
    <modal id="employee-status-modal"
           size="small"
           alignModal="center"
           hideHeader="false"
           :hide-footer="true"
           v-model="showModal"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader">

        <form class="position-relative mb-0"
              :class="{'loading-opacity': loading}"
              ref="form"
              :data-url="`${EMPLOYEES}/${id}`+'/rejoin'">
            <app-overlay-loader v-if="loading"/>

            <app-form-group-selectable
                formGroupClass="mb-0"
                type="select"
                :label="$t('employment_status')"
                list-value-field="name"
                v-model="formData.employment_status_id"
                :chooseLabel="$t('employment_status')"
                :error-message="$errorMessage(errors, 'employment_status_id')"
                :fetch-url="`${SELECTABLE_EMPLOYMENT_STATUS}?excluded=terminated`"
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
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";

import {
    SELECTABLE_EMPLOYMENT_STATUS,
    EMPLOYEES

} from "../../../Config/ApiUrl";

export default {
    mixins: [FormHelperMixins, ModalMixin],
    name: "EmploymentStatus",

    props: {
        id: {}
    },
    data() {
        return {
            SELECTABLE_EMPLOYMENT_STATUS,
            EMPLOYEES,
            formData: {
                _method: 'PATCH'
            }
        }
    },
    mounted() {
        $('#employee-status-modal').on('hidden.bs.modal', function (e) {
            $('#employee-status-modal').modal('hide');
            $(".modal-backdrop").remove();
        })
    },
    methods: {
        afterSuccess({data}) {
            this.toastAndReload(data.message, 'employee-table');
            $('#employee-status-modal').modal('hide');
            $(".modal-backdrop").remove();
            this.$emit('update_employment_statuses')
        },
    },
}
</script>