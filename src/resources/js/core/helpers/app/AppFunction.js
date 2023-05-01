export default class AppFunction{

    static getBaseUrl() {
        const app_url = window.localStorage.getItem('base_url');
        return app_url || window.location.origin;
    }

    static getAppUrl(path) {
        return `${this.getBaseUrl()}/${path.split('/').filter(d => d).join('/')}`;
    }

    static getQueryStringValue(key){
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(key);
    }

    static backToPrevioustUrl(){
        history.back();
    }

    static goToNextUrl(){
        history.forward();
    }
}
