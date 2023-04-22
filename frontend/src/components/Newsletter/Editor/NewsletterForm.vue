<template>
  <v-card flat>
    <v-card-text>
      <v-text-field
          label="Заголовок"
          v-model="newsletter.subject"
          :error-messages="errors.subject"
          :error-count="1"
          :error="!!errors.subject"
      />
      <v-textarea
          label="Текст"
          v-model="newsletter.text"
          :error-messages="errors.text"
          :error-count="1"
          :error="!!errors.text"
      />
    </v-card-text>
    <v-card-actions>
      <v-spacer/>
      <v-btn color="primary" @click="save()">Сохранить</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "NewsletterForm",
  props: ['value'],
  data() {
    return {
      newsletter: this.value,
      errors: {},
    }
  },
  methods: {
    save() {
      this.errors = {};
      if (this.newsletter.id) {
        this.update();
      } else {
        this.store();
      }
    },
    store() {
      axios.post(`newsletters`, this.newsletter).then(body=> {
        this.$emit("input", body.newsletter);
        this.$emit("created", body.newsletter);
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`newsletters/${this.newsletter.id}`, this.newsletter).then(body => {
        this.$emit("input", body.newsletter);
        this.$emit("updated", body.newsletter);
      }).catch(err => {
        this.errors = err.body.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>