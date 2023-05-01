<template>
    <div class="d-flex justify-content-between mb-3 mb-lg-0">
        <div class="d-flex align-items-center month-year-picker mr-2">
            <date-range-calender-filter v-if="showDateRangeCalendar"
                @setFilterAction="dateRangeFilterAction"
            />
            <div v-if="!isDateRangeFilterActive" class="dropdown keep-inside-clicks-open calender-dropdown">
                <a href="#" :class="`text-muted ${isLastMonthOfYears || monthNumber === 0 ? 'disabled d-none' : ''}`" @click="previousMonth()">
                    <app-icon name="chevron-left"/>
                </a>
                <div class="d-inline-flex align-items-center justify-content-center btn p-0" data-toggle="dropdown">
                    <a href="#" :class="`mx-2 ${showActive ? '': 'text-muted'}`">{{ month }}, {{ currentYear }}</a>
                </div>
                <a href="#" :class="`text-muted ${isCurrentMonthYear || monthNumber === 11 ? 'disabled d-none' : ''}`" @click="nextMonth()">
                    <app-icon name="chevron-right"/>
                </a>
                <div class="dropdown-menu p-3 mt-1">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <a href="#" :class="`text-muted ${lastYearOfList ? 'disabled d-none' : ''}`"
                           @click="changeYear('previous')">
                            <app-icon name="chevron-left"/>
                        </a>
                        <div v-click-outside="hideYearDropDown" class="dropdown years-dropdown">
                            <a class="cursor-default mx-2 cursor-pointer"
                               @click.prevent="openYears"
                            >
                                {{ currentYear }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-center dropdown-menu-years" id="month-years">
                                <span class="caret"/>
                                <div class="custom-scrollbar years-wrapper">
                                    <a class="dropdown-item cursor-pointer"
                                       v-for="year in years"
                                       @click.prevent="setYear($event, year)">
                                        {{ year }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a href="#" :class="`text-muted ${firstYearOfList ? 'disabled d-none' : ''}`" class="text-muted"
                           @click="changeYear('next')">
                            <app-icon name="chevron-right"/>
                        </a>
                    </div>
                    <div class="row" v-if="monthLoaded">
                        <div class="col-4" v-for="(month, index) in months">
                            <a :class="`single-month ${index === monthNumber ? 'bg-brand-color' : ''}`"
                               @click.prevent="setMonthYearToSate(month.short, index, month.full)">
                                {{ month.short }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import moment from 'moment'
import {mapState} from 'vuex'
import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {ATTENDANCES} from "../../Config/ApiUrl";
import CoreLibrary from "../../../core/helpers/CoreLibrary";
import DateRangeCalenderFilter from "../View/Attendance/Component/dateRangeCalenderFilter";
import {preventDefault} from "@fullcalendar/core";

export default {
    name: "MonthCalendar",
    components: {DateRangeCalenderFilter},
    extends: CoreLibrary,
    props: {
        year: {
            default: function () {
                return moment(new Date()).format('YYYY')
            }
        },
        periodsUrl: {
            default: function () {
                return `${ATTENDANCES}/periods`;
            }
        },
        showDateRangeCalendar: {}
    },
    data() {
        return {
            currentYear: '',
            years: [],
            monthLoaded: true,
            showActive: true,
            isDateRangeFilterActive: false,
        }
    },
    computed: {
        months() {
            return Array.apply(0, Array(this.isCurrentYear ? parseInt(moment(new Date()).format('M')) : 12)).map((_, i) => {
                return {
                    'short': moment().month(i).format('MMM'),
                    'full': moment().locale(window.appLanguage).month(i).format('MMMM')
                };
            });
        },
        isCurrentYear() {
            return parseInt(moment(new Date).format('YYYY')) === parseInt(this.currentYear)
        },
        isCurrentMonthYear() {
            return parseInt(moment(new Date).format('M')) === parseInt(this.monthNumber + 1) && this.isCurrentYear;
        },
        isLastMonthOfYears() {
            return this.monthNumber === 0 && Math.min(...this.years) === parseInt(this.currentYear);
        },
        ...mapState({
            within: state => state.calendar.filter.within,
            monthNumber: state => state.calendar.filter.month_number,
        }),
        lastYearOfList() {
            return Math.min(...this.years) >= parseInt(this.currentYear);
        },
        firstYearOfList() {
            return Math.max(...this.years) <= parseInt(this.currentYear);
        },
        month() {
            let month_number = this.$store.state.calendar.filter.month_number;
            return month_number > -1 ? moment().month(month_number).format('MMMM') : moment(new Date()).format('MMMM') - 1;
        },
        filteredYear() {
            return this.$store.state.calendar.filter.year
        },
        stateFilterData(){
            return this.$store.getters.getFilters;
        }
    },
    methods: {
        dateRangeFilterAction(value){
            this.isDateRangeFilterActive = Object.keys(value).length > 0;
        },
        // calenderDropdownHide(){
        //   $('.calender-dropdown.open').toggle()
        // },

        dispatchToState(payload){
            this.$store.dispatch('updateFilterObject', payload)
        },
        setMonthYearToSate(month, index, monthFull) {
            this.$hub.$emit('clearAllFilter-attendance-range')
            this.dispatchToState({
                page: 1,
                month,
                year: this.currentYear,
                month_number: index,
                within: false,
                time_range: '',
            })
        },

        goNext(destination = 'previous', month_number) {
            month_number = destination === 'previous' ? month_number - 1 : month_number + 1;

            let year = this.$store.state.calendar.filter.year;
            year = year ? year : moment(new Date()).format('YYYY');

            this.setMonthYearToSate(
                moment([year, month_number]).format('MMM'),
                month_number,
                moment([year, month_number]).format('MMMM')
            )
        },

        previousMonth() {
            let month_number = this.$store.state.calendar.filter.month_number;
            month_number = month_number > 0 ? month_number : (this.isCurrentYear ? moment(new Date()).format('M') : 12);
            this.goNext('previous', month_number);
        },

        nextMonth() {
            let month_number = this.$store.state.calendar.filter.month_number;

            this.goNext('next', month_number)
        },

        openYears() {
            if ($('.dropdown-menu-years').hasClass('show')) {
                $('.dropdown-menu-years').dropdown('hide');
            } else {
                $('.dropdown-menu-years').dropdown('show');
            }
        },

        setYear(e, year) {
            this.monthLoaded = false;

            e.stopPropagation();

            $('.dropdown-menu-years').dropdown('hide');

            this.currentYear = parseInt(year);
            if (this.stateFilterData?.year != year){
                this.$hub.$emit('clearAllFilter-attendance-range')
                this.dispatchToState({
                    page: 1,
                    year,
                    within: false
                })
            }
        },
        hideYearDropDown() {
            $('.dropdown-menu-years').dropdown('hide');
        },
        changeYear(state) {
            if (!this.years.length) {
                return;
            }
            if (state === 'previous') {
                if (parseInt(this.currentYear) > Math.min(...this.years)) {
                    this.monthLoaded = false;
                    this.currentYear = parseInt(this.currentYear) - 1;
                }
            } else {
                if (parseInt(this.currentYear) < Math.max(...this.years)) {
                    this.monthLoaded = false;
                    this.currentYear = parseInt(this.currentYear) + 1;
                }
            }
            this.$hub.$emit('clearAllFilter-attendance-range')
            this.dispatchToState({
                page: 1,
                year: this.currentYear,
                within: false
            })
        }
    },
    mounted() {
        $('.dropdown').on('hidden.bs.dropdown', function () {
            $('.dropdown-menu-years').dropdown('hide')
        });

        this.currentYear = this.year;

        axiosGet(this.periodsUrl).then(response => {
            this.years = ['2021', '2020', '2019', '2018'];
        })

        this.$hub.$on('clear-period-calender', () => {
            this.showActive = false;
        })

        this.$hub.$on('set-period-calender', () => {
            this.showActive = true;
        })
    },
    watch: {
        filteredYear: {
            handler: function (year) {
                this.currentYear = year;
            },
        },
        currentYear: {
            handler: function () {
                this.monthLoaded = true;
            }
        }
    }
}
</script>