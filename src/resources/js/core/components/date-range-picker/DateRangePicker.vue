<template>
    <div class="single-filter calendar-root" v-click-outside="closeCalender">
        <div class="input-date" @click="toggleCalendar()" :class="{'applied' : isApply}">
            <span v-if="label" class="mr-2">
                {{ label }}
            </span>
            <span v-if="visibleValue">
                {{ getDateString(dateRange.start) }} - {{ getDateString(dateRange.end) }}
            </span>
            <span class="clear-icon" v-if="isApply" @click.prevent="clear">
                <app-icon :name="'x'"/>
            </span>
        </div>
        <div class="calendar pt-4" :class="{'calendar-mobile ': isCompact, 'calendar-right-to-left': isRighttoLeft}"
             v-if="isOpen">
            <div class="btn-dropdown-close d-sm-none">
                <span class="title">
                     {{ label }}
                </span>
                <span class="back float-right" @click.prevent="closeCalender">
                    <app-icon :name="'x'"/>
                </span>
            </div>

            <div class="calendar-wrap pb-0">
                <div class="calendar_month_left" :class="{'calendar-left-mobile': isCompact}" v-if="showMonth">
                    <div class="months-text">
                        <i class="left" @click="goPrevMonth"></i>
                        <i class="right" @click="goNextMonth" v-if="isCompact"></i>
                        {{ monthsLocale[activeMonthStart] + ' ' + activeYearStart }}
                    </div>
                    <ul :class="s.daysWeeks">
                        <li v-for="item in shortDaysLocale" :key="item">{{ item }}</li>
                    </ul>
                    <ul v-for="r in 6" :class="[s.days]" :key="r">
                        <li :class="[{[s.daysSelected]: isDateSelected(r, i, 'first', startMonthDay, endMonthDate),
              [s.daysInRange]: isDateInRange(r, i, 'first', startMonthDay, endMonthDate),
              [s.dateDisabled]: isDateDisabled(r, i, startMonthDay, endMonthDate)}]" v-for="i in numOfDays" :key="i"
                            v-html="getDayCell(r, i, startMonthDay, endMonthDate)"
                            @click="selectFirstItem(r, i)"></li>
                    </ul>
                </div>
                <div class="calendar_month_right" v-if="!isCompact">
                    <div class="months-text">
                        {{ monthsLocale[startNextActiveMonth] + ' ' + activeYearEnd }}
                        <i class="right" @click="goNextMonth"></i>
                    </div>
                    <ul :class="s.daysWeeks">
                        <li v-for="item in shortDaysLocale" :key="item">{{ item }}</li>
                    </ul>
                    <ul v-for="r in 6" :class="[s.days]" :key="r">
                        <li :class="[{[s.daysSelected]: isDateSelected(r, i, 'second', startNextMonthDay, endNextMonthDate),
            [s.daysInRange]: isDateInRange(r, i, 'second', startNextMonthDay, endNextMonthDate),
            [s.dateDisabled]: isDateDisabled(r, i, startNextMonthDay, endNextMonthDate)}]"
                            v-for="i in numOfDays" :key="i"
                            v-html="getDayCell(r, i, startNextMonthDay, endNextMonthDate)"
                            @click="selectSecondItem(r, i)"></li>
                    </ul>
                </div>
            </div>
            <div class="calendar-range" :class="{'calendar-range-mobile ': isCompact}" v-if="!showMonth || !isCompact">
                <ul class="calendar_preset">
                    <li class="calendar_preset-ranges"
                        v-for="(item, idx) in finalPresetRanges"
                        :key="idx"
                        @click="updatePreset(item)"
                        :class="{'active-preset': presetActiveLabel === item.label}">

                        <label class="customized-radio radio-default" :for="'filter_radio'+idx">
                            <input type="radio"
                                   class="radio-inline"
                                   :id="'filter_radio'+idx"
                                   name="customRadioDefault"
                                   :checked="presetActiveLabel === item.label"/>
                            <span class="outside">
                                <span class="inside"/>
                            </span>
                            {{ item.label }}
                        </label>
                    </li>
                </ul>
            </div>

            <div class="clearfix d-none d-sm-block"/>
            <div class="row filter-action-button-wrapper">
                <div class="col-12 d-flex justify-content-between">
                    <button type="button"
                            class="btn btn-clear pl-sm-0"
                            :disabled="!isApply"
                            @click="clear">
                        {{ $t('clear') }}
                    </button>
                    <button type="submit"
                            class="btn btn-primary"
                            @click="apply">
                        {{ $t('apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CoreLibrary from "../../helpers/CoreLibrary";
import 'vue-rangedate-picker/dist/vue-rangedate-picker.min'
import fecha from 'fecha';

const defaultConfig = {}
const defaultI18n = 'EN'
const availableMonths = {
    EN: [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ]
};

const availableShortDays = {
    EN: [
        'su',
        'mo',
        'tu',
        'we',
        'th',
        'fr',
        'sa',
    ]
}

const presetRangeLabel = {
    EN: {
        today: 'Today',
        thisMonth: 'This Month',
        lastMonth: 'Last Month',
        lastSevenDays: 'Last 7 Days',
        lastThirtyDays: 'Last 30 Days',
        next7Days: 'Next 7 Days',
        next30Days: 'Next 30 Days',
        year: 'This Year',
        nextYear: 'Next Year',
        lastYear: 'Last Year',
        thisWeek: 'This Week',
        lastWeek: 'Last Week',
        nextWeek: 'Next Week',
        nextMonth: 'Next Month'
    }
}

const defaultCaptions = {
    'title': 'Choose Date',
    'ok_button': 'Ok'
}

const defaultStyle = {
    daysWeeks: 'calendar_weeks',
    days: 'calendar_days',
    daysSelected: 'calendar_days_selected',
    daysInRange: 'calendar_days_in-range',
    firstDate: 'calendar_month_left',
    secondDate: 'calendar_month_right',
    presetRanges: 'calendar_preset-ranges',
    dateDisabled: 'calendar_days--disabled'
}

const defaultPresets = function (i18n = defaultI18n, customPreset = {}) {
    return {
        today: function () {
            const n = new Date()
            const startToday = new Date(n.getFullYear(), n.getMonth(), n.getDate() + 1, 0, 0)
            const endToday = new Date(n.getFullYear(), n.getMonth(), n.getDate() + 1, 23, 59)
            return {
                label: presetRangeLabel[i18n].today,
                active: false,
                dateRange: {
                    start: startToday,
                    end: endToday
                }
            }
        },
        last7Days: function () {
            const n = new Date()
            const start = new Date(n.getFullYear(), n.getMonth(), n.getDate() - 5)
            const end = new Date(n.getFullYear(), n.getMonth(), n.getDate() + 1)
            return {
                label: presetRangeLabel[i18n].lastSevenDays,
                active: false,
                dateRange: {
                    start: start,
                    end: end
                }
            }
        },
        next7Days: function () {
            const n = new Date()
            const start = new Date(n.getFullYear(), n.getMonth(), n.getDate() + 1)
            const end = new Date(n.getFullYear(), n.getMonth(), n.getDate() + 7)
            return {
                label: presetRangeLabel[i18n].next7Days,
                active: false,
                dateRange: {
                    start: start,
                    end: end
                }
            }
        },
        thisWeek: function () {
            const n = new Date()
            const startWeek = new Date(n.getFullYear(), n.getMonth(), n.getDate() - n.getDay() + 1)
            const endWeek = new Date(n.getFullYear(), n.getMonth(), n.getDate() + (7 - n.getDay()))
            return {
                label: presetRangeLabel[i18n].thisWeek,
                active: false,
                dateRange: {
                    start: startWeek,
                    end: endWeek
                }
            }
        },
        lastWeek: function () {
            const n = new Date()
            const startWeek = new Date(n.getFullYear(), n.getMonth(), n.getDate() + (n.getDay() - 6))
            const endWeek = new Date(n.getFullYear(), n.getMonth(), n.getDate() - n.getDay())
            return {
                label: presetRangeLabel[i18n].lastWeek,
                active: false,
                dateRange: {
                    start: startWeek,
                    end: endWeek
                }
            }
        },
        nextWeek: function () {
            const n = new Date()
            const startWeek = new Date(n.getFullYear(), n.getMonth(), n.getDate() + (8 - n.getDay()))
            const endWeek = new Date(n.getFullYear(), n.getMonth(), n.getDate() + (14 - n.getDay()))
            return {
                label: presetRangeLabel[i18n].nextWeek,
                active: false,
                dateRange: {
                    start: startWeek,
                    end: endWeek
                }
            }
        },
        thisMonth: function () {
            const n = new Date()
            const startMonth = new Date(n.getFullYear(), n.getMonth(), 2)
            const endMonth = new Date(n.getFullYear(), n.getMonth() + 1, 1)
            return {
                label: presetRangeLabel[i18n].thisMonth,
                active: false,
                dateRange: {
                    start: startMonth,
                    end: endMonth
                }
            }
        },
        lastMonth: function () {
            const n = new Date()
            const startMonth = new Date(n.getFullYear(), n.getMonth() - 1, 2)
            const endMonth = new Date(n.getFullYear(), n.getMonth(), 1)
            return {
                label: presetRangeLabel[i18n].lastMonth,
                active: false,
                dateRange: {
                    start: startMonth,
                    end: endMonth
                }
            }
        },
        nextMonth: function () {
            const n = new Date()
            const startMonth = new Date(n.getFullYear(), n.getMonth() + 1, 2)
            const endMonth = new Date(n.getFullYear(), n.getMonth() + 2, 1)
            return {
                label: presetRangeLabel[i18n].nextMonth,
                active: false,
                dateRange: {
                    start: startMonth,
                    end: endMonth
                }
            }
        },
        last30Days: function () {
            const n = new Date()
            const start = new Date(n.getFullYear(), n.getMonth(), n.getDate() - 29)
            const end = new Date(n.getFullYear(), n.getMonth(), n.getDate() + 1)
            return {
                label: presetRangeLabel[i18n].lastThirtyDays,
                active: false,
                dateRange: {
                    start: start,
                    end: end
                }
            }
        },
        next30Days: function () {
            const n = new Date()
            const start = new Date(n.getFullYear(), n.getMonth(), n.getDate() + 1)
            const end = new Date(n.getFullYear(), n.getMonth(), n.getDate() + 30)
            return {
                label: presetRangeLabel[i18n].next30Days,
                active: false,
                dateRange: {
                    start: start,
                    end: end
                }
            }
        },
        thisYear: function () {
            const n = new Date()
            const start = new Date(n.getFullYear(), 0, 2)
            const end = new Date(n.getFullYear() + 1, 0, 1)
            return {
                label: presetRangeLabel[i18n].year,
                active: false,
                dateRange: {
                    start: start,
                    end: end
                }
            }
        },
        lastYear: function () {
            const n = new Date()
            const start = new Date(n.getFullYear() - 1, 0, 2)
            const end = new Date(n.getFullYear(), 0, 1)
            return {
                label: presetRangeLabel[i18n].lastYear,
                active: false,
                dateRange: {
                    start: start,
                    end: end
                }
            }
        },
        nextYear: function () {
            const n = new Date()
            const start = new Date(n.getFullYear() + 1, 0, 2)
            const end = new Date(n.getFullYear() + 2, 0, 1)
            return {
                label: presetRangeLabel[i18n].nextYear,
                active: false,
                dateRange: {
                    start: start,
                    end: end
                }
            }
        },
    }
}

export default {
    name: 'vue-rangedate-picker',
    extends: CoreLibrary,
    props: {
        configs: {
            type: Object,
            default: () => defaultConfig
        },
        i18n: {
            type: String,
            default: defaultI18n
        },
        months: {
            type: Array,
            default: () => null
        },
        shortDays: {
            type: Array,
            default: () => null
        },
        captions: {
            type: Object,
            default: () => defaultCaptions
        },
        format: {
            type: String,
            default: 'YYYY-MM-DD'
        },
        styles: {
            type: Object,
            default: () => {
            }
        },
        initRange: {
            type: Object,
            default: () => null
        },
        startActiveMonth: {
            type: Number,
            default: new Date().getMonth()
        },
        startActiveYear: {
            type: Number,
            default: new Date().getFullYear()
        },
        presetRanges: {
            type: Array,
            default: () => null
        },
        compact: {
            type: String,
            default: 'false'
        },
        righttoleft: {
            type: String,
            default: 'false'
        },
        dateClear: {
            type: Boolean,
            default: false
        },
        //to add label
        label: {
            type: String,
            default: null
        },
        //to show selected value
        visibleValue: {
            type: Boolean,
            default: true
        },
        // If use for filter table
        tableId: {}
    },
    data() {
        return {
            dateRange: {},
            finalDateRange: {},
            numOfDays: 7,
            isFirstChoice: true,
            isOpen: false,
            presetActive: '',
            showMonth: false,
            activeMonthStart: this.startActiveMonth,
            activeYearStart: this.startActiveYear,
            activeYearEnd: this.startActiveYear,
            cancelled: true,
            isApply: false
        }
    },
    created() {
        if (this.isCompact) {
            this.isOpen = true
        }
        if (this.activeMonthStart === 11) this.activeYearEnd = this.activeYearStart + 1;
    },
    mounted() {
        this.$hub.$on('clearAllFilter-' + this.tableId, () => {
            this.clearAndInitiate();
        });
    },
    watch: {
        startNextActiveMonth: function (value) {
            if (value === 0) this.activeYearEnd = this.activeYearStart + 1
        },
        dateClear: function (newVal) {
            if (newVal) {
                this.clearDateRange();
            }
            this.$emit('resetdateClears', false);
        },
    },
    computed: {
        monthsLocale: function () {
            return this.months || availableMonths[this.i18n]
        },
        shortDaysLocale: function () {
            return this.shortDays || availableShortDays[this.i18n]
        },
        s: function () {
            return Object.assign({}, defaultStyle, this.style)
        },
        startMonthDay: function () {
            return new Date(this.activeYearStart, this.activeMonthStart, 1).getDay()
        },
        startNextMonthDay: function () {
            return new Date(this.activeYearStart, this.startNextActiveMonth, 1).getDay()
        },
        endMonthDate: function () {
            return new Date(this.activeYearEnd, this.startNextActiveMonth, 0).getDate()
        },
        endNextMonthDate: function () {
            return new Date(this.activeYearEnd, this.activeMonthStart + 2, 0).getDate()
        },
        startNextActiveMonth: function () {
            return this.activeMonthStart >= 11 ? 0 : this.activeMonthStart + 1
        },
        finalPresetRanges: function () {
            const tmp = {}
            const presets = defaultPresets(this.i18n)
            for (const i in presets) {
                const item = presets[i]
                let plainItem = item
                if (typeof item === 'function') {
                    plainItem = item()
                }
                if (this.presetRanges === null || this.presetRanges.includes(item.name)) {
                    tmp[i] = plainItem
                }
            }
            return tmp
        },
        isCompact: function () {
            return this.compact === 'true'
        },
        isRighttoLeft: function () {
            return this.righttoleft === 'true'
        },
        presetActiveLabel() {
            return this.presetActive;
        }
    },
    methods: {
        clearAndInitiate() {
            this.clearDateRange();
            this.activeMonthStart = this.startActiveMonth;
            this.activeYearStart = this.startActiveYear;
            this.activeYearEnd = this.startActiveYear;
            this.isApply = false;
        },
        clearDateRange() {
            this.dateRange = {};
            this.presetActive = '';
        },
        clear() {
            this.clearAndInitiate();
            this.changed();
        },
        nameOfCustomEventToCall() {
            if (this.cancelled) {
                this.toggleCalendar();
            }
            this.cancelled = true;
        },
        callToggle() {
            this.cancelled = false;
            this.toggleCalendar();
        },
        closeCalender: function () {
            this.showMonth = false;
            this.isOpen = false;
        },
        toggleCalendar: function () {
            if (this.isCompact) {
                this.showMonth = !this.showMonth
                return
            }
            this.isOpen = !this.isOpen
            this.showMonth = !this.showMonth
            return
        },
        apply: function () {
            if (this.dateRange.start && this.dateRange.end) {
                this.isApply = true;
                this.changed();
            }
            this.toggleCalendar()
        },
        getDateString: function (date, format = this.format,) {
            if (!date) {
                return null
            }
            const dateparse = new Date(Date.parse(date));
            return fecha.format(new Date(dateparse.getFullYear(), dateparse.getMonth(), dateparse.getDate() - 1), format)
        },
        getDayIndexInMonth: function (r, i, startMonthDay) {
            const date = (this.numOfDays * (r - 1)) + i
            return date - startMonthDay
        },
        getDayCell(r, i, startMonthDay, endMonthDate) {
            const result = this.getDayIndexInMonth(r, i, startMonthDay)
            return result > 0 && result <= endMonthDate ? result : '&nbsp;'
        },
        getNewDateRange(result, activeMonth, activeYear) {
            const newData = {}
            let key = 'start'
            if (!this.isFirstChoice) {
                key = 'end'
            } else {
                newData['end'] = null
            }
            const resultDate = new Date(activeYear, activeMonth, result)
            if (!this.isFirstChoice && resultDate < this.dateRange.start) {
                this.isFirstChoice = false
                return {start: resultDate}
            }
            // toggle first choice
            this.isFirstChoice = !this.isFirstChoice
            newData[key] = resultDate

            return newData
        },
        selectFirstItem(r, i) {
            const result = this.getDayIndexInMonth(r, i, this.startMonthDay) + 1
            this.dateRange = Object.assign({}, this.dateRange, this.getNewDateRange(result, this.activeMonthStart,
                this.activeYearStart))
            if (this.dateRange.start && this.dateRange.end) {
                this.presetActive = ''
                if (this.isCompact) {
                    this.showMonth = false
                }
            }
        },
        selectSecondItem(r, i) {
            const result = this.getDayIndexInMonth(r, i, this.startNextMonthDay) + 1
            this.dateRange = Object.assign({}, this.dateRange, this.getNewDateRange(result, this.startNextActiveMonth,
                this.activeYearEnd))
            if (this.dateRange.start && this.dateRange.end) {
                this.presetActive = ''
            }
        },
        isDateSelected(r, i, key, startMonthDay, endMonthDate) {
            const result = this.getDayIndexInMonth(r, i, startMonthDay) + 1
            if (result < 2 || result > endMonthDate + 1) return false

            let currDate = null
            if (key === 'first') {
                currDate = new Date(this.activeYearStart, this.activeMonthStart, result)
            } else {
                currDate = new Date(this.activeYearEnd, this.startNextActiveMonth, result)
            }
            return (this.dateRange.start && this.dateRange.start.getTime() === currDate.getTime()) ||
                (this.dateRange.end && this.dateRange.end.getTime() === currDate.getTime())
        },
        isDateInRange(r, i, key, startMonthDay, endMonthDate) {
            const result = this.getDayIndexInMonth(r, i, startMonthDay) + 1
            if (result < 2 || result > endMonthDate + 1) return false

            let currDate = null
            if (key === 'first') {
                currDate = new Date(this.activeYearStart, this.activeMonthStart, result)
            } else {
                currDate = new Date(this.activeYearEnd, this.startNextActiveMonth, result)
            }
            return (this.dateRange.start && this.dateRange.start.getTime() < currDate.getTime()) &&
                (this.dateRange.end && this.dateRange.end.getTime() > currDate.getTime())
        },
        isDateDisabled(r, i, startMonthDay, endMonthDate) {
            const result = this.getDayIndexInMonth(r, i, startMonthDay)
            // bound by > 0 and < last day of month
            return !(result > 0 && result <= endMonthDate)
        },
        goPrevMonth() {
            const prevMonth = new Date(this.activeYearStart, this.activeMonthStart, 0)
            this.activeMonthStart = prevMonth.getMonth()
            this.activeYearStart = prevMonth.getFullYear()
            this.activeYearEnd = prevMonth.getFullYear()
        },
        goNextMonth() {
            const nextMonth = new Date(this.activeYearEnd, this.startNextActiveMonth, 1)
            this.activeMonthStart = nextMonth.getMonth()
            this.activeYearStart = nextMonth.getFullYear()
            this.activeYearEnd = nextMonth.getFullYear()
        },
        updatePreset(item) {
            this.presetActive = item.label
            this.dateRange = item.dateRange
            // update start active month
            this.activeMonthStart = this.dateRange.start.getMonth()
            this.activeYearStart = this.dateRange.start.getFullYear()
            this.activeYearEnd =
                this.dateRange.end.getFullYear() > this.dateRange.start.getFullYear() ?
                    this.dateRange.end.getFullYear() - 1 : this.dateRange.end.getFullYear()

        },
        setDateValue: function () {
            console.log('Press ok');
        },
        changed() {
            let range = {};
            if (this.dateRange.start) {
                range = {
                    'start': this.getDateString(this.dateRange.start),
                    'end': this.getDateString(this.dateRange.end)
                }
            }
            this.$emit('changed', range);
        }
    }
}
</script>


