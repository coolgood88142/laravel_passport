<template>
    <div>
        <data-table
            :data="data"
            :columns="columns"
            @on-table-props-changed="reloadTable">
            <div slot="pagination" slot-scope="{ links = {}, meta = {} }">
                <nav class="row">
                    <div class="col-md-6 text-left">
                        <span>
                            Showing {{meta.from}} to {{meta.to}} of {{ meta.total }} Entries
                        </span>
                    </div>
                    <div class="col-md-6 text-right">
                        <button
                            :disabled="!links.prev"
                            class="btn btn-primary"
                            @click="getResults(tableProps.page-1)">
                            上一頁
                        </button>
                        <button
                            :disabled="!links.next"
                            class="btn btn-primary ml-2"
                            @click="getResults(tableProps.page+1)">
                            下一頁
                        </button>
                    </div>
                </nav>
            </div>
        </data-table>
    </div>
</template>

<script>
import DataTable from 'laravel-vue-datatable';
Vue.use(DataTable);

export default {
    props: {
		userPermissionUrl: {
			type: String,
		},
    },
    data() {
        return {
            url:  this.userPermissionUrl,
            data: {},
            tableProps: {
                search: '',
                length: 10,
                column: 'no',
                dir: 'asc',
                page: 1
            },
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    orderable: true,
                },
                {
                    label: '學員名稱',
                    name: 'name',
                    orderable: true,
                },
                {
                    label: '新增時間',
                    name: 'createDate',
                    orderable: true,
                },
            ]
        }
    },
    methods:{
        getData(url = this.url, options = this.tableProps) {
            axios.get(url, {
                params: options
            })
            .then(response => {
                this.data = response.data;
            })
            // eslint-disable-next-line
            .catch(errors => {
                //Handle Errors
            })
        },
        reloadTable(tableProps) {
            this.getData(this.url, tableProps);
        },
        getResults(page) {
            this.tableProps.page = page
            this.getData(this.url, this.tableProps);
        },
    },
    watch:{
        userPermissionUrl(val){
            this.url = val
            this.getData(this.url);
        }
    }
}
</script>
