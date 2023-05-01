<template>
    <div class="form-group" :class="formGroupClass" v-if="page === 'modal'">
        <label :class="recommendation ? 'text-left d-block mb-2 mb-lg-0' : ''">{{ label }}</label>
        <small class="text-muted font-italic">{{ recommendation }}</small>
        <app-input
            :type="type"
            v-model="model"
            @input="$emit('input', $event)"
            v-bind="propsFor()"
            :error-message="errorMessage"
            :label="fileLabel"
            :id="id || $vnode.data.model.expression"
            v-on="$listeners"
            autocomplete="false"
        />
        <slot name="suggestion"></slot>
    </div>
    <div class="form-group" :class="formGroupClass" v-else-if="page === 'page'">
        <div :class="`row ${ labelAlignment === 'center' ? 'align-items-center' : ''}`">
            <div class="col-lg-3 col-xl-3 col-md-3 col-sm-12">
                <label class="text-left d-block mb-lg-0">
                    {{ label }} <br v-if="recommendation"/>
                    <small v-if="recommendation" class="text-muted font-italic">
                        {{ recommendation }}
                    </small>
                </label>
            </div>
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-12">
                <app-input
                    :type="type"
                    v-model="model"
                    @input="$emit('input', $event)"
                    v-bind="propsFor()"
                    :error-message="errorMessage"
                    :label="fileLabel"
                    :id="id || $vnode.data.model.expression"
                    v-on="$listeners"
                    autocomplete="false"
                />
                <slot name="suggestion"></slot>
            </div>
        </div>
    </div>
</template>

<script>
import {FormMixin} from "../../../core/mixins/form/FormMixin";
import _ from 'lodash'
import {kebabCase} from "../../Helper/Support/TextHelper";

export default {
    name: "FormGroup",
    mixins: [FormMixin],
    props: {
        type: {
            type: String,
            default: 'text'
        },
        label: {
            type: String,
            required: true
        },
        value: {
            required: true
        },
        page: {
            default: 'modal'
        },
        errorMessage: {
            type: String,
            default: ""
        },
        recommendation: {},
        fileLabel: {},
        formGroupClass: {
            type: String
        },
        id: {
            type: String,
            default: ''
        },
        labelAlignment: {
            type: String,
            default: 'center'
        }
    },
    data() {
        return {
            model: '',
            modelName: ''
        }
    },
    methods: {
        propsFor() {
            let props = this.$attrs;
            const cTor = this.$options.components['app-input']
            if (cTor) {
                let keys = Object.keys(cTor.options.props)
                return _.pick(props, keys.map(key => {
                    return kebabCase(key)
                }))
            }
        }
    },
    watch: {
        value: {
            handler: function (value) {
                this.model = value;
            },
            immediate: true
        },
        errorMessage: {
            handler: function (errorMessage) {
                if (errorMessage) {
                    this.fieldStatus.isSubmit = true
                }
            },
            immediate: true
        }
    },
}
</script>
