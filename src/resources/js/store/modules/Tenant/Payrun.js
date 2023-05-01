const state = {
    payrunData: {},
    payrunErrors: {}
}

const actions = {
    setPayrunData({commit, state}, data) {
        commit('SET_PAYRUN_DATA', data)
    },
    setErrors({commit, state}, data) {
        commit('SET_PAYRUN_ERRORS', data)
    },

    clearPayrunData({commit, state}){
        commit('CLEAR_PAYRUN_DATA')
    }
}
const getters = {
    getErrors: state => {
        return state.payrunErrors
    }
}

const mutations = {
    SET_PAYRUN_DATA(state, data) {
        state.payrunData = {
            ...state.payrunData,
            ...data
        };
    },
    SET_PAYRUN_ERRORS(state, data) {
        state.payrunErrors = data;
    },

    CLEAR_PAYRUN_DATA(state){
        state.payrunData = {};
        state.payrunErrors = {}
    }
}

export default {
    state,
    actions,
    getters,
    mutations
}
