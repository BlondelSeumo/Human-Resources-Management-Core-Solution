<template>
    <li class="nav-item dropdown">
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
            <h6 class="p-primary mb-0 primary-text-color">
                <a :href="allNotificationUrl">{{ $t('all_notifications') }}</a>
                <span class="badge badge-primary badge-sm badge-circle float-right">
                    {{ data.length }}
                </span>
            </h6>
            <div class="dropdown-divider"/>
            <div class="dropdown-items-wrapper custom-scrollbar">
                <a class="dropdown-item"
                   v-for="(item, index) in data"
                   :href="item.url"
                   :key="index"
                   @click.prevent="$emit('clicked', item)">
                    <div class="media align-items-center">
                        <div class="avatars-w-50 mr-3">
                            <app-avatar
                                :shadow="false"
                                :img="item.img"
                                :title="item.name"
                            />
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
            </div>
        </div>
        <div v-else
             class="dropdown-menu dropdown-menu-right navbar-dropdown notification-dropdown no-notification-dropdown p-primary"
             aria-labelledby="notificationDropdown">
            <div class="d-flex justify-content-center">
                <img
                    :src="noNotificationImg"
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
import AppFunction from "../../../helpers/app/AppFunction";

export default {
    name: "NotificationDropdown",
    props: {
        data: {
            type: Array,
            required: true
        },
        allNotificationUrl: {
            type: String,
            require: true
        },
        showIdentifier: {}
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
            }
        }
    }
}
</script>
