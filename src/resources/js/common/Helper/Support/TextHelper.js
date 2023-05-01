export const ucWords = string => {
    return String(string).toLowerCase()
        .replace(/\b[a-z]/g, (l) => l.toUpperCase())
}

export const columnStringify = string => {
    if (string) {
        return ucWords(String(string).split('_').join(' '))
    }
}
export const ucFirst = string => {
    if (string) {
        return String(string)[0].toUpperCase() + String(string).substring(1)
    }
}

export const studly = string => {
    string = String(string).replace('-', ' ');
    string = string.replace('_', ' ');
    return string.split(' ')
        .map(str => str[0].toUpperCase() + str.substr(1).toLowerCase())
        .join('')
}

export const textTruncate = (str, length, ending) => {
    if (length == null) {
        length = 50;
    }
    if (ending == null) {
        ending = '...';
    }
    if (str.length > length) {
        return str.substring(0, length - ending.length) + ending;
    } else {
        return str;
    }
};

export const snakeCase = (string, glue = '_') => {
    return string.replace(/\W+/g, " ")
        .split(/ |\B(?=[A-Z])/)
        .map(word => word.toLowerCase())
        .join(glue);
};

export const kebabCase = string => {
    return snakeCase(string, '-');
};

export const objectToQueryString = (obj) => {
    return  Object.keys(obj).reduce((carry, key) => {
        if (obj[key] || obj[key] === 0) {
            return carry + `${key}=${(typeof obj[key] == 'object') 
                    ? Object.keys(obj[key]).length > 0 
                            ? JSON.stringify(obj[key])
                            : '' : obj[key]}&`
        }
        return carry;
    }, '').replace(/&+$/, '');
}


