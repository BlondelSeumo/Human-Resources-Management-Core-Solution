import MemoizeMixins from "../../../common/Helper/Support/MemoizeMixins";
import moment from "moment";
import {differentInTime, serverDateFormat} from "../../../common/Helper/Support/DateTimeHelper";

export default {
    mixins:[MemoizeMixins],
    methods: {
        getDetails(working_shift, date) {
            return this.memoize(`details-${working_shift.id}-${date.format('ddd').toLowerCase()}`, () => {
                return working_shift.details.find(details => {
                    return String(date.locale('en').format('ddd')).toLowerCase() === details.weekday;
                })
            })
        },
        getEmployeeWorkingShiftFromDate(working_shifts, date, defaultWorkShift) {
            if (!working_shifts.length) {
                return  defaultWorkShift;
            }

            return working_shifts.find(working_shift => {
                const start_at = moment(working_shift.pivot.start_date, serverDateFormat);
                const end_at = working_shift.pivot?.end_date ? moment(working_shift.pivot.end_date, serverDateFormat): null;

                if ((date.isSame(start_at) || date.isAfter(start_at)) && !working_shift.pivot.end_date) {
                    return true;
                }

                if (working_shift.pivot.end_date && (date.isBetween(start_at, end_at) || date.isSame(start_at)) && date.isBefore(end_at)) {
                    return true;
                }

            }) || defaultWorkShift;
        },
        getTotalWorked(attendance) {
            return this.memoize(`attendance-${attendance.id}`, () => {
                return this.getTotalWorkedDuration(attendance.details)
            });
        },
        getTotalWorkedDuration(details) {
            return details.reduce((carry, details) => {
                return carry.add(moment.duration(differentInTime(details.in_time, details.out_time, true)))
            }, moment.duration(0));
        }
    }
}