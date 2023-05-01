import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {CUSTOM_FIELDS, CUSTOM_FIELDS_TYPES} from "../../../../../common/Config/apiUrl";
import {collection} from "../../../../../common/Helper/helpers";
import Vue from 'vue'

const state = {
    custom_fields: [],
    custom_field_types: [],
};

const actions = {
    getCustomFields({commit}) {
        axiosGet(CUSTOM_FIELDS).then(({data}) => {
            commit('CUSTOM_FIELDS', data)
        })
    },
    getCustomFieldTypes({commit}) {
        axiosGet(CUSTOM_FIELDS_TYPES).then(({data}) => {
            commit('CUSTOM_FIELD_TYPES', data);
        })
    }
};

const mutations = {
    CUSTOM_FIELDS(state, data) {
        state.custom_fields = data;
    },
    CUSTOM_FIELD_TYPES(state, data) {
        state.custom_field_types = Vue.prototype.collection(data).shaper();
    }
};

const getters = {
    getCustomField: state => id => {
        return collection(state.custom_fields.data).find(id)
    }
};


export default {
    state,
    getters,
    actions,
    mutations
}
