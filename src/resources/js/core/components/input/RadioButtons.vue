<template>
    <div class="radio-button-group" :class="{'disabled':data.disabled}">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label v-for="(item, index) in data.list" :key="index" class="btn border"
                   :class="value==item.id? 'focus active':''">
                <input type="radio"
                       :value="item.id"
                       :name="name"
                       :id="name+'-'+index"
                       :required="data.required"
                       @click.prevent="getValue(item.id)"
                       :disabled="item.disabled"
                       :checked="value == item.id">
                <span>{{ item[data.listValueField] }}</span>
            </label>
        </div>
    </div>
</template>

<script>
import {InputMixin} from './mixin/InputMixin.js';

export default {
    name: "RadioButtons",

    mixins: [InputMixin],

    methods: {
        getValue(value) {
            this.fieldValue = value;
            this.changed();
        },
        changed() {
            this.$emit('input', this.fieldValue);
        }
    }
}
</script>
