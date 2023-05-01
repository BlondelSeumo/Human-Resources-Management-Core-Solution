import { axiosGet } from '../../../common/Helper/AxiosHelper'
import {
    LANGUAGES,
    CONFIG,
    STATUSES
} from '../../../common/Config/apiUrl';
import {configFormatter} from '../../../common/Helper/Support/SettingsHelper'
import Vue from 'vue'

const state = {
    languages: [],
    configs: {},
    notification_channels: [],
    notification_events: [],
    statuses: []
};

const getters = {
    getFormattedConfig: state => payload =>  {
        if (Object.keys(state.configs).length) {
            return state.configs[payload].map(format => configFormatter(format))
        }
        return [];
    },

    getNotificationEvent: state => payload => {
        if (state.notification_events.length) {
            return state.notification_events.find(nEvent => nEvent.id === Number(payload))
        }
        return {};
    },

    getSpecificContextConfig: state => payload => {
        return state.configs[payload];
    },

};

const actions = {
    getLanguages({commit, state}) {
        if (!state.languages.length) {
            axiosGet(LANGUAGES).then(({data}) => {
                commit('LANGUAGE_LIST', data)
            });
        }
    },
    getConfig({ commit, state }) {
        if (!state.configs.length) {
            axiosGet(CONFIG).then(({data}) => {
                commit('CONFIG_LIST', data)
            });
        }
    },
    getStatuses({commit}, type) {
        axiosGet(`${STATUSES}?type=${type}`).then(res => {
            commit('SET_STATUSES', res.data)
        })
    }
};

const mutations = {
    LANGUAGE_LIST(state, data) {
        state.languages = data.map((lang) => {
            return {
                ...lang,
                id: lang.key,
                value: lang.title
            }
        });
    },
    CONFIG_LIST(state, data) {
        state.configs = data;
    },
    NOTIFICATION_EVENTS(state, data){
        state.notification_events = data.data;
    },
    NOTIFICATION_CHANNELS(state, data){
        state.notification_channels = data.map(channel => {
            return {
                ...channel,
                name: Vue.prototype.$t(channel.name),
                id: channel.name
            }
        });
    },
    SET_STATUSES(state, data) {
        state.statuses = Vue.prototype.collection(data)
            .shaper();
    }
};


export default {
    state,
    getters,
    actions,
    mutations
}
