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
      // UPLOADER
      chunks: [],

      uploaded: 0,
      upload_id: null,
      key: null,

      chunkSize: 1024 * 1024 * 6,
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
  watch: {
    // uploader
    chunks(n) {
      if (n.length > 0) {
        this.uploadPart();
      }
    }
  },

  computed: {
    progress() {
      return Math.floor((this.uploaded * 100) / this.file.size);
    },
    formData() {
      let formData = new FormData;
      formData.append('part', this.chunks[0], `${this.file.name}`);
      formData.append('PartNumber', (this.uploaded + 1))
      formData.append('Key', this.key)

      /*if (this.chunks.length === 1) {
        formData.append('is_last', true);
      }
      if (this.upload_id) {
        formData.append('upload_id', this.upload_id);
      }*/
      return formData;
    },
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

      if (this.file.size <= this.chunkSize) {
        return await this.regularUpload();
      }
      this.uploaded = 0;
      await this.createMultipartUpload();
      //return;

      let chunks = Math.ceil(this.file.size / this.chunkSize);

      for (let i = 0; i < chunks; i++) {
        let el = this.file.slice(
            i * this.chunkSize, Math.min(i * this.chunkSize + this.chunkSize, this.file.size), this.file.type
        );
        this.chunks.push(el);
      }
    },

    async regularUpload(file) {
      const fd = new FormData;
      fd.append('file', file);
      try {
        const data = await axios.post(`upload`, fd);
        const {url, path, name, mime_type, size} = data;
        this.$emit('uploaded', {
          url: url,
          path: path,
          name: name,
          size: size,
          mime_type: mime_type,
        })
      } catch (e) {
        console.log(e);
      }
    },
    async createMultipartUpload() {
      this.loading = true;
      return axios.post(`upload/multipart`, {
        filename: this.file.name,
        file_type: this.file.type
      }).then((data) => {
        this.key = data.Key;
        this.upload_id = data.UploadId;
      }).catch(err => {
        console.log(err.body);
      })
    },
    uploadPart() {
      axios.post(`upload/multipart/${this.upload_id}`, this.formData, {
        headers: {
          'Content-Type': 'application/octet-stream'
        },
      }).then((data) => {
        if (data.UploadId) {
          this.upload_id = data.UploadId;
        }
        if (data.Key) {
          this.key = data.Key;
        }

        this.uploaded++;
        this.chunks.shift();

        if (this.chunks.length === 0) {
          this.completeMultipartUpload();
        }
      }).catch(err => {
        console.log(err.body);
      });
    },
    async completeMultipartUpload() {
      const data = await axios.get(`upload/multipart/${this.upload_id}?Key=${this.key}`);
      return axios.post(`upload/multipart/${this.upload_id}/complete`, {
        Key: this.key,
        Parts: data.parts,
      }).then(({file}) => {
        this.$emit('input', file.url);
        this.$emit('uploaded', {
          url: file.url,
          path: file.path,
          name: file.name,
          size: file.file_size,
          mime_type: file.mime_type,
        })
        this.loading = false;
      })
    },
  }
}
</script>

<style scoped>

</style>