<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div v-if="profile !== null" class="col-12 col-xs-12 col-sm-12 col-md-4 q-pa-xs">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section >
                        <div class="row">
                            <div v-if="profilePicture !== null" class="col-12 q-mb-sm">
                                <q-img :src="profilePicture.uploadFile" />
                                <q-file 
                                    label="Upload your file here"
                                    bottom-slots 
                                    v-model="fileRacker"
                                    counter
                                    outlined
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="cloud_upload" @click.stop.prevent />
                                    </template>
                                    <template v-slot:append>
                                        <q-icon 
                                            v-if="fileRacker !== null" 
                                            name="mdi-trash-can"
                                            color="red"
                                            @click.stop.prevent="removeFile" 
                                            class="cursor-pointer" 
                                        />
                                        <q-icon 
                                            v-if="fileRacker !== null" 
                                            name="mdi-upload-circle"
                                            color="blue"
                                            @click.stop.prevent="updateAttachment" 
                                            class="cursor-pointer" 
                                        />
                                    </template>
                                </q-file>
                            </div>
                            <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Personal Information</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-caption text-grey q-mr-xl">Full Name</span>
                            </div>
                            <div class="col-9 q-mb-sm">
                                <span class="text-title text-bold">{{`${profile.lastName || '--'}, ${profile.firstName || '--'} ${profile.suffix || '--'}, ${profile.middleName || '--'}`}}</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-caption text-grey q-mr-xl">Contact</span>
                            </div>
                            <div class="col-9 q-mb-sm">
                                <span class="text-title text-bold">{{`${profile.contact || '--'}`}}</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-caption text-grey q-mr-xl">Email</span>
                            </div>
                            <div class="col-9 q-mb-sm">
                                <span class="text-title text-bold">{{`${profile.email || '--'}`}}</span>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div v-if="profile !== null" class="col-12 col-xs-12 col-sm-12 col-md-8 q-pa-xs">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section >
                        <div class="row">
                            <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Other Information</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${profile.civilStatus || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Civil Status</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${moment(profile.dateOfBirth).format("MMMM DD, YYYY") || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Date of Birth</span>
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">{{`${profile.address || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Address</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                            <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Student Information</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${profile.yrLvl || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Year Level</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${profile.courseDetails.title || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Course</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${profile.username || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Student Number</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${profile.schoolAttended || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">School Attended</span>
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">{{`${profile.schoolAddress || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">School Address</span>
                            </div>
                            <!-- <div class="col-12">
                                <q-separator />
                            </div> -->
                        
                            <!-- <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Family Background</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.name || '--'} (${selectedProgram.data.familyBackground.father.livingStatus})`}}</span><br/>
                                <span class="text-caption text-grey">Fathers Name</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.address || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Address</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.occupation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Occupation</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.contact || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Contact</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.educAttainment || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Educational Attainement</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.name || '--'} (${selectedProgram.data.familyBackground.mother.livingStatus})`}}</span><br/>
                                <span class="text-caption text-grey">Fathers Name</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.address || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Address</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.occupation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Occupation</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.contact || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Contact</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.educAttainment || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Educational Attainement</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.totalIncome || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Parents Total Annual Income</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.noOfSiblings || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">No. of Siblings</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">If not living with parents: </span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.guardian.name || '--'} (${selectedProgram.data.familyBackground.mother.livingStatus})`}}</span><br/>
                                <span class="text-caption text-grey">Guardian Name</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.guardian.relation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Relationship</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.guardian.occupation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Occupation</span>
                            </div> -->
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode'

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    data(){
        return {
            profile: null,
            profilePicture: null,

            fileRacker: null,
            fileName: '',
            fileSize: '',
            uploadFile: '',
            remarks: '',
            currDate: dateNow,
            reqStatus: '',
            fileDetails: null
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        },
    },
    watch: {
        fileRacker: async function(newVal){
            let convertedFile = await this.getBase64(newVal)
            this.fileName = newVal.name
            this.fileSize = newVal.size
            this.uploadFile = convertedFile
        }
    },
    created(){
        this.getUserDetails();
        this.getFileStatus();
    },
    methods: {
        moment,
        removeFile(){
            this.fileRacker = null;
            this.fileName = ''
            this.fileSize = ''
        },
        async getBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        },
        getUserDetails(){
            let payload = {
                id: this.user.userId,
            }

            this.$api.post('users/getUserById', payload).then(async (response) => {
                const data = {...response.data};
                this.profile = data
            })
        },
        getFileStatus(){
            this.$q.loading.show();
            let payload = {
                uid: this.user.userId,
                type: 'picture'
            }

            this.$api.post('document/get/attachment', payload).then(async (response) => {
                const data = {...response.data};
                if(!data.error){
                    this.profilePicture = data
                }
            })

            this.loginLoad = false;
            this.$q.loading.hide();
        },
        updateAttachment(){
            if(this.fileRacker === null){
                this.$q.notify({
                    position: 'top-left',
                    type: 'negative',
                    message: "Upload Update Failed",
                    caption: "Please attached file before uploading document",
                    icon: 'mdi-alert-circle-outline'
                })
                return false
            }

            this.$q.loading.show();
            let payload = {
                fileId: this.profilePicture.id,
                updateDetails: {
                    status: 0,
                    fileName: this.fileName,
                    fileSize: this.fileSize,
                    uploadFile: this.uploadFile,
                    remarks: "",
                }
            }
            
            this.$api.post('document/update/attachment', payload).then(async (response) => {
                const data = {...response.data};

                if(!data.error){
                    this.$q.notify({
                        position: 'top-left',
                        type: 'success',
                        message: data.title,
                        caption: data.message,
                        icon: 'mdi-check-all'
                    })
                    this.getFileStatus()
                    this.removeFile()
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
        },
    }
}
</script>
<style scoped>
.alignTextContent {
    display: flex;
    align-content: flex-start !important;
    justify-content: flex-start;
    align-items: self-start;
    text-align: left !important;
}
.my-card{
    border-radius: 15px;
    box-shadow: 0px 0px 3px -2px !important;
}
.my-card-item{
    border-radius: 10px;
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
</style>
