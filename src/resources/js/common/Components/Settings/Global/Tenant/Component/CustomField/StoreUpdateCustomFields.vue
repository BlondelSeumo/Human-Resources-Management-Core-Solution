<template>
    <modal
        id="custom-fields"
        :title="generateModalTitle('custom_field')"
        v-model="showModal"
        @submit="submitData"
        :loading="loading"
        :preloader="preloader"
    >
        <form method="post"
              ref="form"
              :data-url="`${post_url}${selectedUrl ? '/'+formData.id : ''}`"
              @submit.prevent="submitData"
        >
            <app-form-group
                :label="$t('type')"
                type="select"
                v-model="formData.custom_field_type_id"
                :list="custom_field_types"
                :error-message="$errorMessage(errors, 'custom_field_type_id')"
                @input="setCustomFieldType($event)"
            />

            <app-form-group
                v-if="custom_field_type.choose || formData.meta"
                :label="$t('options')"
                :recommendation="`(${$t('Use comma separated values as options of the field')})`"
                type="textarea"
                v-model="formData.meta"
                :error-message="$errorMessage(errors, 'meta')"
                :placeholder="$placeholder('custom_field', 'options')"
            />

            <app-form-group
                :label="$t('name')"
                v-model="formData.name"
                :error-message="$errorMessage(errors, 'name')"
                :placeholder="$placeholder('name')"
            />

            <app-form-group
                :label="$t('context')"
                type="select"
                v-model="formData.context"
                :list="context"
                :error-message="$errorMessage(errors, 'context')"
            />


            <div class="form-group">
                <app-input
                    type="checkbox"
                    v-model="formData.in_list"
                    :list="[{ id: 1, value: $t('Show in the data table') }]"
                    :error-message="$errorMessage(errors, 'in_list')"
                />
            </div>
        </form>
    </modal>
</template>

<script>
import {mapState} from 'vuex'
import FormHelperMixins from "../../../../../../Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../../Mixin/Global/ModalMixin";
import {CUSTOM_FIELDS, TENANT_CUSTOM_FIELDS} from "../../../../../../Config/apiUrl";
import {addChooseInSelectArray} from "../../../../../../Helper/Support/FormHelper";

export default {
    name: "StoreUpdateCustomFields",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        customField: {
            required: false,
            type: Object,
        },
        alias: {
            default: 'app',
        }
    },
    data() {
        return {
            formData: {
                in_list: []
            },
            custom_field_type: {},
            post_url: this.alias === 'app' ? CUSTOM_FIELDS : TENANT_CUSTOM_FIELDS,
        }
    },
    computed: {
        ...mapState({
            custom_field_types: state => state.custom_field.custom_field_types,
        }),
        context() {
            return addChooseInSelectArray(
                this.$store.getters.getFormattedConfig('context'),
                'value',
                'context'
            );
        },
    },
    methods: {
        submitData() {
            this.loading = true;
            this.message = '';
            const customField = {
                ...this.formData,
                type: this.collection(this.custom_field_types).find(this.formData.custom_field_type_id).name
            }
            customField.in_list = this.formData.in_list.reduce((sum, carry) => sum + parseInt(carry), 0) ? 1 : 0;
            this.save(customField);
        },
        afterSuccess({data}) {
            this.formData = {custom_field_type_id: 1, in_list: []};
            this.toastAndReload(data.message, 'app-custom-fields')
            $('#custom-fields').modal('hide')
        },
        setCustomFieldType(custom_field_type_id) {
            this.formData.meta = '';
            this.custom_field_type = this.collection(this.custom_field_types).find(custom_field_type_id);
            this.custom_field_type.choose = ['select', 'checkbox', 'radio'].includes(this.custom_field_type.name)
        },

        afterSuccessFromGetEditData({data}) {
            this.preloader = false;
            this.formData = {
                ...data,
                in_list: data.in_list ? [parseInt(data.in_list)] : []
            };
        }
    },
    mounted() {
        this.$store.dispatch('getCustomFieldTypes');
        this.$store.dispatch('getConfig');
        this.formData.context = 'app';
        this.formData.custom_field_type_id = 1;
    }
}
</script>
