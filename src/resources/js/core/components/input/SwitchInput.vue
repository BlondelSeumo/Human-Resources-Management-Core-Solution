<template>
    <label class="custom-control d-inline border-switch mb-0 mr-3"
           :class="{'disabled': data.disabled}">
        <input type="checkbox"
               :name="name"
               :id="inputFieldId"
               :disabled="data.disabled"
               :checked="value"
               :value="value"
               v-on="listeners"
               class="border-switch-control-input">
        <span class="border-switch-control-indicator"></span>
    </label>
</template>

<script>
import {InputMixin} from "./mixin/InputMixin.js";

export default {
    name: "SwitchInput",
    mixins: [InputMixin]
}
</script>
