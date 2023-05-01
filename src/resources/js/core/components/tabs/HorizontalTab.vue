<template>
    <div class="horizontal-tab">
        <div class="card card-with-shadow border-0">
            <nav>
                <div class="nav nav-tabs">
                    <a v-for="(tab, index) in filteredTab" :key="index"
                       class="nav-item p-primary text-capitalize"
                       :class="{'active':currentIndex == index}"
                       data-toggle="tab" :href="'#'+tab.name+'-'+index"
                       @click.prevent="loadComponent(tab,index)">
                        <span v-if="tab.icon" class="mr-2"><app-icon :name="tab.icon"/></span>{{tab.name}}
                    </a>
                </div>
            </nav>
            <div class="tab-content p-primary">
                <div class="tab-pane fade show active" :id="componentId">
                    <component :is="componentName" :props="componentProps"></component>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import {TabMixin} from './mixins/TabMixin';

    export default {
        name: "HorizontalTab",
        mixins: [TabMixin],
    }
</script>
