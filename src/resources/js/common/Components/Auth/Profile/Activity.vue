<template>
    <div class="content">
        <app-table id="activity-table" :options="options"/>
    </div>
</template>

<script>
    import CoreLibrary from "../../../../core/helpers/CoreLibrary.js";
    import {formatDateToLocal} from "../../../Helper/Support/DateTimeHelper";
    import {ucFirst} from '../../../Helper/Support/TextHelper'

    export default {
        name: "Activity",
        extends: CoreLibrary,
        components: {
            'app-activity-details': require('./Component/ActivityDetails').default
        },
        props: {
            props: {

            }
        },
        data(){
            return{
                options: {
                    url: this.props?.id ? `admin/user/activities/${this.props.id}` :'admin/user/activity-log',
                    showHeader: true,
                    tableShadow: false,
                    showManageColumn: false,
                    tablePaddingClass: 'px-0 py-primary',
                    columns: [
                        {
                            title: this.$t('title'),
                            type: 'text',
                            key: 'description',
                        },
                        {
                            title: this.$t('name'),
                            type: 'custom-html',
                            key: 'subject',
                            modifier: (subject, activity) => {
                                if (subject)
                                    return (ucFirst(this.$t(subject.name)) || subject.full_name) ||
                                        subject.subject;
                                let attributes = this.$optional(activity, 'properties', 'attributes');
                                let old = this.$optional(activity, 'properties', 'old');
                                if (attributes && Object.keys(attributes).length) {
                                    return (attributes.name || attributes.full_name) || attributes.subject;
                                }else if(old && Object.keys(old).length){
                                    return (old.name || old.full_name) || old.subject;
                                }
                            }
                        },
                        {
                            title: this.$fieldTitle('time'),
                            type: 'custom-html',
                            key: 'created_at',
                            modifier: created_at => formatDateToLocal(created_at, true)
                        }
                    ],
                    filters: [

                    ],
                    paginationType: "loadMore",
                    responsive: true,
                    rowLimit: 10,
                    showAction: false,
                    orderBy: 'desc',
                }
            }
        }
    }
</script>
