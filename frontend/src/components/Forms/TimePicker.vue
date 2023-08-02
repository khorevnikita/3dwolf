<template>
  <v-text-field
      v-model="input"
      v-mask="'##:##'"
      :label="label"
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
      tzOffset: moment().utcOffset() / 60,
    }
  },
  watch: {
    value() {
      const time = moment(this.value, "HH:mm");
      time.add(this.tzOffset, 'hours');
      this.input = time.format("HH:mm");
    },
    input() {
      if (!this.input) return;
      const time = moment(this.input, 'HH:mm');
      if (time.isValid()) {
        time.subtract(this.tzOffset, 'hours');
        this.$emit("input", time.format("HH:mm"));
      } else {
        this.input = null;
      }
    }
  }
}
</script>

<style scoped>

</style>