import Vue from "vue";
import optional from "./Optional";
import {date_format} from "./DateTimeHelper";

const settings = window.settings || {};

export const getThousandSeparator = () => {
    return settings.thousand_separator ? settings.thousand_separator : ' ';
}

export const getDecimalSeparator = () => {
    return settings.decimal_separator ? settings.decimal_separator : '.';
}

export const getNumberOfDecimal = () => {
    return settings.number_of_decimal ? settings.number_of_decimal : 0
}

export const numberFormatter = number => {
    if (number && (String(number).indexOf('.') > -1)) {
        number = number.toFixed(getNumberOfDecimal())
    }
    if (!isNaN(parseFloat(number))) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, getThousandSeparator());
        return parts.join(getDecimalSeparator());
    }
    return 0;
}

export const configFormatter = (format) => {
    return {
        id: format,
        value: Vue.prototype.$t(format)
    }
};

export const formatCurrency = (amount) => {
    return Vue.prototype.$t(`format_${optional(settings, 'currency_position')}`,
        {
            symbol: optional(settings, 'currency_symbol'),
            amount: amount
        })
}


