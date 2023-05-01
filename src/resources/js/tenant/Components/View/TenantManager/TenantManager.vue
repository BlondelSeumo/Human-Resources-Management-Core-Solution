<template>
    <div class="dropdown dropdown-with-animation d-inline-block btn-dropdown btn-tenant-dropdown">
        <button type="button"
                v-if="tenants.length || isAppAdmin"
                class="btn"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
            <span class="d-none d-md-inline-block mr-2">{{ currentTenant.name }}</span>
            <app-icon name="chevron-down" class="size-15 primary-text-color"/>
        </button>
        <div class="dropdown-menu p-0">
            <a class="dropdown-item dropdown-title py-4"
               v-if="$can('view_tenants')"
               :href="urlGenerator(apiUrl.TENANTS_FRONT_END)">
                <app-icon name="arrow-left" class="primary-text-color size-18 mr-2"/>
                {{ $t('back_to_multi_tenant') }}
            </a>
            <div class="dropdown-divider" v-if="tenants.length"/>
            <div class="tenant-items" v-if="tenants.length">
                <app-pre-loader v-if="loading" />
                <a class="dropdown-item py-4 d-flex align-items-center"
                   v-else
                   v-for="tenant in tenants"
                   href="#"
                   @click="handleTenantRedirection(tenant)">
                    <img :src="getLogoUrl(tenant)"
                         class="rounded-circle mr-3"
                         width="20"
                         alt="Tenant name">
                    <span>{{ tenant.name }}</span>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import {axiosGet, axiosPost, urlGenerator} from '../../../../common/Helper/AxiosHelper';
import AppFunction from "../../../../core/helpers/app/AppFunction";
import {TENANT_REDIRECTION_URL, UPDATE_USER_TENANT_LAST_LOGIN} from "../../../Config/ApiUrl";
import Permission from "../../../../common/Helper/Permission";

export default {
    name: "TenantManager",
    data() {
        return {
            tenants:{},
            loading: false,
            urlGenerator
        }
    },
    created() {
        this.getTenants();
    },
    methods: {
        getTenants(){
            axiosGet(this.apiUrl.TENANTS).then(res => this.tenants = res.data.filter(tenant => parseInt(tenant.id) !== parseInt(window.tenant.id)))
        },
        getLogoUrl(tenant) {
            const logo = tenant.logo ? tenant.logo.value : 'images/default-tenant-image.png';
            return `${AppFunction.getBaseUrl()}/${logo.split('/').filter(d => d).join('/')}`;
        },
        handleTenantRedirection(tenant) {
            this.loading = true;
            axiosPost(UPDATE_USER_TENANT_LAST_LOGIN, {tenant_id: tenant.id})
                .then(response => {
                    window.location = urlGenerator(`${TENANT_REDIRECTION_URL}/${tenant.short_name}`)
                }).catch(({response}) => {
                if (parseInt(response.status) === 422) {
                    this.$toastr.e(this.$errorMessage(response.data.errors, 'tenant_id'));
                }else {
                    this.$toastr.e(response.data.message);
                }
            }).finally(() => {
                this.loading = false;
            })
        }
    },
    computed: {
        isAppAdmin() {
            return (new Permission()).isAppAdmin()
        }
    }
}
</script>
