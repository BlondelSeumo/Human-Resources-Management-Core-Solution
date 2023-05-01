<template>
    <v-date-picker :min-date="data.minDate"
                   :max-date="data.maxDate"
                   :color="data.dateColor"
                   :value="value"
                   @input="input"
                   :mode="dateMode"
                   :is24hr="timeFormat==24"
                   :isDark="$store.state.theme.darkMode"
                   :masks="{
                       input:[dateFormat],
                       inputDateTime:[dateFormat+' h:mm A'],
                       inputDateTime24hr:[dateFormat+' HH:mm'],
                       inputTime:['h:mm A'],
                       inputTime24hr:['HH:mm']
                   }"
                   :isRange="isRange"
                   :popover="{visibility: 'click', placement: data.popoverPosition}">
        <template v-slot="{ inputValue, inputEvents }">
            <div class="date-picker-input" :class="{'date-picker-input-group':borderGroup}">
                <div class="input-group"
                     :class="{'disabled':data.disabled}"
                     @focusin="borderGroup=true"
                     @focusout="borderGroup=false">
                    <template v-if="isRange">
                        <input :value="inputValue.start || inputValue.end ? inputValue.start+' - '+inputValue.end: ''"
                               :placeholder="data.placeholder"
                               readonly
                               v-on="inputEvents.start || inputEvents.end"
                               class="form-control"/>
                    </template>
                    <template v-else>
                        <input :value="inputValue ? inputValue : ''"
                               :placeholder="data.placeholder"
                               v-on="inputEvents"
                               class="form-control"/>
                    </template>
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <app-icon :name="'calendar'"/>
                        </span>
                    </div>
                </div>
            </div>
        </template>
    </v-date-picker>
</template>

<script>
import {InputMixin} from './mixin/InputMixin.js';
import CoreLibrary from "../../helpers/CoreLibrary";

export default {
    name: "DatePicker",
    extends: CoreLibrary,

    mixins: [InputMixin],

    data() {
        return {
            borderGroup: false,
        }
    },
    computed: {
        isRange() {
            if (this.data.dateMode === 'range') {
                return true
            }
            return this.isUndefined(this.data.isRange) ? false : this.data.isRange;
        },
        dateMode() {
            if (this.data.dateMode === 'range' || this.isUndefined(this.data.dateMode)) {
                return 'date';
            } else return this.data.dateMode;
        }
    },
    methods: {
        input(date) {
            this.fieldValue = date;
            this.$emit('input', date);
        }
    }
}
</script>

