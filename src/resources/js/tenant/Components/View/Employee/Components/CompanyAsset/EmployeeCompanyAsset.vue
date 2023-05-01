<template>
  <div>
    <app-table
        id="employee-company-assets-table"
        :options="options"
        @action="triggerActions"
    />

<!--    <app-company-asset-modal-->
<!--        v-if="isModalActive"-->
<!--        v-model="isModalActive"-->
<!--        :selected-url="selectedUrl"-->
<!--        :editable="isModalEditable"-->
<!--        @close="isModalActive = false"-->
<!--    />-->
  </div>
</template>

<script>
import EmployeeCompanyAssetMixins from "../../../../Mixins/EmployeeCompanyAssetMixins";
import {COMPANY_ASSETS} from "../../../../../Config/ApiUrl";
import HelperMixin from "../../../../../../common/Mixin/Global/HelperMixin";
export default {
  name: "EmployeeCompanyAsset",
  mixins: [HelperMixin, EmployeeCompanyAssetMixins],
  props: {
    props: {
      default: ''
    },
  },
  data() {
    return {
      selectedUrl: '',
      isModalActive: false,
      isModalEditable: false,
    }
  },
  methods: {
    triggerActions(row, action, active) {
      if (action.name === 'preview') {
        this.selectedUrl = `${COMPANY_ASSETS}/${row.id}`;
        this.isModalActive = true;
        this.isModalEditable = false;
      }
    }
  },
}
</script>