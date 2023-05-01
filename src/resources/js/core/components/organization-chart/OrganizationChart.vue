<template>
    <div dir="ltr">
        <organization-chart :datasource="chartData" @node-click="clickedNote" :pan="druggable"></organization-chart>
    </div>
</template>

<script>
    import OrganizationChart from 'vue-organization-chart'
    import 'vue-organization-chart/dist/orgchart.css'
    export default {
        name: "AppOrganizationChart",
        components: {
            OrganizationChart
        },
        props: {
            chartData: {
                type: Object,
                require: true
            },
            height: {
                type: Number,
                default: 530
            },
            druggable: {
                type: Boolean,
                default: false,
            }
        },
        mounted() {
            $(".orgchart-container").css({
                "height": this.height+"px"
            });
        },
        methods: {
            clickedNote(value){
                this.$emit('selected-item', value);
            }
        }
    }
</script>
