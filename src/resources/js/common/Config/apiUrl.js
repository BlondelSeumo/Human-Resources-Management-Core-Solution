import {TENANT_BASE_URL} from './UrlHelper'

export const LANGUAGES = 'admin/languages';
export const CONFIG = 'app/settings-format';
export const STATUSES = 'admin/app/statuses';
export const NOTIFICATIONS = 'admin/user/notifications';
export const APP_USERS = 'admin/auth/users'
export const APP_ROLES = 'admin/auth/roles';
export const USERS = 'admin/users';
export const ROLES = 'admin/roles';
export const DETACH_ROLES = 'admin/auth/users/detach-roles'
export const PERMISSIONS = 'admin/auth/permissions';
export const GENERATE_SHORT_NAMES = 'admin/tenant/generate-short-names';
export const MAIL_SETTINGS_LIST = 'admin/app/settings/delivery-settings';
export const APP_GENERAL_SETTINGS = 'admin/app/settings';
export const NOTIFICATION_EVENT_SETTINGS = 'admin/notification-events/settings/';
export const NOTIFICATION_EVENTS = 'admin/app/notification-events';
export const NOTIFICATION_SETTINGS = 'admin/app/notification-settings';
export const NOTIFICATION_SETTING = 'admin/app/notification-settings/';
export const NOTIFICATION_CHANNELS = 'admin/app/notification-channels';
export const MAIL_CHECK_URL = 'app/check-mail-settings';
export const DELIVERY_SETTINGS_FRONT_END = `admin/settings?tab=delivery`;
export const CUSTOM_FIELDS = 'admin/app/custom-fields';
export const CUSTOM_FIELDS_TYPES = 'admin/app/custom-field-types';
export const NOTIFICATIONS_FRONT_END = '/admin/settings?tab=Notification';
export const CRON_JOB_SETTING = '/admin/app/settings/cronjob';


// Tenant url for simplicity
export const TENANT_SELECTABLE_ROLES = `${TENANT_BASE_URL}selectable/roles`;
export const TENANT_SELECTABLE_USER = `${TENANT_BASE_URL}selectable/users`;
export const TENANT_PERMISSIONS = `${TENANT_BASE_URL}permissions`;
export const TENANT_ROLES = `${TENANT_BASE_URL}roles`;
export const TENANT_USERS = `${TENANT_BASE_URL}users`;
export const TENANT_ATTACH_ROLES = `${TENANT_BASE_URL}users/attach-roles`;
export const TENANT_DETACH_ROLES = `${TENANT_BASE_URL}users/detach-roles`;
export const TENANT_NOTIFICATION_SETTINGS = `${TENANT_BASE_URL}notification/settings`;
export const TENANT_NOTIFICATION_EVENT = `${TENANT_BASE_URL}notification/events`;
export const TENANT_USER_INVITE = `${TENANT_BASE_URL}users/invite-user`;
export const TENANT_DELIVERY_SETTINGS_FRONT_END = `${TENANT_BASE_URL}settings`;
export const TENANT_CUSTOM_FIELDS = `${TENANT_BASE_URL}custom-fields`;
export const TENANT_MAIL_CHECK_URL = `${TENANT_BASE_URL}check-mail-settings`;
export const TENANT_GENERAL_SETTINGS = `${TENANT_BASE_URL}general/settings`;
export const TENANT_NOTIFICATION_SETTINGS_FRONT_END = `${TENANT_BASE_URL}/app/settings?tab=Notification`;
export const TENANT_USER_CANCEL = `${TENANT_BASE_URL}/users/cancel-invitation`;
export const TENANT_EMAIL_SETUP_SETTING = `${TENANT_BASE_URL}app/settings?tab=Email%20setup`;
export const TENANT_SELECTABLE_WORK_SHIFT = `${TENANT_BASE_URL}selectable/working-shifts`;
export const TENANT_SELECTABLE_ROLE_USER = `${TENANT_BASE_URL}selectable/role/users`;
export const TENANT_SELECTABLE_FILTER_ROLES = `${TENANT_BASE_URL}selectable/filter/roles`;
export const TENANT_DEPARTMENTS_URL = `${TENANT_BASE_URL}administration/departments`;

