import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {SELECTABLE_DESIGNATION} from "../../../tenant/Config/ApiUrl";

const state = {
    selectable: []
}

const actions = {
    getSelectableDesignations({commit, state}) {
        if (!Object.keys(state.selectable).length) {
            axiosGet(SELECTABLE_DESIGNATION).then(({data}) => {
                commit('DESIGNATION_LIST', data)
            });
        }
    }
}

const mutations = {
    DESIGNATION_LIST(state, data) {
        state.selectable = data;
    }
}

export default {
    state,
    actions,
    mutations
}
