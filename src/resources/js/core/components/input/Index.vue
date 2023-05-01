<template>
    <div>
        <text-input v-if="type === 'text'" :key="'text'" :data="$props" :value="value" v-on="$listeners"/>

        <email-input v-else-if="type === 'email'" :key="'email'" :data="$props" :value="value" v-on="$listeners"/>

        <number-input v-else-if="type === 'number'" :key="'number'" :data="$props" :value="value" v-on="$listeners"/>

        <decimal-input v-else-if="type === 'decimal'" :key="'decimal'" :data="$props" :value="value" v-on="$listeners"/>

        <password v-else-if="type === 'password'" :key="'password'" :data="$props" :value="value" v-on="$listeners"/>

        <date-picker v-else-if="type === 'date'" :key="'date'" :data="$props" :value="value" @input="input"/>

        <time-picker v-else-if="type === 'time'" :key="'time'" :data="$props" :value="value" @input="input"/>

        <smart-select v-else-if="type === 'smart-select'" :key="'smart-select'" :data="$props" :value="value" @input="input"/>

        <search-select v-else-if="type === 'search-select'" :key="'search-select'" :data="$props" :value="value" @input="input"/>

        <select-input v-else-if="type === 'select'" :key="'select'" :data="$props" :value="value" v-on="$listeners"/>

        <multi-select v-else-if="type === 'multi-select'" :key="'multi-select'" :data="$props" :value="value" v-on="$listeners"/>

        <multi-create v-else-if="type === 'multi-create'" :key="'multi-create'" :data="$props" :value="value" v-on="$listeners"/>

        <text-area v-else-if="type === 'textarea'" :key="'textarea'" :data="$props" :value="value" v-on="$listeners"/>

        <currency v-else-if="type === 'currency'" :key="'currency'" :data="$props" :value="value" v-on="$listeners"/>

        <radio-buttons v-else-if="type === 'radio-buttons'" :key="'radio-buttons'" :data="$props" :value="value" @input="input"/>

        <radio v-else-if="type === 'radio'" :key="'radio'" :data="$props" :value="value" v-on="$listeners"/>

        <switch-input v-else-if="type === 'switch'" :key="'switch'" :data="$props" :value="value" v-on="$listeners"/>

        <single-checkbox v-else-if="type === 'single-checkbox'" :key="'single-checkbox'" :data="$props" :value="value" v-on="$listeners"/>

        <check-box v-else-if="type === 'checkbox'" :key="'checkbox'" :data="$props" :value="value" @input="input" @changed="getEvent"/>

        <text-editor v-else-if="type === 'text-editor'" :key="'text-editor'" :data="$props" :value="value" @input="input"/>

        <tel-input v-else-if="type === 'tel-input'" :key="'tel-input'" :data="$props" :value="value" @input="input"/>

        <file-upload v-else-if="type === 'file'" :key="'file'" :data="$props" :value="value" v-on="$listeners"/>

        <custom-image-file-uploader
            v-else-if="type === 'custom-file-upload'"
            :key="'custom-file-upload'"
            :data="$props"
            :value="value"
            v-on="$listeners"
        />

        <dropzone-uploader v-else-if="type === 'dropzone'" :data="$props" :value="value" @input="input" :key="'dropzone'"/>

        <!--Validation message-->
        <div v-if="!isUndefined(fieldProps.isSubmit) && fieldProps.isSubmit && !error.isValid" :key="'error'">
            <small class="text-danger validation-error">{{ error.message }}</small>
        </div>
    </div>
</template>

