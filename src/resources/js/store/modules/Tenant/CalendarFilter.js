import {objectToQueryString} from "../../../common/Helper/Support/TextHelper";

const state = {
    filter: {

    }
}

const getters = {
    getFilterUrls: state => {
        return objectToQueryString(state.filter)
    },
    getFilters: state => {
        return state.filter
    }
}

const actions = {
    updateFilterObject({commit}, payload) {
        commit('SET_FILTER_OPTIONS', payload);
    },
    clearFilterObject({commit}, payload) {
        commit('CLEAR_FILTER_OPTIONS', payload);
    }
}

const mutations = {
    SET_FILTER_OPTIONS(state, data) {
        state.filter = {...state.filter, ...data};
    },
    CLEAR_FILTER_OPTIONS(state, data) {
        state.filter = {};
    }
}

export default {
    state,
    actions,
    getters,
    mutations
}