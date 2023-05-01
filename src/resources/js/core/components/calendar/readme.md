# Developer guide for use <app-calendar>

### Folder Structure

Form Laravel Application

- resources
    - js
        - core
            - components
                - calendar
                    - Calendar.vue
    
### Register
```js
    Vue.component('app-calendar', require('./components/calendar/Calendar').default);
```

### Note
- The some options of this calendar plugin is not reactive.
- So if you modify options of calendar you have to re-render calendar.
- Or you can use `preloader` for re-render updated options


### Props
1. options
   - `type` : `object`  // Contains options for manage calendar
2. preloader
   - `type` : `Boolean` // true or false for loader over calendar and changing option

### Available options in Calendar
1. select
    - `type` : `Function`
2. eventClick
    - `type` : `Function`
3. eventChange
    - `type` : `Function`
4. initialEvents
    - `type` : `Array` // array of objects
    - Object should have `id`, `title`, `start`, `end`
    - Example:
```
initialEvents: [
    {
        id: 1,
        title: 'Example event',
        start: Start Time of event,
        end: End Time of event
    }
]
```
5. initialDate
    - `type` : `String, Date`
6. initialView
    - `type` : `String`
    - `default` : `timeGridWeek`
    - available type: `dayGridMonth`, `timeGridWeek`, `timeGridDay`
7. editable
    - `type` : `Boolean`
    - `default` : `false` // true for move or increase decrease size of events

### Example usages
```
<template>
    <div class="content-wrapper">
        <div class="card card-with-shadow border-0" style="min-height: 400px">
            <div class="card-body p-primary">
                <app-calendar :preloader="preloader" :options="options"/>
            </div>
        </div>
    </div>
</template>

<script>
import AxiosFunction from "../../helpers/axios/AxiosFunction";

export default {
    name: "TestCalendarView",
    data() {
        return {
            preloader: false,
            options: {
                select: this.createEvent,
                eventClick: this.selectedEvent,
                eventChange: this.eventUpdate,
                initialEvents: [],
                initialDate: new Date(),
            }
        }
    },
    created() {
        this.preloader = true;
    },
    mounted() {
        this.getCalendarData();
    },
    methods: {
        createEvent(arg) {
            // finding calendar data for creating event
            console.log(arg);
        },
        selectedEvent(arg) {
            // get selected event for action
            console.log(arg);
        },
        eventUpdate(arg) {
            // Update Event
            console.log(arg);
        },
        getCalendarData() {
            AxiosFunction.axiosGet('test-calendar-events')
                .then(res => this.options.initialEvents = res.data)
                .finally(() => this.preloader = false)
        }
    },
}
</script>

```
