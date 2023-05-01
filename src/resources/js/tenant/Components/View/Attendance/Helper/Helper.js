import {flatObjectWithKey} from "../../../../../common/Helper/ObjectHelper";
import Vue from "vue";
import {collection} from "../../../../../common/Helper/helpers";

export const filterLastPendingData = (data)=> {
    if(!data){
        return {};
    }

    let pendingData =  data.length > 1 ?
        collection(
            data.filter(value => value.status.name === 'status_pending')
        ).first() :
        flatObjectWithKey(data, 0);

    return Object.keys(pendingData).length > 0 ? pendingData : collection(
            data.filter(value => value.status.name === 'status_reject')
        ).first()
};


export const attendanceBehavior = (behavior) => {
    const classes = {
        early: `badge-warning`,
        late: `badge-danger`,
        regular: `badge-success`
    };

    return `<span class="badge badge-pill ${classes[behavior]}">${Vue.prototype.$t(behavior)}</span>`;
}