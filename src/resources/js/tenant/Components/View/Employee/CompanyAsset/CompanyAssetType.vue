<template>
    <div class="content-wrapper">
        <div class="d-flex align-items-center justify-content-between">
            <app-breadcrumb :page-title="$t('asset_types')"/>

            <div>
                <a :href="urlGenerator('/company-assets')" class="btn btn-success mr-2 mb-4">
                    <app-icon class="size-20 mr-2" name="arrow-left"/>
                    {{ $t('back_to_assets') }}
                </a>

                <button class="btn btn-with-shadow btn-info mb-4"
                        type="button"
                        @click="openModal()">
                    <app-icon class="size-20 mr-2" name="plus"/>
                    {{ $t('add_asset_type') }}
                </button>
            </div>
        </div>

        <app-table :id="tableId" :options="options" @action="triggerActions"/>

        <app-company-asset-type-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('company-asset-type-table')"
            @cancelled="cancelled"
            icon="trash-2"
            sub-title=""
            :message="message"
            modal-class="danger"
        />
    </div>
</template>

<script>
import {COMPANY_ASSET_TYPES} from "../../../../Config/ApiUrl";
import DatatableHelperMixin from "../../../../../common/Mixin/Global/DatatableHelperMixin";
import {urlGenerator} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "CompanyAssetType",
    mixins: [DatatableHelperMixin],
    data() {
        return {
            urlGenerator,
            isModalActive: false,
            selectedUrl: '',
            tableId: 'company-asset-type-table',
            message: '',
            options: {
                url: COMPANY_ASSET_TYPES,
                name: 'company-asset-type-table',
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'text',
                        key: 'name',
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action'
                    }
                ],
                filters: [],
                actions: [
                    {
                        title: this.$t('edit'),
                        name: 'edit',
                        icon: 'edit'
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash-2',
                        message: this.$t('you_are_going_to_delete_message', {resource: this.$t('company_asset_type')}),
                        name: 'delete',
                        modifier: row => this.$can('delete_company_asset_types')
                    }
                ],
                rowLimit: 10,
                orderBy: 'desc',
                responsive: true,
                showHeader: true,
                showFilter: true,
                showSearch: true,
                showAction: true,
                tableShadow: true,
                actionType: 'default',
                datatableWrapper: true,
                paginationType: 'pagination'
            },
        }
    },
    methods: {
        openModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
        },
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${COMPANY_ASSET_TYPES}/${row.id}`;
                this.isModalActive = true;
            } else if (action.name === 'delete') {
                this.delete_url = `${COMPANY_ASSET_TYPES}/${row.id}`;
                this.message = action.message;
                this.confirmationModalActive = true;
            }
        }
    }
}
</script>