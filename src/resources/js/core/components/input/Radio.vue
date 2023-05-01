<template>
    <div class="app-radio-group" :class="data.radioCheckboxWrapper?data.radioCheckboxWrapper:''">
        <label v-for="(item, index) in data.list"
               :class="['customized-radio radio-default', data.customRadioType, data.disabled || item.disabled ? 'disabled' : '']"
               :key="index">
            <input class="radio-inline"
                   :type="data.type"
                   :name="name"
                   :id="name+'-'+index"
                   :required="data.required"
                   :disabled="item.disabled || data.disabled"
                   :checked="item.id == value"
                   :value="item.id"
                   v-on="listeners"
                   ref="radio">
            <span class="outside">
                <span class="inside"/>
            </span>
            {{ item[data.listValueField] }}
        </label>
    </div>
</template>

<script>
import {InputMixin} from "./mixin/InputMixin.js";

export default {
    name: "Radio",
    mixins: [InputMixin],
};
</script>

