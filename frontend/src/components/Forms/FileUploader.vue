<template>
  <v-file-input
      ref="input"
      :label="label"
      :prepend-icon="icon?'mdi-paperclip':''"
      v-model="file"
      @change="fileSelected()"
      :hide-input="icon"
      :error-count="1"
      :error-messages="error"
      :error="!!error"
      :loading="loading"
  >
    <template #prepend-inner>
      <slot name="prepend-inner"/>
    </template>
  </v-file-input>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "FileUploader",
  props: ['label', 'error', 'icon'],
  data() {
    return {
      file: null,
      loading: false,
    }
  },
  created() {
    if (this.link) {
      let initiator = this.$parent.$refs[this.link];
      if (initiator) {
        initiator.$el.addEventListener("click", () => {
          this.init();
        })
      }
    }
  },
  methods: {
    init() {
      try {
        this.$refs.input.$refs['input-slot'].children[0].click()
      } catch (e) {
        console.error("INIT ERR", e)
      }
    },
    async fileSelected() {
      if (!this.file) {
        return;
      }

      await this.regularUpload();
    },

    regularUpload() {
      const fd = new FormData;
      fd.append('file', this.file);
      this.loading = true;
      axios.post(`upload`, fd).then(body => {
        this.$emit('uploaded', body.url)
        this.loading = false;
      })
    }
  }
}
</script>

<style scoped>

</style>