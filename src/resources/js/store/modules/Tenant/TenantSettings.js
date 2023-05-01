import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {TENANT_GENERAL_SETTINGS} from "../../../common/Config/apiUrl";

const state = {
    tenantSettings: {}
}

const actions = {
    getTenantSettings({commit, state}) {
        axiosGet(TENANT_GENERAL_SETTINGS).then(({data}) => {
            commit('TENANT_GENERAL_SETTINGS_LIST', data)
        });
    }
};

const mutations = {
    TENANT_GENERAL_SETTINGS_LIST(state, data) {
        state.tenantSettings = data;
    }
};

const getters = {
    settings: state => state.tenantSettings
};

export default {
    state,
    getters,
    actions,
    mutations
}

