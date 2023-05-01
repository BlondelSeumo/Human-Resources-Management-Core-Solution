import AxiosFunction from "./axios/AxiosFunction";
import Utility from "./utility/Utility";
import DateFunction from "./date/DateFunction";
import AppFunction from "./app/AppFunction";
import RegularExpression from "./reg-exp/RegularExpression";
import Icon from "./icon/Icon";
import TemplateHelper from "./template-helper/TemplateHelper";
import Time from "./time/Time";

export default {
    mounted() {
        this.$nextTick(() => {
            Icon.initFeather();
            TemplateHelper.moveHorizontalScrollbarWithMouse();
            TemplateHelper.preventDropdownMenuCloseOnInsideClick();
        });
    },
    computed: {
        timeFormat() {
            return this.$store.state.settings.timeFormat;
        },
        dateFormat() {
            return this.$store.state.settings.dateFormat;
        }
    },
    directives: {
        "click-outside": {
            bind: function (el, binding, vNode) {
                if (typeof binding.value !== "function") {
                    const compName = vNode.context.name;
                    let warn = `[Vue-click-outside:] provided expression '${binding.expression}' is not a function, but has to be`;
                    if (compName) {
                        warn += `Found in component '${compName}'`;
                    }

                    console.warn(warn);
                }
                const bubble = binding.modifiers.bubble;
                const handler = (e) => {
                    if (bubble || (!el.contains(e.target) && el !== e.target)) {
                        binding.value(e);
                    }
                };
                el.__vueClickOutside__ = handler;
                document.addEventListener("click", handler);
            },
            unbind: function (el, binding) {
                document.removeEventListener("click", el.__vueClickOutside__);
                el.__vueClickOutside__ = null;
            }
        },
    },
    filters: {
        titleFilter(str) {
            str = str.replace(/(^\s*)|(\s*$)/gi, "");
            str = str.replace(/[ ]{2,}/gi, " ");
            str = str.replace(/\n /, "\n");
            let titleArray = str.split(' ');
            if (titleArray.length > 1) {
                return (titleArray[0][0] + titleArray[1][0]).toLocaleUpperCase();
            } else {
                return titleArray[0].substring(0, 2).toLocaleUpperCase();
            }
        },
    },
    methods: {
        /**
         * axios get request
         * */
        axiosGet(url, data = null) {
            url = this.getAppUrl(url);

            return AxiosFunction.axiosGet(url, data);
        },

        /**
         * axios Post request
         * */
        axiosPost(options) {
            options.url = this.getAppUrl(options.url);

            return AxiosFunction.axiosPost(options);
        },

        /**
         * axios patch request
         * */
        axiosPatch(options) {
            options.url = this.getAppUrl(options.url);

            return AxiosFunction.axiosPatch(options);
        },

        /**
         * axios Put request
         * */
        axiosPut(options) {
            options.url = this.getAppUrl(options.url);

            return AxiosFunction.axiosPut(options);
        },

        /**
         * axios Delete request
         * */
        axiosDelete(url) {
            url = this.getAppUrl(url);

            return AxiosFunction.axiosDelete(url);
        },

        /**
         * get app url
         * */
        getAppUrl(url) {
            return AppFunction.getAppUrl(url);
        },

        /**
         * get base url
         * */
        getBaseUrl() {
            return AppFunction.getBaseUrl();
        },

        /**
         * checking function
         * */
        isFunction(func) {
            return Utility.isFunction(func);
        },

        /**
         * checking isUndefined
         * */
        isUndefined(obj) {
            return Utility.isUndefined(obj);
        },

        /**
         * checking given param is empty
         * */
        isEmpty(obj) {
            return Utility.isEmpty(obj);
        },

        /**
         * checking isBoolean
         * */
        isBoolean(obj) {
            return Utility.isBoolean(obj);
        },

        /**
         * checking valid email
         * */
        isValidEmail(email) {
            if (email)
                return RegularExpression.isValidEmail(email);
            else return true
        },

        /**
         * checking valid password
         * */
        isValidPassword(password) {
            if (password)
                return RegularExpression.isValidPassword(password);
            else return true
        },

        /**
         * checking Alphanumeric
         * */
        isAlphanumeric(value) {
            return RegularExpression.isAlphanumeric(value);
        },

        /**
         * compare between two dates
         * */
        isSecondDateAfterFirstDate(firstDate, secondDate) {
            return DateFunction.isSecondDateAfterFirstDate(
                firstDate,
                secondDate
            );
        },

        /**
         * get date format
         * */
        getDateFormat(date) {
            return DateFunction.getDateFormat(date, this.dateFormat);
        },

        isValidTime(timeString) {
            if (timeString)
                return Time.isValidTime(timeString, this.timeFormat);
            else return false;
        },

        objectToQueryString(obj) {

            const results = [];

            _.forOwn(obj, (value, key) => {
                results.push(`${key}=${value}`);
            });

            return results.join('&');
        },

        /**
         * get query strign value
         * @param key
         * */
        getQueryStringValue(key) {
            return AppFunction.getQueryStringValue(key);
        },

        /**
         * Back to previous url
         * */
        backToPrevioustUrl() {
            AppFunction.backToPrevioustUrl();
        },

        /**
         * go to next url
         * */
        goToNextUrl() {
            AppFunction.goToNextUrl();
        },

        /**
         * Tooltip Initialization
         * */
        initializeTooltip(duration = 3000) {
            setTimeout( () => {
                $('[data-toggle="tooltip"]').tooltip()
            }, duration);
        },

        /**
         * Hide Tooltip
         * */
        hideTooltip() {
            $('[data-toggle="tooltip"]').tooltip("hide");
        }
    }
};
