<template>
    <span v-if="icon == 'no-sort'" key="no-sort" class="d-flex flex-column small-sort-icon">
            <app-icon name="arrow-up"/>
            <app-icon name="arrow-down"/>
    </span>
    <span v-else :key="icon+'-sort'">
        <app-icon :key="'arrow-'+icon" :name="'arrow-'+icon"/>
    </span>
</template>

<script>
    export default {
        name: "Sort",
        props:{
            icon: {
                required: true,
                type: String
            }
        }
    }
</script>
