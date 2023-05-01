<template>
    <div>
        <div class="card card-with-shadow border-0" style="min-height: 400px">
            <div class="card-body p-primary">
                <app-calendar :preloader="dataLoader" :options="calendarOptions"/>
            </div>
        </div>

        <app-holiday-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :eventData="eventData"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />
    </div>
</template>

<script>
import HolidayMixins from "../../../Mixins/HolidayMixins";
import {HOLIDAYS} from "../../../../Config/ApiUrl";
import {isSameOrAfterThisYear} from "../../../../../common/Helper/Support/DateTimeHelper";
import * as moment from "moment";

export default {
    name: "HolidayCalendar",
    mixins: [HolidayMixins],
    props: {
        data: {
            type: Array,
            required: true,
            default: function () {
                return []
            }
        },
        actions: {
            type: Array
        },
        dataLoader: {},
        filteredData: {},
    },
    data() {
        return {
            calendarOptions: {
                locale: window.appLanguage,
                initialEvents: [],
                initialDate: new Date(),
                select: this.createEvent,
                initialView: 'dayGridMonth',
                eventClick: this.selectedEvent,
                headerToolbar: {
                    left: 'title',
                    center: 'prev today next',
                    right: '',
                },
            },
            eventData: '',
        }
    },
    methods: {
        createEvent(arg) {
            if (!this.$can('create_holidays')){
                return;
            }
            // if (isAfterNow(arg.start)){
                this.eventData = arg;
                this.selectedUrl = '';
                this.isModalActive = true;
            // }else {
            //     this.$toastr.e(this.$t('cant_add_holidays_past'))
            // }

        },
        selectedEvent(arg) {
            if (!this.$can('update_holidays')){
                return;
            }
            if (arg.event.id && isSameOrAfterThisYear(arg.event.start)){
                this.isModalActive = true;
                this.selectedUrl = `${HOLIDAYS}/${arg.event.id}`;
            }else {
                if (!arg.event.id){
                    this.$toastr.e(this.$t('cant_update_repeated_holidays'))
                }else{
                    this.$toastr.e(this.$t('cant_update_holidays'))
                }
            }
        },
        prepareCalendarOptionsData() {

            this.calendarOptions.initialEvents = this.data.map((item) => {
                let value = {
                    id: item.id,
                    title: item.name,
                    start: item.start_date,
                    end: item.end_date,
                }
                if (value.start !== value.end){
                    value.end = (new Date(value.end))
                    value.end = value.end.setSeconds(value.end.getSeconds() + 1)
                }
                return value
            });

            let repeatedHoliday = this.data.map((item) => {

                if (parseInt(item.repeats_annually)){
                    let repeatValue = {
                        id: '',
                        title: item.name,
                        start: moment(item.start_date).add(1, "year").format('YYYY-MM-DD'),
                        end: moment(item.end_date).add(1, "year").format('YYYY-MM-DD'),
                    }
                    if (repeatValue.start !== repeatValue.end){
                        repeatValue.end = (new Date(repeatValue.end))
                        repeatValue.end = repeatValue.end.setSeconds(repeatValue.end.getSeconds() + 1)
                    }
                return repeatValue
                }
            }).filter(item => item);

            this.calendarOptions.initialEvents = this.calendarOptions.initialEvents.concat(repeatedHoliday)

            if (this.dateFilterKey) {
                if (this.filteredData[this.dateFilterKey]?.start) {
                    this.calendarOptions.initialDate = new Date(this.filteredData[this.dateFilterKey].start);
                } else this.calendarOptions.initialDate = new Date();
            }
        }
    },
    watch: {
        data: {
            handler: 'prepareCalendarOptionsData',
            immediate: true
        }
    },
    computed: {
        dateFilterKey() {
            return this.options.filters?.find(element => element.type === 'range-picker')?.key;
        }
    }
}
</script>



