import axios from "@/plugins/axios";

export default class UploadAdapter {
    constructor(loader) {
        // The file loader instance to use during the upload.
        this.loader = loader;
    }

    // Starts the upload process.
    upload() {
        return this.loader.file
            .then(file => new Promise((resolve, reject) => {
                const data = new FormData();
                data.append('file', file);
                axios.post(`upload`, data).then(body => {
                    resolve({
                        default: body.url
                    })
                }).catch(reject)
            }));
    }
}