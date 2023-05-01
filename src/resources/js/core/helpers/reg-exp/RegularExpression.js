export default class RegularExpression {

    /**
    * check Alphanumeric value
    * */
    static isAlphanumeric(value) {
        let regExp = /^[0-9a-zA-Z]+$/;

        return value.match(regExp) ? true : false;
    }

    /**
    * checking given valid email
    * */
    static isValidEmail(email) {
        let regExp = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return regExp.test(email);
    }
    /**
     * checking given valid password
     * */
    static isValidPassword(password) {
        let regExp = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/;
        return regExp.test(password);
    }
}

