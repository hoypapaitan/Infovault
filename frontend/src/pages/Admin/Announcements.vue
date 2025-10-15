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
                            Announcements
                            <br/>
                            <span class="text-caption text-grey">Creates an informative details of scholarships</span>
                        </span>
                        
                        <q-space />
                        <div class="text-right">
                            <q-btn-group>
                                <!-- <q-btn color="amber" rounded glossy icon="visibility" /> -->
                                <q-btn color="primary" rounded  icon="ti-plus" label="Create Announcement" no-caps @click="modalComponents.modalStatus = !modalComponents.modalStatus" />
                            </q-btn-group>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                       <div v-if="tableLoading && itemsList.length === 0" class="text-center">
                            <q-spinner-bars
                                color="primary"
                                size="3em"
                            />
                        </div>
                        <div 
                            v-if="!tableLoading && itemsList.length === 0" 
                            class="text-center q-pa-md"
                        >
                            <q-icon color="grey-4" name="ti-dropbox-alt" size="6em" /> <br/>
                            <span class="text-caption text-grey-8">
                                No Data Can Be Shown.
                            </span>
                        </div>
                        <q-table
                            v-if="itemsList.length > 0"
                            flat
                            :rows="itemsList"
                            wrap-cells
                            :columns="tableColumns"
                            row-key="name"
                            :filter="search"
                        >  
                            <template v-slot:header="props">
                                <q-tr :props="props">
                                    <q-th
                                        v-for="col in props.cols"
                                        :key="col.name"
                                        :props="props"
                                    >
                                        {{ col.label }}
                                    </q-th>
                                    <q-th>
                                        Action
                                    </q-th>
                                </q-tr>
                            </template>
                            <template v-slot:body="props">
                                <q-tr :props="props">
                                    <q-td
                                        v-for="col in props.cols"
                                        :key="col.name"
                                        :props="props"
                                    >
                                        {{ col.value }}
                                    </q-td>
                                    <q-td class="text-center">
                                        <q-btn-group  rounded>
                                            <!-- <q-btn rounded size="sm" color="secondary" label="Edit" /> -->
                                            <q-btn rounded @click="deleteAnnouncement(props.row)" size="sm" color="negative" label="Delete" />
                                        </q-btn-group>
                                    </q-td>

                                </q-tr>
                            </template>
                        </q-table>
                    </q-card-section>
                </q-card>
            </div>

        </div>

        <!-- Modals -->
        <addUserModal 
            v-bind="modalComponents" 
            @updateStatus="updateModalStatus" 
            @updateTable="getList"
        />
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import addUserModal from "../../components/Modals/AddAnnouncement.vue"

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'AnnouncementPage',
    components:{
        addUserModal
    },
    data(){
        return {
            modalComponents: {
                modalStatus: false,
                appId: 0,
                userDetails: {},
                modalTitle: 'Create Announcement'
            },
            itemsList: [],
            tableLoading: false,
            
        }
    },
    computed: {
        tableColumns: function(){
            return [
                {
                    name: 'title',
                    required: true,
                    label: 'Title',
                    align: 'left',
                    field: row => row.title,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'announcement',
                    required: true,
                    label: 'Content',
                    align: 'left',
                    field: row => row.announcement,
                    format: val => `${val}`,
                    sortable: true
                },
            ]
        }
        
    },
    created(){
        this.getList()
    },
    methods: {
        moment,
        updateModalStatus(){
            this.modalComponents.modalStatus = false;
        },
        async getList(){
            this.itemsList = [];
            this.tableLoading = true;
            
            this.$api.get('announcement/list').then((response) => {
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
        deleteAnnouncement(data){
            // Confirm
            this.$q.dialog({
                title: 'Delete Announcement',
                message: 'Would you like to proceed with this action?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                this.$q.loading.show();

                let payload = {
                    aid: data.id
                }
                
                this.$api.post('announcement/delete/content', payload).then(async (response) => {
                    const data = {...response.data};

                    if(!data.error){
                        this.$q.notify({
                            position: 'top-left',
                            type: 'success',
                            message: data.title,
                            caption: data.message,
                            icon: 'mdi-check-all'
                        })
                        this.getList()
                    } else {
                        this.$q.notify({
                            position: 'top-left',
                            type: 'negative',
                            message: data.title,
                            caption: data.message,
                            icon: 'mdi-alert-circle-outline'
                        })
                    }
                })

                this.$q.loading.hide();
            })

            
        }
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
