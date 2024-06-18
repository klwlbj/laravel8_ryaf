<template>
    <div>
        <a-form-model :loading="loading" :model="formData" ref="dataForm" :label-col="dialogFormLabelCol" :wrapper-col="dialogFormWrapperCol" :rules="formRules">
            <a-form-model-item label="名称" prop="name">
                <a-input v-model="formData.mama_name" />
            </a-form-model-item>


            <a-form-model-item label="备注" prop="remark">
                <a-textarea
                    v-model="formData.mama_name"
                    placeholder="厂家备注"
                    :auto-size="{ minRows: 3, maxRows: 5 }"
                />
            </a-form-model-item>



            <a-form-model-item label="状态" prop="status">
                <a-radio-group v-model="formData.mama_name">
                    <a-radio :value="0">
                        禁用
                    </a-radio>
                    <a-radio :value="1">
                        启用
                    </a-radio>
                </a-radio-group>
            </a-form-model-item>

            <a-form-model-item :wrapper-col="{ span: 14, offset: 4 }">
                <a-button type="primary" @click="submitData">
                    确认
                </a-button>
                <a-button style="margin-left: 10px;" @click="$emit('close')">
                    取消
                </a-button>
            </a-form-model-item>

        </a-form-model>
    </div>
</template>

<script>
module.exports = {
    name: 'manufacturerAdd',
    components: {},
    props: {
        id: {
            default:function(){
                return null
            },
        },
    },
    data () {
        return {
            formData: {

            },
            imageList: [],
            listImageList: [],
            dialogFormLabelCol: { span: 4 },
            dialogFormWrapperCol: { span: 14 },
            formRules: {
                name: [{ required: true, message: '请输入厂家名称', trigger: 'blur' }],
            },
            loading :false,
            cid:undefined
        }
    },
    methods: {
        initForm(){
            this.formData= {
                name:'',
                remark:'',
                status : 1,
            };
        },
        submitData(){
            let that = this;
            this.$refs.dataForm.validate((valid) => {
                if (valid) {
                    if(that.id){
                        that.formData.id = that.id;
                        update(that.formData).then(() => {
                            this.initForm();
                            that.$emit('update');
                        })
                    }else{
                        add(that.formData).then(() => {
                            this.initForm();
                            that.$emit('add');
                        })
                    }
                }else{
                    this.$message.error('表单验证失败');
                }
            })
        },
        getDetail(id){
            if(!id){
                this.$message.error('id不能为空');
                return false;
            }
            this.loading = true;
            getInfo(id).then(response => {
                this.loading = false;
                this.formData = {
                    name: response.data.name,
                    remark: response.data.remark,
                    status: response.data.status,
                }
            })
        }
    },
    created () {
        this.initForm();
        if(this.id){
            this.getDetail(this.id);
        }
    },
    watch: {
        id (newData,oldData) {
            if(newData === oldData){
                return false
            }

            if(newData === null){
                this.initForm();

                return false;
            }

            this.getDetail(newData);
        }
    },
    computed: {

    }

}
</script>
<style scoped>

</style>

