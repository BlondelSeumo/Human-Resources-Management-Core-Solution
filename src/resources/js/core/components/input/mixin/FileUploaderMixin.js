import Utility from "../../../helpers/utility/Utility";
import AppFunction from "../../../helpers/app/AppFunction";

export const FileUploaderMixin = {
    methods: {
        getDataUrl(item){
            let fileName = Utility.splitNameBySlas(item),
                url = this.data.generateFileUrl ? AppFunction.getAppUrl(item.split('/').filter(p => p).join('/')) : item;

            return new Promise((resolve, reject) => {

                fetch(url)
                .then(response => response.blob())
                .then(blob => this.blobToFile(blob, fileName))
                .then(data => {
                    data.url = URL.createObjectURL(data);
                    resolve(data);
                })

            })
        },

        blobToFile(theBlob, fileName){
            const dateValue = new Date(),
            timeValue = dateValue.getTime();

            theBlob.name = fileName;
            theBlob.lastModified = timeValue;
            theBlob.lastModifiedDate = dateValue;
            theBlob.webkitRelativePath = '';

            return theBlob;
        }
    }
}
