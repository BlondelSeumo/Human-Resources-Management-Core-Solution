<template>
    <select :id="inputFieldId"
            :style="'backgroundImage: url('+downArrow()+')'"
            :disabled="data.disabled"
            ref="select"
            :class="'custom-select' + data.inputClass"
            v-bind:value="value" v-on="listeners">
        <option v-for="item in data.list"
                :value="item.id"
                :disabled="item.disabled"
                :selected="item.id === value"
                :key="item.id">
            {{ item[data.listValueField] }}
        </option>
    </select>
</template>

<script>
import {InputMixin} from './mixin/InputMixin.js';
import AppFunction from "../../helpers/app/AppFunction";

export default {
    name: "SelectInput",
    mixins: [InputMixin],
    methods: {
        downArrow() {
            if (this.$store.state.theme.darkMode) {
                return AppFunction.getAppUrl('images/chevron-down-dark.svg');
            } else {
                return AppFunction.getAppUrl('images/chevron-down.svg');
            }
        }
    }
}
</script>
