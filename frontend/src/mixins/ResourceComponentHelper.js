import moment from "moment";
import Swal from "sweetalert2-khonik";
import axios from "@/plugins/axios";

export default {
    data() {
        return {
            headers: [],
            items: [],
            options: {},
            totalItems: 0,
            loading: true,
            query: {},
            errors: {},
            moment: moment,
            editItem: undefined,
            editDialog: false,

            resourceKey: "",
            resourceApiRoute: "",
            resourceApiParams: "",
            deleteSwalTitle: "Вы уверены?"
        }
    },
    watch: {
        options(v) {
            this.query = this.copyObject({...this.query, ...this.optionsToQuery(v)});
            this.$nextTick(() => {
                this.replaceRoute();
            });
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
            this.items.unshift(resource);
        },
        onUpdated(resource) {
            let item = this.items.find(i => i.id === resource.id);
            if (item) {
                item = {...item, ...resource};
            }
        },
        edit(item) {
            this.editItem = item;
            this.$nextTick(() => {
                this.editDialog = true;
            })
        },
        destroy(item) {
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
                    })
                }
            })
        }
    }
}