<template>
    <div class="time-picker-input">
        <div class="dropdown keep-inside-clicks-open">
            <div class="input-group"
                 :class="{'disabled':data.disabled}"
                 :id="'dropdownMenuTimePicker'+inputFieldId"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">

                <input
                    :id="inputFieldId"
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    v-bind:value="value"
                    @input="input($event)"
                    :placeholder="data.placeholder"
                    :required="data.required"
                />

                <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2">
                        <!--Clear 'x' button-->
                        <!--<span class="clear-icon" v-if="timeChanged" @click.prevent="clear">
                            <app-icon name="x"/>
                        </span>-->
                        <app-icon name="clock"/>
                    </span>
                </div>
            </div>
            <div class="dropdown-menu" :aria-labelledby="'dropdownMenuTimePicker'+inputFieldId">
                <div class="hour custom-scrollbar py-2">
                    <ul class="p-0" v-if="timeFormat == 12">
                        <li v-for="(n) in timeFormat"
                            :key="'hour' + n"
                            class="dropdown-item py-2"
                            @click.prevent="selectHour(n)"
                            :class="{ selected: timeChanged && n == hour }">
                            {{ n < 10 ? "0" + n : n }}:00
                        </li>
                    </ul>
                    <ul v-else class="p-0">
                        <li v-for="(n, index) in timeFormat"
                            :key="'hour' + index"
                            class="dropdown-item py-2"
                            @click.prevent="selectHour(n)"
                            :class="{ selected: timeChanged && (n == hour || (n==24 && hour == 0)) }">
                            {{ n < 10 ? "0" + n : n == 24 ? "00" : n }}:00
                        </li>
                    </ul>
                </div>
                <div class="minute custom-scrollbar py-2">
                    <ul class="p-0">
                        <li v-for="(n, index) in 60"
                            :key="'min' + n"
                            class="dropdown-item py-2"
                            @click.prevent="selectMinute(index)"
                            :class="{ selected: timeChanged && index == min }">
                            {{ hour ? hour < 10 ? "0" + hour : hour > timeFormat ? '00' : hour : '00'}}:{{
                            index < 10 ? "0" + index : index
                            }}
                        </li>
                    </ul>
                </div>
                <div class="am-pm custom-scrollbar py-2" v-if="timeFormat == 12">
                    <ul class="p-0">
                        <li class="dropdown-item py-2">

                            <label class="customized-radio radio-default">
                                <input
                                    type="radio"
                                    class="radio-inline"
                                    :name="'TimeFormat'+name+'-'+inputFieldId"
                                    :id="'customRadioAm-'+name+'-'+inputFieldId"
                                    :checked="formatTxtAM"
                                    @click="selectAmPM('AM')"
                                />
                                <span class="outside">
                                    <span class="inside"/>
                                </span>
                                {{ $t('am') }}
                            </label>

                        </li>
                        <li class="dropdown-item py-2">

                            <label class="customized-radio radio-default">
                                <input
                                    type="radio"
                                    class="radio-inline"
                                    :name="'TimeFormat'+name+'-'+inputFieldId"
                                    :id="'customRadioPm-'+name+'-'+inputFieldId"
                                    :checked="!formatTxtAM"
                                    @click="selectAmPM('PM')"
                                />
                                <span class="outside">
                                    <span class="inside"/>
                                </span>
                                {{ $t('pm') }}
                            </label>

                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {InputMixin} from "./mixin/InputMixin.js";
import CoreLibrary from "../../helpers/CoreLibrary";

export default {
    name: "TimePicker",
    mixins: [InputMixin],
    extends: CoreLibrary,
    data() {
        return {
            selectedTime: "",
            timeZone: null,
            timeZoneOffset: null,
            hour: null,
            min: null,
            formatTxt: "AM",
            timeChanged: false,
            formatTxtAM: true
        };
    },
    computed: {
        timeFormat() {
            return this.$store.state.settings.timeFormat;
        }
    },
    methods: {
        setValue() {
            this.selectedTime = this.selectedTime.toUpperCase();
            let timeRegex, result, hour, min;
            timeRegex =
                this.timeFormat == 12 ? /^(.*):(.*)\s(AM|PM)$/i : /^(.*):(.*)$/;
            let formatIsOkay = timeRegex.test(this.selectedTime);

            //check there is minimumm value to be check
            if (formatIsOkay) {
                result = this.selectedTime.match(timeRegex);
                hour = result[1];
                min = result[2];
                this.formatTxtAM = this.formatTxt == "AM" ? true : false;
                this.hour = parseInt(hour);
                this.min = parseInt(min);
                this.changed();
            }
        },
        clear(event) {
            event.stopPropagation();
            this.selectedTime = "";
            this.hour = null;
            this.min = null;
            this.timeChanged = false;
            this.changed();
            // this.input();
            $(".dropdown-menu").removeClass("show");
        },
        input($event) {
            let value = $event.target.value;
            this.selectedTime = value;
            this.changed();
        },
        changed() {
            let hour =
                this.timeFormat == 12
                    ? this.formatTxt == "PM" && this.hour != 12
                    ? this.hour + 12
                    : this.formatTxt == "AM" && this.hour == 12
                        ? 0
                        : this.hour
                    : this.hour;
            this.fieldValue = this.selectedTime;
            this.$emit("input", this.fieldValue);
            this.timeChanged = true;
        },
        selectHour(hour) {
            this.hour = hour == 24 ? 0 : hour;
            this.min = 0;
            this.formatString();
            this.changed();
        },
        selectMinute(minute) {
            this.min = minute;
            this.formatString();
            this.changed();
        },
        selectAmPM(format) {
            this.formatTxtAM = format === 'AM'
            this.formatTxt = format;
            this.formatString();
            this.changed();
        },
        formatString() {
            if (this.hour !== null && this.min !== null) {
                let hourTxt, minTxt;
                let formatTxt = this.timeFormat == 12 ? " " + this.formatTxt : "";
                hourTxt = this.hour < 10 ? "0" + this.hour : this.hour;
                minTxt = this.min < 10 ? "0" + this.min : this.min;
                this.selectedTime = hourTxt + ":" + minTxt + formatTxt;
            }
        }
    },
    mounted() {
        let instance = this;

        //:get-time-zone is true
        if (this.$props.data.getTimeZone) {
            this.timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            this.timeZoneOffset = new Date().getTimezoneOffset();
        }

        this.$nextTick(function () {
            $(document).on(
                "click.bs.dropdown.data-api",
                ".dropdown.keep-inside-clicks-open",
                function (e) {
                    e.stopPropagation();
                }
            );
        });

        //allow only some characters
        $("#" + this.inputFieldId).keypress(function (e) {
            let chr = String.fromCharCode(e.which);
            let allowChar =
                instance.timeFormat == 12 ? "0123456789 :AMPamp" : "0123456789:";
            let allowLen = instance.timeFormat == 12 ? 7 : 4;
            if (this.value.length > allowLen) return false;
            if (allowChar.indexOf(chr) < 0) return false;
        });

        // For show selected value
        if (this.value) {
            let time = this.value.split(/[\s:]+/).filter(i => i)
            if (Number(time[0]) <= Number(this.timeFormat)) {
                if (time[0]) this.selectHour(Number(time[0]))
                if (time[1]) this.selectMinute(Number(time[1]))
                if (time[2]) this.selectAmPM(time[2])
            }
        }
    }
};
</script>
