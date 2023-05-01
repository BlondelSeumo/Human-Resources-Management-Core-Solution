<template>
    <div>
        <app-navbar :user="user"
                    :profile-data="filterProfileData"
                    :selected-language="userLanguage"
                    :language-data="languages"
                    notification-component="app-custom-notification-dropdown"
                    :notification-data="notifications.data"
                    :total-unread="notifications.total"
                    :logo-url="logoIconSrc"
                    :allNotificationUrl="allNotificationUrl"
                    :show-identifier="!!notifications.total_unread"
                    @clicked="readNotification"
                    @loadNotifications="loadNotification">
            <template v-if="isTenantExist" slot="left-option">
                <button class="navbar-toggler shadow-none pl-0">
                    <app-tenant-manager/>
                </button>
            </template>

        </app-navbar>

        <app-working-shift-modal
            v-if="workShiftModal"
            v-model="workShiftModal"
            :make-default="true"
            @close="workShiftModal = false"
        />
    </div>
</template>

<script>
import CoreLibrary from "../../../core/helpers/CoreLibrary";
import {mapActions, mapState} from 'vuex'
import {axiosPost, urlGenerator} from "../../Helper/AxiosHelper";

export default {
    name: "TopBar",
    extends: CoreLibrary,
    props: {
        profileData: {},
        logoIconSrc: {
            type: String,
            default: '/images/logo.png'
        },
        hasWorkShift:{
            type: Boolean
        },
    },
    data() {
        return {
            user: {},
            workShiftModal: false,
        }
    },
    computed: {
        ...mapState({
            languages: state => state.support.languages
        }),
        userLanguage() {
            if (window.appLanguage) {
                return window.appLanguage.toUpperCase();
            }
        },
        notifications() {
            return this.$store.getters.getFormattedNotifications
        },
        isTenantExist() {
            return !!window.tenant && !window.tenant.is_single;
        },
        tenant() {
            return window.tenant || {};
        },
        allNotificationUrl(){
            return this.profileData.find(data => data.optionName === this.$t('notifications'))?.url;
        },
        filterProfileData(){
            return this.profileData.filter(item => item.length !== 0);
        }
    },
    methods: {
        ...mapActions([
            'getLanguages'
        ]),
        setUser() {
            this.user = {
                full_name: this.$optional(window.user, 'full_name'),
                img: this.$optional(window.user, 'profile_picture', 'full_url') ||
                    urlGenerator('/images/avatar.png'),
                status: this.$t('online'),
                role: window.user.roles && window.user.roles.length ? window.user.roles[0].name : ''
            };
        },
        readNotification(notification) {
            axiosPost(`admin/user/notifications/mark-as-read/${notification.id}`).then(({data}) => {
                if (data.data.url) {
                    window.location = data.data.url;
                }
                this.$store.dispatch('getNotifications');
            });
        },
        checkDefaultWorkShift() {
            this.hasWorkShift ? this.workShiftModal = false : this.workShiftModal = true;
        },
        loadNotification(){
           if(this.notifications?.next_page_url){
               this.$store.dispatch('getNotifications', Number(this.notifications.current_page) + 1);
           }
        }
    },
    created() {
        this.getLanguages();
    },
    mounted() {
        this.setUser();
        this.checkDefaultWorkShift();
        this.$store.dispatch('getNotifications');
    }
}
</script>
