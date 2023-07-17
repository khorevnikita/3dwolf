<template>
  <v-autocomplete
      label="Филиал"
      v-model="branch_id"
      :items="branches"
      :loading="isLoading"
      :search-input.sync="search"
      item-value="id"
      item-text="name"
      :error-messages="error"
      :error-count="1"
      :error="!!error"
      :clearable="!readonly"
      :filter="()=>true"
      :dense="dense"
      :hide-details="dense"
      :readonly="readonly"
  >
    <template #item="{item}">
      {{ item.name }}
    </template>
  </v-autocomplete>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "BranchPicker",
  props: ['value', 'error', 'dense', 'readonly'],
  data() {
    return {
      branch_id: this.value,
      branches: [],
      isLoading: false,
      search: ''
    }
  },
  watch: {
    search(oldV, newV) {
      if (!newV) return;
      this.getBranches()
    },
    branch_id: function (branchId) {
      this.$emit('input', branchId);
      const branch = this.customers.find(c => c.id === branchId);
      if (branch) {
        this.$emit('selected', branch);
      }
    }
  },
  created() {
    this.getBranches();
  },
  methods: {
    getBranches() {
      if (this.isLoading) return;
      this.isLoading = true;
      axios.get(`branches?search=${this.searchCustomer ? this.searchCustomer : ''}&field=${this.branch_id ? this.branch_id : ''}`).then(body => {
        this.branches = body.branches;
        this.isLoading = false;
      })
    },
  }
}
</script>

<style scoped>

</style>