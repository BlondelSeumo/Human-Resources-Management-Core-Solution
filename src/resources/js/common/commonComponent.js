import Vue from 'vue'


Vue.component('app-top-navigation-bar', require('./Components/Layout/TopBar').default)

//Support
Vue.component('modal', require('./Components/Helper/Modal').default)
Vue.component('app-page-top-section', require('./Components/Helper/PageTopSection').default)
Vue.component('app-default-button', require('./Components/Helper/Button/DefaultButton').default)
Vue.component('app-submit-button', require('./Components/Helper/Button/SubmitButton').default)
Vue.component('app-cancel-button', require('./Components/Helper/Button/CancelButton').default)
Vue.component('app-context-button', require('./Components/Helper/Button/ContextButton').default)
Vue.component('app-message', require('./Components/Helper/Message/Message').default)
Vue.component('app-form-group', require('./Components/Helper/FormGroup').default)
Vue.component('app-custom-field-builder', require('./Components/Helper/CustomFieldBuilder').default)
Vue.component('app-list-dropdown', require('./Components/Helper/ListDropdown').default)
Vue.component('app-custom-notification-dropdown', require('./Components/Layout/Notification/AppNotificationDropdown').default)

//Users
Vue.component('app-users-roles', require('./Components/UserRole/Index').default);
Vue.component('app-roles-modal', require('./Components/UserRole/UI/RolesModal').default);
Vue.component('app-user-modal', require('./Components/UserRole/UI/UserModal').default);
Vue.component('image-group', require('./Components/UserRole/UI/ImageGroup').default);
Vue.component('app-user-invite-modal', require('./Components/UserRole/UI/UserInviteModal').default);
Vue.component('app-user-invite-confirm', require('./Components/Auth/UserInvitationConfirm').default)
Vue.component('app-user-media', require('./Components/UserRole/UI/UserMedia').default)
Vue.component('app-notifications', require('./Components/Auth/Notifications').default);

//Profile
Vue.component('app-user-profile', require('./Components/Auth/Profile').default)
Vue.component('app-profile-personal-info', require('./Components/Auth/Profile/ProfilePersonalInfo').default);
Vue.component('app-password-change', require('./Components/Auth/Profile/PasswordChange').default)
Vue.component('app-activity', require('./Components/Auth/Profile/Activity').default)

//Settings
Vue.component('app-settings-layout', require('./Components/Settings/Global/App/SettingLayout').default);
Vue.component('app-general-settings', require('./Components/Settings/Global/App/Component/GeneralSetting').default)
Vue.component('app-delivery-settings', require('./Components/Settings/Common/DeliverySettings').default)
Vue.component('app-test-mail-modal', require('./Components/Settings/Common/Delivery/TestMailModal').default)
Vue.component('app-mailgun', require('./Components/Settings/Common/Delivery/Mailgun').default)
Vue.component('app-ses', require('./Components/Settings/Common/Delivery/SES').default)
Vue.component('app-smtp', require('./Components/Settings/Common/Delivery/SMTP').default)
Vue.component('app-mandrill', require('./Components/Settings/Common/Delivery/Mandrill').default)
Vue.component('app-postmark', require('./Components/Settings/Common/Delivery/Postmark').default)
Vue.component('app-sparkpost', require('./Components/Settings/Common/Delivery/Sparkpost').default)
Vue.component('app-sendmail', require('./Components/Settings/Common/Delivery/Sendmail').default)
Vue.component('app-notification-settings', require('./Components/Settings/Common/NotificationSettings').default)
Vue.component('app-update-notification-settings', require('./Components/Settings/Common/http/StoreUpdateNotificationSettings').default)
Vue.component('app-update-notification-templates', require('./Components/Settings/Common/http/StoreUpdateNotificationTemplate').default)
Vue.component('app-mail-template', require('./Components/Settings/Common/http/NotificationTemplate/MailTemplate').default)
Vue.component('app-database-template', require('./Components/Settings/Common/http/NotificationTemplate/DatabaseTemplate').default)

//Tenant settings
Vue.component('tenant-settings-layout', require('./Components/Settings/Global/Tenant/TenantSettingLayout').default);
Vue.component('tenant-custom-field-settings', require('./Components/Settings/Global/Tenant/Component/CustomField/CustomFields').default);
Vue.component('tenant-store-update-custom-fields', require('./Components/Settings/Global/Tenant/Component/CustomField/StoreUpdateCustomFields').default);

//Authentication
Vue.component('app-auth-login', require('./Components/Auth/Login').default);

