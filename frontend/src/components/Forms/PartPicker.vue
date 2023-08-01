<template>
  <v-autocomplete
      :label="label"
      v-model="input"
      :items="parts"
      :loading="isLoadingParts"
      :search-input.sync="searchPart"
      item-value="id"
      item-text="inv_number"
      :error-messages="error"
      :error-count="1"
      :error="!!error"
      :filter="()=>true"
  >
    <template #item="{item}">
      {{ item.inv_number }}. {{ `${item.material.name}, ${item.color} (${item.prod_number})` }}
    </template>
  </v-autocomplete>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "PartPicker",
  props: ['value', 'error', 'label'],
  data() {
    return {
      input: this.value,
      parts: [],
      isLoadingParts: false,
      searchPart: '',
    }
  },
  watch: {
    searchPart(oldV, newV) {
      if (!newV) return;
      this.getParts()
    },
    input() {
      this.$emit("input", this.input);
    }
  },
  created() {
    this.getParts();
  },
  methods: {
    getParts() {
      if (this.isLoadingParts) return;
      this.isLoadingParts = true;
      axios.get(`parts?not_ended=1&search=${this.searchPart ? this.searchPart : ''}&field=${this.input ? this.input : ''}`)
          .then(body => {
            this.parts = body.parts;
            this.isLoadingParts = false;
          });
    },
  }
}
</script>

<style scoped>

</style>