import {NOTIFICATION_EVENTS, TENANT_NOTIFICATION_EVENT} from "../../../../../../common/Config/apiUrl";
import {onlyMailEvent} from "../../../../../../common/Components/Settings/Common/http/NotificationTemplate/Helper";

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

                    },
                    (!this.props.specific && this.props.alias === 'tenant')?
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
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
                    }

                ],
                filters: [

                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 30,
                showAction: true,
                actionType: "default",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'settings',
                        type: 'modal',
                        actionType: 'edit',
                        modifier: (row) => {
                            return !onlyMailEvent.includes(row.name);
                        }
                    }
                ],
            }
        }
    }
}
