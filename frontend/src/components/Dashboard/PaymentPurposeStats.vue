<template>
  <v-card>
    <v-card-title>Цели платежей</v-card-title>
    <v-card-actions>
      <v-btn @click="month--" class="mr-2" icon color="primary">
        <v-icon>mdi-chevron-left</v-icon>
      </v-btn>
      <v-btn @click="month++" class="mr-4" icon color="primary">
        <v-icon>mdi-chevron-right</v-icon>
      </v-btn>
      <div>{{ humanMonth }}/{{ year }}</div>
      <v-spacer/>
    </v-card-actions>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="6">
          <v-skeleton-loader
              v-if="loading"
              class="rounded-circle ml-4"
              max-width="200"
              type="image"
          ></v-skeleton-loader>
          <VueApexCharts v-else :key="month" width="380" type="donut" :options="options" :series="series"/>
        </v-col>
        <v-col cols="12" md="6">
          <v-list-item v-for="purpose in data" :key="purpose.id">
            <v-list-item-icon>
              <v-icon :color="purpose.color">mdi-circle</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>{{ purpose.name }}</v-list-item-title>
              <v-list-item-subtitle>{{ formatPrice(purpose.amount) }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import moment from "moment";
import axios from "@/plugins/axios";
import VueApexCharts from 'vue-apexcharts'
import {formatPrice} from "@/plugins/formats";

export default {
  name: "PaymentPurposeStats",
  components: {VueApexCharts},
  data() {
    return {
      data: [],
      month: moment().month(),
      year: moment().year(),
      formatPrice: formatPrice,
      loading: false,
    }
  },
  computed: {
    humanMonth() {
      return [
        "Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь",
      ][this.month];
    },
    options() {
      if (this.isNA) {
        return {
          labels: ["N/A"],
          colors: ["#8a8a8a"],
        }
      }
      return {
        labels: this.data.map(p => p.name),
        colors: this.data.map(p => p.color),
        legend: {
          show: false,
        }
      }
    },
    series() {
      if (this.isNA) {
        return [1]
      }
      return this.data.map(p => p.amount);
    },
    isNA() {
      return this.data.filter(p => p.amount > 0).length === 0;
    }
  },
  watch: {
    month(m) {
      if (m < 0) {
        this.year--;
        this.month = 11;
      }
      if (m > 11) {
        this.month = 0;
        this.year++;
      }
      this.getData();
    }
  },
  created() {
    this.getData()
  },
  methods: {
    getData() {
      this.loading = true;
      axios.get(`money/purposes?year=${this.year}&month=${this.month}`).then(body => {
        this.data = body.paymentPurposes;
        setTimeout(()=>{
          this.loading = false;
        },3000)
      })
    }
  }
}
</script>

<style scoped>

</style>