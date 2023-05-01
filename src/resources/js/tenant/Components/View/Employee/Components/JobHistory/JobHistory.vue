<template>
    <div>
        <app-overlay-loader v-if="preloading"/>
        <div v-else>
            <container-block
                :title="$t('department')"
                name="department"
                icon="briefcase"
                @openModal="openEditModal"
                :employee-id="employee.id"
                :data="employee.departments"
            />

            <container-block
                :title="$t('work_shift')"
                name="work_shift"
                icon="clock"
                :employee-id="employee.id"
                @openModal="openEditModal"
                :data="employee.working_shifts_with_upcoming"
            />

            <container-block
                :title="$t('designation')"
                name="designation"
                icon="award"
                :employee-id="employee.id"
                @openModal="openEditModal"
                :data="employee.designations"
            />

            <container-block
                :title="$t('employment_status')"
                name="employment_status"
                icon="user-check"
                :employee-id="employee.id"
                @openModal="openEditModal"
                :data="employee.employment_statuses"
            />

            <container-block
                :title="$t('role')"
                name="role"
                icon="flag"
                :employee-id="employee.id"
                @openModal="openEditModal"
                :data="employee.roles"
            />

            <container-block
                :title="$t('joining_date')"
                name="joining_date"
                icon="award"
                :employee-id="employee.id"
                @openModal="openEditModal"
                :data="employee.profile"
            />

            <job-history-edit-modal
                v-if="isJobHistoryEditModalActive"
                v-model="isJobHistoryEditModalActive"
                :modalType="modalType"
                :employee="employee"
                @reload="getEmployeeJobHistory"
            />
        </div>
    </div>
</template>

<script>

import ContainerBlock from "./components/ContainerBlock";
import {axiosGet} from "../../../../../../common/Helper/AxiosHelper";
import JobHistoryEditModal from "./components/JobHistoryEditModal";

export default {
    name: "JobHistory",
    props: ['props'],
    components: {ContainerBlock, JobHistoryEditModal},
    data() {
        return {
            employee: {},
            preloading: false,
            modalType: '',
            isJobHistoryEditModalActive: false,
        }
    },
    mounted() {
        this.getEmployeeJobHistory();
        this.$hub.$on('employeeDetailsActionHappened', (value) => {
            this.getEmployeeJobHistory();
        });
    },
    methods: {
        getEmployeeJobHistory() {
            this.isJobHistoryEditModalActive = false;
            this.preloading = true;
            axiosGet(`${this.apiUrl.EMPLOYEES}/${this.props.id}/job-history`).then(({data}) => {
                this.employee = data;
                let formatUpcomingWorkingShift = this.employee.upcoming_working_shift.map(item => {
                    return {...item.working_shift, ...{
                        'upcoming': true,
                        'pivot': {
                            'start_date': item.start_date,
                            'end_date': null
                        }

                    }}
                })
                this.employee.working_shifts_with_upcoming = formatUpcomingWorkingShift.concat(this.employee.working_shifts)
                this.$store.dispatch("getEmployeeDetails", this.props.id);
            }).finally(() => {
                this.preloading = false;
            })
        },
        openEditModal(type) {
            this.modalType = type;
            this.isJobHistoryEditModalActive = true;
        }
    },
    computed: {}
}
</script>