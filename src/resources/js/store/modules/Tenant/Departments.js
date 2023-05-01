import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {SELECTABLE_DEPARTMENT} from "../../../tenant/Config/ApiUrl";

const state = {
    selectable: [],
    called: false
}

const actions = {
    getSelectableDepartments({commit, state}, managerDept = false) {
        if (!Object.keys(state.selectable).length && !state.called){
            commit('SET_CALLED', true);
            axiosGet(`${SELECTABLE_DEPARTMENT}?${ managerDept ? 'manager=yes' : ''}`).then(({data}) => {
                commit('DEPARTMENT_LIST', data)
            });
        }
    }
}

const mutations = {
    DEPARTMENT_LIST(state, data) {
        state.selectable = data;
    },
    SET_CALLED(state, data) {
        state.called = data;
    }
}

export default {
    state,
    actions,
    mutations
}
