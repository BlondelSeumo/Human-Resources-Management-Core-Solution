<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('designation')">
            <app-default-button
                :title="$fieldTitle('add', 'designation', true)"
                v-if="$can('create_designations')"
                @click="openModal()"
            />
        </app-page-top-section>

        <app-table
            id="designation-table"
            :options="options"
            @action="triggerActions"
        />

        <app-designation-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('designation-table')"
            @cancelled="cancelled"
        />
    </div>
</template>
<script>
    import HelperMixin from "../../../../../common/Mixin/Global/HelperMixin";
    import DesignationMixin from "../../../Mixins/DesignationMixin";
    import {DESIGNATION} from "../../../../Config/ApiUrl";
    import {mapState} from "vuex";

    export default {

        name: "AppDesignation",
        mixins: [HelperMixin, DesignationMixin],
        data() {
            return {
                isModalActive: false,
                selectedUrl: '',
            }
        },

        methods: {
            triggerActions(row, action, active) {
                if (action.title === this.$t('edit')) {
                    this.selectedUrl = `${DESIGNATION}/${row.id}`;
                    this.isModalActive = true;
                } else {
                    this.getAction(row, action, active)
                }
            },

            openModal() {
                this.isModalActive = true;
                this.selectedUrl = ''
            },
        },
    }
</script>
