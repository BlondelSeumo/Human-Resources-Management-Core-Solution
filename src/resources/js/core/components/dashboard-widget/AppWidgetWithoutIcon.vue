<template>
    <div class="card card-with-shadow border-0 p-primary dashboard-widgets">
        <div class="widget-data">
            <h6>{{ data.number }}</h6>
            <p>{{ data.label }}</p>
        </div>
    </div>
</template>
<script>
    import {WidgetMixin} from "./mixins/WidgetMixin.js";

    export default {
        name: "AppWidgetWithoutIcon",
        mixins: [WidgetMixin]
    }
</script>
