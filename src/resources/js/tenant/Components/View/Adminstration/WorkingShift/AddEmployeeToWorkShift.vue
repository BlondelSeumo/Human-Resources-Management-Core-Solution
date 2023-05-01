<template>
    <modal id="working-shift-employee-modal"
           v-model="showModal"
           :title="$addLabel('employee')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
           :scrollable="false"
    >
        <form
            :data-url='`${apiUrl.WORKING_SHIFTS}/${id}/add-employees`'
            method="POST"
            ref="form"
        >
            <app-note
                class="mb-4"
                :title="$t('note')"
                :notes="(checkDisable && $can('update_working_shifts') && !viewOnly) ?
                      $t('this_workshift_is_read_only_due_to_attendance_history') :
                      $t('working_shift_update_note')"
            />
            <app-form-group-selectable
                type="multi-select"
                :label="$t('employee')"
                list-value-field="full_name"
                v-model="formData.users"
                :error-message="$errorMessage(errors, 'users')"
                :fetch-url="`${apiUrl.TENANT_SELECTABLE_USER}?without=admin&employee=only`"
            />
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "AddEmployeeToWorkShift",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        id: {
            required: true
        }
    },
    data() {
        return {
            formData: {
                users: []
            }
        }
    },
    mounted() {
        this.preloader = true;
        this.getWorkShiftUsers()
    },
    methods: {
        afterSuccess({data}) {
            this.formData = {users: []};
            $('#working-shift-employee-modal').modal('hide')
            this.toastAndReload(data.message, 'working-shift-table');
        },
        getWorkShiftUsers() {
            axiosGet(`${this.apiUrl.WORKING_SHIFTS}/${this.id}/users`).then(({data}) => {
                this.preloader = false;
                this.formData.users = data;
            })
        }
    },
}
</script>