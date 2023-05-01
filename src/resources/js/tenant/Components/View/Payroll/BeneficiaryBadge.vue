<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('beneficiary_badge')">
            <app-default-button
                :title="$fieldTitle('add', 'badge', true)"
                v-if="$can('create_beneficiaries')"
                @click="openModal()"
            />
        </app-page-top-section>

        <app-table
            id="beneficiary-badge-table"
            :options="options"
            @action="triggerActions"
        />

        <app-beneficiary-badges-create-edit-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />
        <app-confirmation-modal
            v-if="confirmationModalActive"
            :firstButtonName="$t('yes')"
            modal-class="warning"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('beneficiary-badge-table')"
            @cancelled="confirmationModalActive = false"
        />
    </div>
</template>

<script>


import BeneficiaryBadgeMixin from "../../Mixins/BeneficiaryBadgeMixin";
import {BENEFICIARY_BADGE} from "../../../Config/ApiUrl";
import {axiosDelete} from "../../../../common/Helper/AxiosHelper";
import HelperMixin from "../../../../common/Mixin/Global/HelperMixin";

export default {
    name: "BeneficiaryBadge",
    mixins: [BeneficiaryBadgeMixin, HelperMixin],
    data() {
        return {
            isModalActive: false,
            selectedUrl: '',
            confirmationModalActive: '',
        }
    },

    methods: {
        openModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
        },
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${BENEFICIARY_BADGE}/${row.id}`;
                this.isModalActive = true;
            } else if (action.name === 'delete') {
                this.delete_url = `${BENEFICIARY_BADGE}/${row.id}`;
                this.message = action.message;
                this.confirmationModalActive = true;
            }
        },
        confirmed(id) {
            return axiosDelete(this.delete_url).then(response => {
                this.toastAndReload(response.data.message, id)
                this.confirmationModalActive = false;
            }).catch((error) => {
                if (error.response)
                    this.toastException(error.response.data)
            });
        },
    },
}
</script>
