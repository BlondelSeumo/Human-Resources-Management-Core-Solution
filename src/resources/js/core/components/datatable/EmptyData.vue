<template>
    <div class="no-data-found-wrapper text-center p-primary">
        <img :src="img" class="mb-primary" alt="">
        <p class="mb-0 text-center">{{ $t('nothing_to_show_here') }}</p>
        <p class="mb-0 text-center text-secondary font-size-90">{{ message }}</p>
        <p class="mb-0 text-center text-secondary font-size-90">{{ $t('thank_you') }}</p>
    </div>
</template>

<script>
    import AppFunction from "../../helpers/app/AppFunction";

    export default {
        name: "EmptyData",
        props: {
            img: {
                type: String,
                default: AppFunction.getAppUrl('images/no_data.svg'),
            },
            message: {
                type: String,
                required: true
            }
        }
    }
</script>
