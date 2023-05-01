export default class AxiosFunction {

    static axiosGet(url, data = null) {
        return data ? axios.get(url, data) : axios.get(url);
    }

    static axiosPost(options) {
        return axios.post(options.url, options.data);
    }

    static axiosPut(options) {
        return axios.put(options.url, options.data);
    }

    static axiosDelete(url) {
        return axios.delete(url);
    }

    static axiosPatch(options) {
        return axios.patch(options.url, options.data);
    }

    static responseToCallback(promise, succesCallback, errorCallback) {
        promise.then(response => {
            if (succesCallback) succesCallback(true, response.data); //returns success response data
        }).catch(({response}) => {
            if (errorCallback) errorCallback(false, response.data); //returns error response data
        });
    }
}
