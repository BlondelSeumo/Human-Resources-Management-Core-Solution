<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('holiday')">
            <app-default-button
                :title="$fieldTitle('add', 'holiday', true)"
                v-if="$can('create_holidays')"
                @click="openModal"
            />
        </app-page-top-section>

        <app-table
            id="holiday-table"
            :options="options"
            :card-view="true"
            @action="triggerActions"
        />

        <app-holiday-modal
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
            @confirmed="confirmed('holiday-table')"
            @cancelled="cancelled"
        />
    </div>
</template>

<script>
import HolidayMixins from "../../../Mixins/HolidayMixins";
import {HOLIDAYS} from "../../../../Config/ApiUrl";

export default {
    name: "Holiday",
    mixins: [HolidayMixins],
    data() {
        return {
            selectedUrl: '',
            isModalActive: false,
        }
    },
    methods: {
        openModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
        },
        triggerActions(row, action, active) {
            if (action.type === 'edit') {
                this.selectedUrl = `${HOLIDAYS}/${row.id}`;
                this.isModalActive = true;
            } else {
                this.getAction(row, action, active)
            }
        }
    }
}
</script>

