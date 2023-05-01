<template>
    <div :class="`${ loading ? 'px-primary py-primary' : ''}`">
        <app-overlay-loader v-if="loading"/>
        <template v-else>
            <div class="form-group form-group-with-search px-primary py-half-primary">
            <span class="form-control-feedback">
                <app-icon :name="'search'"/>
            </span>
                <input type="text"
                       class="form-control"
                       v-model="searchValue"
                       :placeholder="$t('search')"/>
            </div>
            <div class="dropdown-divider my-0"/>
            <div class="dropdown-search-result-wrapper custom-scrollbar">
                <a v-for="(list, index) in searchAbleOption"
                   :key="index"
                   class="dropdown-item cursor-pointer text-truncate"
                   @click.prevent="checkList(list.id)">
                    {{ list.name }}
                    <span class="check-sign float-right" v-show="list.id === checkedItem">
                    <i class="fa fa-check"/>
                </span>
                </a>
            </div>
        </template>
    </div>
</template>

<script>
import {axiosGet} from "../../Helper/AxiosHelper";

export default {
    name: "ListDropdown",
    props: {
        value: {},
        url: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            searchValue: '',
            checkedItem: null,
            list: [],
            loading: false,
        }
    },
    created() {
        this.getListItem();
    },
    methods: {
        checkList(id) {
            this.checkedItem = id
            this.$emit('input', this.checkedItem);
        },
        getListItem() {
            this.loading = true;
            axiosGet(this.url).then(({data}) => {
                this.list = data;
                this.loading = false;
            })
        },
    },
    computed: {
        searchAbleOption() {
            if (this.searchValue) {
                return this.list.filter(option => {
                    return option.name.toLowerCase().includes(this.searchValue.toLowerCase())
                })
            }
            return this.list;
        },
        searchableOptionWatcher() {
            return !!this.searchAbleOption.length
        }
    },
    watch: {
        searchableOptionWatcher: function (flag) {
            this.$emit('filteredFlag', flag)
        }
    }
}
</script>
