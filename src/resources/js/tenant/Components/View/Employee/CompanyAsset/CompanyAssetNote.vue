<template>
  <div :id="'dropdown-'+rowData.id"
       class="dropdown keep-inside-clicks-open dropdown-note-editor d-inline-block">
    <button type="button"
            class="btn p-0 primary-text-color"
            data-toggle="dropdown">
      <app-icon name="file-text" class="size-18"/>
    </button>
    <div class="dropdown-menu p-primary mt-1">
      <div>
        <p class="text-secondary">
          {{ $t('note') }}
        </p>
        <div class="note note-warning custom-scrollbar p-4 mb-5">
          <p class="text-muted" v-html="rowData.note">
          </p>
        </div>
        <div class="text-right">

          <a href="#"
             class="btn btn-secondary"
             @click.prevent="closeDropDown">
            {{ $t('close') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AnnouncementDescription",
  props: {
    value: {},
    rowData: {},
    tableId: {}
  },
  methods: {
    closeDropDown() {
      $(".dropdown-menu").removeClass('show')
    },
  }
}
</script>

<style scoped lang="scss">
.dropdown {
  .dropdown-menu {
    width: 350px;
    .note {
      overflow-y: auto;
      max-height: 200px;
    }
  }
}
</style>