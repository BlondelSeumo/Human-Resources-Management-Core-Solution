<template>
  <div v-if="error.isValid === false" :key="'error'">
    <small class="text-danger">{{error.message}}</small>
  </div>
</template>

<script>
export default {
  name: "Error",
  props: {
    data: {
      type: Object
    }
  },
  computed: {
    error() {
      let field = this.data,
          name = this.$parent.name,
          item = this.$parent.mixinInstance.fieldStatus[name];


      if(item){
        return item;
      }
              
      return {
        isValid: true,
        message: "true"
      };
    }
  }
};
</script>
