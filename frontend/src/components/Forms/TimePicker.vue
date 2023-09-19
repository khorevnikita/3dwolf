<template>
  <!--<v-text-field
      v-model="input"
      v-mask="'##:##'"
      :label="label"
  />-->
  <v-autocomplete
      :label="label"
      :items="options"
      v-model="input"
  />
</template>

<script>
import moment from "moment";

export default {
  name: "TimePicker",
  props: ['label', 'value'],
  data() {
    return {
      input: this.value,
      //tzOffset: moment().utcOffset() / 60,
    }
  },
  watch: {
    value() {
      const time = moment(this.value, "HH:mm");
      //time.add(this.tzOffset, 'hours');
      this.input = time.format("HH:mm");
    },
    input() {
      if (!this.input) return;
      const time = moment(this.input, 'HH:mm');
      if (time.isValid()) {
        //time.subtract(this.tzOffset, 'hours');
        this.$emit("input", time.format("HH:mm"));
      } else {
        this.input = null;
      }
    }
  },
  computed: {
    options() {
      const opt = [];
      for (let hour = 0; hour < 24; hour++) {
        for (let minute = 0; minute < 60; minute += 15) {
          opt.push(`${this.fillTimeValue(hour)}:${this.fillTimeValue(minute)}`)
        }
      }
      return opt;
    }
  },
  methods: {
    fillTimeValue(v) {
      return v < 10 ? `0${v}` : v;
    }
  }
}
</script>

<style scoped>

</style>