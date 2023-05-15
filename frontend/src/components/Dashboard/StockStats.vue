<template>
  <v-simple-table>
    <template v-slot:default>
      <thead>
      <tr>
        <th class="text-left">
          Производитель
        </th>
        <th class="text-left">
          Номер
        </th>
        <th class="text-left">
          Материал
        </th>
        <th class="text-left">
          Цвет
        </th>
        <th class="text-left">
          Кол-во
        </th>
      </tr>
      </thead>
      <tbody>
      <tr
          v-for="(item,i) in stock"
          :key="i"
          v-bind:class="{'error white--text':item.count===0}"
      >
        <td>{{ item.manufacturer }}</td>
        <td>{{ item.prod_number }}</td>
        <td>{{ item.material }}</td>
        <td>{{ item.color }}</td>
        <td>{{ item.count }}</td>
      </tr>
      </tbody>
    </template>
  </v-simple-table>
</template>

<script>
export default {
  name: "StockStats",
  props: ['stock'],
  data() {
    return {}
  }
}
</script>

<style scoped>

</style>