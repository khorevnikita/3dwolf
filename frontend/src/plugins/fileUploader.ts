import axios from "@/plugins/axios";
import {EventEmitter} from "events";

export interface IUploadedFile {
    url: string,
    size: number,
    mime_type: string,
}

export default class FileUploader extends EventEmitter {
    file: File | Blob | undefined;
    CHUNK_SIZE = 1024 * 1024 * 6;
    uploaded = 0;
    chunks: Blob[] = [];
    loading = false;

    key: string | undefined;
    upload_id: string | undefined;

    constructor() {
        super()
    }

    uploadFile = async (file: File | Blob) => {
        this.file = file;
        if (file.size <= this.CHUNK_SIZE) {
            await this.regularUpload();
            return
        }
        this.uploaded = 0;
        await this.createMultipartUpload();


        await this.uploadParts();
    }

    createMultipartUpload = async () => {
        if (!this.file) return;
        this.loading = true;
        return axios.post(`upload/multipart`, {
            filename: "name" in this.file ? this.file.name : Date.now(),
            file_type: this.file.type
        }).then(({data}) => {
            this.key = data.Key;
            this.upload_id = data.UploadId;
        }).catch(err => {
            console.log(err);
        });
    }

    async uploadParts() {
        if (!this.file) return;
        let chunks = Math.ceil(this.file.size / this.CHUNK_SIZE);
        for (let i = 0; i < chunks; i++) {
            let el = this.file.slice(
                i * this.CHUNK_SIZE, Math.min(i * this.CHUNK_SIZE + this.CHUNK_SIZE, this.file.size), this.file.type
            );
            this.chunks.push(el);
        }

        while (this.chunks.length !== 0) {
            console.log('fd', this.formData.get('PartNumber'), this.formData.get('Key'), this.formData.get('part'))
            await this.uploadPart();
        }

        await this.completeMultipartUpload();
    }

    uploadPart = async () => {
        const {data} = await axios.post(`upload/multipart/${this.upload_id}`, this.formData);
        if (data.UploadId) {
            this.upload_id = data.UploadId;
        }
        if (data.Key) {
            this.key = data.Key;
        }

        this.uploaded++;
        this.chunks.shift();
    }

    completeMultipartUpload = async () => {
        const {data} = await axios.get(`upload/multipart/${this.upload_id}?Key=${this.key}`);

        return axios.post(`upload/multipart/${this.upload_id}/complete`, {
            Key: this.key,
            Parts: data.parts,
        }).then(({data}) => {
            this.loading = false;
            if (!this.file) return;

            this.emit('uploaded', {
                url: data.file.url,
                size: this.file.size,
                mime_type: this.file.type,
            } as IUploadedFile);
        })
    }

    regularUpload = async () => {
        if (!this.file) return;
        const fd = new FormData;
        fd.append('file', this.file);
        this.loading = true;
        const {data} = await axios.post(`upload`, fd);

        this.emit('uploaded', {
            url: data.file.url,
            size: this.file.size,
            mime_type: this.file.type,
        } as IUploadedFile);

        this.loading = false;
    }

    get progress() {
        if (!this.file) return 0;
        return Math.floor((this.uploaded * 100) / this.file.size);
    }

    get formData() {
        let formData = new FormData;
        if (this.file) if ("name" in this.file) {
            formData.append('part', this.chunks[0], `${this.file.name}`);
        }
        formData.append('PartNumber', (this.uploaded + 1).toString());
        if (this.key) formData.append('Key', this.key);
        return formData;
    }
}
