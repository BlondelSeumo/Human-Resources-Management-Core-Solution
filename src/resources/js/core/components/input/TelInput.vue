<template>
    <vue-tel-input :id="inputFieldId"
                   type="text"
                   :value="value"
                   :required="data.required"
                   mode="international"
                   :disabled="data.disabled"
                   :name="name"
                   :placeholder="data.placeholder"
                   :inputClasses="data.inputClass+'default-font-color'"
                   @input="input"/>
</template>

<script>
import {VueTelInput} from 'vue-tel-input';
import {InputMixin} from './mixin/InputMixin.js';

export default {
    name: "TelInput",
    mixins: [InputMixin],
    components: {VueTelInput},
    methods: {
        input(value) {
            this.$emit("input", value);
        }
    }
}
</script>
