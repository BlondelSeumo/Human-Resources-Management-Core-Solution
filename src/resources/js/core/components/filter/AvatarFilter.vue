<template>
    <div class="single-filter avatars-filter">
        <div v-if="list.length > 10"
             class="indicator-left"
             @click="moveCarousel(-1)"
             :disabled="atHeadOfList"/>
        <div class="avatar-group overflow-hidden">
            <div class="avatar-carousel-wrapper" :style="{ transform: 'translateX' + '(' + currentOffset + 'px' + ')'}">
                <div class="position-relative d-inline-block single-avatar"
                     v-for="(avatar, index) in list"
                     @click.prevent="selectedAvatar(avatar.id)">
                    <img v-if="getAvatarUrl(avatar)"
                         :src="getAvatarUrl(avatar)"
                         class="rounded-circle avatar-bordered"
                         :class="{'active-avatar': avatar.id === selectedAvatarId}"
                         data-toggle="tooltip"
                         data-placement="top"
                         :title="avatar[listValueField]"
                         :alt="avatar[listValueField]"/>
                    <div v-else
                         class="no-img rounded-circle avatar-bordered"
                         :class="{'active-avatar': avatar.id === selectedAvatarId}"
                         data-toggle="tooltip"
                         data-placement="top"
                         :title="avatar[listValueField]">
                        {{ avatar[listValueField] ? avatar[listValueField] : 'AV' | titleFilter }}
                    </div>
                    <span class="status" :class="'bg-'+avatar.status"/>
                </div>
            </div>
        </div>
        <div v-if="list.length > 10"
             class="indicator-right"
             @click="moveCarousel(1)"
             :disabled="atEndOfList"/>
    </div>
</template>

<script>
import {FilterMixin} from "./mixins/FilterMixin";
import CoreLibrary from "../../helpers/CoreLibrary";

export default {
    name: "AvatarFilter",
    mixins: [FilterMixin],
    extends: CoreLibrary,
    props: {
        list: {
            type: Array,
            require: true
        },
        label: {
            type: String,
            default: ''
        },
        listValueField: {
            type: String,
            default: 'name'
        },
        imgRelationship: {
            type: String,
            default: null
        },
        imgKey: {
            type: String,
            default: null
        },
        active: {
            default: 0
        }
    },
    data() {
        return {
            windowSize: 10,
            currentOffset: 0,
            paginationFactor: 27,
            selectedAvatarId: '',
            initActiveId: this.active,
        }
    },
    computed: {
        atEndOfList() {
            return this.currentOffset <= (this.paginationFactor * -1) * (this.list.length - this.windowSize);
        },
        atHeadOfList() {
            return this.currentOffset === 0;
        }
    },
    methods: {
        moveCarousel(direction) {
            if (direction === 1 && !this.atEndOfList) {
                this.currentOffset -= this.paginationFactor;
            } else if (direction === -1 && !this.atHeadOfList) {
                this.currentOffset += this.paginationFactor;
            }
        },
        getAvatarUrl(avatar) {
            if (avatar.img) return avatar.img;
            if (!this.imgRelationship || !avatar[this.imgRelationship]) return false;
            return avatar[this.imgRelationship][this.imgKey]
        },
        selectedAvatar(id) {
            this.selectedAvatarId = id;
            this.returnValue(id)
        },
        init(active) {
            this.selectedAvatarId = active;
            const index = this.list.findIndex((el) => {
                return Number(el.id) === Number(active);
            })

            if (index > 9) {
                return this.currentOffset = - (index * ((this.list.length - 1) === index ? 25.8 : 27 ));
            }

            this.currentOffset = 0;
        }
    },
    mounted() {
        this.initializeTooltip();
        this.$hub.$on('clearAllFilter-' + this.tableId, () => {
            this.init(this.initActiveId);
        });
    },
    watch: {
        active: {
            handler: function (active) {
                this.init(active)
            },
            immediate: true
        },
    }
}
</script>
