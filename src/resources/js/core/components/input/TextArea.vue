<template>
    <textarea
        :type="data.type"
        :id="inputFieldId"
        :name="name"
        :required="data.required"
        :class="'custom-scrollbar ' + data.inputClass"
        :placeholder="data.placeholder"
        :readonly="data.readOnly"
        :disabled="data.disabled"
        :rows="data.textAreaRows"
        :cols="data.textAreaCols"
        :minlength="data.minlength"
        :maxlength="data.maxlength"
        :spellcheck="data.textAreaSpellCheck"
        :style="'direction:'+data.textAreaDir"
        v-bind:value="value"
        v-on="listeners">
    </textarea>
</template>

<script>
import {InputMixin} from './mixin/InputMixin.js';

export default {
    name: "TextArea",
    mixins: [InputMixin]
}
</script>
