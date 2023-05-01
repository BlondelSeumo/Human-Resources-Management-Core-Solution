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
           v-on="listeners"
           :autocomplete="data.autocomplete"
    />
</template>

<script>
import accounting from 'accounting';
import {InputMixin} from './mixin/InputMixin.js';

export default {
    name: "DecimalInput",

    mixins: [InputMixin],
    data() {
        return {
            formattedValue: ''
        }
    },
    methods: {
        // input($event) {
        //     let value = $event.target.value;
        //     this.fieldValue = value;
        //
        //     /** option with accounting.js http://openexchangerates.github.io/accounting.js/
        //      * symbol '$', currency symbol will be '' for this component
        //      * decimal ',','.'
        //      * thousand separator ',','.',' '
        //      * precision 0-10
        //      * format "%s%v","%v%s","%v %s","%s %v" format will be null for this component
        //      **/
        //
        //     let options = {
        //         symbol: '',
        //         decimal: this.data.decimal,
        //         thousand: this.data.thousandSeparator,
        //         precision: this.data.precision,
        //         format: null
        //     };
        //     this.formattedValue = accounting.formatMoney(this.value, options);
        //
        //     this.$emit('input', value);
        // }

    },
}
</script>
