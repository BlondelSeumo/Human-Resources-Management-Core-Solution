import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {TENANT_SELECTABLE_FILTER_ROLES} from "../../../common/Config/apiUrl";

const state = {
    selectable: []
}

const actions = {
    getSelectableRoles({commit}) {
        axiosGet(TENANT_SELECTABLE_FILTER_ROLES).then(({data}) => {
            commit('ROLE_LIST', data)
        });
    }
}

const mutations = {
    ROLE_LIST(state, data) {
        state.selectable = data;
    }
}

export default {
    state,
    actions,
    mutations
}
