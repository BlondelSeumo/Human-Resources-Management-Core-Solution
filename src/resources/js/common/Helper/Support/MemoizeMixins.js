export default {
    name: 'Memoize',
    data() {
        return {
            memoized: {

            }
        }
    },
    methods: {
        memoize(key, callback) {
            if (this.memoized[key]) {
                return this.memoized[key];
            }
            return this.memoized[key] = callback();
        }
    }
}