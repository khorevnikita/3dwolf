import axios from "axios";

export class ApiError extends Error {
    body: any

    constructor(msg: string, body: any) {
        super(msg);
        this.body = body;
    }
}

const instance = axios.create({
    baseURL: `${process.env.VUE_APP_API_ENDPOINT}/api`,
    // timeout: 10000,
    headers: {
        'accept': 'application/json',
    }
});

instance.interceptors.response.use(function (response) {
    // Any status code that lie within the range of 2xx cause this function to trigger
    // Do something with response data
    return response.data;
}, function (error) {
    // Any status codes that falls outside the range of 2xx cause this function to trigger
    // Do something with response error
    return Promise.reject({
        status: error.response.status,
        body: error.response.data
    });
});

instance.interceptors.request.use(function (config) {
    // Do something before request is sent
    console.log("before request", config.headers, localStorage.getItem('access_token'))
    config.headers['Authorization'] = `Bearer ${localStorage.getItem('access_token')}`;
    return config;
});

export default instance;