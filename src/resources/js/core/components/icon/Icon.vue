<template>
    <i v-if="icon() && icon().iconType === 'fontawesome' " :class="icon().iconName"/>
    <i v-else :data-feather="name"/>
</template>

<script>
import CoreLibrary from "../../helpers/CoreLibrary";

export default {
    name: "AppIcon",
    extends: CoreLibrary,
    props: {
        name: {
            default: '',
            type: String,
        },
        iconClass: {
            default: '',
            type: String,
        }
    },
    data() {
        return {
            icon: () => {
                return this.getIcons().find(item => {
                    return item.name === this.name;
                });
            }
        }
    },
    methods: {
        getIcons() {
            return [
                {
                    name: 'sort',
                    iconType: 'fontawesome',
                    iconName: 'fas fa-sort',
                },
                {
                    name: 'pinterest',
                    iconType: 'fontawesome',
                    iconName: 'fab fa-pinterest-p',
                },
                {
                    name: 'dribble',
                    iconType: 'fontawesome',
                    iconName: 'fab fa-dribbble',
                },
                {
                    name: 'behance',
                    iconType: 'fontawesome',
                    iconName: 'fab fa-behance',
                },
                {
                    name: 'google-plus',
                    iconType: 'fontawesome',
                    iconName: 'fab fa-google-plus-g',
                },
                {
                    name: 'skype',
                    iconType: 'fontawesome',
                    iconName: 'fab fa-skype',
                },
                {
                    name: 'paint-brush',
                    iconType: 'fontawesome',
                    iconName: 'fas fa-paint-brush',
                },
                {
                    name: 'palette',
                    iconType: 'fontawesome',
                    iconName: 'fas fa-palette',
                }
            ]
        }
    }
}
</script>
