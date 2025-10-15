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
                        <div class="col col-xs-12 col-sm-12 col-md-12">
                            <span class="text-h6">ACCOUNT CREDENTIAL</span>
                        </div>
                        <q-input 
                            class="col col-xs-12 col-md-6 q-pa-sm q-mt-sm" 
                            bottom-slots
                            v-model="form.username"
                            label="Username" 
                            :dense="true"
                            :rules="[ val => val && val.length > 0 || 'This field is required']"
                        >
                            <template v-slot:prepend>
                                <q-icon name="account_circle" />
                            </template>
                            <template v-slot:append>
                                <q-icon name="close" @click="form.username = ''" class="cursor-pointer" />
                            </template>
                        </q-input>

                        <q-input 
                            class="col col-xs-12 col-md-6 q-pa-sm q-mt-sm" 
                            bottom-slots 
                            v-model="form.password"
                            :type="isPwd ? 'password' : 'text'"
                            label="Password" 
                            :dense="true"
                            :rules="[ val => val && val.length > 0 || 'This field is required']"
                        >
                            <template v-slot:prepend>
                                <q-icon name="password" />
                            </template>
                            <template v-slot:append>
                                <q-icon
                                    :name="isPwd ? 'visibility_off' : 'visibility'"
                                    class="cursor-pointer"
                                    @click="isPwd = !isPwd"
                                />
                            </template>
                        </q-input>





                        <div class="col col-xs-12 col-sm-12 col-md-12 q-mt-lg">
                            <span class="text-h6">USER DETAILS</span>
                        </div>
                        
                        <q-input
                            class="col col-xs-12 col-md-4  q-pa-sm q-mt-sm"
                            dense
                            v-model="form.firstName"
                            label="First Name *"
                            hint="Given Name and Suffix (if has)"
                            :rules="[ val => val && val.length > 0 || 'This field is required']"
                        />
                        <q-input
                            class="col col-xs-12 col-md-4 q-pa-sm  q-mt-sm"
                            dense
                            v-model="form.lastName"
                            label="Last Name *"
                            hint="Family Name"
                            :rules="[ val => val && val.length > 0 || 'This field is required']"
                        />
                        <q-input
                            class="col col-xs-12 col-md-4 q-pa-sm  q-mt-sm"
                            dense
                            v-model="form.middleName"
                            label="Middle Name"
                            hint="Optional"
                        />

                        <q-input
                            class="col col-xs-12 col-md-6 q-pa-sm  q-mt-sm"
                            dense
                            type="email"
                            v-model="form.email"
                            label="Email Address"
                        />
                        <q-input
                            class="col col-xs-12 col-md-6 q-pa-sm  q-mt-sm"
                            dense
                            v-model="form.contact"
                            label="Contact Number"
                        />

                        <q-select
                            class="col col-xs-12 col-md-6 q-pa-sm"
                            bottom-slots
                            v-model="form.sex" 
                            :options="sexOption" 
                            label="Sex"
                            dense 
                            :options-dense="true"
                        >
                            <template v-slot:prepend>
                                <q-icon :name="form.sex === 'Male' ? `male` : `female`" @click.stop />
                            </template>
                        </q-select>

                        <q-select
                            class="col col-xs-12 col-md-6 q-pa-sm "
                            dense
                            v-model="form.userType"
                            use-input
                            input-debounce="0"
                            label="User Type"
                            :options="typeList"
                        />
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
                username: "",
                password: "",
                lastName: "",
                firstName: "",
                middleName: "",
                sex: "Male",
                userType: '',
                email: '',
                contact: '',
                branchId: 1
            },
        }
    },
    watch:{
        modalStatus: function(newVal){
            this.openModal = newVal
            if(newVal){
                this.getTypeList();
            }
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
            this.$refs.formDetails.validate().then(success => {
                if(!success){
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
            })
        },

        async saveOrData(){
            
            let vm = this;
            this.$q.loading.show({
                message: 'Your data is submitting. Please wait...'
            });
            this.btnLoading = true;

            let payload = {
                ...this.form,
                createdBy: this.user.userId,
            }

            payload.userType = payload.userType.value

            api.post('users/register', payload).then((response) => {
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
                username: "",
                password: "",
                lastName: "",
                firstName: "",
                middleName: "",
                sex: "Male",
                userType: '',
                email: '',
                contact: '',
            }
        },
        async getTypeList(){
            api.get('misc/userTypes').then((response)=>{
                let data = {...response.data}
                let sList = [];

                data.list.forEach((el, key) => {
                    if(Number(el.id) !== 2){
                        sList.push({
                            label: el.description,
                            value: el.id
                        })
                    }
                    
                });

                this.typeList = response.status < 300 ? sList : [];
            });
        },

        // ending method
    },
    
}
</script>
