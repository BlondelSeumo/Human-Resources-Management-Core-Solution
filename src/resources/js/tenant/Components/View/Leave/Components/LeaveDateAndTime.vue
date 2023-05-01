<template>
    <div v-if="multiDay" class="width-150">
        <div><span class="text-muted">from: </span> {{ calenderTime(start_at, false) }}</div>
        <div><span class="text-muted">to: </span> {{ calenderTime(end_at, false) }}</div>
    </div>
    <div v-else-if="hours" class="width-150">
        <div><span class="text-muted">from: </span>{{ onlyTime(start_at) }}</div>
        <div><span class="text-muted">to: </span>{{ leaveDateAndTimeFormat(end_at) }}</div>
    </div>
    <div v-else class="width-150">
        <div>{{ `${calenderTime(start_at, false)}` }}</div>
    </div>
</template>

<script>
import {calenderTime, onlyTime} from "../../../../../common/Helper/Support/DateTimeHelper";
import {leaveDateAndTimeFormat} from "../Helper/Helper";

export default {
    name: "LeaveDateAndTime",
    props: {
        value: {},
        rowData: {}
    },
    data() {
        return {
            calenderTime,
            leaveDateAndTimeFormat,
            onlyTime,
        }
    },
    computed: {
        start_at() {
            return this.rowData.start_at;
        },
        end_at() {
            return this.rowData.end_at;
        },
        multiDay() {
            return this.rowData.duration_type === 'multi_day';
        },
        hours() {
            return this.rowData.duration_type === 'hours';
        }
    }
}
</script>

<style scoped>

</style>