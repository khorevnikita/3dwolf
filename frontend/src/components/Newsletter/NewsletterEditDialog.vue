<template>
  <v-card>
    <v-toolbar dark color="secondary">
      <v-btn icon dark @click="$emit('close')">
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <v-toolbar-title>Редактирование рассылки</v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>

    <v-tabs v-model="tab" grow>
      <v-tab>Текста</v-tab>
      <v-tab v-if="newsletter.id">Получатели</v-tab>
    </v-tabs>

    <v-tabs-items class="mx-3" v-model="tab">
      <v-tab-item>
        <NewsletterForm
            @created="onCreated"
            @updated="onUpdated"
            v-on:input="onSaved"
            v-model="newsletter"
        />
      </v-tab-item>
      <v-tab-item v-if="newsletter.id">
        <NewsletterReceivers @customers_updated="$emit('customers_updated')" :newsletter="newsletter"/>
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</template>

<script>
import Swal from "sweetalert2-khonik";
import NewsletterForm from "@/components/Newsletter/Editor/NewsletterForm";
import NewsletterReceivers from "@/components/Newsletter/Editor/NewsletterReceivers";

export default {
  name: "NewsletterEditDialog",
  components: {NewsletterReceivers, NewsletterForm},
  props: ['value'],
  data() {
    return {
      tab: 0,
      newsletter: this.value,
    }
  },
  created() {
    this.unsubscribe = this.$store.subscribe((mutation) => {
      if (['detachFromNewsletter', 'attachToNewsletter'].includes(mutation.type)) {
        this.$emit('customers_updated');
      }
    });
  },
  methods: {
    onSaved() {
      Swal.fire('Данные сохранены');
    },
    onCreated() {
      this.$emit('created', this.newsletter);
    },
    onUpdated() {
      this.$emit('updated', this.newsletter);
    },

  }
}
</script>

<style scoped>

</style>