<script>
import TextInput from "./TextInput";
import SelectInput from "./SelectInput";
import DatePicker from "./DatePicker";
import TimePicker from "./TimePicker";
import FileUpload from "./FileUpload";
import CustomImageFileUploader from "./CustomImageUpload";
import DropzoneUploader from "./Dropzone";
import SmartSelect from "./SmartSelect";
import TextArea from "./TextArea";
import Currency from "./Currency";
import CheckBox from "./CheckBox";
import SingleCheckbox from "./SingleCheckbox";
import Radio from "./Radio";
import Password from "./Password";
import NumberInput from "./NumberInput";
import DecimalInput from "./DecimalInput";
import EmailInput from "./EmailInput";
import Error from "./error/Error";
import MultiSelect from "./MultiSelect";
import TextEditor from "./TextEditor";
import RadioButtons from "./RadioButtons";
import TelInput from "./TelInput";
import SearchSelect from "./SearchSelect";
import SwitchInput from "./SwitchInput";

import coreLibrary from "../../helpers/CoreLibrary";
import MultiCreate from "./MultiCreate";

export default {
    name: "AppInput",
    extends: coreLibrary,
    components: {
        Error,
        EmailInput,
        DecimalInput,
        TextInput,
        DatePicker,
        TimePicker,
        FileUpload,
        CustomImageFileUploader,
        DropzoneUploader,
        SmartSelect,
        SelectInput,
        TextArea,
        Currency,
        CheckBox,
        SingleCheckbox,
        Radio,
        Password,
        NumberInput,
        MultiSelect,
        MultiCreate,
        TextEditor,
        RadioButtons,
        TelInput,
        SearchSelect,
        SwitchInput
    },
    props: {
        value: {},
        type: {
            type: String,
            default: 'text'
        },
        autocomplete: {
            type: String,
            default: 'off'
        },
        readOnly: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        id: {
            type: String,
            default: ''
        },
        initialValue: {},
        placeholder: {
            type: String,
            default: ''
        },

        // Required
        required: {
            type: Boolean,
            default: false
        },

        // Error message
        errorMessage: String,

        // Style
        inputClass: {
            type: String,
            default: ''
        },
        label: {
            default: ''
        },
        labelClass: {
            type: String,
            default: ''
        },

        // Custom select
        list: {
            type: Array,
            default: function () {
                return [
                    {
                        id: 1,
                        value: "option 1"
                    },
                    {
                        id: 2,
                        value: "option 2"
                    },
                    {
                        id: 3,
                        value: "option 3"
                    },
                    {
                        id: 4,
                        value: "option 4"
                    }
                ];
            }
        },
        listValueField: {
            type: String,
            default: 'value'
        },
        selectedTextClass: {
            type: String,
            default: ''
        },
        listClass: {
            type: String,
            default: ''
        },
        listItemClass: {
            type: String,
            default: ''
        },
        listItemInputClass: {
            type: String,
            default: ''
        },

        // Multi select
        isAnimatedDropdown: {
            type: Boolean,
            default: false
        },

        // Multi create
        storeData: {
            type: Function
        },
        multiCreatePreloader: {
            type: Boolean,
            default: false
        },

        // Time picker
        minTime: String,
        maxTime: String,
        getTimeZone: {
            type: Boolean,
            default: false
        },

        // Text area
        textAreaCols: Number,
        textAreaRows: Number,
        textAreaWrap: {
            type: String,
            default: "soft"
        },
        textAreaSpellCheck: {
            type: Boolean,
            default: false
        },
        textAreaDir: String,

        // Currency
        symbol: {
            type: String,
            default: "$"
        },
        decimal: {
            type: String,
            default: "."
        },
        thousandSeparator: {
            type: String,
            default: ","
        },
        precision: {
            type: Number,
            default: 2
        },
        currencyFormat: {
            type: String,
            default: "%v%s"
        },

        // Radio & checkbox
        checked: {
            type: Boolean,
            default: false
        },
        customRadioType: {
            type: String,
            default: 'custom-radio-default'
        },
        customCheckboxType: {
            type: String,
            default: 'checkbox-default'
        },
        radioCheckboxName: {
            type: String
        },
        radioCheckboxWrapper: {
            type: String
        },

        // Password match
        sameAs: {
            type: String,
            default: ''
        },
        showPassword: {
            type: Boolean,
            default: false
        },

        // Special Validation password
        specialValidation: {
            type: Boolean,
            default: false
        },

        // Date picker
        minDate: {
            type: [String, Date]
        },
        maxDate: {
            type: [String, Date]
        },
        notBefore: {
            type: String,
            default: ''
        },
        dateMode: {
            type: String,
            default: "date"
        },
        dateColor: {
            type: String,
            default: "blue"
        },
        isRange: {
            type: Boolean,
            default: false
        },
        popoverPosition: {
            type: String,
            default: 'bottom-start'
        },

        // File
        maxFiles: {
            type: Number,
            default: null
        },
        maxLength: Number,
        minLength: Number,
        generateFileUrl: {
            type: Boolean,
            default: true
        },
        maxNumber: Number,
        minNumber: Number,
        alphanumeric: {
            type: Boolean,
            default: false
        },
        wrapperClass: {
            type: String,
            default: null
        },

        // Summernote
        height: {
            type: Number,
            default: 300
        },
        minimal: {
            type: Boolean,
            default: false
        },
        textEditorHints: {
            type: Object,
            default: function () {
                return {};
            }
        }
    },
    data() {
        return {
            inputValue: '',
            name: ''
        };
    },
    computed: {
        fieldProps: function () {
            return this.mixinInstance.fieldStatus;
        },
        error() {
            let item = this.mixinInstance.fieldStatus[this.name];
            if (this.errorMessage) {
                return {
                    isValid: false,
                    message: this.errorMessage
                }
            } else if (item) return item;
            return {
                isValid: true,
                message: "true"
            };
        }
    },
    created() {
        this.findMixin();

        if (this.mixinInstance) {
            //set initial value
            this.name = this.getComponentName();
            this.mixinInstance.changed({key: this.name, value: this.inputValue});
        } else {
            // console.log("You can't use this component without the FormMixin");
        }
    },
    methods: {
        input(value) {
            this.$emit('input', value);
            this.changed(value);
        },
        changed(value) {
            value = this.type === "dropzone" ? this.getFiles(value) : value;
            // : this.type === "checkbox"
            // ? this.getCheckedValue(value)

            this.mixinInstance.changed({key: this.name, value: value});
            this.inputValue = value;
        },

        getEvent($event) {
            this.$emit('changed', $event);
        },

        //to get multiple file
        getFiles(obj) {
            let keys = Object.keys(obj);
            let files = [];
            keys.map(i => {
                let file = obj[i];
                files.push(file);
            });
            return obj;
        },

        // for checkbox
        getCheckedValue(value) {
            this.value = this.value ? this.value : [];
            // check value is already there,it will be remove

            if (!Array.isArray(value)) {
                if (this.value.indexOf(value) > -1) {
                    let result = [];
                    this.value.map(item => {
                        if (item != value) {
                            result.push(item);
                        }
                    });
                    this.value = result;
                }
                //unless it will add
                else {
                    this.value.push(value);
                }
            }

            return this.value;
        },

        findMixin() {

            let instance = this,
                parent = instance;

            //we'll find upto 10 level of parent to get the mixin
            for (let i = 0; i < 10; i++) {
                if (parent.mixinName === "formMixin") {
                    instance.mixinInstance = parent;
                    break;
                }
                parent = parent.$parent;
            }

        },
        getComponentName() {
            let name = this.$vnode.data.model.expression;

            if (name.includes(".")) {
                let nameArr = name.split("."),
                    nameStr = '';
                // return nameArr[nameArr.length - 1];

                for (let index in nameArr) {
                    nameStr += index == nameArr.length - 1 ? nameArr[index] : nameArr[index] + "_";
                    // nameStr += item+"_";
                }
                return nameStr;

            } else return name;
        }
    }
};
</script>
