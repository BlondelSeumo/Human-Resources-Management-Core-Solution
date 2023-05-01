<template>
    <ul class="nav tab-filter-menu d-flex align-items-center">
        <li class="nav-item" v-for="{id, value} in filters" @click="filterValue(id)">
            <a href="#" :class="`nav-link py-0 ${showActive && filter === id ? 'active' : ''}`">
                {{ value }}
            </a>
        </li>
    </ul>
</template>

<script>
import {getDateRange} from "../../../common/Helper/Support/DateTimeHelper";
import {mapState} from "vuex";

export default {
    name: "PeriodCalendar",
    props: {
        filters: {
            default: function () {
                return [
                    {
                        id: 'today',
                        value: this.$t('today')
                    },
                    {
                        id: 'thisWeek',
                        value: this.$t('this_week')
                    },
                    {
                        id: 'lastWeek',
                        value: this.$t('last_week')
                    },
                    {
                        id: 'thisMonth',
                        value: this.$t('this_month')
                    },
                    {
                        id: 'lastMonth',
                        value: this.$t('last_month')
                    },
                    {
                        id: 'thisYear',
                        value: this.$t('this_year')
                    }
                ];
            }
        },
        initialFilterValue: {},
    },
    data() {
        return {
            filter: '',
            showActive: true
        }
    },
    methods: {
        filterValue(filter) {
            this.filter = filter;
            this.setWithinToState(filter)
        },
        setWithinToState(filter) {
            if (filter === 'total'){
                this.$store.dispatch('updateFilterObject', {
                    page: 1,
                    within: filter,
                    month: '',
                    month_number: '',
                    year: '',
                    time_range: '',
                })
            }else {
                const range = getDateRange(filter);
                const month = filter === 'today' ? range : range[0];
                this.$hub.$emit('clearAllFilter-attendance-range')
                this.$store.dispatch('updateFilterObject', {
                    page: 1,
                    within: filter,
                    month: month.format('MMM'),
                    month_number: month.format('M') - 1,
                    year: month.format('YYYY'),
                    time_range: '',
                })
            }
        }
    },
    computed: {
        ...mapState({
            within: state => state.calendar.filter.within,
        }),
    },
    created() {
        this.filter = this.initialFilterValue ? this.initialFilterValue : 'thisMonth';
        this.setWithinToState(this.filter);
    },
    mounted() {
        this.$hub.$on('clear-period-calender', () => {
            this.showActive = false;
        })

        this.$hub.$on('set-period-calender', () => {
            this.showActive = true;
        })
    },
    watch:{
        within(period){
            if (!period){
                this.filter = null;
            }
        }
    }
}
</script>

<style scoped>

</style>