<template>
  <v-row>
    <v-col cols="12" md="4">
      <v-text-field
          label="Поиск"
          hide-details
          v-model="query.search"
          dense
      />
    </v-col>
    <!--<v-col cols="12" md="4">
      <v-select
          dense
          label="Статус"
          v-model="query.status"
          hide-details
          :items="[{key:'',value:'Все'},{key:'without_company',value:'Без компании'},{key:'boss',value:'Администратор компании'},{key:'staff',value:'Сотрудник компании'},]"
          item-value="key"
          item-text="value"
      />
    </v-col>-->
    <!--<v-col cols="12" md="4" class="d-flex align-center">
      <v-btn small color="primary" @click="search">Найти</v-btn>
    </v-col>-->
  </v-row>
</template>

<script>
export default {
  name: "ReceiversFilter",
  props: ['value'],
  data() {
    return {
      query: this.value,
    }
  },
  methods: {
    search() {
      this.$emit('input', this.query);
    }
  }
}
</script>

<style scoped>

</style>