<template>
    <div class="form-group form-group-with-search d-flex align-items-center">
        <span :key="'search'"
              class="form-control-feedback">
            <app-icon name="search"/>
        </span>
        <input
            type="text"
            class="form-control"
            v-model="searchValue"
            :placeholder="$t('search')"
            @keydown.enter.prevent="getSearchValue"
        />
    </div>
</template>

<script>
    export default {
        name: "AppSearch",
        data() {
            return {
                searchValue: '',
            }
        },
        watch: {
            searchValue: function (value) {
                if (value === '')
                    this.getSearchValue();
            }
        },
        methods: {
            getSearchValue() {
                this.$emit('input', this.searchValue);
            },
        }
    }
</script>
