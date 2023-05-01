export default class Utility {

    /**
     * checking given param is function
     * */
    static isFunction(func) {
        return typeof func === "function";
    }

    /**
     * checking given param is boolean
     * */
    static isBoolean(obj) {
        return typeof obj === "boolean";
    }

    /**
     * checking given param is Undefined
     * */
    static isUndefined(obj) {
        return typeof obj === "undefined";
    }
    
    /**
     * checking given param is empty
     * */
    static isEmpty(obj) {
        return _.isEmpty(obj);
    }

    /**
     * split name from by slash
     * */
    static splitNameBySlas(item) {
        
        if (item.includes("/")) {

            let itemArr = item.split("/");
            return itemArr[itemArr.length - 1];
        } 
        return item;
    }

    static makeFile(item){
        const dateValue = new Date(),
            timeValue = dateValue.getTime(),
            fileName = this.splitNameBySlas(item),
            extension = this.getFileExtension(fileName),
            mineType = this.getFileType(extension),
            obj = { 
                name: fileName, 
                type: mineType,
                url: item,
                lastModified: timeValue,
                lastModifiedDate: dateValue,
                webkitRelativePath: '',
            },
            buffer = new ArrayBuffer(80000),
            view = new Int32Array(buffer, 10000, 10000);
            
        return new File(view, fileName, obj);
    }

    static getFileExtension(fileName){
        return fileName.split('.').pop();
    }

    static getFileType(extension){
        const types = {
            jpg : 'image/jpeg',
            jpeg : 'image/jpeg',
            png : 'image/png',
            gz : 'application/gzip',
            gif : 'image/gif',
            scss : 'text/x-scss',
            xlsx : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            docx : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            odt : 'application/vnd.oasis.opendocument.text',
            txt : 'text/plain',
            pdf : 'application/pdf',
            csv : 'text/csv',
            tsv : 'text/tab-separated-values',
        };
        return types[extension];
    }

    static dataURLtoFile(dataurl) {
        if(dataurl){
            let arr = dataurl.split('/'), mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n),
                filename = this.splitNameBySlas(dataurl);

                console.log(arr, 'arr');
                // console.log(arr, 'arr');

                // while(n--){
                //     u8arr[n] = bstr.charCodeAt(n);
                // }
                // return new File([u8arr], filename, {type:mime});
        }
        // else return null;
    }


}
