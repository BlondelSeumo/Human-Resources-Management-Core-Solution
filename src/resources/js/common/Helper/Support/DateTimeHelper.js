import moment from "moment-timezone";
import Vue from "vue";
import optional from "./Optional";

const settings = window.settings || {};

moment.locale(window.appLanguage)

export const date_format = () => {
    return {
        'd-m-Y': 'DD-MM-YYYY',
        'm-d-Y': 'MM-DD-YYYY',
        'Y-m-d': 'YYYY-MM-DD',
        'm/d/Y': 'MM/DD/YYYY',
        'd/m/Y': 'DD/MM/YYYY',
        'Y/m/d': 'YYYY/MM/DD',
        'm.d.Y': 'MM.DD.YYYY',
        'd.m.Y': 'DD.MM.YYYY',
        'Y.m.d': 'YYYY.MM.DD',
    };
};

export const localTimeZone = moment.tz.guess();

export const serverDateTimeFormat = 'YYYY-MM-DD H:mm:ss';

export const serverDateFormat = 'YYYY-MM-DD';

export const serverTimeFormat = 'H:mm:ss';

export const formatted_date = () => {
    return date_format()[optional(settings, 'date_format')] || date_format()['d-m-Y'];
};

export const formatted_time = () => {
    return optional(settings, 'time_format') === 'h' ? '12' : '24';
}

export const time_format = () => {
    const format = optional(settings, 'time_format');

    const time_format = settings.time_format || 'H'

    return format === 'h' ? `${settings.time_format}:mm A` : `${time_format}:mm`;
}

export const formatDateToLocal = (date, withTime = false) => {
    if (!date)
        return '';
    const formatString = withTime ? `${formatted_date()} ${time_format()}` : formatted_date();

    return moment.utc(date, withTime ? serverDateTimeFormat : serverDateFormat)
        .local()
        .format(formatString);
};

export const dateTimeToLocalWithFormat = (date = null) => {
    if (!date) {
        return '';
    }

    return moment.utc(date, serverDateTimeFormat)
        .local()
        .format(serverDateTimeFormat)
}

export const timeInterval = (date) => {
    return moment(date).utc(false).fromNow();
};

export const onlyTime = date => {
    return moment.utc(date, serverDateTimeFormat)
        .local()
        .format(time_format());
};

export const formatUtcToLocal = (time = null, format = false) => {
    if (!time)
        return null;

    return moment.utc(time, serverTimeFormat).local().format(format || time_format());
}

export const isValidDate = string => {
    if (!string)
        return false;

    if (typeof parseInt(string) === 'number' && string.split('-').length <= 1)
        return false;

    return !isNaN(new Date().getTime());
}

export const calenderTime = (date, withTime = true) => {
    date = moment(formatDateToLocal(date, true), formatted_date());

    const days = moment(date).diff(moment.now(), 'days');
    if (moment(date).format('YYYY') < moment().format('YYYY')) {
        return moment(date).format(formatted_date())
    }
    if (days < -1 || days > 1) {
        return moment(date).format('DD MMM, YY')
    }

    // nextWeek: '[' + Vue.prototype.$t('next_week') + ']',
    // lastWeek: '[' + Vue.prototype.$t('last') + '] dddd',

    moment.locale(window.appLanguage, {})
    let format = {
        sameDay: '[' + Vue.prototype.$t('today') + ']',
        lastDay: '[' + Vue.prototype.$t('yesterday') + ']',
        nextDay: '[' + Vue.prototype.$t('tomorrow') + ']',
        sameElse: `${date_format()[settings.date_format]}`
    };
    if (withTime) {
        return date.calendar(format);
    }

    return date.calendar(null, format);
};


export const localToUtc = (time = null) => {
    if (!time) {
        return '';
    }

    moment.locale('en');
    return moment(time, time_format()).utc().format('HH:mm')
}

export const formatDateForServer = (date = null) => {
    if (!date) {
        return '';
    }

    moment.locale('en');
    return moment(moment(date), formatted_date()).format(serverDateFormat);
}

export const formatTimeForServer = (time = null) => {
    if (!time) {
        return '';
    }

    moment.locale('en');
    return moment(time, time_format()).format(serverTimeFormat);
}

export const today = () => {
    return moment(new Date());
}

export const thisWeek = () => {
    return [
        moment().startOf('week'),
        moment().endOf('week')
    ];
}

export const lastWeek = () => {
    return [
        moment().subtract(1, 'weeks').startOf('week'),
        moment().subtract(1, 'weeks').endOf('week')
    ];
}

export const thisMonth = () => {
    return [
        moment().startOf('month'),
        moment().endOf('month')
    ]
}

export const lastMonth = () => {
    return [
        moment().subtract(1, 'month').startOf('month'),
        moment().subtract(1, 'month').endOf('month')
    ]
}

export const thisYear = () => {
    return [
        moment(new Date()).startOf('year'),
        moment(new Date()).endOf('year'),
    ]
}

export const lastYear = () => {
    return [
        moment(new Date()).subtract(1, 'year').startOf('year'),
        moment(new Date()).subtract(1, 'year').endOf('year'),
    ]
}

