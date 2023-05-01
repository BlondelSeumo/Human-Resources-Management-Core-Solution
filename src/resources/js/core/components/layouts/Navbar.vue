<template>
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
            <a href="" class="align-self-center d-lg-none pl-0 navbar-brand">
                <img :src="logoUrl" alt="logo"/>
            </a>
            <div class="d-flex justify-content-between w-100">
                <div class="d-flex justify-content-start">
                    <button class="navbar-toggler align-self-center d-none d-lg-block pl-0"
                            type="button">
                <span v-if="leftMenuType === 'normal'"
                      :key="'align-left'"
                      @click.prevent="togglingLeftMenu('active-icon-only-menu')"
                      :title="$t('collapse_sidebar')">
                    <app-icon :name="'align-left'"/>
                </span>
                        <span v-else-if="leftMenuType === 'icon-only'"
                              :key="'align-center'"
                              @click.prevent="togglingLeftMenu('active-floating-menu')"
                              :title="$t('floating_sidebar')">
                    <app-icon :name="'align-center'"/>
                </span>
                        <span v-else-if="leftMenuType === 'floating'"
                              :key="'align-justify'"
                              @click.prevent="togglingLeftMenu('active-normal-menu')"
                              :title="$t('full_sidebar')">
                    <app-icon :name="'align-justify'"/>
                </span>
                    </button>
                    <button class="navbar-toggler align-self-center d-lg-none pl-0"
                            type="button"
                            data-toggle="offcanvas"
                            @click="sidebarOffcanvas">
                        <app-icon :name="'align-left'"/>
                    </button>
                    <slot name="left-option"></slot>
                </div>
                <div class="d-flex justify-content-center">
                    <slot name="center-option"></slot>
                </div>
                <div>
                    <ul class="navbar-nav navbar-nav-right ml-auto">
                        <li class="nav-item">
                            <button class="navbar-toggler align-self-center"
                                    type="button"
                                    @click.prevent="toggleDarkMode">
                <span v-if="darkMode" :key="'sun'" :title="$t('light_mood')">
                    <app-icon :name="'sun'"/>
                </span>
                                <span v-else :key="'moon'" :title="$t('dark_mood')">
                    <app-icon :name="'moon'"/>
                </span>
                            </button>
                        </li>
                        <li class="nav-item d-none d-lg-block">
                            <a v-if="fullScreen" class="d-flex align-items-center nav-link" href="#" :key="'maximize'"
                               @click="fullscreen" :title="$t('exit_fullscreen')">
                                <app-icon :name="'minimize'"/>
                            </a>
                            <a v-else class="d-flex align-items-center nav-link" href="#" :key="'minimize'"
                               @click="fullscreen"
                               :title="$t('fullscreen')">
                                <app-icon :name="'maximize'"/>
                            </a>
                        </li>

                        <app-navbar-language-dropdown
                            :selected-language="selectedLanguage"
                            :data="languageData"
                        />

                        <component
                            :is="notificationComponent"
                            :all-notification-url="allNotificationUrl"
                            :data="notificationData"
                            :show-identifier="showIdentifier"
                            :total-unread="totalUnread"
                            @clicked="sendNotification"
                            @loadNotifications="$emit('loadNotifications')"
                        />

                        <span class="topbar-divider d-none d-sm-block"/>

                        <app-navbar-profile-dropdown
                            :user="user"
                            :data="profileData"
                        />
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import ThemeMixin from "../../mixins/global/ThemeMixin"
import CoreLibrary from "../../helpers/CoreLibrary";
import AppFunction from "../../helpers/app/AppFunction";

