import moment from "moment";
import Vue from "vue";

export const payrunTitleByDateRange = (dateRange)=> {
    if (typeof dateRange != 'object' || Object.keys(dateRange).length !== 2)
        return '';
    let startDate = moment(dateRange[0]), endDate = moment(dateRange[1])

    if (moment(startDate).isSame(endDate, 'month')){
       return `${moment(startDate).format('DD')} - ${moment(endDate).format('DD MMM, YY')}`
    }

    if (moment(startDate).isSame(endDate, 'year')){
        return `${moment(startDate).format('DD MMM')} - ${moment(endDate).format('DD MMM, YY')}`
    }

    return `${moment(startDate).format('DD MMM, YY')} - ${moment(endDate).format('DD MMM, YY')}`
}

export const makePayrunRanges = (type) => {
    let previousStartDate = type === 'weekly' ?
        moment().subtract(1, 'weeks').startOf('week').format('YYYY-MM-DD') :
        moment().subtract(1, 'months').startOf('month').format('YYYY-MM-DD')

    let previousEndDate = type === 'weekly' ?
        moment().subtract(1, 'weeks').endOf('week').format('YYYY-MM-DD') :
        moment().subtract(1, 'months').endOf('month').format('YYYY-MM-DD')

    return [previousStartDate, previousEndDate];
}

export const getConsiderOvertimeTitle = (consider_overtime) => {
    if (typeof consider_overtime === 'boolean'){
        return Vue.prototype.$t(consider_overtime ? 'include' : 'exclude')
    }

    if (parseInt(consider_overtime) === 1){
        return Vue.prototype.$t('include')
    }

    if (parseInt(consider_overtime) === 0){
        return Vue.prototype.$t('exclude')
    }

    return Vue.prototype.$t(consider_overtime);
}