export const total = () => {
    return [
        'total'
    ]
}

export const startAndEndOf = (year, month) => {
    return [
        moment([year, month]).startOf('month'),
        moment([year, month]).endOf('month')
    ]
}

export const getDateRange = (type) => {
    return {
        today,
        thisWeek,
        lastWeek,
        thisMonth,
        lastMonth,
        thisYear,
        lastYear,
        total,
    }[type]();
}

export const differentInTime = (startTime, endTime, withDate = false) => {
    if (withDate) {
        return moment.duration(moment(endTime, serverDateTimeFormat).diff(moment(startTime, serverDateTimeFormat)));
    }

    if (moment(endTime, serverTimeFormat).diff(moment(startTime, serverTimeFormat), 'hours') < 0) {
        startTime = moment(`${today().format(serverDateFormat)} ${startTime}`);
        endTime = moment(`${today().add(1, 'day').format(serverDateFormat)} ${endTime}`)
    }

    return moment.duration(moment(endTime, serverTimeFormat).diff(moment(startTime, serverTimeFormat)));
}

export const getDifferentTillNow = (startTime, endTime = moment.now()) => {
    return differentInTime(dateTimeToLocalWithFormat(startTime), moment(endTime).format(serverDateTimeFormat), true);
}

export const convertSecondToHourMinutes = (seconds) => {
    const min = parseInt(parseInt(seconds) / 60);
    const hour = min / 60;
    const absHour = parseInt(hour);
    const absMin = Math.abs(min - (absHour * 60));

    let timeFormat = `${String(absHour).length === 1 ? `0${absHour}` : absHour}:${String(absMin).length === 1 ? `0${absMin}` : String(absMin).substr(0, 2)}`;

    return (absHour === 0 && (min - (absHour * 60)) < 0) ? `-${timeFormat}` : timeFormat;
}

export const dateTimeFormat = (value) => {
    if (value) {
        return `${onlyTime(value)}, ${calenderTime(value, false)}`
    }
    return null;
};

export const timeToDateTime = (date, time) => {
    return moment(`${moment(date, serverDateFormat).format(serverDateFormat)} ${time}`);
}

export const formatDateTimeForServer = (dateTime = null) => {
    if (!dateTime) {
        return '';
    }
    return moment(dateTime, `${formatted_date()} ${time_format()}`).utc().format(serverDateTimeFormat)
}

export const isAfterNow = (value) => {
    return moment(value, serverDateFormat).isAfter(moment.now());
}

export const isSameOrAfterThisYear = (value) => {
    return moment(value).isSameOrAfter(moment.now(), "year");
}

export const getUtcToLocalTimeOnly = (value) => {
    return moment.utc(value, serverDateTimeFormat)
        .local()
        .format(serverTimeFormat);
}

export const calenderTimeWithMonthSortName = (date) => {
    return moment(date).format('DD MMM, YY')
}

export const getHoursAndMinutesInString = (seconds, short = false, roundedTo = 'abs') => {
    let min = Math.abs(parseInt(seconds) / 60);

    if (roundedTo === 'ceil') {
        min = Math.ceil(parseInt(seconds) / 60);
    }

    const hour = min / 60;
    const absHour = parseInt(hour);
    const absMin = Math.abs(min - (absHour * 60));
    let H = 'hours'
    let h = 'hour'
    let M = 'minutes'
    let m = 'minute'
    if (short) {
        h = 'h';
        H = 'h';
        m = 'm';
        M = 'm';
    }
    if (absHour > 0) {
        return `${absHour} ${absHour > 1 ? H : h} 
        ${absMin > 0 ? `and ${String(absMin).substr(0, 2)} ${absMin > 1 ? M : m}` : ''}`;
    }
    return `${String(absMin).substr(0, 2)} ${absMin > 1 ? M : m}`;
}

export const getGreetingTime = (currentTime = null) => {
    if (currentTime) {
        currentTime = moment(currentTime);
    } else {
        currentTime = moment(new Date());
    }
    const splitAfternoon = 12; // 24hr time to split the afternoon
    const splitEvening = 18; // 24hr time to split the evening
    const currentHour = parseFloat(currentTime.format('HH'));

    if (currentHour >= splitAfternoon && currentHour <= splitEvening) {
        // Between 12 PM and 5PM
        return 'Good afternoon';
    } else if (currentHour >= splitEvening) {
        // Between 5PM and Midnight
        return 'Good evening';
    }
    // Between dawn and noon
    return 'Good morning';
}

export const getDateDifferenceString = (start, end) => {
    let startMonthNo = moment(start).month();
    let endMonthNo = moment(end).month();

    let startFormat = moment(start).date();
    let endFormat = moment(end).format('D MMM YYYY');
    if (startMonthNo !== endMonthNo) {
        startFormat = moment(start).format('D MMM');
    }

    return `${startFormat} - ${endFormat}`
}

export const customDateFormat = (date, format='D MMM, YYYY') => {
    return moment(date).format(format)
}

export const getSecondFromTime = (time = null) => {
    if (!time)
        return null;

    return moment.duration(time).asSeconds();
}