<template>
  <v-card>
    <v-card-title>Файлы</v-card-title>
    <v-card-text>
      <v-alert type="info" v-if="items.length===0">Файлов нет</v-alert>
      <v-list v-else>
        <v-list-item v-for="file in items" :key="file.id" :href="file.url" target="_blank">
          <v-list-item-content>
            <v-list-item-title>{{ file.name }}</v-list-item-title>
            <v-list-item-subtitle>{{ file.mime_type }} - {{ formatWeight(file.size) }}</v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <v-btn icon color="error" @click.prevent="destroy(file)">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </v-card-text>
    <v-card-actions>
      <v-spacer/>
      <FileUploader
          label="Выберите файл для загрузки"
          @uploaded="onUploaded"
      />
    </v-card-actions>
  </v-card>
</template>

<script>
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import FileUploader from "@/components/Forms/FileUploader";
import axios from "@/plugins/axios";
import {formatWeight} from '@/plugins/formats'

export default {
  name: "FilesCard",
  components: {FileUploader},
  mixins: [ResourceComponentHelper],
  props: ['orderId'],
  data() {
    return {
      resourceKey: "orderFiles",
      resourceApiRoute: `orders/${this.orderId}/order-files`,
      deleteSwalTitle: `Безвозвратно удалить файл?`,
      formatWeight: formatWeight,
    }
  },
  watch: {
    page() {
      this.query.page = this.page;
      this.getItems();
    }
  },
  methods: {
    onUploaded(file) {
      //this.items.unshift(file);
      axios.post(`orders/${this.orderId}/order-files`, file).then(({orderFile}) => this.onCreated(orderFile))
    }
  }
}
</script>

<style scoped>

</style>