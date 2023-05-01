import {NOTIFICATION_CHANNELS, NOTIFICATION_EVENTS} from "../../../../common/Config/apiUrl";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";

const state = {
    notification_event: {},
    notification_channels: []
};

const actions = {
    getNotificationEvent({commit, dispatch}, payload) {
        commit('SET_LOADER', true);
        axiosGet(`${NOTIFICATION_EVENTS}/${payload}`).then(res => {
            commit('SET_NOTIFICATION_EVENT', res.data)
            commit('SET_LOADER', false);
            dispatch('getNotificationChannels')
        });
    },
    getNotificationChannels({commit, state}){
        if (!state.notification_channels.length){
            axiosGet(NOTIFICATION_CHANNELS).then(({data}) => {
                commit('NOTIFICATION_CHANNELS', data);
            });
        }
    },
};

const mutations = {
    SET_NOTIFICATION_EVENT(state, data) {
        state.notification_event = data;
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
