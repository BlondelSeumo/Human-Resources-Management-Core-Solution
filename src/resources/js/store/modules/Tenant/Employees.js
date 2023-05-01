import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {EMPLOYEES} from "../../../tenant/Config/ApiUrl";

const state =  {
    employee: {},
    socialLinks: [],
    socialLinkLoader: false
}

const getters = {
    getEmployeeDetails: state => {
        return state.employee
    }
};

const actions = {
    getEmployeeDetails({commit, state}, employeeId){
        commit('SET_LOADER', true)
        axiosGet(`${EMPLOYEES}/${employeeId}`).then(({ data }) =>{
            commit('EMPLOYEE_DETAILS_DATA', data)
        }).finally(()=> commit('SET_LOADER', false))
    },

    getEmployeeSocialLinks({commit, state}, employeeId) {
        commit('SET_SOCIAL_LINK_LOADER', true);
        axiosGet(`${EMPLOYEES}/${employeeId}/social-links`).then(({data}) => {
            commit('SET_SOCIAL_LINKS', data);
        }).finally(() => commit('SET_SOCIAL_LINK_LOADER', false));
    }
}

const mutations = {
    EMPLOYEE_DETAILS_DATA(state, data){
        state.employee = data;
    },
    SET_SOCIAL_LINKS(state, data) {
        state.socialLinks = data;
    },
    SET_SOCIAL_LINK_LOADER(state, data) {
        state.socialLinkLoader = data;
    }
}

export default {
    state,
    actions,
    getters,
    mutations
}
