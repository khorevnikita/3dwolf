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
      <!--<v-textarea
          label="Текст"
          v-model="newsletter.text"
          :error-messages="errors.text"
          :error-count="1"
          :error="!!errors.text"
      />-->
      <ckeditor style="min-height: 400px" :editor="editor" v-model="newsletter.text" :config="editorConfig"></ckeditor>

      <div class="text-h6">Вложения</div>
      <FileUploader label="Выберите файл" @uploaded="onUploaded"/>

      <v-chip-group column v-if="newsletter.files && newsletter.files.length>0">
        <v-chip v-for="(file,l) in newsletter.files" :key="l">
          {{ file.name }}
          <v-btn x-small icon @click="remove(file)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </v-chip>
      </v-chip-group>
    </v-card-text>
    <v-card-actions>
      <v-spacer/>
      <v-btn color="primary" @click="save()">Сохранить</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from "@/plugins/axios";
import CKEditor from '@ckeditor/ckeditor5-vue2';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import uploader from "@/plugins/uploader";
import FileUploader from "@/components/Forms/FileUploader";


export default {
  name: "NewsletterForm",
  props: ['value'],
  components: {
    FileUploader,
    ckeditor: CKEditor.component
  },
  data() {
    return {
      newsletter: this.value,
      errors: {},

      editor: ClassicEditor,
      editorConfig: {
        // The configuration of the editor.
        extraPlugins: [uploader,],
      },
    }
  },
  watch: {
    value: {
      handler() {
        this.newsletter = this.value;
      }, deep: true
    }
  },
  methods: {
    onUploaded(file) {
      this.newsletter.files.push(file)
    },
    save() {
      this.errors = {};
      if (this.newsletter.id) {
        this.update();
      } else {
        this.store();
      }
    },
    store() {
      axios.post(`newsletters`, this.newsletter).then(body => {
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
    },
    remove(file) {
      this.newsletter.files.splice(this.newsletter.files.indexOf(file), 1);
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