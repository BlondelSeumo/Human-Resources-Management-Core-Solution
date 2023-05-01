<template>
    <div>
        <div class="dropdown d-inline-block btn-dropdown">
            <button type="button"
                    class="btn dropdown-toggle ml-0 mr-2"
                    :class="btnClass"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                {{ title }}
            </button>
            <div class="dropdown-menu">
                <template v-for="(option, index) in list">
                    <a v-if="option.type==='link'" :href="option.url"
                       class="dropdown-item d-flex align-items-center p-3 cursor-pointer">
                        <app-icon
                            v-if="option.icon"
                            stroke-width="1"
                            :name="option.icon"
                            width="16"
                            height="16"
                            class="mr-3"/>
                        {{ option.name }}
                    </a>
                    <a v-if="option.type==='button' || option.type === 'modal'" href="#"
                       @click.prevent="triggerAction(option)"
                       class="dropdown-item d-flex align-items-center p-3 cursor-pointer">
                        <app-icon
                            v-if="option.icon"
                            stroke-width="1"
                            :name="option.icon"
                            width="16"
                            height="16"
                            class="mr-3"/>
                        {{ option.name }}
                    </a>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AppDropdownMenu",
    props: {
        title: {
            type: String,
            required: true
        },
        btnClass: {
            type: String,
            default: 'btn-success'
        },
        list: {
            type: Array,
            required: true
        }
    },
    methods: {
        triggerAction(action) {
            this.$emit('action', action);
        }
    }
}
</script>
