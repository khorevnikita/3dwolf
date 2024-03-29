<template>
  <div>
    <div v-if="estimate">
      <h4 class="text-h4">{{ estimate.name }}</h4>
      <p class="text-subtitle-1">от {{ moment(estimate.date).format("DD.MM.YYYY") }}</p>
      <v-simple-table>
        <template v-slot:default>
          <thead>
          <tr>
            <th class="text-left">Обозначение</th>
            <th class="text-left">Название</th>
            <th class="text-left">Кол-во</th>
            <th class="text-left">Катушка</th>
            <th class="text-left">Цена</th>
            <th class="text-left">Итого</th>
            <th class="text-left">Вес</th>
            <th class="text-left">Общий вес</th>
            <th class="text-left">Время печати</th>
            <th class="text-left"></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="line in items" :key="line.id">
            <td>{{ line.key }}</td>
            <td>
              {{ line.name }} {{ line.filling ? `(${line.filling}%)` : '' }}
            </td>
            <td>{{ line.count }}</td>
            <td>
              {{ line.part ? `${line.part.material.name}, ${line.part.color} (${line.part.prod_number})` : '-' }}
            </td>
            <td>{{ formatPrice(line.price) }}</td>
            <td>{{ formatPrice(line.amount) }}</td>
            <td>{{ line.weight }}</td>
            <td>{{ line.total_weight }}</td>
            <td>{{ formatDuration(line.print_duration) }}</td>
            <td>
              <v-btn color="warning" icon @click="edit(line)">
                <v-icon>mdi-pencil</v-icon>
              </v-btn>

              <v-btn color="error" icon @click="destroy(line)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </td>
          </tr>
          <tr>
            <td colspan="9">
              <v-btn x-small color="primary" @click="create()">Добавить</v-btn>
            </td>
          </tr>
          </tbody>
        </template>
      </v-simple-table>

      <div class="mt-5">
        <div class="subtitle-1">Итого: <b class="text-h6">{{ formatPrice(totalAmount) }}</b></div>
        <div class="subtitle-1">Вес итого: <b class="text-h6">{{ totalWeight }} гр.</b></div>
        <div class="subtitle-1">Время итого: <b class="text-h6">{{ formatDuration(totalTime) }}</b></div>
      </div>

      <v-dialog v-model="editDialog" max-width="500">
        <EstimateLineEditor
            v-if="editDialog"
            @close="editDialog=false"
            v-model="editItem"
            @created="onCreated"
            @updated="onUpdated"
            :estimate_id="id"
        />
      </v-dialog>
    </div>
  </div>
</template>

<script>
import axios from "@/plugins/axios";
import moment from "moment";
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import EstimateLineEditor from "@/components/Estimate/EstimateLineEditor";

export default {
  name: "EstimateItem",
  components: {EstimateLineEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      moment: moment,
      id: this.$route.params.id,
      estimate: undefined,
      resourceKey: "estimateLines",
      resourceApiRoute: `estimates/${this.$route.params.id}/estimate-lines`,
      deleteSwalTitle: `Безвозвратно удалить позицию?`,
    }
  },
  created() {
    this.getEstimate();
  },
  computed: {
    totalAmount() {
      const amount = this.items.reduce((acc, item) => acc += (Number(item.price) * Number(item.count)), 0)
      return Math.round(amount * 100) / 100;
    },
    totalWeight() {
      const weight = this.items.reduce((acc, item) => acc += (Number(item.weight) * Number(item.count)), 0);
      return Math.round(weight * 100) / 100;
    },
    totalTime() {
      const time = this.items.reduce((acc, item) => acc += Number(item.print_duration) * Number(item.count), 0);
      return Math.round(time * 100) / 100;
    },
  },
  methods: {
    getEstimate() {
      axios.get(`estimates/${this.id}`).then(body => {
        this.estimate = body.estimate;
      })
    }
  }
}
</script>

<style scoped>

</style>