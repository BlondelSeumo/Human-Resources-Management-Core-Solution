<template>
    <section class="m-0 p-0">
        <app-form-group
            v-for="field in customFields" :key="field.id"
            :page="viewType"
            :label="field.name"
            :list="generateInputList(field)"
            :type="field.type"
            v-model="fieldsWithNameAsKey[field.name]"
            v-bind="propsFor()"
            :id="generateId(field)"
            v-on="$listeners"
            @input="generateIdBased($event, field.id)"
        />
    </section>
</template>

<script>
import CoreLibrary from "../../../core/helpers/CoreLibrary";
import _ from "lodash";
import {FormMixin} from "../../../core/mixins/form/FormMixin";
import {kebabCase} from "../../Helper/Support/TextHelper";
import moment from 'moment'
import {isValidDate} from "../../Helper/Support/DateTimeHelper";

export default {
    extends: CoreLibrary,
    name: "CustomFieldBuilder",
    mixins: [FormMixin],
    props: {
        fetchUrl: {
            required: true,
            type: String
        },
        viewType: {
            default: 'page'
        },
        customFieldValues: {}
    },
    data() {
        return {
            customFields: [],
            fieldsWithNameAsKey: {},
            fieldsWithIdAsKey: {}
        }
    },
    methods: {
        getCustomFields() {
            this.axiosGet(this.fetchUrl).then(response => {
                if (response.data.data) {
                    this.customFields = response.data.data.map(field => {
                        return {
                            id: field.id,
                            name: field.name,
                            meta: field.meta,
                            type: field.custom_field_type ? field.custom_field_type.name : ''
                        }
                    });
                }else {
                    this.customFields = response.data;
                }
            })
        },
        propsFor() {
            let props = this.$attrs;
            const cTor = this.$options.components['app-input']
            if (cTor) {
                let keys = Object.keys(cTor.options.props)
                return _.pick(props, keys.map(key => {
                    return kebabCase(key);
                }))
            }
        },
        generateInputList({meta}) {
            if (meta) {
                return Array.from(new Set(meta.split(','))).map(m => {
                    return { id: m, value: m }
                })
            }
        },
        generateId(field) {
            return field.name;
        },
        generateIdBased(value, id) {
            this.fieldsWithIdAsKey[id] = value instanceof Date ? moment(value).format('YYYY-MM-DD') : value;
            this.$emit('fieldWithNameAsKey', this.generateNameBasedField())
            this.$emit('fieldWithIdAsKey', this.fieldsWithIdAsKey)
        },
        generateNameBasedField() {
            const fields = {};
            Object.keys(this.fieldsWithNameAsKey).forEach(f => {
                fields[f] = this.fieldsWithNameAsKey[f] instanceof Date ?  moment(this.fieldsWithNameAsKey[f]).format('YYYY-MM-DD') : this.fieldsWithNameAsKey[f];
            })
            return fields;
        }
    },
    created() {
        this.getCustomFields();
    },
    watch: {
        customFieldValues: {
            deep: true,
            immediate: true,
            handler: function () {
                this.customFieldValues.forEach(value => {
                    this.fieldsWithNameAsKey[value.custom_field.name] = isValidDate(value.value) ? new Date(value.value) : value.value;
                    this.fieldsWithIdAsKey[value.custom_field.id] = isValidDate(value.value) ? new Date(value.value) : value.value;
                })
                this.$emit('fieldWithNameAsKey', this.generateNameBasedField())
                this.$emit('fieldWithIdAsKey', this.fieldsWithIdAsKey)
            }
        }
    }
}
</script>

<style scoped>

</style>
