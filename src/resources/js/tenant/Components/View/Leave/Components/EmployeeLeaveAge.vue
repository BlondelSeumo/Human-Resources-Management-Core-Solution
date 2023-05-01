<template>
    <div class="column-content text-muted px-primary d-flex justify-content-center flex-column">
        <p class="mb-0" v-if="days>0">{{ days + ' ' + (days>1 ? this.$t('days') : this.$t('day')) }} </p>
        <p class="mb-0" v-if="halfDays>0">{{ halfDays + ' ' + (halfDays>1 ? this.$t('half_days') : this.$t('half_day')) }}</p>
        <p class="mb-0" v-if="hours>0">{{ hours + ' ' + (hours>1 ? this.$t('hours') : this.$t('hour')) }}</p>
    </div>
</template>

<script>

import moment from "moment";
import {serverDateFormat, serverDateTimeFormat} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "EmployeeLeaveAge",
    props:{
        leaves:{
            type: Array,
            required: true
        }
    },
    computed:{
        days(){
            let days = 0;
            this.leaves.map(function (leave){
                if(leave.duration_type === 'multi_day'){
                    days += Number(`${moment(leave.end_at, serverDateFormat).diff(moment(leave.start_at, serverDateFormat), 'days') + 1}`)
                }else if (leave.duration_type === 'single_day'){
                    days += 1;
                }
            })
            return days;
        },
        halfDays(){
            let days = 0;
            this.leaves.map(function (leave){
                if(leave.duration_type === 'first_half' || leave.duration_type === 'last_half'){
                    days += 1;
                }
            })
            return days;
        },
        hours(){
            let days = 0;
            this.leaves.map(function (leave){
                if(leave.duration_type === 'hours'){
                    days += Number(`${moment(leave.end_at, serverDateTimeFormat).diff(moment(leave.start_at, serverDateTimeFormat), 'hours')}`)
                }
            })
            return days;
        }

    }
}
</script>