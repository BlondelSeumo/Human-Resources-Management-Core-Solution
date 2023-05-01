<template>
    <div>
        <app-update-notification-settings :alias="props.alias"
               v-if="showNotificationSettingsModal"
               v-model="showNotificationSettingsModal"
               :event-id="event_id"
        />

        <app-table id="notification-event"
                   :options="options"
                   @action="getAction"/>

        <app-update-notification-templates v-model="showTemplateModal"
                 :event-id="event_id"
                 v-if="showTemplateModal"
                 @showForm="showTemplateList = true"
                 :selected-url="selectedUrl"
                 :alias="props.alias"/>
    </div>
</template>

<script>
import NotificationEventMixin from "../../../Mixin/Global/NotificationEventMixins";

export default {
    name: "NotificationSettings",
    mixins: [NotificationEventMixin],
    props: {
        props: {
            default: function () {
                return {
                    alias: 'app',
                    specific: false
                }
            }
        }
    },
    data() {
        return {
            showNotificationSettingsModal: false,
            event_id: '',
            showTemplateModal: false,
            template_id: '',
            selectedUrl: ''
        }
    },
    methods: {
        getAction(n_event, action, active) {
            if (action.actionType === 'edit') {
                this.showNotificationSettingsModal = true
                this.event_id = n_event.id
            }
            if (action.actionType === 'manage') {
                this.showTemplateModal = true
                this.event_id = n_event.id
                this.selectedUrl = `admin/app/notification-templates/${n_event.id}`;
            }
        }
    },

}
</script>
