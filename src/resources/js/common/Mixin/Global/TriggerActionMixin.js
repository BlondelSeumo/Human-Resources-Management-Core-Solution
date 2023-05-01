import {urlGenerator} from "../../Helper/AxiosHelper";

export default {
    methods: {
        getAction(row, action, active) {
            const baseURL = `${action.url}/${row.id}`;
            if (action.type === 'page') {
                window.location = urlGenerator(`${baseURL}/${action.name}`);
            }

            if (action.name === 'delete') {
                this.delete_url = baseURL
                this.confirmationModalActive = true;
            }
        },
    }
}
