<template>
  <v-card>
    <v-card-title>Методы доставки</v-card-title>
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
          <v-list-item v-for="address in data" :key="address.id">
            <v-list-item-content>
              <v-list-item-title>{{ address.name }}</v-list-item-title>
              <v-list-item-subtitle>{{ formatPrice(address.amount) }}</v-list-item-subtitle>
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
  name: "DeliveryMethodStats",
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
        colors: ["#ff4d4d","#ffb24d","#f3ff4d","#59ff4d", "#4dffaf","#4db5ff","#654dff","#ff4de1","#ff4df6","#ff4d6e"],
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
      axios.get(`money/delivery-methods?year=${this.year}&month=${this.month}`).then(body => {
        this.data = body.deliveryAddresses;
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