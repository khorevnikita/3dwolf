<template>
  <v-card>
    <v-card-title>Редактирование шаблона</v-card-title>
    <v-card-text>
      <v-select
          v-if="!model.id"
          label="Статус заказа"
          :items="freeStatuses"
          item-value="value"
          item-text="text"
          v-model="model.order_status"
          :error-messages="errors.order_status"
          :error-count="1"
          :error="!!errors.order_status"
      />

      <v-textarea
          label="Шаблон для СМС"
          v-model="model.template_sms"
          :error-messages="errors.template_sms"
          :error-count="1"
          :error="!!errors.template_sms"
      />

      <p class="text-hint">Шаблон для e-mail</p>
      <div>
        <ckeditor
            v-if="rendered"
            :editor="editor"
            v-model="model.template_email"
            :config="editorConfig"
        />
      </div>

      <p v-if="errors.template" class="error--text text-hint">{{ errors.template.join(', ') }}</p>

      <p class="text-hint mt-4">
        [fio] - Фамилия Имя Отчество<br/>
        [email] - Почта<br/>
        [phone] - Телефон<br/>
        [status] - Статус наряд-заказа<br/>
        [total] - Сумма заказа<br/>
        [total50] - 50% от суммы заказа <br/>
        [discount] - размер скидки (число)<br/>
        [totalDiscount] - Сумма заказа с учётом скидки<br/>
        [totalDiscount50] - 50% от суммы заказа с учетом скидки<br/>
        [address] - Адрес доставки<br/>
        [tkLink] - Трек-номер<br/>
        [qr] - QR-код<br/>
      </p>
    </v-card-text>
    <v-card-actions>
      <v-btn text @click="$emit('close')">Закрыть</v-btn>
      <v-spacer/>
      <v-btn color="primary" @click="save()">Сохранить</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from "@/plugins/axios";
import CKEditor from "@ckeditor/ckeditor5-vue2";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import uploader from "@/plugins/uploader";

export default {
  name: "OrderNotificationTemplateEditDialog",
  props: ['value', 'freeStatuses'],
  components: {
    ckeditor: CKEditor.component
  },
  data() {
    return {
      model: this.value,
      modelName: 'template',
      rendered: false,
      errors: {},
      editor: ClassicEditor,
      editorConfig: {
        // The configuration of the editor.
        extraPlugins: [uploader,],
      },
    }
  },
  mounted() {
    setTimeout(() => {
      this.rendered = true
    }, 100)
  },
  computed: {},
  methods: {
    save() {
      this.errors = {};
      if (this.model.id) {
        this.update();
      } else {
        this.store();
      }
    },
    store() {
      axios.post(`order-notification-templates`, this.model).then(body => {
        this.$emit("created", body[this.modelName]);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`order-notification-templates/${this.model.id}`, this.model).then(body => {
        this.$emit("updated", body[this.modelName]);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    }
  }
}
</script>

<style>
.ck-editor__editable {
  height: 400px;
  overflow-x: scroll;
}
</style>