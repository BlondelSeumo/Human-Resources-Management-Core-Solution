export default class Collection {

    constructor(list) {
        this.list = list;
    }

    first() {
        return this.list.length ? this.list[0] : {};
    }

    last() {
        return this.list.length ? this.list[this.list.length - 1] : {};
    }

    removeObject(value, field = 'id') {
        return this.list.filter(l => {
            return String(l[field]) !== String(value);
        })
    }

    filter(value, field = 'id') {
        return this.list.filter(l => l[field] === value)
    }

    latest(field = 'id') {
        const list = [...this.list]
        list.sort((l1, l2) => l2[field] - l1[field]);
        this.list = list;
        return this;
    }

    limit(limit = 2, offset = 0) {
        this.list = this.list.slice(offset, limit);
        return this;
    }

    pluck(field = 'id') {
        return this.list.map(data => {
            return data[field];
        })
    }

    fields(...fields) {
        this.fields = fields.length ? fields : ['id', 'value'];
        return this;
    }

    merge(list) {
        this.list = this.list.concat(list);
        return this;
    }

    shaper(field = 'translated_name', id = 'id') {
        if (this.list.length) {
            return this.list.map(data => {
                data.value = data[field];
                data.id = data[id];
                return data;
            })
        }
        return [];
    }

    find(value, field = 'id') {
        if (!this.list)
            return {};
        return this.list.find(l => {
            return l[field] == value;
        });
    }

    unique() {
        return Array.from(new Set(this.list));
    }

    all() {
        return this.list;
    }

}
