import {NOTIFICATION_EVENTS, TENANT_NOTIFICATION_EVENT} from "../../Config/apiUrl";
import {onlyMailEvent} from "../../Components/Settings/Common/http/NotificationTemplate/Helper";

export default {
    data() {
        return {
            options: {
                name: this.$fieldTitle('notification', 'template'),
                url: this.props.specific ? TENANT_NOTIFICATION_EVENT :`${NOTIFICATION_EVENTS}?type=${this.props.alias}`,
                showHeader: true,
                tableShadow: false,
                showManageColumn: false,
                tablePaddingClass: 'px-0 py-primary',
                columns: [
                    {
                        title: this.$fieldTitle('event', 'name'),
                        type: 'text',
                        key: 'translated_name',
                        uniqueKey: 'id',
                    },
                    (this.props.alias === 'app' || this.props.specific) ?
                        {
                            title: this.$fieldTitle('notification', 'channel'),
                            type: 'custom-html',
                            key: 'settings',
                            isVisible: true,
                            modifier: (settings, row) =>  {
                                if (onlyMailEvent.includes(row.name)) {
                                    return `<span class="badge badge-pill badge-success">${this.$t('mail')}</span>`;
                                }
                                if (!settings)
                                    return '';
                                return settings.notify_by.map(type => {
                                    return `<span class="badge badge-pill ${type === 'database' ? 'badge-primary' : 'badge-success'}">${this.$t(type)}</span>`
                                }).join(' ')
                            }

                        } : {},
                    !this.props.specific ?
                        {
                            title: this.$t('templates'),
                            type: 'button',
                            key: 'id',
                            className: 'btn btn-sm btn-primary py-1',
                            icon: 'trello',
                            actionType: 'manage',
                            modifier: (id) =>  this.$t('update')
                        }
                        : {},
                    (this.props.alias === 'app' || this.props.specific) ?
                        {
                            title: this.$t('actions'),
                            type: 'action',
                            key: 'invoice',
                            isVisible: true
                        } : {},

                ],
                filters: [

                ],
                paginationType: "loadMore",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                actionType: "default",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'settings',
                        type: 'modal',
                        actionType: 'edit',
                        modifier: (row) => {
                            return !['user_invitation', 'password-reset'].includes(row.name);
                        }
                    }
                ],
            }
        }
    }
}
