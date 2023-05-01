import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {SELECTABLE_WORKING_SHIFT} from "../../../tenant/Config/ApiUrl";

const state = {
    selectable: []
}

const actions = {
    getSelectableWorkingShifts({commit, state}) {
        if (!Object.keys(state.selectable).length) {
            axiosGet(SELECTABLE_WORKING_SHIFT).then(({data}) => {
                commit('WORKING_SHIFT_LIST', data)
            });
        }
    }
}

const mutations = {
    WORKING_SHIFT_LIST(state, data) {
        state.selectable = data;
    }
}

export default {
    state,
    actions,
    mutations
}
