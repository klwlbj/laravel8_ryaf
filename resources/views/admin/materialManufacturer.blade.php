<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>平安穗月</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" type="text/css"  href="{{asset('statics/css/antd.min.css')}}"></script>
</head>
<style>

</style>
<body>
<div id="app">
    <a-card>
        <div>
            <a-form layout="inline" >
                <a-form-item>
                    <a-input v-model="listQuery.keyword" placeholder="厂家名称" style="width: 200px;" />
                </a-form-item>
                <a-form-item>
                    <a-button icon="search" v-on:click="handleFilter">查询</a-button>
                </a-form-item>
                <a-form-item>
                    <a-button v-on:click="onCreate" type="primary" icon="edit">添加厂家</a-button>
                </a-form-item>
            </a-form>

            <a-table :columns="columns" :data-source="listSource" :loading="listLoading" :row-key="(record, index) => { return index }"
                     :pagination="pagination">

                <div slot="status" slot-scope="text, record">
                    <a-tag v-if="record.status == 0"  color="red">禁用</a-tag>
                    <a-tag v-else color="green">启用</a-tag>
                </div>

                <div slot="action" slot-scope="text, record">
                    <a style="margin-right: 8px" @click="onUpdate(record)">
                        修改
                    </a>

                    <a-popconfirm
                        title="是否确定删除商品?"
                        ok-text="确认"
                        cancel-text="取消"
                        v-on:confirm="onDel(record)"
                    >
                        <a style="margin-right: 8px">
                            删除
                        </a>
                    </a-popconfirm>
                </div>
            </a-table>
        </div>

        <a-modal :mask-closable="false" v-model="dialogFormVisible"
                 :title="status"
                 width="800px" :footer="null">
            <manufacturer-add ref="manufacturerAdd"
                 :id="id"
                 v-on:update="update"
                 v-on:add="add"
                 v-on:close="dialogFormVisible = false;"
            >
            </manufacturer-add>
        </a-modal>

    </a-card>
</div>


<script src="{{asset('statics/js/vue.js')}}"></script>
<script src="{{asset('statics/js/httpVueLoader.js')}}"></script>
<script src="{{asset('statics/js/antd.min.js')}}"></script>
<script src="{{asset('statics/js/axios.min.js')}}"></script>
<script>
    Vue.use(httpVueLoader)
    new Vue({
        el: '#app',
        data: {
            listQuery: {
                keyword: "",
            },
            listSource: [],
            listLoading:false,
            status:'新增',
            pagination: {
                pageSize: 10,
                total: 0,
                current: 1,
                onChange: this.paginationChange,
                onShowSizeChange: this.paginationChange,
            },
            columns:[
                {
                    title: 'Id',
                    dataIndex: 'mama_id',
                    width: 80
                },
                {
                    title: '厂家名称',
                    dataIndex: 'mama_name',
                    width: 100
                },
                {
                    title: '备注',
                    dataIndex: 'mama_remark'
                },
                {
                    title: '状态',
                    scopedSlots: { customRender: 'status' },
                    dataIndex: 'mama_status'
                },
                {
                    title: '提交时间',
                    dataIndex: 'mama_crt_time'
                },
                {
                    title: '操作',
                    scopedSlots: { customRender: 'action' },
                }
            ],
            dialogFormVisible:false,
            id:null
        },
        created () {
            this.listQuery.page_size = this.pagination.pageSize;
            this.handleFilter()
        },
        components: {
          "manufacturer-add":  httpVueLoader('/statics/components/material/manufacturerAdd.vue')
        },
        methods: {
            paginationChange (current, pageSize) {
                this.listQuery.page = current;
                this.pagination.current = current;
                this.listQuery.page_size = pageSize;
                this.getPageList()
            },
            // 刷新列表
            handleFilter () {
                this.listQuery.page = 1
                this.pagination.current = 1;
                this.getPageList()
            },
            // 获取列表
            getPageList () {
                this.listLoading = true
                axios({
                    // 默认请求方式为get
                    method: 'post',
                    url: '/admin/materialManufacturer/getList',
                    // 传递参数
                    data: this.listQuery,
                    responseType: 'json',
                    headers:{
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    let res = response.data;
                    this.listSource = res.data.list
                    this.pagination.total = res.data.total
                    this.listLoading = false
                }).catch(error => {
                    this.$message.error('请求失败');
                });
            },
            onCreate(){
                this.dialogFormVisible = true;
            },
            add(){

            },
            update(){

            }
        },

    })


</script>
</body>
</html>
