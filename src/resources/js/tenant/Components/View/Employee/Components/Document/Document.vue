<template>
    <div>
        <app-table
            id="documents-table"
            :options="options"
            @action="triggerActions"
        />

        <app-document-create-edit-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            :user-id="props.id"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('documents-table')"
            @cancelled="cancelled"
        />
    </div>
</template>

<script>
import DeleteMixin from "../../../../../../common/Mixin/Global/DeleteMixin";
import HelperMixin from "../../../../../../common/Mixin/Global/HelperMixin";
import DocumentMixins from "../../../../Mixins/DocumentMixins";
import TriggerActionMixin from "../../../../../../common/Mixin/Global/TriggerActionMixin";

export default {
    name: "Document",
    mixins: [HelperMixin, DocumentMixins, TriggerActionMixin, DeleteMixin],
    props: {
        props: {
            default: ''
        },
        id: {
            type: String
        }
    },
    data() {
        return {
            selectedUrl: '',
            isModalActive: false,
            confirmationModalActive: false
        }
    },
    methods: {
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${this.apiUrl.DOCUMENTS}/${row.id}`;
                this.isModalActive = true;
            } else {
                this.getAction(row, action, active)
            }
        },
    },
    mounted() {
        this.$hub.$on('headerButtonClicked-' + this.id, (component) => {
            this.isModalActive = true;
            this.selectedUrl = '';
        })
    },
}
</script>