<template>
    <modal id="notification-settings"
           v-model="showModal"
           :title="`${$t('settings')}: ${Object.keys(notification_event).length ?  $optional(notification_event, 'translated_name') : ''}`"
           @submit="submitData"
           :scrollable="false"
           :loading="loading"
           :preloader="loader">
        <form method="post"
              ref="form"
              :data-url="`${getUrl}/${notification_settings.id}`"
        >
            <app-form-group
                type="multi-select"
                :label="$t('choose_multi_field', {field: $fieldTitle('notification', 'channel')})"
                v-model="notification_settings.notify_by"
                :list="getNotificationChannels()"
                list-value-field="name"
                :isAnimatedDropdown="true"
                :error-message="$errorMessage(errors, 'notify_by')"
            />

            <fieldset v-if="isAudiencesEnabled">
                <legend class="font-size-default pt-0 mb-3">
                    {{ $fieldTitle('notification', 'audiences') }}
                </legend>

                <app-form-group
                    :label="$t('roles')"
                    type="multi-select"
                    v-model="notification_settings.roles"
                    :list="getFormattedRoles"
                    :isAnimatedDropdown="true"
                    :error-message="$errorMessage(errors, 'audiences.0.audience_type')"
                />

                <app-form-group
                    :label="$t('users')"
                    type="multi-select"
                    v-model="notification_settings.users"
                    :list="getFormattedUsers"
                    :isAnimatedDropdown="true"
                    :error-message="$errorMessage(errors, 'audiences.1.audience_type')"
                />
            </fieldset>
        </form>
    </modal>
</template>

<script>
import {mapGetters, mapState} from 'vuex'
import FormHelperMixins from "../../../../Mixin/Global/FormHelperMixins"
import {roles, users} from '../../../../Helper/NotificationSettings'
import {axiosGet, axiosPatch} from "../../../../Helper/AxiosHelper";
import {
    TENANT_NOTIFICATION_EVENT,
    TENANT_NOTIFICATION_SETTINGS,
    NOTIFICATION_SETTING,
    NOTIFICATION_EVENTS
} from '../../../../Config/apiUrl'
import ModalMixin from "../../../../Mixin/Global/ModalMixin";
import NotificationEventMixin from "../Mixin/NotificationEventMixin";
import {noAudienceEvent} from "./NotificationTemplate/Helper";

export default {
    name: "StoreUpdateNotificationSettings",
    mixins: [FormHelperMixins, ModalMixin, NotificationEventMixin],
    props: {
        alias: {
            required: true,
            type: String,
            default: 'app'
        },
        eventId: {
            required: true
        },
        specificTenant: {
            default: false
        }
    },
    data() {
        return {
            notification_settings: {
                notification_event_id: '',
                notify_by: [],
                roles: [],
                users: [],
                id: ''
            },
            notification_event: {},
        }
    },
    methods: {
        submitData() {
            this.loading = true;
            const notification_settings = {
                ...this.notification_settings,
                audiences: [
                    this.notification_settings.roles.length ? {
                        audience_type: 'roles',
                        audiences: this.notification_settings.roles
                    } : '',
                    this.notification_settings.users.length ? {
                        audience_type: 'users',
                        audiences: this.notification_settings.users
                    } : '',
                ]
            };

            notification_settings.audiences = notification_settings.audiences.filter(a => a);

            const url = `${this.getUrl}/${this.notification_settings.id}`;

            axiosPatch(url, notification_settings).then(({data}) => {
                this.toastAndReload(data.message, 'notification-event')
                $('#notification-settings').modal('hide')
            }).catch(error => {
                this.errors = error.response.data.errors;
                this.message = '';
            }).finally(res => {
                this.loading = false;
            })
        },

        getNotificationEvent() {
            this.preloader = true;

            const url = `${this.getEventUrl}/${this.eventId}`;
            axiosGet(url).then(response => {
                this.notification_event = response.data;
            }).finally(() => {
                this.preloader = false;
            })
        }

    },
    computed: {
        ...mapState({
            notification_channels: state => state.support.notification_channels,
            notification_events: state => state.support.notification_events,
            loader: state => state.loading
        }),
        ...mapGetters([
            'getFormattedUsers',
            'getFormattedRoles'
        ]),
        getUrl() {
            if (!this.tenant || this.tenant.is_single) {
                return  `admin/app/notification-settings`;
            }
            return TENANT_NOTIFICATION_SETTINGS;
        },

        getEventUrl() {
            if (!this.tenant || this.tenant.is_single) {
                return NOTIFICATION_EVENTS;
            }
            return TENANT_NOTIFICATION_EVENT;
        },
        tenant() {
            return window.tenant || {};
        },
        isAudiencesEnabled() {
            return !noAudienceEvent.includes(this.notification_event.name)
        }
    },
    created() {
        this.$store.dispatch('getUsers', {alias: this.alias});
        this.$store.dispatch('getRoles', {alias: this.alias});
        this.$store.dispatch('getNotificationChannels');
        this.getNotificationEvent();
    },
    watch: {
        notification_event: {
            handler: function (notification_event) {
                if (Object.keys(notification_event).length) {
                    this.notification_settings.roles = roles(notification_event);
                    this.notification_settings.users = users(notification_event);
                    this.notification_settings.notify_by = this.$optional(notification_event.settings, 'notify_by') || [];
                    this.notification_settings.id = this.$optional(notification_event.settings, 'id');
                    this.notification_settings.notification_event_id = notification_event.id
                }
            },
            deep: true
        },
    }
}
</script>

<style scoped>

</style>
