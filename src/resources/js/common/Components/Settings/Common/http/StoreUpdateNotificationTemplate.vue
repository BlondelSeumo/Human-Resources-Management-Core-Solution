<template>
    <modal id="notification-template"
           v-model="showModal"
           :title="`${$t('template')}: ${!loader ? notification_event.translated_name : ''}`"
           @submit="submitData"
           :loading="loading"
           size="large"
           :preloader="loader"
           body-class="p-0"
    >
        <app-tab type="horizontal" :tabs="tabs"/>
    </modal>
</template>

<script>
import ModalMixin from "../../../../Mixin/Global/ModalMixin";
import TemplateMixins from "./NotificationTemplate/TemplateMixins";
import {mapState} from "vuex";
import {onlyMailEvent} from "./NotificationTemplate/Helper";

export default {
    name: "StoreUpdateNotificationTemplate",
    mixins: [ModalMixin, TemplateMixins],
    props: {
        eventId: {
            required: true
        },
        alias: {
            default: 'app'
        }
    },
    data() {
        return {
            tabs: [
                {
                    name: this.$t('database'),
                    title: this.$t('database'),
                    component: "app-database-template",
                    props: this.$props,
                    permission: false,
                    icon: 'bell',
                },
                {
                    name: this.$t('mail'),
                    title: this.$t('mail'),
                    component: "app-mail-template",
                    permission: true,
                    icon: 'mail',
                    props: this.$props
                },
            ],
            template: {
                type: 'mail'
            }
        }
    },
    computed: {
        ...mapState({
            notification_event: state => state.notification_event.notification_event,
            loader: state => state.loading
        }),
    },
    methods: {
        submitData() {
            this.loading = true;
            let template = {...this.template, custom_content: this.template.content};
            return this.submitFromFixin('patch', `admin/app/notification-templates/${this.template.id}`, template);
        },
    },
    watch: {
        eventId: {
            handler: function (eventId) {
                this.$store.dispatch('getNotificationEvent', eventId)
            },
            immediate: true
        },
        notification_event: {
            handler: function (notification_event) {
                this.tabs.find(event => {
                    return event.component === 'app-database-template';
                }).permission = !onlyMailEvent.includes(notification_event.name)
            },
            deep: true
        }
    },
    created() {
        this.$hub.$on('setTemplate', template => {
            this.template = template;
        })
    }
}
</script>
