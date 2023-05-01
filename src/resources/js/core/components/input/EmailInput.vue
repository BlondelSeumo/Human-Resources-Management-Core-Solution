<template>
    <!--input-->
    <input :type="data.type"
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
import {InputMixin} from './mixin/InputMixin.js';

export default {
    name: "EmailInput",
    mixins: [InputMixin]
}
</script>
