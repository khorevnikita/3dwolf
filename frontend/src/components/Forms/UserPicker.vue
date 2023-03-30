<template>
  <v-autocomplete
      label="Пользователь"
      v-model="user_id"
      :items="users"
      :loading="isLoading"
      :search-input.sync="search"
      item-value="id"
      :error-messages="error"
      :error-count="1"
      :error="!!error"
      clearable
      :filter="()=>true"
      :dense="dense"
      :hide-details="dense"
  >
    <template #item="{item}">
      {{ item.name }} {{item.surname}}
    </template>
    <template #selection="{item}">
      {{ item.name }} {{item.surname}}
    </template>
  </v-autocomplete>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "UserPicker",
  props: ['value', 'error', 'dense'],
  data() {
    return {
      user_id: this.value,
      users: [],
      isLoading: false,
      search: ''
    }
  },
  watch: {
    search(oldV, newV) {
      if (!newV) return;
      this.getUsers()
    },
    user_id: function (userId) {
      this.$emit('input', userId);
    }
  },
  created() {
    this.getUsers();
  },
  methods: {
    getUsers() {
      if (this.isLoading) return;
      this.isLoading = true;
      axios.get(`users?search=${this.search ? this.search : ''}&field=${this.user_id ? this.user_id : ''}`).then(body => {
        this.users = body.users;
        this.isLoading = false;
      })
    },
  }
}
</script>

<style scoped>

</style>