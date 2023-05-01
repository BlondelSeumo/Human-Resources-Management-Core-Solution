import Vue from 'vue'
import vuexI18n from 'vuex-i18n';
import store from '../store/Index'
import './Helper/translation';

Vue.use(vuexI18n.plugin, store)

// add translations directly to the application
let language = JSON.parse(window.localStorage.getItem('app-languages') || '') || {};
let key = window.localStorage.getItem('app-language') || 'en';
Vue.i18n.add(key, language);
// set the start locale to use
Vue.i18n.set(key);
/*------ localization end ------*/