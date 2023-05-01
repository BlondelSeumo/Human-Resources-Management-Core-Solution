import coreLibrary from "../../../helpers/CoreLibrary";

export const TabMixin = {
    extends: coreLibrary,
    props: {
        tabs: {
            type: Array,
            require: true
        }
    },
    data() {
        return {
            queryStringKey: 'tab',
            queryStringValue: '',
            currentIndex: 0,
            componentName: '',
            componentId: '',
            componentProps: '',
            componentTitle: '',
            componentButton: {}
        }
    },
    computed: {
        filteredTab() {
            return this.tabs.filter(tab => (this.isUndefined(tab.permission) || tab.permission !== false))
        }
    },
    mounted() {
        this.queryStringValue = this.getQueryStringValue(this.queryStringKey);
        this.setTabByName();
    },
    methods: {
        setQueryString(name) {
            const pageTitle = window.document.title;
            window.history.pushState("", pageTitle, `?${this.queryStringKey}=${name}`);
        },

        setTabByName() {
            let currentTab = {},
                result = {};
            result = this.filteredTab.find((item, index) => {
                if (item.name == this.queryStringValue) {
                    this.currentIndex = index;
                    return item;
                }
            });

            currentTab = result ? result : this.filteredTab[this.currentIndex];

            this.loadComponent(currentTab, this.currentIndex);
        },

        loadComponent(tab, index) {
            this.currentIndex = index;
            this.componentTitle = tab.title;
            this.componentId = tab.name + '-' + index;
            this.componentName = tab.component;
            this.componentProps = tab.props;
            if (!this.isUndefined(tab.headerButton)) {
                this.componentButton = tab.headerButton;
            } else this.componentButton = {};

            this.setQueryString(tab.name);
        }
    }
};
