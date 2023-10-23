<template>
  <div>
    <FormSearchComponent :step="step" :loading="loading" />
  </div>
</template>
<script>
// components
import FormSearchComponent from './components/form-search.vue'

export default {
  name: 'App',
  components: {
    FormSearchComponent
  },
  data() {
    return {
      step: 1,
      loading: false,
    }
  }
}
</script>

<style>
@import url('vue-select/dist/vue-select.css');
@import url('../../assets/css/main.css');
</style>