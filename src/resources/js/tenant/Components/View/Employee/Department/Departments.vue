<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('departments')" icon="briefcase">
            <app-default-button
                v-if="$can('create_departments')"
                :title="$addLabel('department')"
                @click="openDepartmentModal()"
            />
        </app-page-top-section>

        <app-table
            id="department-table"
            :options="options"
            @action="triggerActions"
        />

        <app-department-modal
            v-if="isDepartmentModalActive"
            v-model="isDepartmentModalActive"
            :selected-url="selectedUrl"
        />

        <app-confirmation-modal
            :message="promptSubtitle"
            :modal-class="modalClass"
            :icon="promptIcon"
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="changeStatus"
            @cancelled="cancelled"
        />

        <app-employee-move-modal
            v-if="isEmployeeMovementModalActive"
            v-model="isEmployeeMovementModalActive"
            :department-name="departmentName"
            :department-id="departmentId"
        />
    </div>
</template>

<script>
import DepartmentMixin from "../../../Mixins/DepartmentMixin";
import {axiosPost} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "Departments",
    mixins: [DepartmentMixin],
    data() {
        return {
            department: '',
            departmentId: '',
            departments: [],
            selectedUrl: '',
            modalClass: '',
            promptIcon: '',
            promptSubtitle: '',
            departmentName: '',
            departmentUsers: '',
            isDepartmentModalActive: false,
            confirmationModalActive: false,
            isEmployeeMovementModalActive: false,
        }
    },
    created() {
        this.$store.dispatch('getStatuses', 'department')
    },
    watch: {
        isDepartmentModalActive: function (value) {
            if (!value) {
                this.selectedUrl = '';
            }
        }
    },
    methods: {
        openDepartmentModal() {
            this.selectedUrl = '';
            this.isDepartmentModalActive = true;
        },
        changeStatus() {
            axiosPost(`${this.apiUrl.DEPARTMENTS}/${this.departmentId}/update-status`).then(({data}) => {
                this.toastAndReload(data.message, 'department-table');

                if (data.users.length && data.data.status.name === 'status_inactive') {
                    this.departmentName = data.data.name;
                    this.isEmployeeMovementModalActive = true;
                }
            }).catch(({response}) => {
                this.toastAndReload(response.data.message, 'department-table');
            }).finally(() => {
                this.confirmationModalActive = false;
            });
        },
        triggerActions(row, action, active) {
            this.departmentId = row.id;
            this.promptIcon = action.icon;
            this.modalClass = action.modalClass;
            this.promptSubtitle = action.promptSubtitle;

            if (action.name === 'edit') {
                this.selectedUrl = `${action.url}/${row.id}`;
                this.isDepartmentModalActive = true;
            } else if (['deactivate', 'activate'].includes(action.key)) {
                this.confirmationModalActive = true;
            } else if (action.name === 'move-employee') {
                this.departmentId = row.id;
                this.departmentName = row.name;
                this.isEmployeeMovementModalActive = true;
            } else {
                this.getAction(row, action, active)
            }
        }
    }
}
</script>
