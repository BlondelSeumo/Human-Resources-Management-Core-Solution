<template>
    <div>
        <app-widget-with-icon v-if="type === 'app-widget-with-icon'" :data="$props"/>
        <app-widget-without-icon v-else-if="type === 'app-widget-without-icon'" :data="$props"/>
        <app-widget-with-circle v-else-if="type === 'app-widget-with-circle'" :data="$props"/>
    </div>
</template>

<script>
	import AppWidgetWithIcon from "./AppWidgetWithIcon";
    import AppWidgetWithoutIcon from "./AppWidgetWithoutIcon";
    import AppWidgetWithCircle from "./AppWidgetWithCircle";

	export default {
		name: "Index",
		components: {
            AppWidgetWithIcon,
            AppWidgetWithoutIcon,
            AppWidgetWithCircle,
        },
		props: {
			type: {
                required: true,
                type: String
            },
			label: {
				type: String,
				required: true,
			},
			number: {
				required: true,
			},

			icon: {
				type: String,
			},
		}
	}
</script>