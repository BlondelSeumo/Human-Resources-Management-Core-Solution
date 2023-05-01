export default class Time {
    static isValidTime(timeString, timeFormat = 24) {
        let timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]\s(AM|PM)$/i;
        timeRegex =
            timeFormat == 12
                ? timeRegex
                : /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
        return !timeRegex.test(timeString);
    }
}
