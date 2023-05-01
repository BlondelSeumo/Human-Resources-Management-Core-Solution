<template>
    <div class="media align-items-center">
        <app-avatar :title="mediaTitle" avatar-class="avatars-w-50" :img="imgUrl"
                    :alt-text="altText"/>
        <div class="media-body ml-3">
            {{ mediaTitle }}
            <p class="text-muted font-size-90 mb-0">{{ mediaSubtitle }}</p>
        </div>
    </div>
</template>

<script>
    export default {
        name: "MediaObject",
        props: {
            imgUrl: {
                type: String,
                require: true
            },
            altText: {
                type: String,
            },
            mediaTitle: {
                type: String,
                require: true
            },
            mediaSubtitle: {
                type: String,
                require: true
            }
        },
    }
</script>
