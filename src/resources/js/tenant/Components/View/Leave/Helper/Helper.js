import moment from "moment";
import Vue from 'vue';
import {
    serverDateFormat,
    differentInTime,
    convertSecondToHourMinutes, onlyTime, calenderTime
} from "../../../../../common/Helper/Support/DateTimeHelper";

export const leaveDurations = (row)=> {
    if(!row){
        return {};
    }

    if (row.duration_type === 'multi_day') {
        let totalDays = moment(row.end_at, serverDateFormat)
            .startOf('day').diff(moment(row.start_at, serverDateFormat)
                .startOf('day'), 'days') + 1;
        let offDays = row.leave_days ? totalDays - Number(row.leave_days) : false;

        return `<div class='width-150'>${totalDays} ${Vue.prototype.$t('days')} <br>
                   ${offDays ? `<span class='text-muted'>${Vue.prototype.$t(
            offDays > 1 ? 'includes_off_days' : 'includes_off_day', {
                count: offDays
            })}</span>` : ''}</div>`
    }

    if (row.duration_type === 'single_day') {
        return `1 ${Vue.prototype.$t('day')}`;
    }

    if (row.duration_type === 'hours') {
        return `${convertSecondToHourMinutes(differentInTime(row.start_at, row.end_at, true).asSeconds())} ${Vue.prototype.$t('hours')}`
    }

    return Vue.prototype.$t(row.duration_type);
};

export const leaveDateAndTimeFormat = (value) => {
    if (value) {
        return `${onlyTime(value)}, ${calenderTime(value, false)}`
    }
    return null;
}

export const leaveRequestStatusBuild = (departments, lastReview) => {
    if (Object.keys(departments).length < 1 || Object.keys(lastReview).length < 1){
        return '';
    }

    if (lastReview[0].department?.manager_id == window.user.id){
        return {
            class: 'light',
            translated_name: 'bypassed'
        }
    }

    if (departments.includes(lastReview[0].department_id)){
        return '';
    }

    return {
        class: 'secondary',
        translated_name: 'reviewed'
    }

}

export const canUpdate = (departments, row) => {
    if (Object.keys(departments).length < 1 || Object.keys(row.last_review).length < 1){
        return true;
    }

    if (row.user_id == window.user.id){
        return false
    }

    return departments.includes(parseInt(row.last_review[0]?.department_id))
}