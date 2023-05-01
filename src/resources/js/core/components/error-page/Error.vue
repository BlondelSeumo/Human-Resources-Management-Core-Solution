<template>
    <div class="row justify-content-center align-items-center h-100 white-base-color">
        <div class="col-12 col-sm-9 col-md-7 col-lg-5 col-xl-6">
            <div class="text-center">
                <img v-if="errorType === '400'" :src="imagesPath('images/error-400.png')" class="img-fluid d-block mx-auto" alt="">
                <img v-else-if="errorType === '401'" :src="imagesPath('images/error-401.png')" class="img-fluid d-block mx-auto"
                     alt="">
                <img v-else-if="errorType === '403'" :src="imagesPath('images/error-403.png')" class="img-fluid d-block mx-auto"
                     alt="">
                <img v-else-if="errorType === '404'" :src="imagesPath('images/error-404.png')" class="img-fluid d-block mx-auto"
                     alt="">
                <img v-else-if="errorType === '500'" :src="imagesPath('images/error-500.png')" class="img-fluid d-block mx-auto"
                     alt="">
                <img v-else-if="errorType === '503'" :src="imagesPath('images/error-503.png')" class="img-fluid d-block mx-auto"
                     alt="">
                <h5 class="my-4">{{ errorType }} - {{ errorTitle }}</h5>
                <p class="text-muted mb-4">
                    {{ errorDescription }}
                </p>
                <a :href="url" class="btn btn-primary">{{ $t('go_back_to_your_page') }}</a>
            </div>
        </div>
    </div>
</template>

<script>
    import ThemeMixin from "../../mixins/global/ThemeMixin";
    import AppFunction from "../../helpers/app/AppFunction";

    export default {
        name: "Error",
        mixins: [ThemeMixin],
        props: {
            errorType: {
                type: String,
                require: true
            },
            errorTitle: {
                type: String,
                default: 'Something went wrong!'
            },
            errorDescription: {
                type: String,
                default: ''
            },
            url: {
                type: String,
                default: '/'
            }
        },
        methods:{
            imagesPath(value){
                return AppFunction.getAppUrl(value);
            }
        }
    }
</script>

<style scoped lang="scss">
    html, body {
        height: 100%;
    }

    img {
        max-width: 400px;
    }
</style>