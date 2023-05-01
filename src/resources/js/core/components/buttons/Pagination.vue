<template>
    <nav>
        <ul class="pagination justify-content-center justify-content-md-end mb-0">
            <li :class="{'disabled': activePage <= 1}" class="page-item">
                <a class="page-link border-0" href="#" @click.prevent="prevArrow()" aria-label="Previous">
                    <app-icon :name="'arrow-left'"/>
                </a>
            </li>
            <li v-for="(page, index) in paginationPages" :key="index" class="page-item" v-if="page+addition<=totalPage">
                <a class="page-link border-0" :class="{'active disabled': activePage === (page+addition)}" href="#"
                   @click.prevent="activated((page+addition))">{{ (page + addition) }}</a>
            </li>

            <li :class="{'disabled': activePage >= totalPage}" class="page-item">
                <a class="page-link border-0 align-content-center" href="#" aria-label="Next"
                   @click.prevent="nextArrow()">
                    <app-icon :name="'arrow-right'" class="menu-arrow"/>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    name: "AppPagination",
    props: {
        totalRow: {
            type: Number,
            require: true
        },
        rowLimit: {
            type: Number,
            require: true
        },
        maxPage: {
            type: Number,
            default: 5
        }
    },
    mounted() {
        this.$hub.$on('resetPaginationState', (value) => {
            if (value === 1) this.addition = 0;
            this.activePage = value;
        });
    },
    data() {
        return {
            activePage: 1,
            addition: 0,
        }
    },
    computed: {
        totalPage: function () {
            return Math.ceil(this.totalRow / this.rowLimit);
        },
        paginationPages: function () {
            if (this.maxPage >= this.totalPage) {
                return this.totalPage
            } else {
                return this.maxPage;
            }
        }
    },
    methods: {
        activated(page) {
            this.activePage = page;
            this.activatedPage();
        },
        nextArrow() {
            if (this.activePage === this.paginationPages + this.addition) {
                this.addition += this.paginationPages;
                this.activePage = this.addition + 1;
            } else {
                this.activePage++;
            }
            this.activatedPage();
        },
        prevArrow() {
            if (this.activePage > this.addition) {
                this.activePage--;
            }
            if (this.activePage === this.addition) {
                this.addition -= this.paginationPages;
            }
            this.activatedPage();
        },
        activatedPage() {
            this.$emit('submit', this.activePage);
        }
    }

}
</script>
