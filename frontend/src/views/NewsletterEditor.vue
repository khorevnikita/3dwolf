<template>
  <div>
    <v-breadcrumbs :items="breadcrumps" divider="-"/>
    <h4 class="text-h4 mb-4">Редактирование рассылки</h4>
    <div style="clear:both"></div>
    <v-card v-if="newsletter" class="mt-3">
      <v-card-actions>
        <v-spacer/>
        <v-btn color="primary" text :disabled="!newsletter.id" :to="`/newsletters/${newsletter.id}`">Перейти к
          рассылке
        </v-btn>
      </v-card-actions>
      <v-card-text>
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
            <NewsletterReceivers :newsletter="newsletter"/>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>

    </v-card>
  </div>
</template>

<script>
import Swal from "sweetalert2-khonik";
import NewsletterForm from "@/components/Newsletter/Editor/NewsletterForm";
import NewsletterReceivers from "@/components/Newsletter/Editor/NewsletterReceivers";
import axios from "@/plugins/axios";

export default {
  name: "NewsletterEditDialog",
  components: {NewsletterReceivers, NewsletterForm},
  props: ['value'],
  data() {
    return {
      tab: 0,
      newsletter: {
        subject: "",
        text: "",
      },
      newsletterId: Number(this.$route.params.id),
    }
  },
  /*created() {
    this.unsubscribe = this.$store.subscribe((mutation) => {
      if (['detachFromNewsletter', 'attachToNewsletter'].includes(mutation.type)) {
        this.$emit('customers_updated');
      }
    });
  },*/
  created() {
    if (this.newsletterId) this.getNewsletter();
  },
  computed: {
    breadcrumps() {
      return [
        {
          text: 'Рассылки',
          disabled: false,
          href: '/newsletters',
        },
        {
          text: `Редактирование рассылки`,
          disabled: true,
          href: '#',
        },
      ]
    }
  },
  methods: {
    getNewsletter() {
      axios.get(`newsletters/${this.newsletterId}`).then(body => {
        this.newsletter = {
          id: body.newsletter.id,
          subject: body.newsletter.subject,
          text: body.newsletter.text,
        };
        console.log(this.newsletter);
      })
    },
    onSaved() {
      Swal.fire('Данные сохранены');
    },
    onCreated() {
      //this.$emit('created', this.newsletter);
      //this.$router.push(`/newsletters/${this.newsletter.id}`)
    },
    onUpdated() {
      // this.$emit('updated', this.newsletter);
      this.$router.push(`/newsletters/${this.newsletter.id}`)
    },

  }
}
</script>

<style scoped>

</style>