<template>
    <!--input-->
    <input :type="'number'"
           :name="name"
           :id="inputFieldId"
           :required="data.required"
           :class="'form-control '+data.inputClass"
           :placeholder="data.placeholder"
           :readonly="data.readOnly"
           :disabled="data.disabled"
           v-bind:value="value"
           v-on="listeners"/>
</template>

<script>
import accounting from 'accounting';
import {InputMixin} from './mixin/InputMixin.js';

export default {
    name: "Currency",

    mixins: [InputMixin],

    data() {
        return {
            formattedValue: ''
        }
    },
    methods: {
        // input($event) {
        //     let value = $event.target.value;
        //
        //     this.fieldValue = value;
        //     this.changed();
        // },
        // changed() {
        //
        //     /**
        //      * option with accounting.js http://openexchangerates.github.io/accounting.js/
        //      * symbol '$',whatever currency symbol
        //      * decimal ',','.'
        //      * thousand separator ',','.',' '
        //      * precision 0-10
        //      * format "%s%v","%v%s","%v %s","%s %v"
        //      * */
        //
        //     let options = {
        //         symbol: this.data.symbol,
        //         decimal: this.data.decimal,
        //         thousand: this.data.thousandSeparator,
        //         precision: this.data.precision,
        //         format: this.data.currencyFormat
        //     };
        //     this.formattedValue = accounting.formatMoney(this.fieldValue, options);
        //     this.$emit('input', this.fieldValue);
        // },
        // initComponent(initialValue){
        //     //:initial-value has a value,it will replace with default value
        //     this.value = initialValue ? initialValue : this.value;
        // },
    }
}
</script>
