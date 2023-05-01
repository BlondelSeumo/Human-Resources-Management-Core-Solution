<template>
    <modal id="training-progress-modal"
           size="large"
           v-model="showModal"
           :title="this.$t('employee_list')"
           :cancel-btn-label="$t('close')"
           :hide-submit-button="true"
           @submit=""
           :preloader="preloader">

        <div class="d-flex flex-column justify-content-center mb-primary border-bottom">
            <h3 class="text-center">
                {{ department.name }}
            </h3>
            <p class="text-center">{{ $t('total_employee') }} - {{employees.length}} </p>
        </div>

        <app-pre-loader v-if="loading"/>

        <template v-else>
            <div v-for="user in employees" :key="user.id" class="mb-primary border-bottom">
                <app-user-media :value="user.profile_picture" :row-data="user"/>
            </div>
            <h6 v-if="!employees.length" class="text-center m-primary">{{ $t('this_department_has_no_employee') }}</h6>
        </template>

    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "DepartmentEmployee",
    mixins: [ModalMixin],
    props: {
        department: {
            type: Object,
            default() {
                return {}
            }
        }
    },
    data() {
        return {
            employees: [],
            loading: true,
        }
    },
    mounted() {
        this.getDepartmentEmployees();
    },
    methods: {
        getDepartmentEmployees() {
            axiosGet(`${this.apiUrl.DEPARTMENTS}/${this.department.id}/employees`).then(({data}) => {
                this.employees = data;
            }).catch(({response}) => {
                this.$toastr.e(response.data.message);
            }).finally(() => {
                this.loading = false;
            })
        },
    }
}
</script>

