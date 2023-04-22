<template>
  <div>
    Найдено: {{ totalCount }}
    <v-list>
      <v-list-item link v-for="customer in customers" :key="customer.id">
        <v-list-item-content>
          <v-list-item-title>{{ customer.title }}</v-list-item-title>
          <v-list-item-subtitle>{{ customer.phone }} {{ customer.email }}</v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-action>
          <v-btn icon @click="attach(customer)">
            <v-icon>mdi-chevron-right</v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </v-list>
    <v-pagination v-model="page" :length="pagesCount"/>
  </div>
</template>

<script>
import QueryHelper from "@/mixins/QueryHelper";
import axios from "@/plugins/axios";

export default {
  name: "AvailableReceivers",
  props: ['query', 'newsletter', 'reFetch'],
  mixins: [QueryHelper],
  data() {
    return {
      customers: [],
      page: 1,
      pagesCount: 1,
      totalCount: 0,
    }
  },
  created() {
    this.getCustomers();

    this.unsubscribe = this.$store.subscribe((mutation, state) => {
      if (mutation.type === 'detachFromNewsletter') {
        if (state.detachedCustomer) {
          this.customers.unshift(state.detachedCustomer);
          this.$store.commit('detachFromNewsletter', undefined);
          this.totalCount++;
        }
      }
    });
  },
  beforeDestroy() {
    this.unsubscribe;
  },
  watch: {
    'query': {
      handler() {
        this.page = 1;
        this.getCustomers();
      },
      deep: true,
    },
    page: {
      handler() {
        this.getCustomers();
      }
    },
    reFetch: {
      handler(v) {
        v ? this.getCustomers() : undefined;
      }
    }
  },
  methods: {
    getCustomers() {
      axios.get(`newsletters/${this.newsletter.id}/available-receivers?page=${this.page}&${this.setQueryString(this.query)}`).then(body => {
        this.customers = body.customers;
        this.totalCount = body.totalCount;
        this.pagesCount = body.pagesCount;

      })
    },
    attach(customer) {
      axios.post(`newsletters/${this.newsletter.id}/attach/${customer.id}`).then(() => {
        this.customers.splice(this.customers.indexOf(customer), 1);
        this.$store.commit('attachToNewsletter', customer);
        this.totalCount--;
      })
    }
  }
}
</script>

<style scoped>

</style>