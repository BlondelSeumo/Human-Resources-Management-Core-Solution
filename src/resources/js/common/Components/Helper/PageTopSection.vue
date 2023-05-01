<template>
    <div class="row">
        <div :class="`col-sm-12 col-md-${hideButton ? '12' : '6'}`">
            <app-breadcrumb :page-title="title" :directory="directory" :icon="icon"/>
        </div>
        <div class="col-sm-12 col-md-6" v-if="!hideButton">
            <div class="float-md-right mb-3 mb-sm-3 mb-md-0">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "PageTopSection",
    props: {
        title: {
            required: true,
            type: String
        },
        directory: {
            required: false,
            type: String
        },
        icon: {
            type: String
        },
        hideButton: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<style scoped>

</style>
