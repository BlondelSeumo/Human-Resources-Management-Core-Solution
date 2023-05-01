<template>
    <div class="form-group-with-icon" :class="{'disabled':data.disabled}">
        <input
            :type="passwordFieldType"
            :name="name"
            :id="inputFieldId"
            :required="data.required"
            :class="['form-control '+data.inputClass, {'text-right-padding': data.showPassword}]"
            :placeholder="data.placeholder"
            :readonly="data.readOnly"
            :disabled="data.disabled"
            :minlength="data.minlength"
            :maxlength="data.maxlength"
            v-bind:value="value"
            v-on="listeners"
            :autocomplete="data.autocomplete"
        />
        <div v-if="data.showPassword"
             class="form-control-feedback cursor-pointer"
             :class="{'disabled': !value}"
             :title="passwordFieldType === 'password' ? $t('show_password') : $t('hide_password')"
             @click.prevent="showPasswordText">
            <span v-if="passwordFieldType === 'password'"
                  :key="passwordFieldType"
                  class="animate__animated animate__fadeIn">
                <app-icon name="eye" class="size-19"/>
            </span>
            <span v-else
                  :key="passwordFieldType"
                  class="animate__animated animate__fadeIn">
                <app-icon name="eye-off" class="size-19"/>
            </span>
        </div>
    </div>
</template>

<script>
import {InputMixin} from './mixin/InputMixin.js';

export default {
    name: "PassWord",
    mixins: [InputMixin],
    data() {
        return {
            passwordFieldType: ''
        }
    },
    methods: {
        showPasswordText() {
            this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
        }
    },
    mounted() {
        this.passwordFieldType = this.data.type;
    }
}
</script>
