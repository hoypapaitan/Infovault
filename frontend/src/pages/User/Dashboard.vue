<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <span class="text-h6 text-bold">Hi {{`${user.fullName}`}},</span><br/>
                <span class="text-caption">Welcome to ASCOT Scholarship Application</span><br/>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-4 q-pa-sm">
                <q-card
                    flat
                    class="q-mb-xl"
                >
                    <q-card-section >
                        <span class="text-h6">Announcement</span><br/>
                        <q-separator></q-separator>
                        <div v-if="!announceLoading && announcements.length > 0">
                            <q-list class="rounded-borders">
                            <q-item 
                                v-for="(itm, indx) in announcements"
                                :key="indx"
                            >
                                <!-- <q-item-section avatar>
                                <q-avatar>
                                    <q-icon name="newspaper" size="md" />
                                </q-avatar>
                                </q-item-section> -->

                                <q-item-section>
                                        <q-item-label class="text-bold text-h6" lines="1">{{itm.title}}</q-item-label>
                                        <q-item-label caption>
                                            <div v-html="itm.announcement"></div>
                                        </q-item-label>
                                        <q-item-label caption>
                                            {{ moment(itm.postedDate).format("LL LT") }}
                                        </q-item-label>
                                </q-item-section>
                            </q-item>
                            </q-list>
                        </div>
                        <div v-if="!announceLoading && announcements.length === 0" class="text-center">
                            <q-icon color="grey-4" name="mdi-bullhorn-variant" size="6em" /> <br/>
                            <span class="text-caption text-grey-8">
                                No Announcement to be shown.
                            </span>
                        </div>
                    </q-card-section>
                </q-card>
                <q-card
                    flat
                    class="my-card active-scholar"
                >
                    <q-card-section>
                        <div class="row">
                            <div class="col-6">
                                <span class="text-h6">Active Scholarship</span><br/>
                                <span class="text-caption text-bold">{{ approvedApplication !== null ? approvedApplication.title : 'No Approved Program' }}</span><br/>
                                <span class="text-caption">{{ approvedApplication !== null ? moment(approvedApplication.data.dateApproved).format("LL") : '--' }}</span><br/>
                                <span v-if="approvedApplication !== null" class="text-caption">
                                    <a :href="approvedApplication.data.scholarship.otherDetailsLink" target="_blank" >
                                        {{ approvedApplication !== null ? approvedApplication.data.scholarship.otherDetailsLink : '--' }}
                                    </a>
                                </span><br/>
                            </div>
                            <div class="col-6 text-right">
                                <q-img :src="`/imgs/ASCT_logo2.png`" />
                            </div>
                            <div class="col-12">
                                <div class="text-right">
                                    <!-- <q-btn flat color="white" size="sm" label="View Scholarship Details" /> -->
                                </div>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-8 q-pa-sm">
                <q-card
                    flat
                    class=" bg-white"
                >
                    <q-card-section>
                        <span class="text-h6">Available Scholarship</span><br/>
                        <q-separator></q-separator>
                        <div v-if="approvedApplication !== null" class="text-center">
                            <q-icon color="grey-4" name="mdi-bullhorn-variant" size="6em" /> <br/>
                            <span class="text-caption text-grey-8">
                                You have already enrolled to an Scholarship Program
                            </span>
                        </div>
                        <div v-if="approvedApplication === null" class="q-mt-md">
                            <q-list
                                v-for="(item, idx) in itemsScholarList"
                                :key="idx"
                                bordered 
                                class="rounded-borders itemBorder q-mb-sm"
                            >
                                <q-item-label 
                                    class="text-bold"
                                    :class="Number(item.original.slot) === Number(item.original.applied) ? 'text-grey-8' : 'text-primary' " 
                                    header
                                >
                                    {{`${item.original.provider}`}} 
                                    <span v-if="Number(item.original.slot) === Number(item.original.applied)">(No Slots Available)</span><br/>
                                    {{item.title}}
                                </q-item-label>

                                <q-item>
                                    <q-item-section avatar top>
                                        <q-img :src="`/imgs/${item.original.provider}.png`"  />
                                    </q-item-section>

                                    <q-item-section >
                                        <q-item-label lines="1">
                                            <span class="text-weight-medium">Submission Due Date: {{moment(item.original.dueDate).format('LL')}}</span><br/>
                                            <span class="text-grey-8">
                                                Details Link: <a :href="item.original.otherDetailsLink" target="_blank" >
                                                    {{ item.original.otherDetailsLink }}
                                                </a>
                                            </span>
                                        </q-item-label>
                                    </q-item-section>

                                    <q-item-section side>
                                    <div class="text-grey-8 q-gutter-xs">
                                        <!-- <q-btn class="gt-xs" size="12px" flat dense round icon="delete" /> -->
                                        <q-btn 
                                            @click="applyForScholarship(item.original)"
                                            :disabled="Number(item.original.slot) === Number(item.original.applied)"
                                            class="gt-xs" 
                                            outline 
                                            color="primary" 
                                            no-caps  
                                            dense 
                                            label="Apply Scholarship" 
                                        />
                                        <!-- <q-btn size="12px" flat dense round icon="more_vert" /> -->
                                    </div>
                                    </q-item-section>
                                </q-item>
                            </q-list>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            
            
        </div>


        <!-- Application Submit -->
        <q-drawer
            side="right"
            v-model="drawerRight"
            bordered
            overlay
            :width="900"
        >
            <q-scroll-area class="fit">
                <q-card
                    flat
                    class=" bg-white"
                >
                    <q-card-section class="row items-center no-wrap">
                        <div>
                            <div class="text-h5 text-weight-bold">{{selectedProgram.title}}</div>
                        </div>
                        <q-space />
                        <q-btn size="sm" rounded color="red" icon="ti-close" label="Cancel" @click="drawerRight = !drawerRight" />
                    </q-card-section>
                    <q-separator />
                    <q-card-section >
                        <q-stepper
                            v-model="step"
                            ref="stepper"
                            color="primary"
                            animated
                            flat
                            :swipeable="false"
                        >
                            <q-step
                                :name="1"
                                title="Details"
                                icon="settings"
                                :done="step > 1"
                            >   
                                <div class="row">
                                    <div class="col-6 q-mb-sm">
                                        <span class="text-caption text-grey">Read more details for this program: </span>
                                        <span class="text-title text-bold"><a :href="selectedProgram.otherDetailsLink" target="_blank"> {{`${selectedProgram.otherDetailsLink || '--'}`}}</a></span>
                                    </div>
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-caption text-grey">Courses Covered on Program: </span><br/>
                                        <q-chip 
                                        v-for="(itm, indx) in convertCourses(selectedProgram.coveredCourses)"
                                        :key="indx"
                                        outline color="primary" text-color="white">
                                            {{ itm }}
                                        </q-chip>
                                    </div>
                                </div> 
                                <q-separator />
                                <div class="row q-mt-md">
                                    <div class="col-12">
                                        <q-list>
                                            <q-item 
                                                v-for="(item, index) in selectedProgram.requirements" 
                                                :key="item.name" 
                                                tag="label" 
                                                v-ripple
                                            >
                                                <q-item-section side>
                                                    <q-icon :color="item.color" name="task_alt" />
                                                </q-item-section>

                                                <q-item-section>
                                                    <q-item-label>{{ item.label }}</q-item-label>
                                                </q-item-section>

                                                <q-item-section side>
                                                    <span v-if="item.fileUploaded !== undefined">Already uploaded</span>
                                                    <q-file
                                                        v-if="item.fileUploaded === undefined"
                                                        outlined  
                                                        v-model="item.file"  
                                                        label="Upload File Here"
                                                        @update:model-value="evnt => {return checkFile(evnt, index)}"
                                                        dense
                                                    >
                                                        <template v-slot:prepend>
                                                        <q-icon name="cloud_upload" @click.stop.prevent />
                                                        </template>
                                                        <template v-slot:append>
                                                        <q-icon name="close" @click.stop.prevent="item.file = null" class="cursor-pointer" />
                                                        </template>
                                                    </q-file>
                                                </q-item-section>
                                            </q-item>
                                        </q-list>
                                    </div>
                                </div>


                            </q-step>

                            <q-step
                                :name="2"
                                title="Requirements"
                                icon="create_new_folder"
                                :done="step > 2"
                            >
                                <div class="row">
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <span class="text-title text-bold">Father Details</span>
                                    </div>
                                    <div class="col-12 col-md-8 q-pa-xs">
                                        <q-input
                                            v-model="form.father.name"
                                            label="Father's Name"
                                            placeholder="Enter Name"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <div class="q-gutter-sm">
                                            <q-radio v-model="form.father.livingStatus" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" val="living" label="Living" />
                                            <q-radio v-model="form.father.livingStatus" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" val="deceased" label="Deceased" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.father.occupation"
                                            label="Occupation"
                                            placeholder="Enter occupation"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <!-- <q-input
                                            v-model="form.father.educAttainment"
                                            label="Educational Attainment"
                                            placeholder="Enter educational attainment"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input> -->
                                        <q-select
                                            outlined
                                            dense
                                            stack-label 
                                            v-model="form.father.educAttainment"
                                            :options="educAttOpt"
                                            label="Educational Attainment"
                                            emit-value
                                            map-options
                                        >
                                            <template v-slot:option="{ itemProps, opt, selected, toggleOption }">
                                                <q-item v-bind="itemProps">
                                                    <q-item-section>
                                                        <q-item-label v-html="opt" />
                                                    </q-item-section>
                                                    <q-item-section side>
                                                        <q-checkbox :model-value="selected" checked-icon="task_alt" unchecked-icon="radio_button_unchecked" @update:model-value="toggleOption(opt)" />
                                                    </q-item-section>
                                                </q-item>
                                            </template>
                                        </q-select>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.father.contact"
                                            label="Contact Number"
                                            placeholder="Enter Mobile/Phone"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <q-input
                                            type="textarea"
                                            v-model="form.father.address"
                                            label="Address"
                                            placeholder="Enter Address"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <span class="text-title text-bold">Mother Details</span>
                                    </div>
                                    <div class="col-12 col-md-8 q-pa-xs">
                                        <q-input
                                            v-model="form.mother.name"
                                            label="Mothers's Name"
                                            placeholder="Enter Name"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <div class="q-gutter-sm">
                                            <q-radio v-model="form.mother.livingStatus" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" val="living" label="Living" />
                                            <q-radio v-model="form.mother.livingStatus" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" val="deceased" label="Deceased" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.mother.occupation"
                                            label="Occupation"
                                            placeholder="Enter occupation"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <!-- <q-input
                                            v-model="form.mother.educAttainment"
                                            label="Educational Attainment"
                                            placeholder="Enter educational attainment"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input> -->
                                        <q-select
                                            outlined
                                            dense
                                            stack-label 
                                            v-model="form.mother.educAttainment"
                                            :options="educAttOpt"
                                            label="Educational Attainment"
                                            emit-value
                                            map-options
                                        >
                                            <template v-slot:option="{ itemProps, opt, selected, toggleOption }">
                                                <q-item v-bind="itemProps">
                                                    <q-item-section>
                                                        <q-item-label v-html="opt" />
                                                    </q-item-section>
                                                    <q-item-section side>
                                                        <q-checkbox :model-value="selected" checked-icon="task_alt" unchecked-icon="radio_button_unchecked" @update:model-value="toggleOption(opt)" />
                                                    </q-item-section>
                                                </q-item>
                                            </template>
                                        </q-select>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.mother.contact"
                                            label="Contact Number"
                                            placeholder="Enter Mobile/Phone"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <q-input
                                            type="textarea"
                                            v-model="form.mother.address"
                                            label="Address"
                                            placeholder="Enter Address"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <span class="text-title text-bold">Other Details</span>
                                    </div>
                                    <div class="col-12 col-md-6 q-pa-xs">
                                        <q-input
                                            v-model="form.totalIncome"
                                            label="Total Parent Gross Income"
                                            placeholder="Enter Amount"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-6 q-pa-xs">
                                        <q-input
                                            v-model="form.noOfSiblings"
                                            label="No. Of Siblings in the family"
                                            placeholder="Enter Count"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <span class="text-title">Are you living with your parents?: </span>
                                        <div class="q-gutter-sm">
                                            <q-radio v-model="form.notWithParents" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" :val="true" label="Yes" />
                                            <q-radio v-model="form.notWithParents" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" :val="false" label="No" />
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!form.notWithParents" class="row">
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <span class="text-title text-bold">Guardian Details</span>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.guardian.name"
                                            label="Guardian Name"
                                            placeholder="Enter name"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.guardian.occupation"
                                            label="Occupation"
                                            placeholder="Enter value"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.guardian.relation"
                                            label="Relationship"
                                            placeholder="Enter relation to the guadian"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                </div>
                            </q-step>

                            <q-step
                                :name="3"
                                title="Summary"
                                icon="assignment"
                            >
                                <div class="row">
                                    <div class="col-12 col-md-12">
                                        <span class="text-title text-bold">Personal Information</span>
                                    </div>
                                    <div class="col-3 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.firstName || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">First Name</span>
                                    </div>
                                    <div class="col-3 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.middleName || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Middle Name</span>
                                    </div>
                                    <div class="col-3 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.lastName || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Last Name</span>
                                    </div>
                                    <div class="col-3 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.suffix || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">suffix</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.civilStatus || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Civil Status</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${moment(myInfo.dateOfBirth).format("MMMM DD, YYYY") || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Date of Birth</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.contact || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Contact</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.email || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Email</span>
                                    </div>
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.address || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Address</span>
                                    </div>
                                    <div class="col-12">
                                        <q-separator />
                                    </div>
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <span class="text-title text-bold">Student Information</span>
                                    </div>
                                    <div class="col-3 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.yrLvl || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Year Level</span>
                                    </div>
                                    <div class="col-3 q-mb-sm">
                                        <span class="text-title text-bold">{{`${convertCourses(myInfo.courseId) || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Course</span>
                                    </div>
                                    <div class="col-3 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.username || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Student Number</span>
                                    </div>
                                    <div class="col-3 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.schoolAttended || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">School Attended</span>
                                    </div>
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.schoolAddress || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">School Address</span>
                                    </div>
                                    <div class="col-12">
                                        <q-separator />
                                    </div>
                                
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <span class="text-title text-bold">Family Background</span>
                                    </div>
                                    <div class="col-6 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.father.name || '--'} (${form.father.livingStatus})`}}</span><br/>
                                        <span class="text-caption text-grey">Fathers Name</span>
                                        <br/>
                                        <span class="text-title text-bold">{{`${form.father.address || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Address</span>
                                        <br/>
                                        <span class="text-title text-bold">{{`${form.father.occupation || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Occupation</span>
                                        <br/>
                                        <span class="text-title text-bold">{{`${form.father.contact || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Contact</span>
                                        <br/>
                                        <span class="text-title text-bold">{{`${form.father.educAttainment || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Educational Attainement</span>
                                    </div>
                                    <div class="col-6 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.mother.name || '--'} (${form.mother.livingStatus})`}}</span><br/>
                                        <span class="text-caption text-grey">Mothers Name</span>
                                        <br/>
                                        <span class="text-title text-bold">{{`${form.mother.address || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Address</span>
                                        <br/>
                                        <span class="text-title text-bold">{{`${form.mother.occupation || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Occupation</span>
                                        <br/>
                                        <span class="text-title text-bold">{{`${form.mother.contact || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Contact</span>
                                        <br/>
                                        <span class="text-title text-bold">{{`${form.mother.educAttainment || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Educational Attainement</span>
                                    </div>
                                    <div class="col-6 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.totalIncome || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Parents Total Annual Income</span>
                                    </div>
                                    <div class="col-6 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.noOfSiblings || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">No. of Siblings</span>
                                    </div>
                                    <div class="col-12">
                                        <q-separator />
                                    </div>
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-title text-bold">If not living with parents: </span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.guardian.name || '--'} (${form.mother.livingStatus})`}}</span><br/>
                                        <span class="text-caption text-grey">Guardian Name</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.guardian.relation || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Relationship</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.guardian.occupation || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Occupation</span>
                                    </div>
                                    <div class="col-12">
                                        <q-separator />
                                    </div>
                                    
                                    <div class="col-12 col-md-12 q-pa-xs q-mt-md">
                                        <span class="text-title text-bold">Requirement Checklist</span>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <q-list>
                                            <q-item 
                                                v-for="(item, index) in selectedProgram.requirements" 
                                                :key="item.name" 
                                                tag="label" 
                                                v-ripple
                                            >
                                                <q-item-section side>
                                                    <q-icon :color="item.color" name="task_alt" />
                                                </q-item-section>

                                                <q-item-section>
                                                    <q-item-label>{{ item.label }}</q-item-label>
                                                </q-item-section>

                                                <q-item-section side>
                                                    <div v-if="item.fileUploaded === undefined">
                                                        <q-btn outline size="sm" rounded color="red" label="Request For Upload" />
                                                    </div>
                                                    <div v-if="item.fileUploaded !== undefined">
                                                        <q-btn
                                                            @click="previewDocs(item.uploadFile, item.name)"
                                                            outline 
                                                            size="sm" 
                                                            class="q-mr-xs" 
                                                            rounded 
                                                            color="primary" 
                                                            label="View"
                                                        />
                                                    </div>
                                                </q-item-section>
                                            </q-item>
                                        </q-list>
                                    </div>

                                    <div class="col-12 col-md-12 q-pa-xs q-mt-md">
                                        <span class="text-title text-bold">Qualifications</span>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <q-list>
                                            <q-item 
                                                v-for="(itm, indx) in selectedProgram.qualification"
                                                :key="indx"
                                            >
                                                <q-item-section avatar>
                                                    <q-avatar>
                                                        <q-icon name="mdi-asterisk-circle-outline" size="sm" />
                                                    </q-avatar>
                                                </q-item-section>

                                                <q-item-section>
                                                    <q-item-label>
                                                        {{itm.description}}
                                                    </q-item-label>
                                                </q-item-section>
                                            </q-item>
                                        </q-list>
                                    </div>
                                </div>
                            </q-step>
                        </q-stepper>

                        
                    </q-card-section>
                    <q-separator />
                    <q-card-actions>
                        <q-stepper-navigation >
                            <q-btn  
                                v-if="step < 3" @click="$refs.stepper.next()" 
                                color="primary"
                                label="Continue"
                                :disable="checkFormData"
                            />
                            <q-btn v-if="step === 3" @click="openPrint" class="q-mr-sm" color="positive" label="Preview Form" />
                            <q-btn v-if="step === 3" @click="submitApplicationFormData" color="positive" label="Finish" />
                            <q-btn v-if="step > 1" flat color="primary" @click="$refs.stepper.previous()" label="Back" class="q-ml-sm" />
                        </q-stepper-navigation>
                    </q-card-actions>
                </q-card>
            </q-scroll-area>
        </q-drawer>
        <!-- End of Application Submit Drawer -->
        <printFormModal 
            :modalStatus="printModal"
            :appData="appData"
            @updatePrintStatus="closeFormModal"
        />
        <previewModal 
            :modalStatus="previewModalStatus"
            :appData="selectedFile"
            @updatePrintStatus="closePreviewModal"
        />
    </div>
</template>

<script>
import moment from 'moment'
import printFormModal from '../../components/Modals/PrintFormModel.vue';
import previewModal from '../../components/Modals/PreviewDocument.vue';
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';


const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    components: {
        printFormModal,
        previewModal
    },
    data(){
        return {
            tableLoading: false,
            announceLoading: false,
            approvedApplication: null,
            itemsList: [],
            announcements: [],

            step: 1,
            printModal: false,
            appData: {},
            itemsScholarList: [],

            drawerRight: false,
            selectedProgram: {},
            myInfo: {},
            courseOpt: [],
            requirementTab: "",
            educAttOpt: [
                'graduate', 
                'undergraduate', 
                'senior highschool graduate', 
                'junior highschool graduate', 
                'elementary graduate'
            ],
            form: {
                father:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                mother:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                totalIncome: 0,
                noOfSiblings: 0,
                notWithParents: true,
                guardian: {
                    name:"",
                    occupation:"",
                    relation:"",
                },
            },

            selectedFile: "",
            previewModalStatus: false,
        }
    },
    watch:{
        drawerRight(newVal){
            if(newVal){
                this.getFileStatus()
            }
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        },
        checkFormData(){
            let res = false
            if(this.step === 1){
                let filterFiles = this.selectedProgram?.requirements?.filter(el => el.fileUploaded === undefined)
                res = filterFiles?.length > 0
            } else if(this.step === 2){
                if(this.itemsList.length > 0){
                    for(const i in this.form){
                        this.form[i] = this.itemsList[0].data.familyBackground[i]
                    }
                }


                let checkItemVal = 0;
                let unvalidate = "occupation,educAttainment"
                for(const father in this.form.father){
                    if(
                    this.form.father[father] === "" &&
                    !unvalidate.includes(father)
                    ){
                        checkItemVal += 1
                    }
                }
                for(const mother in this.form.mother){
                    if(
                    this.form.mother[mother] === "" &&
                    !unvalidate.includes(mother)
                    ){
                        checkItemVal += 1
                    }
                }

                if(!this.form.notWithParents){
                    for(const guardian in this.form.guardian){
                        if(
                            this.form.guardian[guardian] === ""
                        ){
                            checkItemVal += 1
                        }
                    }
                }
                
                res = checkItemVal > 1
            }

            return res
        }
    },
    created(){
        this.getApplied()
        this.getAnnouncementList()
        this.getCourses()
        this.getList()
        this.getMyDetails()
    },
    methods: {
        moment,
        previewDocs(file, reqType){
            this.previewModalStatus = true
            this.selectedFile = {
                url: file,
                type: reqType
            }
        },
        closePreviewModal(){
            this.previewModalStatus = false
        },
        checkStepProcess(data){
            let res = 1;

            if(Number(data.evaluatedBy) !== 0 && Number(data.approvedBy) === 0){
                res = 2
            } else if(Number(data.evaluatedBy) > 0 && Number(data.approvedBy) > 0){
                res = 3
            }
            return res
        },
        async getApplied(){
            this.tableLoading = true
            this.itemsList = []

            this.$api.post('scholarship/applied/status', {sId: this.user.userId}).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.itemsList = data.list

                    let checkApproved = data.list.filter(el => {return Number(el.data.appStatus) === 2})
                    this.approvedApplication = checkApproved.length > 0 ? checkApproved[0] : null
                } 

                this.tableLoading = false
            })
        },
        async getAnnouncementList(){
            this.announcements = [];
            this.announceLoading = true;
            
            this.$api.get('announcement/list').then((response) => {
                const data = {...response.data};

                if(!data.error){
                    this.announcements = response.status < 300 ? data.list.sort((a, b) => +(a.createdDate < b.createdDate) || -(a.createdDate > b.createdDate)) : [];
                }

            })

            this.announceLoading = false;
        },
        async submitApplicationFormData(){
            this.$q.dialog({
                title: 'Submit Application',
                message: 'Do you finalize and submit your application?',
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
                this.loginLoad = true;
                let vm = this;
                let payload = {
                    status: "Submitted application for evaluation",
                    familyBackground: this.form,
                    studentId: Number(this.user.userId),
                    scholarId: Number(this.selectedProgram.id),
                };

                this.$api.post('scholarship/submit/application', payload).then(async (response) => {
                    const data = {...response.data};
                    if(!data.error){
                        this.$q.notify({
                            position: 'top-left',
                            type: 'positive',
                            message: data.title,
                            caption: data.message,
                            icon: 'verified'
                        })
                        this.resetForm()
                        this.drawerRight = false
                    } else {
                        this.$q.notify({
                            position: 'top-left',
                            type: 'negative',
                            message: data.title,
                            caption: data.message,
                            icon: 'report_problem'
                        })
                    }
                })

                this.$q.loading.hide();
            })
        },
        resetForm(){
            this.form = {
                father:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                mother:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                totalIncome: 0,
                noOfSiblings: 0,
                notWithParents: true,
                guardian: {
                    name:"",
                    occupation:"",
                    relation:"",
                },
            }
        },
        async checkFile(fileVal, index){
            this.$q.loading.show();
            let convertedFile = await this.getBase64(fileVal)
            let payload = {
                userId: this.user.userId,
                reqType: this.selectedProgram.requirements[index].name,
                reqTitle: this.selectedProgram.requirements[index].label,
                fileName: fileVal.name,
                fileSize: fileVal.size,
                uploadFile: convertedFile,
                remarks: 'Waiting for validation of the scholarship unit',
                status: 0
            }

            this.$api.post('document/upload', payload).then(async (response) => {
                const data = {...response.data};

                if(!data.error){
                    this.$q.notify({
                        position: 'top-left',
                        type: 'success',
                        message: data.title,
                        caption: data.message,
                        icon: 'mdi-check-all'
                    })
                    this.selectedProgram.requirements[index].fileUploaded = true
                    this.selectedProgram.requirements[index].color = 'green'
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
        async getBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        },
        getFileStatus(){
            let payload = {
                uid: this.user.userId,
            }

            this.$api.post('document/get/attachments', payload).then(async (response) => {
                const data = {...response.data};

                if(!data.error){
                    // fill the already uploaded document
                    data.list.forEach(el => {
                        let filterMatch = this.selectedProgram.requirements.filter((elr) => {return elr.name === el.reqType})
                        let val = filterMatch[0]
                        let index = this.selectedProgram.requirements.indexOf(val)

                        this.selectedProgram.requirements[index].file = el.fileName
                        this.selectedProgram.requirements[index].uploadFile = el.uploadFile
                        this.selectedProgram.requirements[index].fileUploaded = true
                        this.selectedProgram.requirements[index].color = 'green'
                    });
                } else {
                    // error
                }
            })

            this.loginLoad = false;
            this.$q.loading.hide();
        },
        applyForScholarship(val){
            // apply/validate
            this.$q.loading.show({
                message: 'Application Check. Please wait...'
            });
            let payload = {
                sId: this.user.userId,
                scholarId: val.id
            }
            this.$api.post('scholarship/apply/validate', payload).then((response) => {
        
                const data = {...response.data};
                if(!data.error){
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title: 'Unable to Apply',
                        message: 'You already submitted the application wait for the scholarship units to evaluate and approve your application',
                        icon: 'report_problem'
                    })
                } else {
                    this.drawerRight = true
                    this.selectedProgram = val
                }
            })

            this.$q.loading.hide();
            
        },
        openPrint(){
            let data = {
                student: {...this.myInfo},
                scholar: {...this.selectedProgram},
                others: {...this.form}
            }
            this.appData = data
            this.printModal = true
        },
        closeFormModal(){
            this.printModal = false
        },
        async getList(){
            this.itemsScholarList = []
            this.$api.get('scholarship/list').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.itemsScholarList = data.list
                } else {
                    // this.$q.notify({
                    //     position: 'top-left',
                    //     type: 'negative',
                    //     message: data.title,
                    //     caption: data.message,
                    //     icon: 'report_problem'
                    // })
                }
            })
        },
        async getMyDetails(){
            this.$api.post('users/getUserById', {id:this.user.userId}).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.myInfo = data
                } else {
                    // this.$q.notify({
                    //     position: 'top-left',
                    //     type: 'negative',
                    //     message: data.title,
                    //     caption: data.message,
                    //     icon: 'report_problem'
                    // })
                }
            })
        },
        convertCourses(courseArray){
            let result = ""
            let list = []

            if(courseArray === undefined){
                return "--"
            }

            
            courseArray = courseArray.split(",")
            list = courseArray.map((val, indx) => {
                const res = this.courseOpt.filter(el => el.value === val)
                return res[0].label
            });
            // console.log(list)
            // result = list.join(" , ")

            return list 
        },
        async getCourses(){
            this.courseOpt = [];
            this.$api.get('misc/courseList').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    let opt = data.list.map((el) => {
                        return {
                            label: el.title,
                            value: el.id
                        }
                    })
                    
                    this.courseOpt = opt
                } else {
                    // this.$q.notify({
                    //     position: 'top-left',
                    //     type: 'negative',
                    //     message: data.title,
                    //     caption: data.message,
                    //     icon: 'report_problem'
                    // })
                }
            })
        },

    }
}
</script>
<style scoped>
.customStepper{
    
    padding: 0px !important;
}
.itemBorder{
    border-left: 3px solid rgb(0,177,250);
}
.q-stepper__header--contracted {
    min-height: 32px!important;
}
.no-content{
    display: none;
}
.my-card{
    border-radius: 15px;
    box-shadow: 0px 0px 3px -2px !important;
}
.active-scholar{
    height: 150px;
    background: rgb(0,177,250);
    background: linear-gradient(67deg, rgb(255, 255, 255) 0%, rgba(17, 140, 240, 0.966) 100%);
}
.my-card-item{
    border-radius: 10px;
}
.formBG-InLife{
    background: url(/imgs/CHED.png) no-repeat;
    background-position: 96% 4px;
    border-radius: 13px;
    background-size: 23%;
    background-repeat: no-repeat;
}
</style>
