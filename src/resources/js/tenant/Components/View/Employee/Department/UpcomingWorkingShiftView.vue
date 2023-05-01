<template>
    <app-pre-loader v-if="loading"/>
    <div v-else class="bg-gray mb-primary">
        <ul v-for="workshift in workingShifts">
            <li :key="workshift.id">
                <p class="p-0">
                    <span class="text-warning">{{ $t('upcoming') }}</span> : {{ workshift.working_shift.name }}, {{ $t('from') }}
                    <b class="font-size-90">{{ isAfterNow(workshift.start_date) ? customDateFormat(workshift.start_date) : $t('tomorrow') }}</b> |
                    <a href="#" @click.prevent="deleteWorkingShift(workshift.id)">{{ $t('remove') }}</a>
                </p>
            </li>
        </ul>

    </div>
</template>

<script>
import {customDateFormat, isAfterNow} from "../../../../../common/Helper/Support/DateTimeHelper";
import {axiosDelete} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "UpcomingWorkingShiftView",
    props: {
        workingShifts: {}
    },
    data(){
        return{
            isAfterNow,
            customDateFormat,
            loading: false,
        }
    },
    methods:{
        deleteWorkingShift(id){
            this.loading = true;
            axiosDelete(`${this.apiUrl.DEPARTMENTS}/upcoming/working-shift/${id}`).then(({data}) => {
                this.$toastr.s('', data.message);
            }).catch(({response}) => {
                this.$toastr.e('', response.data.message);
            }).finally(() => {
                this.$emit('workingShiftRemoved');
            })
        }
    }
}
</script>

<style scoped>

</style>