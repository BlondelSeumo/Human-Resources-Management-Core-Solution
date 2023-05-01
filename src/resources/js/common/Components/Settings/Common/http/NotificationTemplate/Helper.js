import Vue from "vue";

export const onlyMailEvent = [
    'user_invitation',
    'user_create',
    'password_reset',
    'tenantInvitation_canceled',
    'tenant_invitation',
    'user_invitation_canceled',
    'employee_invitation',
    'employee_invitation_canceled',
    'employee_terminated',
    'employee_salary_increment',
    'employee_payslip_generate',
    'employee_password_reset',
];

export const noAudienceEvent = [
    'attendance_requested',
    'attendance_rejected',
    'attendance_approved',
    'attendance_canceled',
    'leave_requested',
    'leave_rejected',
    'leave_approved',
    'leave_canceled',
    'leave_bypassed',
    'leave_assigned',
    'employee_salary_increment',
    'employee_payslip_generate',
    'employee_password_reset',
];

export const mailTag = () => {
    if (window.tenant && window.tenant.is_single) {
        return {
            '{company_name}': Vue.prototype.$t('company_name'),
            '{company_logo}': Vue.prototype.$t('company_logo'),
            ...mailCommonTag
        }
    }

    if (window.tenant && window.tenant.id) {
        return {
            '{tenant_name}': Vue.prototype.$t('tenant_name'),
            '{tenant_logo}': Vue.prototype.$t('tenant_logo'),
            ...mailCommonTag
        }
    }

    return  {
        '{app_name}': Vue.prototype.$t('Name of the app'),
        '{app_logo}': Vue.prototype.$t('Logo of the app'),
        ...mailCommonTag
    }

}

export const databaseTemplate = () => {
    if (window.tenant && window.tenant.is_single) {
        return {
            ...databaseCommonTag,
            '{company_name}': Vue.prototype.$t('company_name')
        }
    }

    if (window.tenant && window.tenant.id) {
        return {
            ...databaseCommonTag,
            '{tenant_name}': Vue.prototype.$t('tenant_name')
        }
    }

    return  {
        ...databaseCommonTag,
        '{app_name}': Vue.prototype.$t('Name of the app'),
    }
}

const mailCommonTag = {
    '{name}': Vue.prototype.$t('The resource name of the event'),
    '{action_by}': Vue.prototype.$t('The user who performed the action'),
    '{receiver_name}': Vue.prototype.$t('The user who will receive the notification'),
    '{resource_url}': Vue.prototype.$t('Page link of resource'),
    '{invitation_url}': Vue.prototype.$t('Invitation url for the user'),
    '{reset_password_url}': Vue.prototype.$t('Reset password url of user'),
}

const databaseCommonTag = {
    '{name}': Vue.prototype.$t('The resource name of the event'),
    '{app_name}': Vue.prototype.$t('Name of the app'),
    '{action_by}': Vue.prototype.$t('The user who performed the action'),
}
