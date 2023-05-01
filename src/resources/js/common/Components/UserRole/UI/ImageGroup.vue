<template>
    <app-avatars-group :avatars-group-class="avatarsGroupClass"
                       :name="avatarsGroupName+index"
                       :avatars="avatars"
                       :border="true"
                       :shadow="true"/>
</template>

<script>
export default {
    name: "ImageGroup",
    props: {
        value: {},
        rowData: {},
        index: {}
    },
    data() {
        return {
            avatarsGroupClass: 'avatars-group-w-50',
            avatarsGroupName: 'exampleAvatarsGroup',
        }
    },
    computed: {
        avatars() {
            if (!this.value)
                return [];
            return this.value.map(item => {
                return {
                    img: item.profile_picture ? item.profile_picture.full_url : undefined,
                    alt: item.full_name || '',
                    title: item.full_name || '',
                    subTitle: item.email
                };
            })
        }
    },
}
</script>
