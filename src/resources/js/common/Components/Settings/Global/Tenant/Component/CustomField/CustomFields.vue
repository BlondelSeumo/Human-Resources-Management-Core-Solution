<template>

    <div class="container-fluid p-0">
        <app-table id="app-custom-fields"
                   :options="options"
                   @action="triggerActions"/>

        <tenant-store-update-custom-fields v-model="showCustomFieldModal"
                                           v-if="showCustomFieldModal"
                                           :custom-field="customField"
                                           :selected-url="selectedUrl"/>

        <app-confirmation-modal v-if="confirmationModalActive"
                                modal-id="app-confirmation-modal"
                                @confirmed="confirmed('app-custom-fields')"
                                @cancelled="cancelled"/>
    </div>
</template>

<script>
import CustomFieldTable from "../../../../../../Mixin/Global/CustomFiled/CustomFieldTableMixin";
import {CUSTOM_FIELDS, TENANT_CUSTOM_FIELDS} from "../../../../../../Config/apiUrl";

export default {
    name: "CustomFields",
    mixins: [CustomFieldTable],
    props: {
        id: {},
        props: {
            default: function () {
                return {
                    alias: 'app'
                }
            }
        }
    },
    data() {
        return {
            showCustomFieldModal: false,
            selectedUrl: '',
            customField: {}
        }
    },
    methods: {
        triggerActions(customField, action, active) {
            this.customField = {...customField};
            if (action.name === 'edit') {
                this.$emit('showAddForm', {...customField})
                this.showCustomFieldModal = true;
                delete customField.created_by;
                this.selectedUrl = `${this.props.alias === 'app' ? CUSTOM_FIELDS : TENANT_CUSTOM_FIELDS}/${customField.id}`;
                this.customField = customField;
            } else {
                this.getAction(customField, action, active)
            }
        },
    },

    mounted() {
        this.$hub.$on('headerButtonClicked-' + this.id, () => {
            this.showCustomFieldModal = true
        })
    },
    watch: {
        showCustomFieldModal: function (showCustomFieldModal) {
            if (!showCustomFieldModal)
                this.selectedUrl = null;
        }
    },
    beforeDestroy() {
        this.$hub.$off('headerButtonClicked-' + this.id);
    }
}
</script>
