<template>
    <div>
        <app-store-update-notification-templates v-model="showTemplateModal"
                                                 :event-id="event_id"
                                                 v-if="showTemplateModal"
                                                 @showForm="showTemplateList = true"
                                                 :selected-url="selectedUrl"/>
        <app-table id="notification-template" :options="options" @action="getAction"/>
    </div>
</template>

<script>
import NotificationTemplateSettingsMixin from "../../../DataTable/DataTableMixins/NotificationTemplateSettingsMixin";

export default {
    name: "NotificationTemplateSettings",
    mixins: [NotificationTemplateSettingsMixin],
    props: {
        props: {
            default: function () {
                return {
                    alias: 'app'
                }
            }
        }
    },
    data() {
        return {
            event_id: '',
            showTemplateModal: false,
            template_id: '',
            selectedUrl: ''

        }
    },
    methods: {
        getAction(n_event, action, active) {
            if (action.actionType === 'edit') {
                this.showTemplateModal = true
                this.event_id = n_event.id
                this.selectedUrl = `admin/app/notification-templates/${n_event.id}`;
            }
        }
    },
}
</script>
