import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {SELECTABLE_EMPLOYMENT_STATUS} from "../../../tenant/Config/ApiUrl";

const state = {
    selectable: []
}

const actions = {
    getSelectableEmploymentStatuses({commit, state}) {
        if (!Object.keys(state.selectable).length) {
            axiosGet(SELECTABLE_EMPLOYMENT_STATUS).then(({data}) => {
                commit('EMPLOYMENT_STATUS_LIST', data)
            });
        }
    }
}

const mutations = {
    EMPLOYMENT_STATUS_LIST(state, data) {
        state.selectable = data;
    }
}

export default {
    state,
    actions,
    mutations
}
