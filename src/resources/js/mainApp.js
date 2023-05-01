import './bootstrap';
import './plugins';
import Vue from 'vue';
import './core/coreApp';
import store from './store/Index'
import './common/Translator'
import './common/Helper/helpers'
import './common/commonComponent'
import './tenant/tenantComponent'
import "./tenant/punchAlert";


const app = new Vue({
    store,
    el: '#app',
});
