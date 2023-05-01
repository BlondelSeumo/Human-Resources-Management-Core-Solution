<template>
    <div class="dropzone" :class="{'disabled':data.disabled}" :id="inputFieldId" :name="name">
        <div class="dz-default dz-message">
            <app-icon class="text-primary" name="upload-cloud"/>
            <h5 class="title text-muted text-center">{{ $t('drag_and_drop') }}</h5>
            <p class="p-0 m-0 text-center">{{ $t('or') }}</p>
            <p class="text-primary text-center">{{ $t('browse') }}</p>
        </div>
    </div>
</template>

<script>
    import Dropzone from "dropzone/dist/dropzone";
    import {InputMixin} from "./mixin/InputMixin.js";
    import {FileUploaderMixin} from './mixin/FileUploaderMixin.js';

    Dropzone.autoDiscover = false;
    Dropzone.autoProcessQueue = false;

    export default {
        name: "FileUploader",

        mixins: [InputMixin, FileUploaderMixin],

        data() {
            return {
                url: "files/store",
            };
        },
        watch: {
            value: function (val) {
                if(!val || val.length===0){
                    const dropzoneObj = Dropzone.forElement("#" + this.inputFieldId);
                    dropzoneObj.removeAllFiles(true);
                }
            }
        },
        mounted() {
            //dropzone configuration
            this.$nextTick(() => {
                const myDropzone = new Dropzone("#" + this.inputFieldId, {
                    autoQueue: false,
                    url: this.url,
                    // maxFiles: this.data.maxFiles,
                    init:  () => {
                        this.setInitValue();
                    },
                    // paramName:this.data.name,
                    dictDefaultMessage: "Drop your file....",
                    // previewTemplate: customPreviewTemplate,
                    addRemoveLinks: true
                    /* The rest of your configuration options */
                });

                // implement event listeners
                //https://www.dropzonejs.com/#events

                myDropzone.on("drop",(file) => {
                    setTimeout(() => {
                        this.changed();
                    }, 300);
                });
                myDropzone.on("sending", (file, responseText) => {

                });
                myDropzone.on("addedfile", (file) => {
                    setTimeout(() => {
                        this.changed();
                    }, 300);
                });

                myDropzone.on("removedfile", (file) => {
                    setTimeout(() => {
                        this.changed();
                    }, 300);
                });
                myDropzone.on("maxfilesexceeded",  (file, responseText) => {
                });

                myDropzone.on("maxfilesreached",  (file, responseText) => {
                });
                myDropzone.on("thumbnail",  (file, responseText) => {
                });
                myDropzone.on("processing",  (file, responseText) => {
                });
                myDropzone.on("uploadprogress",  (file, responseText) => {
                });
                myDropzone.on("uploadprogress",  (file, responseText) => {
                });
                myDropzone.on("complete",  (file, responseText) => {
                });
                myDropzone.on("canceled",  (file, responseText) => {
                });

                myDropzone.on("success",  (file, responseText) => {
                });
                myDropzone.on("error",  (file, responseText) => {
                });

            });
        },
        methods: {
            changed() {
                const dropzoneObj = Dropzone.forElement("#" + this.inputFieldId);

                this.removeFirstElementFromNode(dropzoneObj);
                this.getFiles(dropzoneObj);
            },

            getFiles(dropzoneObj){
                this.$emit("input", dropzoneObj.getAcceptedFiles());
            },

            setInitValue(){
                const dropzoneObj = Dropzone.forElement("#" + this.inputFieldId);

                if(this.value?.length > 0){

                    this.value.map((item, index) => {

                        this.getDataUrl(item)
                            .then(data => {
                                data.accepted = true;
                                data.status = Dropzone.ADDED;
                                data.height = 400;
                                data.width = 400;
                                data.dataURL = '';

                                dropzoneObj.displayExistingFile(data, item);
                                dropzoneObj.files.push(data);
                                this.changed();
                        });
                    });
                }
            },
            removeFirstElementFromNode(dropzoneObj){

                if(dropzoneObj.files.length > 1 && this.data.maxFiles == 1) {
                    dropzoneObj.files.splice(0, 1);
                    $('.dz-image-preview').each(function(index, item){
                        if(index == 0) $(this).remove();
                    })
                }

            }

        },
    };
</script>
