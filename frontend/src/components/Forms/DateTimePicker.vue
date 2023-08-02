<template>
  <div class="datetime-container">
    <DatePicker
        label="Дата"
        v-model="date"
        :error="error"
    />
    <TimePicker
        v-model="time"
        label="Время"
    />
  </div>
</template>

<script>
import moment from "moment";
import DatePicker from "@/components/Forms/DatePicker";
import TimePicker from "@/components/Forms/TimePicker";

export default {
  name: "DateTimePicker",
  components: {TimePicker, DatePicker},
  props: ['value', 'error', 'label'],
  data() {
    return {
      input: this.value,
      date: null,
      time: null,
    }
  },
  created() {
    this.parseInput();
  },
  watch: {
    /*value() {
      this.input = this.value;
    },*/
    time() {
      this.collectInput();
    },
    date() {
      this.collectInput();
    },
    input() {
      this.$emit("input", moment(this.input).format("YYYY-MM-DD HH:mm"));
    }
  },
  methods: {
    parseInput() {
      if (this.input && moment(this.input).isValid()) {
        this.date = moment.utc(this.input).local().format("YYYY-MM-DD");
        this.time = moment.utc(this.input).local().format("HH:mm");
      }
    },
    collectInput() {
      const datetime = moment(this.date);
      if (this.time) {
        const [hourStr, minuteStr] = this.time.split(":");
        datetime.hours(Number(hourStr));
        datetime.minutes(Number(minuteStr));
      }
      this.input = datetime.format("YYYY-MM-DD HH:mm")
    }
  }
}
</script>

<style scoped lang="scss">
.datetime-container {
  display: flex;
  flex-flow: column;
  @media (min-width: 992px) {
    flex-flow: row;
    column-gap: 8px;
  }
}
</style>