<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('employment_status')" icon="user">
            <app-default-button v-if="$can('create_employment_statuses')"
                                @click="isModalActive = true"
                                :title="$addLabel('employment_status')"/>
        </app-page-top-section>

        <app-table
            id="tenant-employment-status"
            :options="options"
            @action="triggerActions"
        />

        <app-employment-status-create-edit-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('tenant-employment-status')"
            @cancelled="cancelled"
        />
    </div>
</template>

<script>
import HelperMixin from "../../../../../common/Mixin/Global/HelperMixin";
import EmploymentStatusMixins from "../../../Mixins/EmploymentStatusMixins";
import DeleteMixin from "../../../../../common/Mixin/Global/DeleteMixin";

export default {
    name: "EmploymentStatus",
    mixins: [HelperMixin, EmploymentStatusMixins, DeleteMixin],
    data() {
        return {
            isModalActive: false,
            selectedUrl: '',
            delete_url: ''
        }
    },
    methods: {
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${this.apiUrl.EMPLOYMENT_STATUSES}/${row.id}`;
                this.isModalActive = true;
            } else {
                this.getAction(row, action, active)
            }
        }
    },
    watch: {
        isModalActive(isModalActive) {
            if (!isModalActive) {
                this.selectedUrl = '';
            }
        }
    }
}
</script>

<style scoped>

</style>
