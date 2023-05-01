<template>
    <modal id="attendance-modal"
           size="large"
           v-model="showModal"
           :title="$fieldTitle(buttonFirstTitle, 'attendance', true)"
           @submit="submitData"
           :scrollable="false"
           :loading="loading"
           :preloader="preloader">
        <form
            ref="form"
            @submit.prevent="submitData"
        >
            <app-form-group-selectable
                :label="$t('employee')"
                type="search-select"
                v-if="canUpdateStatus"
                v-model="formData.employee_id"
                list-value-field="full_name"
                :placeholder="$t('search_and_select_an_employee')"
                :error-message="$errorMessage(errors, 'employee_id')"
                :fetch-url="`${apiUrl.TENANT_SELECTABLE_USER}?without=admin&employee=only&with_auth=yes`"
            />

            <app-form-group
                :label="$t('punch_in_time')"
                type="date"
                date-mode="dateTime"
                :max-date="new Date()"
                v-model="formData.in_time"
                :placeholder="$placeholder('punch_in_time')"
                :required="true"
                :error-message="$errorMessage(errors, 'in_time')"
            />

            <app-form-group
                :label="$t('punch_out_time')"
                type="date"
                date-mode="dateTime"
                :max-date="new Date()"
                v-model="formData.out_time"
                :placeholder="$placeholder('punch_out_time')"
                :required="true"
                :error-message="$errorMessage(errors, 'out_time')"
            />

            <app-form-group
                :label="$t('reason_note_for_manual_entry')"
                type="textarea"
                form-group-class="mb-0"
                :text-area-rows="4"
                v-model="formData.note"
                :placeholder="$placeholder('note')"
                :error-message="$errorMessage(errors, 'note')"
            />
        </form>
    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {formatDateTimeForServer, formatDateForServer} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "AttendanceCreateEditModal",
    mixins: [ModalMixin, FormHelperMixins],
    props: {
        tableId: {},
        employee: {},
        specificId: {}
    },
    data() {
        return {
            formData: {},
        }
    },
    mounted() {
        this.$nextTick(()=>{
            if (window.innerHeight < 700){
                document.getElementsByClassName('modal-body')[0].style.height = '560px'
            }
        })
    },
    methods: {
        submitData() {
            this.loading = true;
            const formData = {...this.formData};
            formData.in_date = formatDateForServer(this.formData.in_time);
            formData.in_time = formatDateTimeForServer(this.formData.in_time);
            formData.out_time = formatDateTimeForServer(this.formData.out_time);

            this.submitFromFixin(
                'post',
                `${this.apiUrl.EMPLOYEES}/add-attendance`,
                formData
            );
        },
        afterSuccess({data}) {
            this.loading = false;
            $('#attendance-modal').modal('hide');
            this.$toastr.s('', data.message);
            if (this.tableId) {
                this.$hub.$emit(`reload-${this.tableId}`);
            } else {
                this.$emit('reload')
            }
        },
    },
    created() {
        if (!this.employee && !this.canUpdateStatus && !this.specificId) {
            this.formData.employee_id = window.user.id
        }

        if (this.specificId) {
            this.formData.employee_id = this.specificId
        }
    },
    computed: {
        canUpdateStatus() {
            if (this.specificId && this.$can('update_attendance_status')) {
                return false;
            }
            if (this.employee && this.$can('update_attendance_status')) {
                this.formData.employee_id = this.employee.id
                return false;
            }
            return this.$can('update_attendance_status');
        },
        user_id() {
            return window.user.id;
        },
        buttonFirstTitle() {
            return this.$can('update_attendance_status') ? 'add' : 'request';
        }
    }
}
</script>