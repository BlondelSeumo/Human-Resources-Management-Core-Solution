<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('assets')">
            <app-default-button
                btn-class="btn btn-success mr-1"
                :title="$t('export_all')"
                @click="viewConfirmationModal()"
            />

            <button
                type="button" v-if="!props.id && $can('view_company_asset_types')"
                class="btn btn-success btn-with-shadow mr-1"
                @click="viewAssetTypePage">
                <app-icon name="list" class="size-20 mr-2"/>
                {{ $t('asset_types') }}
            </button>

            <app-default-button
                :title="$fieldTitle('Add', $t('asset'), true)"
                v-if="$can('create_company_assets')"
                @click="openModal()"
            />
        </app-page-top-section>

        <app-table
            id="company-asset-table"
            :options="options"
            @action="triggerActions"
        />

        <app-company-asset-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            :editable="isModalEditable"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('company-asset-table')"
            @cancelled="cancelled"
            icon="trash-2"
            sub-title=""
            :message="message"
            modal-class="danger"
        />

        <app-confirmation-modal
            v-if="exportConfirmationModal"
            :title="modalSubtitle"
            :message="modalMessage"
            modal-id="app-confirmation-modal"
            modal-class="primary"
            icon="download"
            :first-button-name="$t('export')"
            :second-button-name="$t('cancel')"
            @confirmed="exportFilteredAsset()"
            @cancelled="exportConfirmationModal = false"
            :self-close="false"
        />
    </div>
</template>

<script>
import HelperMixin from "../../../../../common/Mixin/Global/HelperMixin";
import {COMPANY_ASSETS} from "../../../../Config/ApiUrl";
import CompanyAssetMixin from "../../../Mixins/CompanyAssetMixin";
import {axiosGet, urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import {localTimeZone} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "CompanyAssets",
    mixins: [HelperMixin, CompanyAssetMixin],
    props: {
        props: {
            default: function () {
                return {}
            }
        },
    },
    data() {
        return {
            isModalActive: false,
            isModalEditable: false,
            companyAssetId: '',
            selectedUrl: '',
            message: '',
            exportConfirmationModal: false,
            modalMessage: '',
            modalSubtitle: '',
        }
    },
    methods: {
        openModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
            this.isModalEditable = true;
        },
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${COMPANY_ASSETS}/${row.id}`;
                this.isModalEditable = true;
                this.isModalActive = true;
            } else if (action.name === 'delete') {
                this.delete_url = `${COMPANY_ASSETS}/${row.id}`;
                this.message = action.message;
                this.confirmationModalActive = true;
            } else if (action.name === 'preview') {
                this.selectedUrl = `${COMPANY_ASSETS}/${row.id}`;
                this.isModalActive = true;
                this.isModalEditable = false;
            }
        },
        viewConfirmationModal() {
            this.modalSubtitle = this.$t('all_asset_export_title');
            this.modalMessage = this.$t('all_asset_export_message');
            this.exportConfirmationModal = true;
            // if (this.tableData < 1) {
            //     this.noDataFoundModal = true;
            // } else {
            //     this.exportConfirmationModal = true;
            // }
        },
        exportFilteredAsset() {
            window.location = urlGenerator(`${this.apiUrl.EXPORT}/asset/all?timeZone=${localTimeZone}`);
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.exportConfirmationModal = false;
        },
        viewAssetTypePage() {
            window.location = urlGenerator('/company-asset-types');
        },
        getAssetTypes() {
            axiosGet(this.apiUrl.SELECTABLE_COMPANY_ASSET_TYPES).then(({data}) => {
                this.options.filters.find(fl => fl.key === 'asset_type' && fl.type === 'multi-select-filter').option = data;
            })
        }

    },
}
</script>