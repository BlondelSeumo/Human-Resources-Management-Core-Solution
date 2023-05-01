<template>
  <div>
    <app-default-button
        :title="$t('view_details')"
        @click="openModal()"
    />

    <modal id="dashboard-announcement-detail-modal"
           size="large"
           v-model="showModal"
           :title="$t('announcement_details')"
           :hide-submit-button="true"
           :cancel-btn-label="$t('close')"
           @input="closeModal">
      <div class="announcement-detail">
        <p class="label">{{ $t('title') }}</p>
        <p class="value">{{ rowData.name }}</p>
      </div>

      <div class="announcement-date-range">
        <div class="date-detail">
          <div class="announcement-detail start-date">
            <p class="label">{{ $t('start_date') }}</p>
            <p class="value">{{ calenderTimeWithMonthSortName(rowData.start_date) }}</p>
          </div>
        </div>

        <div class="date-detail">
          <div class="announcement-detail end-date">
            <p class="label">{{ $t('end_date') }}</p>
            <p class="value">{{ calenderTimeWithMonthSortName(rowData.end_date) }}</p>
          </div>
        </div>
      </div>

      <div class="announcement-detail description">
        <p class="label">{{ $t('description') }}</p>
        <p class="value" v-html="rowData.description"></p>
      </div>

    </modal>
  </div>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {calenderTimeWithMonthSortName} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
  name: "EmployeeAnnouncementDetails",
  mixins: [ModalMixin],
  props: {
    value: {},
    rowData: {},
    tableId: {}
  },
    data(){
      return {
          calenderTimeWithMonthSortName,
      }
    },
  created () {
    this.showModal = false;
  },
  methods: {
    openModal() {
      this.showModal = true;
    },
    closeModal(value) {
      if(value == false)
        this.showModal = false;
    },
  }
}
</script>

<style scoped>
/*resetting all the margins*/
p {
  margin: 0;
}
.announcement-detail{
  margin-bottom: 2rem;
}

.announcement-detail .label {
  margin-bottom: 0.25rem;
}

.announcement-detail .value {
  font-weight: bold;
}

.announcement-date-range {
  display: flex;
  gap: 6rem;
}
</style>