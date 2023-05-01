<template>
    <div class="column-content">
        <div class="hour-title">
            <div class="hour-value">
                <span data-toggle="tooltip" data-placement="top" :title="$t('scheduled')">
                    {{ $t('scheduled').charAt(0) }}
                </span>
            </div>
            <div class="hour-value">
                <span data-toggle="tooltip" data-placement="top" :title="$t('worked')">
                    {{ $t('worked').charAt(0) }}
                </span>
            </div>
            <div class="hour-value">
                <span data-toggle="tooltip" data-placement="top" :title="$t('paid_leave')">
                    {{ $t('paid_leave') | titleFilter }}
                </span>
            </div>
            <div class="hour-value">
                <span data-toggle="tooltip" data-placement="top" :title="$t('balance')">
                    {{ $t('balance').charAt(0) }}
                </span>
            </div>
        </div>
        <div class="hour-value">
            <div class="hour-value">{{ attendanceSummery.scheduled || 0 }}</div>
            <div class="hour-value">{{ attendanceSummery.worked || 0 }}</div>
            <div class="hour-value">{{ attendanceSummery.paid_leave || 0 }}</div>
            <div class="hour-value" :class="attendanceSummery.balance.includes('-') ? 'text-warning' : 'text-success'">{{ attendanceSummery.balance || 0 }}</div>
        </div>
    </div>
</template>

<script>

export default {
    name: "AppEmployeeWorkHourType",
    props: {
        attendanceSummery: {
            required: true,
            type: Object,
            default: function () {
                return {};
            }
        }
    },
    data() {
        return {

        }
    },
    filters: {
        titleFilter(str) {
            str = str.replace(/(^\s*)|(\s*$)/gi, "");
            str = str.replace(/[ ]{2,}/gi, " ");
            str = str.replace(/\n /, "\n");
            let titleArray = str.split(' ');
            if (titleArray.length > 1) {
                return (titleArray[0][0] + titleArray[1][0]).toLocaleUpperCase();
            } else {
                return titleArray[0].substring(0, 2).toLocaleUpperCase();
            }
        }
    }
}
</script>