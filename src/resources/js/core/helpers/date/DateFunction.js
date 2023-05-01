export default class DateFunction {

    /**
     * compare between two dates
     * */
    static isSecondDateAfterFirstDate(date1, date2) {
        date1 = new Date(date1);
        date2 = new Date(date2);
        return date2 >= date1;
    }

    /**
     * get date format
     * */
    static getDateFormat(date, dateFormat) {
        return moment(date.toString()).format(dateFormat)
    }
}
