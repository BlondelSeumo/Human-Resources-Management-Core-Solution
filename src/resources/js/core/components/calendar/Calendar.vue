<template>
    <div class="calendar-view">
        <app-overlay-loader v-if="preloader"/>
        <FullCalendar v-else :options="calendarOptions"/>
    </div>
</template>

<script>

import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";

export default {
    name: "Calendar",
    components: {
        FullCalendar
    },
    props: {
        options: {
            type: Object
        },
        preloader: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            initOptions: {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                headerToolbar: {
                    left: 'title',
                    center: 'prev today next',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay',
                },
                initialView: 'timeGridWeek',
                selectable: true,
                views: {
                    dayGridMonth: {
                        titleFormat: {month: 'long', year: 'numeric'}
                    },
                    timeGridWeek: {
                        titleFormat: {month: 'long', year: 'numeric'},
                        weekNumbers: true
                    },
                    timeGridDay: {
                        titleFormat: {day: 'numeric', month: 'long', year: 'numeric'},
                    }
                },
            }
        }
    },
    computed: {
        calendarOptions() {
            return {
                ...this.initOptions,
                slotLabelFormat: this.slotLabel
                , ...this.options
            }
        },
        slotLabel() {
            if (this.$store.state.settings.timeFormat === 12) return undefined;
            return {
                'hour12': false,
                'hour': '2-digit',
                'minute': '2-digit'
            }
        }
    }
}
</script>
