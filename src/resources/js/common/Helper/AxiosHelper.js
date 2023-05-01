import AxiosFunction from "../../core/helpers/axios/AxiosFunction";
import AppFunction from '../../core/helpers/app/AppFunction'
import axios from 'axios';

export const urlGenerator = url => `${AppFunction.getBaseUrl()}/${url.split('/').filter(d => d).join('/')}`;

export const axiosGet = url => AxiosFunction.axiosGet(urlGenerator(url));


export const axiosPost = (url, data) => {
    return AxiosFunction.axiosPost({
        url: urlGenerator(url),
        data
    });
};

export const axiosPatch = (url, data) => axios.patch(urlGenerator(url), data);

export const axiosDelete = (url) => AxiosFunction.axiosDelete(urlGenerator(url));