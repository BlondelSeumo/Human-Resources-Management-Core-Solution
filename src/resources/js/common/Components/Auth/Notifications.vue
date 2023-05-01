<template>
    <div class="content-wrapper">
        <app-page-top-section
            :title="$allLabel('notifications')"
            icon="bell"
            :directory="$t('notifications')"
        />
        <app-table
            id="segment-table"
            :options="options"
            @action="triggerActions"
        />
    </div>
</template>

<script>
import {NOTIFICATIONS} from "../../Config/apiUrl";
import {formatDateToLocal, onlyTime} from "../../Helper/Support/DateTimeHelper";
import optional from '../../Helper/Support/Optional'
import {axiosPost} from "../../Helper/AxiosHelper";

export default {
    name: "Notifications",
    props:['unread'],
    data() {
        return {
            options: {
                name: this.$t('notifications'),
                url: NOTIFICATIONS,
                showHeader: true,
                columns: [
                    {
                        title: this.$t('title'),
                        type: 'button',
                        key: 'data',
                        className: 'btn btn-sm btn-link',
                        actionType: 'read',
                        modifier: data => data.message.replace(/<[^>]*>/g, '')
                    },
                    {
                        title: this.$t('notification_form'),
                        type: 'object',
                        key: 'notifier',
                        uniqueKey: 'id',
                        modifier: function (notifier, rowData) {
                            return optional(notifier, 'full_name');
                        }
                    },
                    {
                        title: this.$t('date'),
                        type: 'object',
                        key: 'created_at',
                        uniqueKey: 'id',
                        modifier: function (created_at, rowData) {
                            return formatDateToLocal(created_at)
                        }
                    },
                    {
                        title: this.$t('time'),
                        type: 'object',
                        key: 'created_at',
                        uniqueKey: 'id',
                        modifier: function (created_at, rowData) {
                            return onlyTime(created_at)
                        }
                    },
                ],
                filters: [
                    {
                        "title": this.$t('created'),
                        "type": "range-picker",
                        "key": "date",
                        "option": ["today", "thisMonth", "last7Days", "thisYear"]
                    }
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: false,
                actionType: 'default',
                actions: [
                    {
                        title: this.$t('copy'),
                        icon: 'copy',
                        type: 'page',
                        url: `/`,
                        name: 'copy'
                    }
                ],
            }
        }
    },
    methods: {
        triggerActions(row, action, active) {
            this.readNotification(row);
        },
        readNotification(notification) {
            axiosPost(`admin/user/notifications/mark-as-read/${notification.id}`).then(({data}) => {
                if (data.data.url) {
                    window.location = data.data.url;
                }
            });
        },
    },
    mounted() {
        this.unread ? this.options.url = `${NOTIFICATIONS}?unread=1` : null;
    }
}
</script>

<style scoped>

</style>
