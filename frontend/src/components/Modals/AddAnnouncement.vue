<template>
    <div class="q-pa-sm">
        <q-dialog persistent v-model="openModal">
            <q-card style="width: 700px; max-width: 80vw;">
                <q-card-section>
                    <div class="text-h6">{{ modalTitle }}</div>
                </q-card-section>

                <q-separator />

                <q-card-section style="max-height: 60vh" class="scroll">
                    <q-form
                        ref="formDetails"
                        class="row"
                    >
                        <div class="col col-xs-12 col-sm-12 col-md-12 q-mb-sm">
                            <q-input
                                dense
                                outlined=""
                                v-model="form.title"
                                label="Title"
                            />
                        </div>
                        <div class="col col-xs-12 col-sm-12 col-md-12">
                            <q-editor v-model="form.announcement"  min-height="5rem" />
                        </div>
                    </q-form>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" :loading="btnLoading" color="negative" @click="closeModal" />
                    <q-btn flat label="Submit" :loading="btnLoading" color="positive" @click="submitForm" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode'
import { api } from 'boot/axios'

const dateNow = moment().format('MM/DD/YYYY');

export default {
    name: 'AddUserModal',
    props: {
        appId: {
            type: Number
        },
        userDetails: {
            type: Object
        },
        modalStatus: {
            type: Boolean
        },
        modalTitle: {
            type: String
        }
    },
    data(){
        return {
            btnLoading: false,
            openModal: false,
            maximizedToggle: true,
            isPwd: true,
            typeList: [],
            sexOption: [
                'Male',
                'Female'
            ],
            form: {
                title: "",
                announcement: "",
                postedBy: ""
            },
        }
    },
    watch:{
        modalStatus: function(newVal){
            this.openModal = newVal
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        }
    },
    methods: {
        async closeModal(){
            this.$emit('updateStatus', false);
        },
        submitForm(){
            if(this.form.announcement === ""){
                this.$q.notify({
                    color: 'negative',
                    position: 'top-right',
                    title: 'Incomplete Form',
                    message: 'Please fill the required fields',
                    icon: 'report_problem'
                })
                return false;
            } else {
                // Confirm
                this.$q.dialog({
                    title: 'Save Data',
                    message: 'Would you like to save your data?',
                    ok: {
                        label: 'Yes'
                    },
                    cancel: {
                        label: 'No',
                        color: 'negative'
                    },
                    persistent: true
                }).onOk(() => {
                    this.saveOrData();
                })
            }
        },

        async saveOrData(){
            
            let vm = this;
            this.$q.loading.show({
                message: 'Your announcement is posting. Please wait...'
            });
            this.btnLoading = true;

            let payload = {
                ...this.form,
                createdBy: this.user.userId,
            }

            api.post('announcement/create/new', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$q.notify({
                        color: 'positive',
                        position: 'top-right',
                        title:data.title,
                        message: data.message,
                        icon: 'done'
                    })
                    this.$q.loading.hide();
                    
                    this.$nextTick(() => {
                        this.resetForm();
                        this.$emit('updateTable');
                    });
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })
                    this.$q.loading.hide();
                }

            })
            
            this.btnLoading = false;
        },
        resetForm(){
            this.form = {
                announcement: "",
                postedBy: ""
            }
        },
        // ending method
    },
    
}
</script>
