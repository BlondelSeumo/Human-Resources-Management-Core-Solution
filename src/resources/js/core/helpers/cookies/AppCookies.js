import VueCookies from 'vue-cookies';
import Vue from 'vue';

Vue.use(VueCookies);
/**
 * expireTimes String format
 *
 * Unit	full name
 * y	year
 * m	month
 * d	day
 * h	hour
 * min	minute
 * s	second
 *
 * example: '1d
 */

export default class AppCookie {
    static config(expireTimes,path,domain,secure,sameSite){
        $cookies.config(expireTimes,path, domain, secure, sameSite);
    }
    static set(keyName, value, expireTimes = '1d', path = '/', domain = '', secure = '', sameSite = '') {
        $cookies.set(keyName, value, expireTimes, path, domain, secure, sameSite);
        return this;
    }

    static get(keyName) {
        return $cookies.get(keyName);
    }
    static keyExist(keyName){
        return $cookies.isKey(keyName)
    }
    static keys() {
        return $cookies.keys();
    }

    static remove(keyName) {
        return $cookies.remove(keyName);
    }


}
