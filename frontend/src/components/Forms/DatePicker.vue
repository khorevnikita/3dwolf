<template>
  <v-menu
      v-model="menu"
      :close-on-content-click="false"
      :nudge-right="40"
      transition="scale-transition"
      offset-y
      min-width="auto"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-text-field
          :value="readValue"
          :label="label"
          prepend-icon="mdi-calendar"
          readonly
          v-bind="attrs"
          v-on="on"
      ></v-text-field>
    </template>
    <v-date-picker
        v-model="input"
        @input="menu = false"
        :locale="'ru'"
        :first-day-of-week="1"
    ></v-date-picker>
  </v-menu>
</template>

<script>
import moment from "moment";

export default {
  name: "DatePicker",
  props: ['value', 'error', 'label'],
  data() {
    return {
      input: this.value,
      menu: false,
    }
  },
  watch: {
    input(v) {
      this.$emit("input", v)
    }
  },
  computed: {
    readValue() {
      if (!moment(this.input).isValid()) return;
      return moment(this.input).format("DD.MM.YYYY");
    }
  }
}
</script>

<style scoped>

</style>