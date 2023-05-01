<template>
    <div class="single-record mb-primary">
        <div v-if="name !== 'joining_date' && dataIsNull">
            <p class="font-size-90 mb-0">{{ $t('not_yet_added') }}</p>
        </div>

        <div v-else>
            <p class="font-size-90 mb-0">{{ title }}</p>
            <div v-if="singleDate" class="primary-text-color font-size-80 mb-0">
                {{ name === 'joining_date' ? '': $t('present') }}
            </div>

            <div v-else>
                <p v-if="name === 'work_shift' && data.upcoming" class="mb-0">
                    <span class="font-size-80 text-warning">
                        {{ `${$t('upcoming')}` }}
                    </span>
                    <span class="font-size-80">
                        {{ `(${inDateAfterNow(data.pivot.start_date) ? startDate : $t('tomorrow')})` }}
                    </span>
                </p>
                <p v-else class="mb-0">
                    <span class="font-size-80 text-muted">{{ startDate }}</span>
                    -
                    <span class="font-size-80" :class="!!data.pivot.end_date ? 'text-muted': 'primary-text-color'">
                        {{ endDate }}
                    </span>
                </p>
                <p v-if="description" class="font-size-80 text-muted mb-0"> {{ description }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import {calenderTime} from "../../../../../../../common/Helper/Support/DateTimeHelper";
import {isAfterNow} from "../../../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "BlockContent",
    props: {
        data: {},
        name: {},
    },
    methods: {
        formatDate(date){
            return date ? calenderTime(date, false) : 'null'
        },

        inDateAfterNow(date){
            return isAfterNow(date);
        }
    },
    computed: {
        dataIsNull(){
            return !this.data || (typeof this.data === 'object' && Object.keys(this.data).length === 0)
        },
        startDate(){
            return  this.formatDate(this.data.pivot?.start_date)
        },
        endDate(){
            return  !!this.data.pivot.end_date ? this.formatDate(this.data.pivot.end_date) : this.$t('present')
        },
        description(){
            return this.data.pivot?.description;
        },
        title(){
            if (this.name === 'role' && this.data){
                let title = '';
                this.data.map((data) => {
                    title += ` ${data.name},`
                })
                return title.replace(/,+$/, '');
            }

            if (this.name === 'joining_date'){
                return this.data?.joining_date ? calenderTime(this.data?.joining_date, false) : this.$t('not_yet_joined')
            }

            return this.data?.name;
        },
        singleDate(){
            return this.name === 'joining_date' || this.name === 'role'
        }
    }
}
</script>

<style scoped>

</style>