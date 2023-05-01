import {axiosGet} from "../../../../common/Helper/AxiosHelper";
import { APP_GENERAL_SETTINGS } from '../../../../common/Config/apiUrl'
import {formatted_date, formatted_time} from "../../../../common/Helper/Support/DateTimeHelper";

const state = {
    settings: {},
    darkMode: false,
    dateFormat: formatted_date(),
    timeFormat: parseInt(formatted_time()),
    ...window.settings
};

const actions = {
    getSettings({commit, state}) {
        axiosGet(APP_GENERAL_SETTINGS).then(({ data }) => {
            commit('APP_GENERAL_SETTINGS_LIST', data)
        });
    }
};

const mutations = {
    APP_GENERAL_SETTINGS_LIST(state, data) {
        state.settings = data;
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
