<template>
    <div class="q-pa-sm" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12  q-pa-sm">
                <q-card
                    flat
                    class="bg-white"
                >
                    <q-card-section class="fit row wrap justify-start items-center content-center">
                        <span class="text-h6 text-bold">
                            Site Settings
                            <br/>
                            <span class="text-caption text-grey">
                                Manage site settings and database
                            </span>
                        </span>
                        
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                        <q-banner inline-actions rounded class="bg-primary text-white q-mb-md">
                            Database Backup

                            <template v-slot:action>
                                <q-btn @click="showModal('backup')" flat no-caps label="Download" icon="mdi-database-export" />
                            </template>
                        </q-banner>
                        <q-banner inline-actions rounded class="bg-primary text-white">
                            Database Restore

                            <template v-slot:action>
                                <q-file outlined dense v-model="fileracker">
                                    <template v-slot:prepend>
                                    <q-icon name="attach_file" />
                                    </template>
                                </q-file>
                                <q-btn @click="showModal('restore')" flat no-caps label="Upload" icon="mdi-database-import" />
                            </template>
                        </q-banner>
                    </q-card-section>
                </q-card>
            </div>

        </div>


        <q-dialog persistent v-model="modalStatus">
            <q-card style="width: 500px; max-width: 80vw;">
                <q-card-section>
                    <div class="text-h6">Export Database</div>
                </q-card-section>

                <q-separator />

                <q-card-section style="max-height: 60vh" class="scroll">
                    <q-form ref="passForm" class="q-gutter-md">
                        <q-input 
                            class="q-pt-md" 
                            bottom-slots 
                            v-model="password"
                            outlined
                            :type="isPwd ? 'password' : 'text'"
                            label="Enter User Password" 
                            :dense="true"
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
                    </q-form>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" :loading="btnLoading" color="negative" @click="cancelChange" />
                    <q-btn 
                        v-if="actionType === 'backup'"
                        flat 
                        label="Download" 
                        :loading="btnLoading" 
                        color="positive" 
                        @click="downloadSQL" 
                    />
                    <q-btn 
                        v-if="actionType === 'restore'"
                        flat 
                        label="Restore" 
                        :loading="btnLoading" 
                        color="positive" 
                        @click="restoreDb" 
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';


const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'PayrollPage',
    components:{},
    data(){
        return {
            actionType: "backup",
            fileracker: null,
            password: '',
            isPwd: true,
            modalStatus: false,
            itemsList: [],
            tableLoading: false,
        }
    },
    computed: {
        user(){
            const user = LocalStorage.getItem('userData')
            return jwtDecode(user);
        },
        tableColumns: function(){
            return [
                {
                    name: 'name',
                    required: true,
                    label: 'Name',
                    align: 'left',
                    field: row => row.name,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'username', label: 'Username', field: 'username'},
                { name: 'desc', label: 'Type', field: 'desc' },
                { name: 'email', label: 'Email Address', field: 'email' },
                { name: 'contact', label: 'Contact #', field: 'contact' },
                // { name: 'status', label: 'Status', field: 'status' },
                { name: 'createdAt', label: 'Created Date', field: 'createdAt' }
            ]
        }
        
    },
    created(){
        this.getList()
    },
    methods: {
        moment,
        showModal(action){
            this.actionType = action
            this.modalStatus = true;
            
        },
        downloadSQL(){
            this.$q.loading.show({
                message: 'Downloading database. Please wait...'
            });
            let payload = {
				userId: this.user.userId,
				password: this.password 
			}

			this.$api.post("misc/database/backup", payload).then((res) => {
				let response = {...res.data}
				if(!response.error){
					// console.log(res.data)
					const anchor = document.createElement('a');
					anchor.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(res.data);
					anchor.target = '_blank';
					anchor.download = `database-backup-${new Date().toISOString()}.sql`;
					anchor.click();
					this.modalStatus = false;
				} else {
					// show Error
					console.log('there is some error')
				}

                this.$q.loading.hide();
			})
        },
        restoreDb(){
            
            if (!this.fileracker) {
                console.log('No file selected');
                return;
            }

            this.$q.loading.show({
                message: 'Restoring database. Please wait... '
            });

            const formData = new FormData();
            formData.append('backup_file', this.fileracker);
            formData.append('userId', this.user.userId);
            formData.append('password', this.password);

            this.$api.post('misc/database/restore', formData, {
                headers: {
                'Content-Type': 'multipart/form-data'
                }
            }).then((res) => {
                let response = { ...res.data };
                if (!response.error) {
                    console.log('Database restored successfully');
                    this.modalStatus = false;
                } else {
                    console.log('There is some error');
                }

                this.$q.loading.hide();
            })
        },
        cancelChange(){
            this.modalStatus = false;
        },
        updateModalStatus(){
            this.modalComponents.modalStatus = false;
        },
        updateUserStatus(data){
            this.$q.dialog({
                title: 'Update User Status',
                message: 'Are you sure you want to take this action?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                let payload = {
                    id: data.id,
                    status: Number(data.status) === 1 ? 0 : 1
                };
                this.$q.loading.show({
                    message: 'Changing user status. Please wait...'
                });

                this.$api.post('users/update/status', payload).then((response) => {
        
                    const data = {...response.data};
                    if(!data.error){
                        this.$q.notify({
                            color: 'positive',
                            position: 'top-right',
                            title:data.title,
                            message:'Status updated',
                            icon: 'report_problem'
                        })
                        this.getList()
                    } else {
                        this.$q.notify({
                            color: 'negative',
                            position: 'top-right',
                            title:data.title,
                            message: vm.$t(`errors.${data.error}`),
                            icon: 'report_problem'
                        })
                    }
                })

                this.$q.loading.hide();

            })
        },
        async getList(){
            this.itemsList = [];
            this.tableLoading = true;
            
            this.$api.get('users/getAllUserList').then((response) => {
                const data = {...response.data};

                if(!data.error){
                    this.itemsList = response.status < 300 ? data.list : [];
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })
                }

            })

            this.tableLoading = false;
        },
    }
}
</script>
<style scoped>
.my-card{
    border-radius: 10px;
    box-shadow: 0px 0px 3px -2px !important;
}
.my-card-item{
    border-radius: 5px;
    border: 1px solid gray;
}
.generatedDash{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(255 251 176) 0%, rgb(0 55 255 / 61%) 89%);
}
.generatedDash2{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(38 148 243) 0%, rgb(45 253 215 / 61%) 89%);
}
.prod-item{
    cursor: pointer;
    height: 1.5rem;
    border-radius: 4px;
}
.prod-item:hover{
    background-color: rgb(199, 196, 196);
}
</style>
