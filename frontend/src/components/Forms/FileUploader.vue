<template>
  <v-file-input
      ref="input"
      :label="label"
      :prepend-icon="icon?'mdi-paperclip':''"
      v-model="files"
      @change="fileSelected()"
      :hide-input="icon"
      :error-count="1"
      :error-messages="error"
      :error="!!error"
      :loading="loading"
      multiple
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
      files: [],
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
      if (this.files.length === 0) {
        return;
      }

      this.loading = true;
      for (let file of this.files) {
        await this.regularUpload(file);
      }
      this.loading = false;
    },

    async regularUpload(file) {
      const fd = new FormData;
      fd.append('file', file);
      try {
        const {url, path, name} = await axios.post(`upload`, fd);
        this.$emit('uploaded', {
          url: url,
          path: path,
          name: name,
        })
      } catch (e) {
        console.log(e);
      }
    }
  }
}
</script>

<style scoped>

</style>