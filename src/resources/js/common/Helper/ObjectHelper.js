export const flatObjectWithKey = (object, key) => {
    if (!object) {
        return {}
    }

    if (!object[key]) {
        return  {}
    }

    let obj = {};

    Object.keys(object[key]).forEach(k => obj[k] = object[key][k]);

    return obj;
}