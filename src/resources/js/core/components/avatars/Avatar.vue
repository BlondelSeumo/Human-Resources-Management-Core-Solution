<template>
    <div :class="avatarClass">
        <img v-if="img" class="rounded-circle" :class="{'avatar-shadow':shadow,'avatar-bordered':border}" :src="img"
             :alt="altText ? altText : $t('not_found')">
        <div v-else class="no-img rounded-circle" :class="{'avatar-shadow':shadow,'avatar-bordered':border}">
            {{title? title: 'AV' | titleFilter}}
        </div>
        <span v-if="status" class="status" :class="'bg-'+status"/>
    </div>
</template>

<script>
    export default {
        name: "AppAvatar",
        props: {
            avatarClass: {
                type: String,
                require: true
            },
            img: {
                type: String,
                require: true
            },
            altText: {
                type: String
            },
            shadow: {
                type: Boolean,
                default: false
            },
            title: {
                type: String
            },
            status: {
                type: String,
                default: null
            },
            border: {
                type: Boolean,
                default: false
            }
        },
        filters: {
            titleFilter(str) {
                str = str.replace(/(^\s*)|(\s*$)/gi, "");
                str = str.replace(/[ ]{2,}/gi, " ");
                str = str.replace(/\n /, "\n");
                let titleArray = str.split(' ');
                if (titleArray.length > 1) {
                    return (titleArray[0][0] + titleArray[1][0]).toLocaleUpperCase();
                } else {
                    return titleArray[0].substring(0, 2).toLocaleUpperCase();
                }
            },
        }
    }
</script>
