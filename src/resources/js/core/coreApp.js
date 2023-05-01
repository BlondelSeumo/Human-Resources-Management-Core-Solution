import Vue from 'vue';

import VueToastr from "vue-toastr";

Vue.use(VueToastr, {
    /* OverWrite Plugin Options if you need */
    defaultTimeout: 3000,
    defaultPosition: "toast-top-right",
    defaultClassNames: ["vueToaster"]
    //defaultClassNames: ["animate__animated", "animate__slideInRight", "vueToaster"]
});


/**
 * Components
 *   1. Component name prefix should "app"-component-name
 * */
Vue.component('app-input', require('./components/input/Index').default);
Vue.component('app-pre-loader', require('./components/preloders/Preloader').default);
Vue.component('app-overlay-loader', require('./components/preloders/OverlayLoader').default);
Vue.component('app-modal', require('./components/modal/Modal').default);
Vue.component('app-table', require('./components/datatable/Index').default);
Vue.component('app-load-more', require('./components/buttons/LoadMore').default);
Vue.component('app-pagination', require('./components/buttons/Pagination').default);
Vue.component('app-filter', require('./components/filter/Index').default);
Vue.component('app-search', require('./components/datatable/Search').default);
Vue.component('app-tab-filter', require('./components/filter/TabFilter').default);
Vue.component('app-icon', require('./components/icon/Icon').default);
Vue.component('app-avatar', require('./components/avatars/Avatar').default);
Vue.component('app-avatars-group', require('./components/avatars-group/AvatarsGroup').default);
Vue.component('app-tab', require('./components/tabs/Index').default);
Vue.component('app-form-wizard', require('./components/form-wizard/FormWizard').default);
Vue.component('app-button', require('./components/buttons/AppButton').default);
Vue.component('app-breadcrumb', require('./components/breadcrumb/index').default);
Vue.component('app-badge', require('./components/badge/Badge').default);
Vue.component('app-card-view', require('./components/card-view/index').default);
Vue.component('app-template-preview-card', require('./components/template-preview-card/AppTemplatePreviewCard').default);
Vue.component('app-note', require('./components/badge/Note').default);
Vue.component("app-chart", require('./components/charts/Index').default);
Vue.component("app-dropdown-menu", require('./components/buttons/DropdownMenu').default);
Vue.component("app-navbar", require('./components/layouts/Navbar').default);
Vue.component("app-navbar-language-dropdown", require('./components/layouts/navbar-dropdowns/LanguageDropdown').default);
Vue.component("app-navbar-notification-dropdown", require('./components/layouts/navbar-dropdowns/NotificationDropdown').default);
Vue.component("app-navbar-profile-dropdown", require('./components/layouts/navbar-dropdowns/ProfileDropdown').default);
Vue.component("app-widget", require('./components/dashboard-widget/Index').default);
Vue.component("app-confirmation-modal", require('./components/confirmation-modal/Index').default);
Vue.component("app-empty-data-block", require('./components/datatable/EmptyData').default);
Vue.component("app-media-object", require('./components/media-object/MediaObject').default);
Vue.component("app-error", require('./components/error-page/Error').default);
Vue.component("app-calendar", require('./components/calendar/Calendar').default);
Vue.component("app-filter-with-search", require('./components/filter/FilterWithSearch').default);
Vue.component("app-submit-button", require('./components/buttons/SubmitButton').default);
Vue.component("app-organization-chart", require('./components/organization-chart/OrganizationChart').default);
Vue.component("app-tag-manager", require('./components/tag/TagManager').default);

//Sidebar
Vue.component('sidebar', require('./components/layouts/Sidebar').default);

/**
 * v-calder
 * */
import Calendar from 'v-calendar/lib/components/calendar.umd';
import DatePicker from 'v-calendar/lib/components/date-picker.umd';

Vue.component('v-calendar', Calendar);
Vue.component('v-date-picker', DatePicker);

/**
 * An EventHub to share events between components
 * */
Vue.prototype.$hub = new Vue();

/**
 * cookies
 */

import AppCookie from "./helpers/cookies/AppCookies";
window.AppCookie = AppCookie;

