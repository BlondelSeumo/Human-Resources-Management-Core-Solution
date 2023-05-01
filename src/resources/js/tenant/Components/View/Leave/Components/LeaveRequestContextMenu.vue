<template>
    <div>
        <div class="bulk-floating-action-wrapper">
            <div class="actions">
                <div class="p-2">
                    <button class="btn btn-success" @click="openConfirmationModal('approved')">
                        {{ $t('approve') }}
                    </button>
                    <button class="btn btn-danger" @click="openConfirmationModal('rejected')">
                        {{ $t('reject') }}
                    </button>
                </div>
            </div>
        </div>

        <leave-request-bulk-action-modal
            v-if="confirmationModalActive"
            v-model="confirmationModalActive"
            :modal-type="status"
            :requests="requests"
            :all-selected="allSelected"
            @leaveRequestUpdated="$emit('reload')"
        />
    </div>
</template>

<script>

import LeaveRequestBulkActionModal from "./LeaveRequestBulkActionModal";

export default {
    name: "LeaveRequestContextMenu",
    components: {LeaveRequestBulkActionModal},
    props: {
        requests: {},
        allSelected: {},
    },
    data() {
        return {
            confirmationModalActive: false,
            loading: false,
            status: '',
        }
    },
    methods: {
        openConfirmationModal(status) {
            this.confirmationModalActive = true;
            this.status = status;
        },
    }
}
</script>
