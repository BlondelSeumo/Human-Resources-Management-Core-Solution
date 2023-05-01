import { axiosGet } from '../../../common/Helper/AxiosHelper'
import {ROLES, TENANT_SELECTABLE_ROLES} from '../../../common/Config/apiUrl'
import optional from "../../../common/Helper/Support/Optional";

const state = {
    roles: []
};

const actions = {
    getRoles({commit, state}, payload) {
        commit('SET_LOADER', true)
        const url = payload.alias === 'tenant' ? TENANT_SELECTABLE_ROLES : ROLES;
        axiosGet(url).then(({data}) => {
            commit('SET_LOADER', false)
            commit('SET_ROLES', data)
        })
    },
    getAllRoles({commit, state}, tenant = null) {
        commit('SET_LOADER', true)
        axiosGet(`${TENANT_SELECTABLE_ROLES}${tenant ? '?tenant_id='+tenant : ''}`).then(({data}) => {
            commit('SET_LOADER', false)
            commit('SET_ROLES', data)
        })
    }
};

const mutations = {
    SET_ROLES(state, data) {
        state.roles = data;
    }
};

const getters = {
    getFormattedRoles: state => {
        if (state.roles) {
            return state.roles.map(role => {
                if (role.brand_id) {
                    role.value = `${role.name}(${optional(role, 'tenant', 'name')})`;
                }else {
                    role.value = role.name;
                }
                return role;
            });
        }
    }
};


export default {
    state,
    getters,
    actions,
    mutations
}
