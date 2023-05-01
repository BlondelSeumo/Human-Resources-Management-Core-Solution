export default class Permission {
    getPermissions() {
        return window.localStorage.getItem('permissions');
    }

    can(ability) {
        if (this.permissions().is_tenant_admin && ability === 'view_brands') {
            return false;
        }

        if (this.permissions().is_app_admin || this.permissions().is_tenant_admin) {
            return true
        }
        return this.permissions()[ability];
    }
    
    isAppAdmin() {
        return this.permissions().is_app_admin
    }

    isAdmin(){
        return !!this.permissions().is_app_admin || !!this.permissions().is_tenant_admin;
    }

    isOnlyDepartmentManager(){
        if (this.permissions()['access_all_departments']){
            return false;
        }

        return this.permissions()['access_own_departments'] || false
    }

    permissions() {
        return JSON.parse(this.getPermissions());
    }
}
