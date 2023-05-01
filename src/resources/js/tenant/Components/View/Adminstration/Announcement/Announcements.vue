<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('announcements')">
            <app-default-button
                :title="$fieldTitle('add', 'announcement', true)"
                v-if="$can('create_announcements')"
                @click="openModal()"
            />
        </app-page-top-section>

        <app-table
            id="announcement-table"
            :options="options"
            @action="triggerActions"
        />

        <app-announcement-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('announcement-table')"
            @cancelled="cancelled"
            icon="trash-2"
            sub-title=""
            :message="message"
            modal-class="danger"
        />
    </div>
</template>

<script>

import HelperMixin from "../../../../../common/Mixin/Global/HelperMixin";
import {ANNOUNCEMENTS} from "../../../../Config/ApiUrl";
import AnnouncementMixin from "../../../Mixins/AnnouncementMixin";

export default {
    name: "Announcement",
    mixins: [HelperMixin, AnnouncementMixin],
    data() {
        return {
            isModalActive: false,
            announcementId: '',
            selectedUrl: '',
            message: '',
        }
    },

    methods: {
        openModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
        },
        triggerActions(row, action, active) {
            if (action.name === 'edit') {
                this.selectedUrl = `${ANNOUNCEMENTS}/${row.id}`;
                this.isModalActive = true;
            } else if (action.name === 'delete') {
                this.delete_url = `${ANNOUNCEMENTS}/${row.id}`;
                this.message = action.message;
                this.confirmationModalActive = true;
            } else if (action.name === 'add-employee') {
                this.isAddEmployeeModalActive = true;
                this.announcementId = row.id
            }
        }
    },
}
</script>
