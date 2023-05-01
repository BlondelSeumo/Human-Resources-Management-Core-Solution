import Vue from "vue";

export const formDataAssigner = function (formData = new FormData, dataObject) {
    Object.keys(dataObject).map((key) => {
        if (dataObject[key] && !dataObject[key].length > 0 && Object.keys(dataObject[key]).length > 0) {
            Object.keys(dataObject[key]).map(childKey => {
                return formData.append(key + `[${childKey}]`, dataObject[key][childKey]);
            })
        } else if (Array.isArray(dataObject[key])) {
            dataObject[key].map((el, index) => {
                Object.keys(el).map(objectKeys => {
                    formData.append(key + `[${index}][${objectKeys}]`, el[objectKeys]);
                });
            })
        } else {
            return formData.append(key, dataObject[key]);
        }
    });
    return formData;
};

Vue.prototype.$errorMessage = (errorObject, field, isArray = true, withId= false) => {
    if (!Object.keys(errorObject).length)
        return '';
    if (isArray) {
        let error = errorObject[field]
        if (error && !withId) {
            return error[0].replace(' id', '');
        }else if (error && withId){
            return error[0];
        }
        return '';
    }
    return errorObject[field];
};
//The details.0.end_at field is required when details.0.start at is present.
export const errorMessageForArray = (errors, field_name, extra = '', isArray = true) => {
    if (!Object.keys(errors).length) {
        return '';
    }

    if (isArray) {
        let error = errors[field_name]
        if (error) {
            let message = error[0];
            return String(message)
                .replace(field_name, field_name.split('.').pop().split('_').join(' '))
                .replace(extra, extra.split('.').pop().split('_').join(' '));
        }
        return '';
    }
    return errors[field_name];
}

export const errorMessageInArray = (errors, field_name) => {
    if (!Object.keys(errors).length) {
        return '';
    }
    let error = errors[field_name];

    if (error) {
        let message = '';
        for (let i = 0; i < error.length; i++) {
            message = `${message + error[i]} \n`
        }
        return (message)
    }
}

export const dropZoneErrorGenerator = (errors, file_name = 'attachments') => {
    const file_errors = Object.keys(errors).filter(key => {
        return String(key).includes(file_name);
    });
    let first_part_of_error = '';
    let last_part_of_error = '';
    const message = file_errors.map(key => {
        const errorArray = errors[key][0].split(key)
        first_part_of_error = errorArray[0];
        last_part_of_error = errorArray[1];
        return key.split('.').join(' ');
    }).join(', ')

    return `${first_part_of_error} ${message} ${last_part_of_error}`
}

export const addChooseInSelectArray = (array, valueField = 'name', label = '', choose = true) => {
    let obj = {id: '', disabled: true};
    obj[valueField] = Vue.prototype.$t(choose ? 'choose_field' : 'select_field', {field: Vue.prototype.$t(label).toLowerCase()})

    return [obj].concat(array);
}

export const addSelectInSelectArray = (array, valueField = 'name', label = '') => {
    return addChooseInSelectArray(array, valueField, label, false);
}
