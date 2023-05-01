<template>
    <div v-if="type === 'dropdown'"
         class="dropdown d-inline-block btn-dropdown "
         :class="{'keep-inside-clicks-open': keepOpen}"
         :title="title">
        <button type="button"
                class="btn btn-light dropdown-toggle border-right"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
            <app-icon :name="icon"/>
        </button>
        <div class="dropdown-menu py-0" :class="dropdownMenuClass">
            <slot></slot>
        </div>
    </div>

    <div v-else-if="type === 'button'"
         class="dropdown d-inline-block btn-dropdown"
         :title="title">
        <button type="button"
                class="btn btn-light dropdown-toggle border-right"
                @click="$emit('action')">
            <app-icon :name="icon"/>
        </button>
    </div>
</template>

<script>
export default {
    name: "ContextButton",
    props: {
        title: {
            type: String
        },
        type: {
            default() {
                return 'dropdown'
            }
        },
        icon: {
            require: true,
        },
        dropdownMenuClass: {
            type: String
        },
        keepOpen: {
            default() {
                return false;
            }
        }
    }
}
</script>