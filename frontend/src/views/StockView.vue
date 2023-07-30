<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center flex-column flex-md-row">
        <div class="text-h6 mb-2 mb-md-0">Склад</div>
        <v-spacer/>
        <div class="">
          <v-btn small to="/masks" color="secondary" class="mr-3 mb-2 mb-md-0">Маски внутренних номеров</v-btn>
          <v-btn small @click="create()" color="primary">Добавить позицию</v-btn>
        </div>
      </div>
    </v-col>
    <v-col cols="12">
      <v-card>
        <v-card-title>Фильтр</v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="3">
              <v-text-field
                  label="Поиск"
                  hide-details
                  dense
                  v-model="query.search"
              />
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                  label="Производитель"
                  v-model="query.manufacturer_id"
                  :items="manufacturersFilterList"
                  item-value="id"
                  item-text="name"
                  dense
                  hide-details
              />
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                  label="Материал"
                  v-model="query.material_id"
                  dense
                  hide-details
                  :items="materialFilterList"
                  item-value="id"
                  item-text="name"
              />
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                  label="Статус"
                  v-model="query.status"
                  dense
                  hide-details
                  multiple
                  :items="[
              {value:'',text:'Все'},
              {value:'new',text:'Новая'},
              {value:'opened',text:'Вскрытая'},
              {value:'ended',text:'Закончилась'},
          ]"
                  item-value="value"
                  item-text="text"
              />
            </v-col>
            <v-col cols="12" md="3">
              <v-switch label="Скрыть закончившиеся" v-model="showNotEnded"/>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" @click="replaceRoute">Найти</v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
    <v-col cols="12">
      <v-data-table
          :headers="headers"
          :items="items"
          :options.sync="options"
          :server-items-length="totalItems"
          :loading="loading"
          class="elevation-1 mt-3"
      >
        <template #item="{item}">
          <tr v-bind:class="{'is-new':item.status==='new','is-opened':item.status==='opened','is-finished':item.status==='ended'}">
            <td>{{ item.id }}</td>
            <td>{{ moment(item.bought_at).format("DD.MM.YYYY") }}</td>
            <td>{{ item.inv_number }}</td>
            <td>{{ item.prod_number }}</td>
            <td>{{ item.manufacturer ? item.manufacturer.name : '-' }}</td>
            <td>{{ item.material ? item.material.name : '-' }}</td>
            <td>{{ item.color }}</td>
            <td>{{ item.weight }}</td>
            <td>{{ formatPrice(item.price) }}</td>
            <td>{{ statuses[item.status] }}</td>
            <td style="width: 140px;">
              <v-btn color="primary" icon @click="downloadSticker(item)">
                <v-icon>mdi-barcode</v-icon>
              </v-btn>
              <v-btn color="warning" icon @click="edit(item)">
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
              <v-btn color="error" icon @click="destroy(item)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </td>
          </tr>
        </template>
      </v-data-table>
    </v-col>

    <v-dialog v-model="editDialog" max-width="500">
      <PartEditor
          v-if="editDialog"
          @close="editDialog=false"
          v-model="editItem"
          @created="onCreated"
          @multiple_created="onMultipleCreated"
          @updated="onUpdated"
      />
    </v-dialog>
  </v-row>
</template>

<script>

import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import PartEditor from "@/components/Part/PartEditor";
import axios from "@/plugins/axios";

export default {
  name: "StockView",
  components: {PartEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: true},
        {text: "Дата покупки", value: "bought_at", sortable: true},
        {text: "Вн. номер", value: "inv_number", sortable: true},
        {text: "Пр. номер", value: "prod_number", sortable: true},
        {text: "Производитель", value: "manufacturer_id", sortable: false},
        {text: "Материал", value: "material_id", sortable: false},
        {text: "Цвет", value: "color", sortable: false},
        {text: "Вес", value: "weight", sortable: true},
        {text: "Цена", value: "price", sortable: true},
        {text: "Статус", value: "status", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "parts",
      resourceApiRoute: `parts`,
      deleteSwalTitle: `Безвозвратно удалить позицию?`,
      statuses: {
        new: "Новая",
        opened: "Вскрытая",
        ended: "Закончилась"
      },
      manufacturers: [],
      materials: [],
      showNotEnded: false,
    }
  },
  created() {
    this.getManufacturers();
    this.getMaterials();
  },
  methods: {
    downloadSticker(item) {
      console.log('item', item);
      axios.get(`parts/${item.id}/export/auth`).then(body => {
        window.open(body.download_link, '_blank')
      })
    },
    getManufacturers() {
      axios.get('manufacturers').then(body => {
        this.manufacturers = body.manufacturers;
      })
    },
    getMaterials() {
      axios.get('materials').then(body => {
        this.materials = body.materials;
      })
    },
    onMultipleCreated(parts) {
      this.items = [...parts, ...this.items];
    }
  },
  watch: {
    showNotEnded() {
      if (this.showNotEnded) {
        this.query.status = ["new", "opened"];
      } else {
        this.query.status = [];
      }
    }
  },
  computed: {
    manufacturersFilterList() {
      return [
        {id: '', name: 'Все'},
        ...this.manufacturers.map(m => {
          return {
            id: String(m.id),
            name: m.name,
          }
        })
      ]
    },
    materialFilterList() {
      return [
        {id: '', name: 'Все'},
        ...this.materials.map(m => {
          return {
            id: String(m.id),
            name: m.name,
          }
        })
      ]
    },
  }
}
</script>

<style>
.is-new {
  background: #dfffae;
}

.is-opened {
  background: #bbe8ff;
}

.is-finished {
  color: gray;
  text-decoration: line-through;
}
</style>