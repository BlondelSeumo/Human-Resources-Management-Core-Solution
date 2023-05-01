
import Vue from 'vue'
import {ucFirst} from "./Support/TextHelper";

Vue.prototype.$placeholder = (subject, key = null) => {
    return Vue.prototype.$t('place_holder', {
        subject: Vue.prototype.$t(subject).toLowerCase(),
        type: key ? Vue.prototype.$t(key).toLowerCase() : ''
    });
};

Vue.prototype.$fieldTitle = (subject, title = null, titleCapitalize = false, infix = null) => {
    if (title) {
        let translation = Vue.prototype.$t('field_title', {
            subject: Vue.prototype.$t(subject),
            infix: infix ? Vue.prototype.$t(infix).toLowerCase() : '',
            title: titleCapitalize ? Vue.prototype.$t(title) : Vue.prototype.$t(title).toLowerCase()
        })
        return translation.split(' ').filter(a => a).join(' ');
    }
    return Vue.prototype.$t(subject);
};

Vue.prototype.$totalLabel = (name) => {
    return Vue.prototype.$t('total_context', {name: Vue.prototype.$t(name).toLowerCase()})
}
Vue.prototype.$rateLabel = (name) => {
    return Vue.prototype.$t('status_wise_rate', {status: Vue.prototype.$t(name)})
}
Vue.prototype.$allLabel = (name) => {
    return Vue.prototype.$t('all_feature_name', {name: Vue.prototype.$t(name)})
}
Vue.prototype.$addLabel = (name) => {
    return Vue.prototype.$t('add_feature_name', {name: Vue.prototype.$t(name)})
}
Vue.prototype.$editLabel = (name) => {
    return Vue.prototype.$t('edit_feature_name', {name: Vue.prototype.$t(name)})
}
Vue.prototype.$copyLabel = (name) => {
    return Vue.prototype.$t('copy_feature_name', {name: Vue.prototype.$t(name)})
}
Vue.prototype.$fieldLabel = (subject, key = 'name') => {
    return Vue.prototype.$t('field_label', {
        subject: Vue.prototype.$t(subject),
        key: key ? Vue.prototype.$t(key).toLowerCase() : '',
    })
}
Vue.prototype.$textAreaPlaceHolder = (name) => {
    return Vue.prototype.$t('textarea_placeholder', {
        name: Vue.prototype.$t(name).toLowerCase()
    })
}
