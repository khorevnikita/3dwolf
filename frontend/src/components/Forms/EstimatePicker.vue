<template>
  <v-autocomplete
      :label="label"
      v-model="input"
      :items="estimates"
      :loading="loading"
      :search-input.sync="search"
      item-value="id"
      item-text="name"
      :error-messages="error"
      :error-count="1"
      :error="!!error"
      :filter="()=>true"
  >
    <template #item="{item}">
      {{ item.name }} - {{ moment(item.date).format("DD.MM.YYYY") }}
    </template>
  </v-autocomplete>
</template>

<script>
import axios from "@/plugins/axios";
import moment from "moment";

export default {
  name: "EstimatePicker",
  props: ['value', 'error', 'label'],
  data() {
    return {
      input: this.value,
      estimates: [],
      loading: false,
      search: '',
      moment: moment,
    }
  },
  watch: {
    search(oldV, newV) {
      if (!newV) return;
      this.getItems()
    },
    input() {
      this.$emit("input", this.input);
    }
  },
  created() {
    this.getItems();
  },
  methods: {
    getItems() {
      if (this.loading) return;
      this.loading = true;
      axios.get(`estimates?search=${this.searchPart ? this.searchPart : ''}&sort_desc=1&field=${this.input ? this.input : ''}`)
          .then(body => {
            this.estimates = body.estimates;
            this.loading = false;
          });
    },
  }
}
</script>

<style scoped>

</style>