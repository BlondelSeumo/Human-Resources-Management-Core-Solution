import coreLibrary from "../../helpers/CoreLibrary";

export const FormMixin = {
    extends: coreLibrary,
    props: {
        selectedUrl: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            mixinName: "formMixin",
            fields: {},
            fieldStatus: {
                isSubmit: false,
            },
        };
    },

    created() {
        if (this.selectedUrl) {
            this.getEditData(this.selectedUrl);
        }
    },
    methods: {
        /**
         * get data from DB
         */
        getEditData(url) {

            if (this.isFunction(this.beforeGetEditData))
                this.beforeGetEditData();

            this.axiosGet(url)
                .then((response) => {

                    if (this.isFunction(this.afterSuccessFromGetEditData))
                        this.afterSuccessFromGetEditData(response);

                }).catch(({response}) => {

                if (this.isFunction(this.afterErrorFromGetEditData))
                    this.afterErrorFromGetEditData(response);
            });
        },

        /**
         * trigger point for db
         */
        save(submitData) {

            let url = this.$refs.form.dataset["url"],
                reqType = "";
            if (this.selectedUrl) {
                reqType = "patch";
            } else {
                reqType = "post";
            }
            this.submitFromFixin(reqType, url, submitData);
        },

        /**
         * @param reqType
         * @param url
         * @param submitData
         * for submit data
         */
        submitFromFixin(reqType, url, submitData) {
            let axioscall = "";

            this.fieldStatus = {};
            this.fieldStatus.isSubmit = true;

            if (this.isValidForm()) {

                //trigger before submit
                if (this.isFunction(this.beforeSubmit))
                    this.beforeSubmit();

                if (reqType === "post") {

                    axioscall = this.axiosPost({
                        url: url,
                        data: submitData,
                    });
                } else {

                    axioscall = this.axiosPatch({
                        url: url,
                        data: submitData,
                    });

                }

                axioscall.then((response) => {

                    //trigger after success
                    if (this.isFunction(this.afterSuccess))
                        this.afterSuccess(response);

                }).catch(({response}) => {

                    //trigger after error
                    if (this.isFunction(this.afterError))
                        this.afterError(response);

                }).finally(() => {

                    //trigger after finally
                    if (this.isFunction(this.afterFinalResponse))
                        this.afterFinalResponse();

                });

                //trigger after update
                if (this.isFunction(this.afterSubmit))
                    this.afterSubmit();
            } else {
                if (this.isFunction(this.afterJsValidationFail))
                    this.afterJsValidationFail();
            }
        },

        /**
         * geting app-input component
         */
        getInputComponents() {
            let childs = [];

            this.$children.filter((comp) => {

                if (comp.$options.name === "AppInput") childs.push(comp);
                else if (comp.$children.length > 0) {
                    comp.$children.filter(smComp => {

                        if (smComp.$options.name === "AppInput") childs.push(smComp);
                        else if (smComp.$children.length > 0){

                            smComp.$children.filter(miniComp => {

                                if (miniComp.$options.name === "AppInput") childs.push(miniComp);
                            });
                        }
                    })
                }
            });
            return childs;
        },

        /**
         * check form validation
         */
        isValidForm() {
            let instance = this,
                childComponents = this.getInputComponents(),
                invalidFields = [],
                message = "";

            childComponents.filter((item) => {
                instance.fields[item.name] = item.value;

                if (item.required && (!item.value || item.value.length === 0)) {

                    message = this.$t("this_field_is_required");
                    invalidFields.push(item);
                    instance.makeFieldStatusObject(item, message, "required");

                } else if (item.type === "password") {
                    if(item.specialValidation && this.isValidPassword(item.value))
                    {
                        if (!_.isEmpty(item.sameAs)) {

                            message = this.$t("passwords_are_not_matched");
                            if (instance.fields[item.sameAs] !== item.value) {

                                invalidFields.push(item);
                                instance.makeFieldStatusObject(item, message, "sameAs", "sameAs");
                            }

                        }
                    } else if(!item.specialValidation){
                        if (!_.isEmpty(item.sameAs)) {

                            if(instance.fields[item.sameAs] || item.value)
                            {
                                message = this.$t("passwords_are_not_matched");
                                if (instance.fields[item.sameAs] !== item.value) {
                                    invalidFields.push(item);
                                    instance.makeFieldStatusObject(item, message, "sameAs", "sameAs");
                                }
                            }

                        }
                    } else{
                        message = this.$t("please_enter_a_strong_password");
                        invalidFields.push(item);
                        instance.makeFieldStatusObject(item, message, "password");
                    }

                } else if (item.type === "date" && !_.isEmpty(item.notBefore)) {

                    message = `${item.name} ${this.$t("can_not_before")} ${item.notBefore}`;
                    if (!instance.isSecondDateAfterFirstDate(instance.fields[item.notBefore], item.value)) {

                        invalidFields.push(item);
                        instance.makeFieldStatusObject(item, message, "notBefore", "notBefore");
                    }

                } else if (item.type === "email" && !this.isValidEmail(item.value)) {

                    message = this.$t("this_field_is_invalid");
                    invalidFields.push(item);
                    instance.makeFieldStatusObject(item, message, "email");

                } else if (item.type === "time" && this.isValidTime(item.value)) {

                    message = this.$t("this_field_is_invalid");
                    invalidFields.push(item);
                    instance.makeFieldStatusObject(item, message, "time");

                } else {
                    let itemLength = item.value ? item.value.length : null;

                    if (itemLength && item.minLength && itemLength < item.minLength) {

                        message = `${this.$t("minimum_length_is")} ${item.minLength}.`;
                        invalidFields.push(item);
                        instance.makeFieldStatusObject(item, message, "minLength");

                    } else if (itemLength && item.maxLength && itemLength > item.maxLength) {

                        message = `${this.$t("maximum_length_is")} ${item.maxLength}.`;
                        invalidFields.push(item);
                        instance.makeFieldStatusObject(item, message, "maxLength");

                    } else if (item.minNumber && item.value < item.minNumber) {

                        message = `${this.$t("minimum_number_is")} ${item.minNumber}.`;
                        invalidFields.push(item);
                        instance.makeFieldStatusObject(item, message, "minNumber");

                    } else if (item.maxNumber && item.value > item.maxNumber) {

                        message = `${this.$t("maximum_number_is")} ${item.maxNumber}.`;
                        invalidFields.push(item);
                        instance.makeFieldStatusObject(item, message, "maxNumber");

                    } else if (item.alphanumeric && !instance.isAlphanumeric(item.value)) {

                        message = this.$t("this_field_is_not_alphanumeric");
                        invalidFields.push(item);
                        instance.makeFieldStatusObject(item, message, "alphanumeric");
                    }
                }
            });

            return _.isEmpty(invalidFields);
        },

        makeFieldStatusObject(item, message, invalidKey, relatedValueKey = null) {

            this.fieldStatus[item.name] = {
                // [invalidKey]: "invalid",
                isValid: false,
                // [item[relatedValueKey]]: this.fields[item[relatedValueKey]],
                message: message
            };
        },

        /**
         * emit from input component
         */
        changed(obj) {
            let item = this.fieldStatus[obj.key];
            if (_.has(item, 'isValid')) {
                item.isValid = true;
                item.message = 'ok';
            }
            // this.fields[obj.key] = obj.value;

            // //if obj has null value
            // if (obj.value === null) {
            //     delete this.fields[obj.key];
            // }
        },
    },
};
