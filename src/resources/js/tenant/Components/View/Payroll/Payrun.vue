<template>
    <div class="content-wrapper">
        <div class="row align-items-center">
            <div class="col-md-12 col-lg-6">
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb p-0 d-flex align-items-center mb-primary">
                            <li class="breadcrumb-item page-header d-flex align-items-center">
                                <h4 class="mb-0">{{ pageTitle }}</h4>
                            </li>
                            <template v-if="isManualPayrunModalActive">
                                <li class="ml-2">|</li>
                                <li>
                                    <a href="" @click.prevent="openPayrun"
                                       class="btn btn-link text-primary pl-2">{{ $t('back_to_payrun') }}</a>
                                </li>
                            </template>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-md-12 col-lg-6">
                <div v-if="!isManualPayrunModalActive"
                     class="d-flex justify-content-lg-end mb-primary">
                    <app-default-button
                        btn-class="btn btn-success mr-2"
                        :title="$t('settings')"
                        v-if="$can('view_payroll_settings')"
                        :url="apiUrl.PAYROLL_SETTINGS_FRONTEND"
                    />
                    <div class="btn-group dropdown"
                         v-if="$can('run_default_payrun') || $can('run_manual_payrun')">
                        <app-default-button
                            :title="$can('run_default_payrun') ? $t('default_payrun') : $t('manual_payrun')"
                            @click="$can('run_default_payrun') ? openDefaultPayrunModal() : openManualPayrun()"
                        />
                        <button
                            v-if="$can('run_default_payrun') && $can('run_manual_payrun')"
                            class="btn btn-primary rounded-right px-3"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fas fa-chevron-down fa-sm"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" @click="openManualPayrun">
                                {{ $t('manual_payrun') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <app-filter-with-search
            v-if="!isManualPayrunModalActive"
            :options="options"
            @filteredValue="setFilterValues"
            @searchValue="setSearchValue"
        />
        <default-payrun-create-modal
            v-if="isDefaultPayrunCreateModalActive"
            v-model="isDefaultPayrunCreateModalActive"
            @openManualPayrun="openManualPayrun"
            @reload="reload"
        />
        <app-manual-payrun
            v-if="isManualPayrunModalActive"
            @cancel="openPayrun"
            :props-payrun-id="updatePayrunId"
        />

        <div v-else>
            <app-overlay-loader v-if="preLoader"/>

            <div v-else>
                <template v-for="payrun in payruns">
                    <PayrunItemsRow
                        :payrun="payrun"
                        class="mb-4"
                        @reload="reload"
                        @openManualPayrun="updateManualPayrun"
                        @openConflictedPayslip="openConflictedPayslips"
                    />
                </template>
            </div>

            <div v-if="payruns.length === 0 && !preLoader" class="card card-with-shadow p-5 border-0">
                <app-empty-data-block :message="$t('no_payruns_found')"/>
            </div>

            <app-pagination
                v-if="payruns.length !== 0 && paginationData.totalRow > paginationData.rowLimit"
                :totalRow="paginationData.totalRow"
                :rowLimit="paginationData.rowLimit"
                @submit="pageChange"
            />

        </div>

        <app-payrun-conflict-modal
            v-if="payrunConflictModal"
            v-model="payrunConflictModal"
            :payrunId="conflictedPayrunId"
            @input="reload"
        />

    </div>
</template>

<script>

import PayrunMixin from "../../Mixins/PayrunMixin";
import PayrunItemsRow from "./Components/PayrunItemsRow";
import DefaultPayrunCreateModal from "./Components/DefaultPayrunCreateModal";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";

export default {
    name: "payrun",
    mixins: [PayrunMixin],
    components: {PayrunItemsRow, DefaultPayrunCreateModal},
    data() {
        return {
            isDefaultPayrunCreateModalActive: false,
            isManualPayrunModalActive: false,
            selectedUrl: '',
            confirmationModalActive: '',
            pageTitle: this.$t('payrun'),
            payruns: {},
            paginationData: {},
            preLoader: false,
            updatePayrunId: '',
            payrunConflictModal: false,
            conflictedPayrunId: null,
        }
    },
    mounted() {
        this.$hub.$on('reload-payrun-table', () => {
            this.openPayrun()
            this.reload()
        });

        this.$hub.$on('open-payrun-table', () => {
            this.openPayrun()
        });
    },
    methods: {
        getPayruns(page = null) {
            this.preLoader = true;
            axiosGet(`${this.apiUrl.PAYRUNS}?${this.queryString}`)
                .then(({data}) => {
                    this.preLoader = false
                    this.payruns = data.data;
                    this.paginationData = {
                        totalRow: data.total,
                        rowLimit: data.per_page,
                    }
                })
        },
        openManualPayrun() {
            this.isManualPayrunModalActive = true;
            this.pageTitle = this.$t('manual_payrun')
        },

        updateManualPayrun(id = null) {
            this.updatePayrunId = id || ''
            this.isManualPayrunModalActive = true;
            this.pageTitle = `${this.$t('update')} ${this.$t('manual_payrun')}`
        },

        openPayrun() {
            this.updatePayrunId = ''
            this.isManualPayrunModalActive = false;
            this.pageTitle = this.$t('payrun')
        },
        reload() {
            this.getPayruns();
        },
        openDefaultPayrunModal() {
            this.isDefaultPayrunCreateModalActive = true;
        },

        pageChange(page) {
            this.$store.dispatch('updateFilterObject', {page: page})
        },
        openConflictedPayslips(id) {
            this.conflictedPayrunId = id;
            this.payrunConflictModal = true;
        },
    },
    computed: {
        queryString() {
            return this.$store.getters.getFilterUrls;
        },
    },
    watch: {
        queryString: {
            handler: function (queryString) {
                this.reload()
            },
            immediate: true
        }
    },
}
</script>