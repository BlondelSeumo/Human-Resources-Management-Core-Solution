<template>
    <div class="app-checkbox-group" :class="data.radioCheckboxWrapper?data.radioCheckboxWrapper:''">
        <div class="customized-checkbox"
             :class="data.customCheckboxType"
             v-for="(item, index) in data.list"
             :key="index">
            <input :type="data.type"
                   :name="name"
                   :id="name+'-'+item.id"
                   :required="data.required"
                   :disabled="item.disabled || data.disabled"
                   :placeholder="data.placeholder"
                   :value="item.id"
                   :checked="isInclude(item.id)"
                   @input="input($event)"
                   ref="checkbox"/>
            <label :for="name+'-'+item.id" :class="data.labelClass">
                {{ item[data.listValueField] }}
            </label>
        </div>
    </div>
</template>

<script>
import {InputMixin} from "./mixin/InputMixin.js";

export default {
    name: "CheckBox",

    mixins: [InputMixin],

    data() {
        return {
            inputValue: [],
        }
    },
    mounted() {
        if (this.value.length != 0) {
            this.inputValue = [...this.inputValue, ...this.value];
            this.$emit("input", this.inputValue);
        }
    },
    watch: {
        checkboxValue: function (val) {
            this.inputValue = val;
            this.$emit("input", this.inputValue);
        }
    },
    computed: {
        checkboxValue() {
            return this.value;
        }
    },
    methods: {
        input($event) {
            let ele = $event.target,
                value = ele.value;
            if (ele.checked) {
                this.inputValue.push(value);
            } else {
                const index = this.matchedWithoutCase(value);
                this.inputValue.splice(index, 1);
            }
            this.$emit("input", this.inputValue);
            this.$emit("changed", $event);
        },
        isInclude(value) {
            return this.inputValue.includes(value);
        },
        matchedWithoutCase(value) {
            for (let i = 0; i < this.inputValue.length; i++) {
                if (this.inputValue[i] == value) {
                    return i;
                }
            }
        }
    },
}
</script>

