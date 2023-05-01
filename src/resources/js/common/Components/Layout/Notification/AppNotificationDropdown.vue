<template>
    <li class="nav-item dropdown keep-inside-clicks-open">
        <a href="#"
           @click.prevent="randomDataGenerate"
           id="notificationDropdown"
           class="d-flex align-items-center nav-link count-indicator dropdown-toggle"
           data-toggle="dropdown">
            <app-icon :name="'bell'"/>
            <span class="count-symbol bg-primary spinner-grow" v-if="showIdentifier"/>
        </a>
        <div v-if="data.length !== 0" class="dropdown-menu dropdown-menu-right navbar-dropdown notification-dropdown"
             aria-labelledby="notificationDropdown">
            <h6 class="p-primary">{{ $t('notifications') }}</h6>
            <div class="px-primary pb-2 primary-text-color d-flex justify-content-between">
                <!--                <span class="badge badge-primary badge-sm badge-circle float-right">-->
                <!--                    {{ data.length }}-->
                <!--                </span>-->
                <a :href="unreadNotificationUrl">{{ totalUnread }} {{ $t('new') }}</a>
                <a href="#" @click.prevent="markAsRead">{{ $t('mark_all_as_read') }}</a>
                <a :href="allNotificationUrl">{{ $t('see_all') }}</a>
            </div>
            <div class="dropdown-divider"/>
            <app-pre-loader v-if="loading" class="py-primary"/>
            <div v-else class="dropdown-items-wrapper custom-scrollbar max-height-300" id="infinite-list">
                <a class="dropdown-item"
                   v-for="(item, index) in data"
                   :href="item.url"
                   :key="index"
                   @click.prevent="$emit('clicked', item)">
                    <div class="media align-items-center">
                        <div class="avatars-w-50 mr-3">
                            <app-avatar :shadow="false"
                                        :img="item.img"
                                        :title="item.name"/>
                        </div>
                        <div class="media-body">
                            <p class="my-0 media-heading" v-html="item.title"></p>
                            <span class="text-muted">
                                {{ textTruncate(item.description, 25) }}
                            </span>
                            <span class="primary-text-color link">
                                <span class="mr-3">{{ item.date }}</span>
                                <span>{{ item.time }}</span>
                            </span>
                        </div>
                    </div>
                </a>
                <div class="py-2" v-if="preloader">
                    <app-pre-loader/>
                </div>
            </div>
        </div>
        <div v-else
             class="dropdown-menu dropdown-menu-right navbar-dropdown notification-dropdown no-notification-dropdown p-primary"
             aria-labelledby="notificationDropdown">
            <div class="d-flex justify-content-center">
                <img :src="noNotificationImg"
                     class="no-notification-img"
                     alt="">
            </div>
            <p class="text-center font-size-80 m-0 pt-2 pb-0">
                {{ noNotificationTitle }}
            </p>
        </div>
    </li>
</template>

<script>

import AppFunction from "../../../../core/helpers/app/AppFunction";
import {axiosPost} from "../../../Helper/AxiosHelper";
import {NOTIFICATIONS} from "../../../Config/apiUrl";

export default {
    name: "AppNotificationDropdown",
    props: {
        data: {
            type: Array,
            required: true
        },
        allNotificationUrl: {
            type: String,
            require: true
        },
        showIdentifier: {},
        totalUnread: {}
    },
    data() {
        return {
            noNotificationData: [
                {
                    img: 'images/no-notifications/Cheers',
                    title: this.$t('no_notification_one')
                },
                {
                    img: 'images/no-notifications/IceCream',
                    title: this.$t('no_notification_two')
                },
                {
                    img: 'images/no-notifications/Music',
                    title: this.$t('no_notification_three')
                }
            ],
            noNotificationImg: '',
            noNotificationTitle: '',
            loading: false,
        }
    },
    methods: {
        textTruncate(str, length = 50, ending = '...') {
            if (str.length > length) {
                return str.substring(0, length - ending.length) + ending;
            } else {
                return str;
            }
        },
        randomDataGenerate() {
            if (this.data.length === 0) {
                let index = Math.floor(Math.random() * this.noNotificationData.length);
                if (this.$store.state.theme.darkMode) {
                    this.noNotificationImg = AppFunction.getAppUrl(this.noNotificationData[index].img + '-Dark.png');
                } else {
                    this.noNotificationImg = AppFunction.getAppUrl(this.noNotificationData[index].img + '-Light.png');
                }
                this.noNotificationTitle = this.noNotificationData[index].title;
            }else {
                this.initiateScrollEvent();
            }
        },
        markAsRead() {
            this.loading = true;
            axiosPost(`${NOTIFICATIONS}/mark-all-as-read`, {}).then(({data}) => {
                this.$toastr.s(data.message);
                this.$store.dispatch('setNotificationEmpty');
            }).finally(() => {
                this.loading = false;
            })
        },
        initiateScrollEvent(){
            // Detect when scrolled to bottom.
            const listElm = document.querySelector('#infinite-list');
            listElm.addEventListener('scroll', e => {
                if(listElm.scrollTop + listElm.clientHeight >= listElm.scrollHeight) {
                    if (!this.preloader){
                        this.$emit('loadNotifications');
                    }
                }
            });
        },
    },
    computed: {
        preloader(){
            return this.$store.state.loading;
        },
        unreadNotificationUrl() {
            return `${this.allNotificationUrl}?unread=1`
        }
    },

}
</script>
