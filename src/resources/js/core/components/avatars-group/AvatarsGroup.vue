<template>
    <div>
        <div v-if="!extended"
             class="avatar-group"
             :class="avatarGroupClass"
             @click.prevent="extended = true">
            <template v-for="(avatar,index) in avatars" v-if="index < 5">
                <img v-if="avatar.img"
                     :src="avatar.img"
                     class="rounded-circle"
                     :class="{'avatar-bordered': border, 'avatar-shadow': shadow}"
                     :alt="avatar.alt ? avatar.alt : $t('not_found')"/>
                <div v-else class="no-img rounded-circle" :class="{'avatar-bordered': border, 'avatar-shadow': shadow}">
                    {{ avatar.title ? avatar.title : 'AV' | titleFilter }}
                </div>
            </template>
            <a v-if="avatars.length > 5" class="pl-3 text-muted">
                +{{ avatars.length - 5 }} {{ $t('more') }}
            </a>
        </div>
        <div v-else class="row animate__animated animate__fadeIn">
            <div class="col-12 d-flex justify-content-between">
                <div class="avatar-group-extended">
                    <div v-for="(avatar,index) in avatars" :key="index" class="media d-flex align-items-center pt-2">
                        <div :class="avatarGroupClass">
                            <img class="rounded-circle"
                                 v-if="avatar.img"
                                 :class="{'avatar-bordered':border, 'avatar-shadow': shadow}"
                                 :src="avatar.img"
                                 :alt="avatar.alt ? avatar.alt : $t('not_found')">
                            <div v-else class="no-img rounded-circle"
                                 :class="{'avatar-bordered': border, 'avatar-shadow': shadow}">
                                {{ avatar.title ? avatar.title : 'AV' | titleFilter }}
                            </div>
                        </div>
                        <div class="media-body ml-3">
                            {{ avatar.title }}
                            <p class="text-muted font-size-90 mb-0">{{ avatar.subTitle }}</p>
                        </div>
                    </div>
                </div>
                <a class="mt-4" href="#" @click.prevent="extended = false">
                    <app-icon :name="'maximize-2'"/>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AvatarsGroup",
    props: {
        avatarGroupClass: {
            type: String,
            default: 'avatars-group-w-50'
        },
        avatars: {
            type: Array,
            required: true
        },
        shadow: {
            type: Boolean,
            default: false
        },
        border: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            extended: false
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
