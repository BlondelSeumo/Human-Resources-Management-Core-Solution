export default {
    methods: {
        getNotificationChannels() {
            return this.notification_channels.filter(channel => {
                if (channel.id === 'database' && ['invite_user', 'password-reset'].includes(this.notification_event.name))
                    return false;
                return true;
            })
        }
    }
}
