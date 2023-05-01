<template>
    <div>
        <div class="position-relative min-height-400">
            <app-overlay-loader v-if="preloader"/>
            <div v-if="cards.length>0" class="row" :class="{'loading-opacity': preloader}">
                <div v-for="(card,index) in cards" :key="index" class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
                    <app-template-preview-card
                        :preview-image-key="properties.previewImageKey"
                        :preview-type="properties.previewType"
                        :id="id"
                        :card="card"
                        :default-content-key="properties.defaultContentKey"
                        :custom-content-key="properties.customContentKey"
                        :subject-key="properties.subjectKey"
                        :show-action="properties.showAction"
                        :actions="properties.actions"
                    />
                </div>
            </div>
            <div v-if="cards.length === 0 && !preloader" class="card card-with-search border-0 card-with-shadow">
                <div class="card-body">
                    <app-empty-data-block :message="$t('nothing_to_show_here')"/>
                </div>
            </div>
        </div>
        <div v-if="totalRow> 0 && paginationCardLimit>0 && cards.length>0" class="mt-primary">
            <div class="text-center">
                <app-load-more
                    v-if="totalRow > cardOffset"
                    :preloader="loadMorePreloader"
                    :disabled="loadMoreDisabled"
                    @submit="loadMoreCard"
                />
            </div>
        </div>

    </div>
</template>

<script>
import coreLibrary from '../../helpers/CoreLibrary';

export default {
    name: "AppCardView",
    extends: coreLibrary,
    props: {
        properties: {
            type: Object,
            require: true
        },
        filteredData: {
            type: Object
        },
        searchValue: {
            type: String
        },
        id: {
            type: String,
            require: true
        }
    },
    data() {
        return {
            cards: [],
            paginationCardLimit: 0,
            totalRow: 0,
            cardOffset: 0,
            preloader: false,
            activePaginationPage: 1,
            loadMorePreloader: false,
            loadMoreDisabled: false,
            filteredValues: {},
            search: null
        }
    },
    mounted() {
        this.cardViewInit();
        this.reloadCardView();
    },
    methods: {
        /**
         * init cards for render
         * */
        cardViewInit() {
            this.filteredValues = this.filteredData;
            this.search = this.searchValue;
            this.paginationCardLimit = this.properties.cardLimit ? this.properties.cardLimit : 10;
            this.cards = [];
            this.cardOffset = 0;
            this.getCards();
        },
        /**
         * Get cards from database
         * */
        getCards() {

            let filter = _.cloneDeep(this.filteredValues),
                reqData = {},
                url = this.properties.url;

            filter.per_page = this.paginationCardLimit;
            filter.page = parseInt(this.activePaginationPage);
            filter.search = this.search;
            reqData.params = filter;

            this.setPreloader(true);

            this.axiosGet(
                url,
                reqData
            ).then(response => {
                this.activePaginationPage = response.data.current_page;
                this.cards = [...this.cards, ...response.data.data];
                this.cardOffset = this.cards.length;
                this.totalRow = response.data.total;
            }).catch(({response}) => {

                // trigger after error

            }).finally(() => {
                this.loadMorePreloader = false;
                this.setPreloader(false);
            });
        },
        /**
         * for reload cards after filtering
         * */
        reloadCardView() {
            let name = 'reload-' + this.id;
            this.$hub.$on(name, (value = true) => {
                if (value) {
                    this.activePaginationPage = 1;
                    this.cardViewInit();
                }
            });
        },
        /**
         * for loaded more cards component
         * */
        loadMoreCard() {
            this.loadMorePreloader = true;
            this.activePaginationPage = Number(this.activePaginationPage) + Number(1);
            // this.cardOffset += this.cardLimit;
            this.getCards();
        },
        /**
         * @pram = boolean
         * set preloader for data table
         * */

        setPreloader(val) {
            this.preloader = val;
        }
    }
}
</script>
