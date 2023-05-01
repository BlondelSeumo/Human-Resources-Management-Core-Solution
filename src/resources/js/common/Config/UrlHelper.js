let baseUrl = '';
if (window.hasOwnProperty('tenant')) {
    baseUrl = tenant.is_single ? '' : `t/${tenant.short_name}/`
}

export const TENANT_BASE_URL = baseUrl;