export default {
    name: "Navbar",
    mixins: [ThemeMixin],
    extends: CoreLibrary,
    props: {
        user: {
            type: Object,
            require: true
        },
        profileData: {
            type: Array,
            require: true
        },
        selectedLanguage: {
            type: String,
            default: 'EN'
        },
        languageData: {
            type: Array,
            require: true
        },
        notificationData: {
            type: Array,
            require: true
        },
        logoUrl: {
            type: String,
            default: AppFunction.getAppUrl('images/core.png'),
        },
        allNotificationUrl: {
            type: String,
            require: true
        },
        showIdentifier: {
            type: Boolean,
            default: false
        },
        totalUnread: {
            type: Number
        },
        notificationComponent: {
            type: String,
            default: 'app-navbar-notification-dropdown'
        }

    },
    data() {
        return {
            fullScreen: false,
            leftMenuType: 'normal',
            darkMode: false,
        }
    },
    watch: {
        leftMenuType: function () {
            let htmlElement = document.documentElement;

            if (this.leftMenuType === 'normal') {
                localStorage.setItem('sidebar', 'normal');
                htmlElement.setAttribute('sidebar', 'normal');
            } else if (this.leftMenuType === 'icon-only') {
                localStorage.setItem('sidebar', 'icon-only');
                htmlElement.setAttribute('sidebar', 'icon-only');
            } else {
                localStorage.setItem('sidebar', 'floating');
                htmlElement.setAttribute('sidebar', 'floating');
            }
        }
    },
    mounted() {
        // Check for active theme
        let body = $('body'),
            htmlElement = document.documentElement,
            theme = localStorage.getItem('theme'),
            sidebar = localStorage.getItem('sidebar');

        if (theme === 'dark') {
            htmlElement.setAttribute('theme', 'dark');
            this.darkMode = true;
            this.$store.state.theme.darkMode = true;
        } else {
            htmlElement.setAttribute('theme', 'light');
            this.darkMode = false;
            this.$store.state.theme.darkMode = false;
        }

        if (sidebar === 'normal') {
            localStorage.setItem("sidebar", 'normal');
            htmlElement.setAttribute('sidebar', 'normal');
            body.removeClass('sidebar-icon-only');
            body.removeClass('sidebar-hover-only');
            this.leftMenuType = 'normal';
        } else if (sidebar === 'icon-only') {
            localStorage.setItem("sidebar", 'icon-only');
            htmlElement.setAttribute('sidebar', 'icon-only');
            body.removeClass('sidebar-hover-only');
            body.toggleClass('sidebar-icon-only');
            this.leftMenuType = 'icon-only';
        } else if (sidebar === 'floating') {
            localStorage.setItem("sidebar", 'floating');
            htmlElement.setAttribute('sidebar', 'floating');
            body.removeClass('sidebar-icon-only');
            body.toggleClass('sidebar-hover-only');
            $('.sidebar').find('.collapse.show').collapse('hide');
            this.leftMenuType = 'floating';
        }
    },
    methods: {
        sendNotification(notification) {
            this.$emit('clicked', notification);
        },
        sidebarOffcanvas() {
            $('.sidebar-offcanvas').toggleClass('active')
        },
        fullscreen() {
            if ((document.fullScreenElement !== undefined && document.fullScreenElement === null) || (document.msFullscreenElement !== undefined && document.msFullscreenElement === null) || (document.mozFullScreen !== undefined && !document.mozFullScreen) || (document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen)) {
                if (document.documentElement.requestFullScreen) {
                    document.documentElement.requestFullScreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullScreen) {
                    document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
                } else if (document.documentElement.msRequestFullscreen) {
                    document.documentElement.msRequestFullscreen();
                }
                this.fullScreen = true;
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                this.fullScreen = false;
            }
        },
        togglingLeftMenu(action) {
            let body = $('body');

            if (action === 'active-icon-only-menu') {
                body.removeClass('sidebar-hover-only');
                body.toggleClass('sidebar-icon-only');
                this.leftMenuType = 'icon-only';
            } else if (action === 'active-floating-menu') {
                body.removeClass('sidebar-icon-only');
                body.toggleClass('sidebar-hover-only');
                $('.sidebar').find('.collapse.show').collapse('hide');
                this.leftMenuType = 'floating';
            } else if (action === 'active-normal-menu') {
                body.removeClass('sidebar-icon-only');
                body.removeClass('sidebar-hover-only');
                this.leftMenuType = 'normal';
            }
        }
    }
}
</script>
