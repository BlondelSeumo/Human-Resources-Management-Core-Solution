import Vue from 'vue'
import {axiosGet, urlGenerator} from "../../../../common/Helper/AxiosHelper";
import {NOTIFICATION_EVENT_SETTINGS, NOTIFICATIONS} from '../../../../common/Config/apiUrl'
import {calenderTime, onlyTime} from '../../../../common/Helper/Support/DateTimeHelper'
import optional from '../../../../common/Helper/Support/Optional'
import axios from 'axios'

const state = {
    notification_setting: {},
    notification_settings: [],
    notifications: {}
};

const actions = {
    getNotificationSettingWithEvent({commit, state}, payload){
        axiosGet(`${NOTIFICATION_EVENT_SETTINGS}${payload}`).then(({data}) => {
            commit('NOTIFICATION_EVENT_SETTING', data);
        })
    },

};

const mutations = {
    NOTIFICATION_EVENT_SETTING(state, data) {
        state.notification_setting = data;
    },
    NOTIFICATIONS(state, data){
        if (!Object.keys(state.notifications).length){
            state.notifications = data;
        }else {
            state.notifications.next_page_url = data.next_page_url;
            state.notifications.data = state.notifications.data.concat(data.data);
        }
    }
};

const getters = {


};


export default {
    state,
    getters,
    actions,
    mutations
}
