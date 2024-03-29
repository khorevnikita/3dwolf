<template>
  <div>
    <v-progress-circular indeterminate color="primary" v-if="!newsletter"/>
    <div v-else>
      <v-breadcrumbs :items="breadcrumps" divider="-"/>
      <h4 class="text-h4 mb-3">{{ newsletter.subject }}</h4>
      <div class="d-flex">
        <v-chip class="mr-3">
          {{ newsletter.status }}
        </v-chip>
        <v-chip class="mr-3">
          <v-icon>mdi-account</v-icon>
          {{ newsletter.customers_count }}
        </v-chip>
        <v-btn icon outlined color="success" @click="send()" :disabled="newsletter.status!=='draft'">
          <v-icon>mdi-play</v-icon>
        </v-btn>
        <v-spacer/>
        <v-btn :to="`/newsletters/${newsletter.id}/edit`" icon outlined color="warning"
               :disabled="!newsletter.editable">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn icon outlined color="error" class="ml-2" @click="destroy(item)" :disabled="!newsletter.editable">
          <v-icon>mdi-delete</v-icon>
        </v-btn>

      </div>
      <v-row>
        <v-col cols="12" sm="8" md="6">
          <div class="mt-3" v-html="newsletter.text"></div>
          <v-chip-group column v-if="newsletter.files && newsletter.files.length>0">
            <v-chip :href="file.url" target="_blank" v-for="(file,l) in newsletter.files" :key="l">
              {{ file.name }}
            </v-chip>
          </v-chip-group>
        </v-col>
        <v-col cols="12">
          <v-simple-table>
            <template v-slot:default>
              <thead>
              <tr>
                <th class="text-left">
                  ID
                </th>
                <th class="text-left">
                  Имя
                </th>
                <th class="text-left">
                  Контакты
                </th>
                <th class="text-left">
                  Компания
                </th>
                <th class="text-left">
                  Отправлено
                </th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="customer in newsletter.customers" :key="customer.id">
                <td>{{ customer.id }}</td>
                <td>
                  {{ customer.name }} {{ customer.surname }}
                </td>
                <td>{{ [customer.phone, customer.email].join(', ') }}</td>
                <td>
                  {{ customer.title }}
                </td>
                <td>
                  {{
                    customer.pivot.sent_at ? moment(customer.pivot.sent_at).format("HH:mm DD.MM.YYYY") : 'Не отправлено'
                  }}
                </td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
          <v-pagination v-model="page" :length="pagesCount"/>
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2-khonik";
import moment from "moment";
import axios from "@/plugins/axios";

export default {
  name: "NewsletterItem",
  data() {
    return {
      newsletterId: Number(this.$route.params.id),
      newsletter: undefined,
      page: 1,
      pagesCount: 1,
      moment: moment
    }
  },
  created() {
    this.getNewsletter();
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
          text: `${this.newsletter?.subject}`,
          disabled: true,
          href: '#',
        },
      ]
    }
  },
  watch: {
    page() {
      this.getNewsletter();
    }
  },
  methods: {
    getNewsletter() {
      axios.get(`newsletters/${this.newsletterId}?page=${this.page}`).then(body => {
        this.newsletter = body.newsletter;
        this.pagesCount = body.pagesCount;
      });
    },
    send() {
      Swal.fire({
        title: "Подтверждаете отправку рассылки?",
        showDenyButton: false,
        showCancelButton: true,
        cancelButtonText: 'Отменить',
        showCloseButton: false,
        showConfirmButton: true,
        confirmButtonText: 'Подтверждаю'
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          axios.post(`newsletters/${this.newsletter.id}/send`).then(() => {
            this.newsletter.status = 'sending';
          })
        }
      });
    },
    destroy(item, onDeleted = undefined) {
      Swal.fire({
        title: this.deleteSwalTitle,
        showDenyButton: true,
        denyButtonText: `Удалить`,
        showCancelButton: true,
        cancelButtonText: 'Отменить',
        showCloseButton: false,
        showConfirmButton: false,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isDenied) {
          axios.delete(`${this.resourceApiRoute}/${item.id}`).then(() => {
            this.items.splice(this.items.indexOf(item), 1);
            if (onDeleted) onDeleted();
          })
        }
      })
    }
  }
}
</script>

<style scoped>

</style>