const optional = (obj, ...props) => {
    if (!obj || typeof obj !== 'object')
        return undefined;
    const val = obj[props[0]];
    if (props.length === 1 || !val) return val;
    const rest = props.slice(1);
    return optional.apply(null, [val, ...rest])
}

export default optional;
