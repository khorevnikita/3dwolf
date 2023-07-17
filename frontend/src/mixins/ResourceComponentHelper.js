import moment from "moment";
import Swal from "sweetalert2-khonik";
import axios from "@/plugins/axios";
import {formatPrice, formatDuration} from "@/plugins/formats";

export default {
    data() {
        return {
            headers: [],
            items: [],
            options: {},
            pagesCount: 1,
            totalItems: 0,
            loading: true,
            query: {},
            errors: {},
            moment: moment,
            editItem: undefined,
            editDialog: false,
            formatPrice: formatPrice,
            formatDuration: formatDuration,

            resourceKey: "",
            resourceApiRoute: "",
            resourceApiParams: "",
            deleteSwalTitle: "Вы уверены?"
        }
    },
    watch: {
        options: {
            handler(v) {
                this.query = this.copyObject({...this.query, ...this.optionsToQuery(v)});
                this.$nextTick(() => {
                    this.replaceRoute();
                });
            }, deep: true
        },
        "$route": {
            handler() {
                this.readRoute();
            }, deep: true
        },
    },
    mounted() {
        this.readRoute();
    },
    methods: {
        truncate(str, n) {
            return (str.length > n) ? str.slice(0, n - 1) + '&hellip;' : str;
        },
        search() {
            this.options.page = 1;
            this.replaceRoute();
        },
        readRoute() {
            this.query = this.$route.query;
            Object.keys(this.query).forEach(key => {
                if (this.query[key].includes(',')) {
                    this.query[key] = this.query[key].split(",")
                }
            })
            this.options = this.copyObject({...this.options, ...this.queryToOptions(this.query)});
            this.$nextTick(() => {
                this.getItems();
            })
        },
        replaceRoute() {
            this.$router.replace(`${this.$route.path}?${this.setQueryString(this.query)}`).catch(() => {
            });
        },
        getItems() {
            axios.get(`${this.resourceApiRoute}?${this.resourceApiParams}&${this.setQueryString(this.query)}`).then(body => {
                this.items = body[this.resourceKey];
                this.totalItems = body.totalCount;
                this.pagesCount = body.pagesCount;
                this.loading = false;
            })
        },
        create() {
            this.editItem = {};
            this.$nextTick(() => {
                this.editDialog = true;
            });
        },
        onCreated(resource) {
            /*if (this.items.length === 0) {
                this.$set(this, 'items', []);
            }
            this.items.unshift(resource);*/
            this.getItems();
        },
        onUpdated(resource) {
            this.getItems();
        },
        edit(item) {
            this.editItem = item;
            this.$nextTick(() => {
                this.editDialog = true;
            })
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