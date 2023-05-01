<template>
    <div class="d-flex align-items-center">
        <app-input
            class="custom-date-range-calendar mr-1"
            type="date"
            v-model="dateRange"
            date-mode="range"
            :required="true"
            :error-message="$errorMessage(errors, 'date')"
            :placeholder="''"
        />

        <div v-if="activeDateRangeFilter" class="d-inline-flex align-items-center">
            <span class="text-muted">{{ showFilteredTime }}</span>
            <span class="mx-2 text-muted">|</span>
            <a href="#" @click="clearDateRangeFilter">Clear</a>
        </div>
    </div>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {
    calenderTimeWithMonthSortName,
    formatDateForServer,
    serverDateFormat
} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "dateRangeCalenderFilter",
    mixins: [FormHelperMixins],

    data() {
        return {
            isDateRangeActive: true,
            dateRange: '',
            errors: {}
        }
    },
    mounted() {
        this.$hub.$on('clearAllFilter-attendance-range', () => {
            this.clearDateRangeFilter()
        })
    },
    computed: {
        dateRangeValue(){
            return this.dateRange;
        },
        activeDateRangeFilter(){
            return Object.keys(this.dateRange).length > 0;
        },
        showFilteredTime(){
            return Object.keys(this.dateRange).length > 0 ?
                `${calenderTimeWithMonthSortName(this.dateRange.start)} - ${calenderTimeWithMonthSortName(this.dateRange.end)}`
                : '';
        }
    },
    methods: {
        clearDateRangeFilter(){
            this.dateRange = {}
            this.$hub.$emit('set-period-calender')
        }
    },
    watch: {
        dateRangeValue: {
            handler: function (value) {
                if (value?.start) value.start = formatDateForServer(value.start)
                if (value?.end) value.end = formatDateForServer(value.end)

                this.$emit('setFilterAction', value)
                let filterValue = {time_range: value}
                this.$store.dispatch('updateFilterObject', filterValue)
                if (Object.keys(value).length > 0){
                    this.$hub.$emit('clear-period-calender')
                }
            },
            immediate: true
        }
    }
}
</script>

<style scoped>

</style>