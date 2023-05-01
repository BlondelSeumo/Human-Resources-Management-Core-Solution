<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb p-0 d-flex align-items-center mb-primary">
            <li class="breadcrumb-item page-header d-flex align-items-center">
                <h4 class="mb-0">{{ textTruncate(pageTitle, 40) }}</h4>
            </li>
            <li v-if="button" class="ml-2">|</li>
            <li v-if="button" class="">
                <a href="" class="btn btn-link text-primary pl-2"
                   @click.prevent="goBack">{{ button.label ? button.label : 'Back' }}</a>
            </li>
        </ol>
    </nav>
</template>

<script>
export default {
    name: "AppBreadcrumb",
    props: {
        pageTitle: {
            required: true,
            type: String
        },
        directory: {
            required: false,
            type: [String, Array]
        },
        icon: {
            type: String
        },
        button: {
            type: Object
        }
    },
    computed: {
        directories() {
            if (this.directory) {
                return typeof this.directory === 'string' ? [this.directory] : this.directory;
            }
            return null;
        }
    },
    methods: {
        goBack() {
            if (!this.button.url) {
                history.back();
            } else location.replace(this.button.url);
        },
        textTruncate(str, length = 50, ending = '...') {
            if (str.length > length) {
                return str.substring(0, length - ending.length) + ending;
            } else {
                return str;
            }
        }
    }
}
</script>
