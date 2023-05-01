<template>
    <div class="card card-with-shadow border border-0 py-primary">
        <div class="circular-progress-bar">
            <div class="progress" :data-percentage="parseInt(data.number)">
                <span class="progress-left">
                    <span class="progress-bar"/>
                </span>
                <span class="progress-right">
                    <span class="progress-bar"/>
                </span>
                <div class="progress-value">
                    <h6 class="m-0">
                        {{ data.number }}%
                    </h6>
                </div>
            </div>
            <small class="mt-3 text-center d-block">{{ data.label }}</small>
        </div>
    </div>
</template>

<script>
    import {WidgetMixin} from "./mixins/WidgetMixin.js";

    export default {
        name: "AppWidgetWithCircle",
        mixins: [WidgetMixin]
    }
</script>
