<template>
    <div class="wizard-steps">
        <ul class="nav nav-pills mb-4" id="pills-tab">
            <li class="nav-item" v-for="(tab,index) in tabs" :key="'nav-link'+index" @click.prevent="disableTab(index)">
                <a class="nav-link"
                   :class="{'active':index===currentTabIndex,'disabled':index>enableLimitIndex}"
                   :id="'pills-'+tab.name+'-tab'" data-toggle="pill"
                   :href="'#'+tab.name+'-'+index"
                   @click="selectedTab(index)">
                    <div class="text-capitalize" :class="[index===0 ? 'tab-step-init': 'tab-step']">
                        {{tab.name}}
                    </div>
                </a>
            </li>
        </ul>
        <hr>
        <div class="tab-content pt-4" id="pills-tabContent" style="min-height: 200px">
            <div v-for="(tab,index) in tabs" :key="'tab-pane'+index"
                 class="tab-pane fade"
                 :class="{'show active':index===currentTabIndex}"
                 :id="tab.name+'-'+index">
                <component :is="tab.component" :props="tab.props" @next="nextTab" @back="prevTab"/>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "FormWizard",
        props: {
            tabs: {
                type: Array,
                required: true
            },
            tabInit: {
                type: Number,
                default: 0
            },
            enableAll: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                currentTabIndex: this.tabInit,
                enableLimitIndex: this.tabInit,
            }
        },
        mounted() {
            if (this.enableAll) {
                this.currentTabIndex = this.tabInit;
                this.enableLimitIndex = this.tabs.length - 1;
            }
            this.currentComponentName();
        },
        methods: {
            nextTab(value) {
                if (value == true) {
                    if (this.tabs.length - 1 > this.currentTabIndex) {
                        if (this.enableLimitIndex === this.currentTabIndex)
                            this.enableLimitIndex++;
                        this.currentTabIndex++;
                        this.currentComponentName();
                    }
                }
            },
            prevTab(value) {
                if (value == true) {
                    if (this.currentTabIndex > 0) {
                        this.currentTabIndex--;
                        this.currentComponentName();
                    }
                }
            },
            selectedTab(value) {
                this.currentTabIndex = value;
                this.currentComponentName();
            },
            currentComponentName() {
                this.$emit('selectedComponentName', this.tabs[this.currentTabIndex].component);
            },
            disableTab(index) {
                if (index > this.enableLimitIndex) {
                    this.$emit('disabledTab', this.tabs[index]);
                }
            }

        }
    }
</script>
