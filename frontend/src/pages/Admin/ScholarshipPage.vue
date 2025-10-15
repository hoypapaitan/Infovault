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
                            Scholarship Management
                            <br/>
                            <span class="text-caption text-grey">Manage scholarship program details and requirements</span>
                        </span>
                        
                        <q-space />
                        <div class="text-right">
                            <q-btn-group>
                                <!-- <q-btn color="amber" rounded glossy icon="visibility" /> -->
                                <q-btn color="primary" rounded  icon="ti-plus" label="Add New Program" no-caps @click="drawerRight = !drawerRight" />
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
                            :columns="columns"
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
                                        <div v-if="col.name === 'slotAvailable'">
                                            {{ Number(props.row.slotAvailable) - Number(props.row.original.applied) }}
                                        </div>
                                        <div v-else-if="col.name === 'status'">
                                            {{ Number(props.row.original.status) === 1 ? 'Available' : 'Closed' }}
                                        </div>
                                        <div v-else>
                                            {{ col.value }}
                                        </div>
                                        
                                    </q-td>
                                    <q-td class="text-center">
                                        <q-btn
                                            @click="showDetails(props.row)"
                                            rounded
                                            color="primary" 
                                            size="sm" 
                                            no-caps
                                            label="View Details"
                                            icon="mdi-file-find" 
                                            class="q-mr-sm"
                                        />
                                        <q-btn
                                            @click="updateScholarStatus(props.row)"
                                            rounded
                                            :color="props.row.original.status === '1' ? 'red' : 'green'" 
                                            size="sm"
                                            no-caps
                                            :label="props.row.original.status === '1' ? 'Close Submission' : 'Open Submission'"
                                            :icon="props.row.original.status === '1' ? 'mdi-close' : 'mdi-lock-open-variant-outline'" 
                                            class="q-mr-sm"
                                        />
                                        <q-btn
                                            @click="deleteScholarship(props.row)"
                                            rounded
                                            color="red" 
                                            size="sm"
                                            no-caps
                                            label="Delete"
                                            icon="mdi-delete-empty" 
                                        />
                                    </q-td>

                                </q-tr>
                            </template>
                        </q-table>
                    </q-card-section>
                </q-card>
            </div>

        </div>

        <!-- Modals -->
        <q-drawer
            side="right"
            v-model="drawerRight"
            bordered
            overlay
            :width="700"
        >
            <q-scroll-area class="fit">
                <q-card
                    flat
                    class=" bg-white"
                >
                    <q-card-section class="row items-center no-wrap">
                        <div>
                            <div class="text-weight-bold">Create Scholarship Program</div>
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
                                    <div class="col-12 col-md-12 text-grey-8 q-pa-xs">
                                        <q-input
                                            v-model="form.title"
                                            label="Title"
                                            placeholder="Enter Scholarship Program"
                                            outlined
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
                                        <q-input
                                            v-model="form.otherDetailsLink"
                                            label="Link for more Details"
                                            placeholder="Enter URL Link for the other information"
                                            outlined
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
                                        <q-input
                                            v-model="form.slot"
                                            label="Available Slot"
                                            placeholder="Enter Slots"
                                            outlined
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-pa-xs">
                                        <q-select
                                            outlined
                                            stack-label 
                                            v-model="form.coveredCourses"
                                            :options="courseOpt"
                                            label="Course Covered"
                                            placeholder="Select course covered on this Program"
                                            multiple
                                            emit-value
                                            map-options
                                        >
                                            <template v-slot:before-options>
                                                <q-item>
                                                    <q-item-section>
                                                        <q-item-label>Select All</q-item-label>
                                                    </q-item-section>
                                                    <q-item-section side>
                                                        <q-checkbox v-model="selectAllCourse" checked-icon="task_alt" unchecked-icon="radio_button_unchecked" />
                                                    </q-item-section>
                                                </q-item>
                                                <q-separator />
                                            </template>
                                            <template v-slot:option="{ itemProps, opt, selected, toggleOption }">
                                                <q-item v-bind="itemProps">
                                                    <q-item-section>
                                                        <q-item-label v-html="opt.label" />
                                                    </q-item-section>
                                                    <q-item-section side>
                                                        <q-checkbox :model-value="selected" checked-icon="task_alt" unchecked-icon="radio_button_unchecked" @update:model-value="toggleOption(opt)" />
                                                    </q-item-section>
                                                </q-item>
                                            </template>
                                        </q-select>
                                    </div>
                                    <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
                                        <q-input
                                            v-model="form.provider"
                                            label="Program Provider"
                                            placeholder="Enter Provider"
                                            outlined
                                            stack-label
                                        >
                                        </q-input>
                                        <!-- <q-select 
                                            outlined 
                                            v-model="form.provider" 
                                            :options="providerOpt" 
                                            label="Program Provider" 
                                            stack-label 
                                            options-dense
                                        >
                                        </q-select> -->
                                    </div>
                                    <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
                                        <q-input
                                            type="date"
                                            v-model="form.dueDate"
                                            label="Valid Until"
                                            outlined
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    
                                    <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                        <div class="fit row wrap justify-start items-center content-center">
                                            <div class="text-bold text-h6 q-mb-sm">Qualifications</div>
                                            <q-space />
                                            <div class="text-right">
                                                <q-btn-group>
                                                    <q-btn color="secondary" rounded  icon="ti-plus" label="Add Qualification" no-caps size="sm" @click="addQualificationItem" />
                                                </q-btn-group>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                        <div v-for="(itm, idx) in form.qualification" :key="idx" class="row">
                                            <div class="col-12 col-md-11 q-pa-sm">
                                                <q-input
                                                    outlined
                                                    v-model="itm.targetValue" 
                                                    label="Target Qualification"
                                                    stack-label 
                                                    dense
                                                >
                                                </q-input>
                                            </div>
                                            <div class="col-12 col-md-1 q-pa-sm text-right">
                                                <q-btn @click="removeItem(idx)" size="sm" class=" q-mt-xs" color="red" icon="ti-trash" />
                                            </div>
                                            <div class="col-12 col-md-12 q-pa-sm">
                                                <q-input
                                                    type="textarea"
                                                    outlined
                                                    v-model="itm.description" 
                                                    label="Description" 
                                                    stack-label
                                                    dense
                                                >
                                                </q-input>
                                            </div>
                                            
                                            
                                        </div>
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
                                    <div 
                                        v-for="(req, index) in form.requirements"
                                        :key="index"
                                        class="col-12 col-md-12 q-mt-sm q-pa-xs"
                                    >
                                        <q-checkbox
                                            v-model="req.required"
                                            checked-icon="task_alt" 
                                            unchecked-icon="radio_button_unchecked" 
                                        >
                                            {{req.label}}
                                        </q-checkbox>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                        <div class="fit row wrap justify-start items-center content-center">
                                            <div class="text-bold text-h6 q-mb-sm">Other Requirements</div>
                                            <q-space />
                                            <div class="text-right">
                                                <q-btn-group>
                                                    <q-btn color="secondary" rounded  icon="ti-plus" label="Add Requirements" no-caps size="sm" @click="addRequirementItem" />
                                                </q-btn-group>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                        <div v-for="(itm, idx) in otherRequirements" :key="idx" class="row">
                                            <div class="col-12 col-md-11 q-pa-sm">
                                                <q-input
                                                    outlined
                                                    v-model="itm.name" 
                                                    label="Key Parameter"
                                                    stack-label 
                                                    dense
                                                >
                                                </q-input>
                                            </div>
                                            <div class="col-12 col-md-1 q-pa-sm text-right">
                                                <q-btn @click="removeRequirementItem(idx)" size="sm" class=" q-mt-xs" color="red" icon="ti-trash" />
                                            </div>
                                            <div class="col-12 col-md-12 q-pa-sm">
                                                <q-input
                                                    type="textarea"
                                                    outlined
                                                    v-model="itm.label" 
                                                    label="Description" 
                                                    stack-label
                                                    dense
                                                >
                                                </q-input>
                                            </div>
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                            </q-step>

                            <q-step
                                :name="3"
                                title="Summary"
                                icon="assignment"
                            >
                                <div class="row">
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.title || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Scholarship Program Title</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.provider.label || '0'}`}}</span><br/>
                                        <span class="text-caption text-grey">Scholarship Provider</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${moment(form.dueDate).format("MMMM DD, YYYY") || '0'}`}}</span><br/>
                                        <span class="text-caption text-grey">Valid Until</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.slot || '0'}`}}</span><br/>
                                        <span class="text-caption text-grey">Available Slot</span>
                                    </div>
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-title text-bold">{{`${convertCourses(form.coveredCourses) || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Courses Covered on Program</span>
                                    </div>
                                </div>
                                <q-separator />
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-bold text-h6 q-mb-sm">Qualification</div>
                                    </div>
                                    <q-list>
                                        <q-item 
                                            v-for="(itm, indx) in form.qualification"
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
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-bold text-h6 q-mb-sm">Requirements</div>
                                    </div>
                                    
                                    <q-list>
                                        <q-item 
                                            v-for="(itm, indx) in convertRequirements(form.requirements)"
                                            :key="indx"
                                        >
                                            <q-item-section avatar>
                                                <q-avatar>
                                                    <q-icon name="task_alt" size="sm" />
                                                </q-avatar>
                                            </q-item-section>

                                            <q-item-section>
                                                <q-item-label>
                                                    {{itm.label}}
                                                </q-item-label>
                                            </q-item-section>
                                        </q-item>
                                    </q-list>
                                </div>
                            </q-step>
                        </q-stepper>

                        
                    </q-card-section>
                    <q-separator />
                    <q-card-actions>
                        <q-stepper-navigation >
                            <q-btn v-if="step < 3" @click="$refs.stepper.next()" color="primary" label="Next" />
                            <q-btn v-if="step === 3" @click="submitRegister" color="positive" label="Finish" />
                            <q-btn v-if="step > 1" flat color="primary" @click="$refs.stepper.previous()" label="Back" class="q-ml-sm" />
                        </q-stepper-navigation>
                    </q-card-actions>
                </q-card>
            </q-scroll-area>
        </q-drawer>

        <!-- Preview Existing Details -->
        <q-drawer
            side="right"
            v-model="drawerRightDetails"
            bordered
            overlay
            :width="700"
        >
            <q-scroll-area class="fit">
                <q-card
                    flat
                    class=" bg-white"
                >
                    <q-card-section class="row items-center no-wrap">
                        <div>
                            <div class="text-weight-bold">Scholarship Program Details</div>
                        </div>
                        <q-space />
                        <q-btn size="sm" rounded color="red" icon="ti-close" label="Cancel" @click="closeDetails" />
                    </q-card-section>
                    <q-separator />
                    <q-card-section >
                        <div class="row">
                            <div class="col-12 col-md-12 text-grey-8 q-pa-xs">
                                <q-input
                                    v-model="form.title"
                                    label="Title"
                                    placeholder="Enter Scholarship Program"
                                    outlined
                                    stack-label
                                >
                                </q-input>
                            </div>
                            <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
                                <q-input
                                    v-model="form.otherDetailsLink"
                                    label="Link for more Details"
                                    placeholder="Enter URL Link for the other information"
                                    outlined
                                    stack-label
                                >
                                </q-input>
                            </div>
                            <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
                                <q-input
                                    v-model="form.slot"
                                    label="Available Slot"
                                    placeholder="Enter Slots"
                                    outlined
                                    stack-label
                                >
                                </q-input>
                            </div>
                            <div class="col-12 col-md-12 text-grey-8 q-pa-xs">
                                <q-select
                                    outlined
                                    stack-label 
                                    v-model="form.coveredCourses"
                                    :options="courseOpt"
                                    label="Course Covered"
                                    placeholder="Select course covered on this Program"
                                    multiple
                                    emit-value
                                    map-options
                                >  
                                    <template v-slot:before-options>
                                        <q-item>
                                            <q-item-section>
                                                <q-item-label>Select All</q-item-label>
                                            </q-item-section>
                                            <q-item-section side>
                                                <q-checkbox v-model="selectAllCourse" checked-icon="task_alt" unchecked-icon="radio_button_unchecked" />
                                            </q-item-section>
                                        </q-item>
                                        <q-separator />
                                    </template>
                                    <template v-slot:option="{ itemProps, opt, selected, toggleOption }">
                                        <q-item v-bind="itemProps">
                                            <q-item-section>
                                                <q-item-label v-html="opt.label" />
                                            </q-item-section>
                                            <q-item-section side>
                                                <q-checkbox :model-value="selected" checked-icon="task_alt" unchecked-icon="radio_button_unchecked" @update:model-value="toggleOption(opt)" />
                                            </q-item-section>
                                        </q-item>
                                    </template>
                                </q-select>
                            </div>
                            <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
                                <q-input
                                    v-model="form.provider"
                                    label="Program Provider"
                                    placeholder="Enter Provider"
                                    outlined
                                    stack-label
                                >
                                </q-input>
                                <!-- <q-select 
                                    outlined 
                                    v-model="form.provider" 
                                    :options="providerOpt" 
                                    label="Program Provider" 
                                    stack-label 
                                    options-dense
                                >
                                </q-select> -->
                            </div>
                            <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
                                <q-input
                                    type="date"
                                    v-model="form.dueDate"
                                    label="Valid Until"
                                    outlined
                                    stack-label
                                >
                                </q-input>
                            </div>
                            
                            <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                <div class="fit row wrap justify-start items-center content-center">
                                    <div class="text-bold text-h6 q-mb-sm">Qualifications</div>
                                    <q-space />
                                    <div class="text-right">
                                        <q-btn-group>
                                            <q-btn color="secondary" rounded  icon="ti-plus" label="Add Qualification" no-caps size="sm" @click="addQualificationItem" />
                                        </q-btn-group>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                <div v-for="(itm, idx) in form.qualification" :key="idx" class="row">
                                    <div class="col-12 col-md-11 q-pa-sm">
                                        <q-input
                                            outlined
                                            v-model="itm.targetValue" 
                                            label="Target Qualification"
                                            stack-label 
                                            dense
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-1 q-pa-sm text-right">
                                        <q-btn @click="removeItem(idx)" size="sm" class=" q-mt-xs" color="red" icon="ti-trash" />
                                    </div>
                                    <div class="col-12 col-md-12 q-pa-sm">
                                        <q-input
                                            type="textarea"
                                            outlined
                                            v-model="itm.description" 
                                            label="Description" 
                                            stack-label
                                            dense
                                        >
                                        </q-input>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                        <div class="text-bold text-h6 q-mb-sm">Requirements</div>
                                    </div>
                                    <div 
                                        v-for="(req, index) in form.requirements"
                                        :key="index"
                                        class="col-12 col-md-12 q-mt-sm q-pa-xs"
                                    >
                                        <q-checkbox
                                            v-model="req.required"
                                            checked-icon="task_alt" 
                                            unchecked-icon="radio_button_unchecked" 
                                        >
                                            {{req.label}}
                                        </q-checkbox>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                        <div class="fit row wrap justify-start items-center content-center">
                                            <div class="text-bold text-h6 q-mb-sm">Other Requirements</div>
                                            <q-space />
                                            <div class="text-right">
                                                <q-btn-group>
                                                    <q-btn color="secondary" rounded  icon="ti-plus" label="Add Requirements" no-caps size="sm" @click="addRequirementItem" />
                                                </q-btn-group>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                        <div v-for="(itm, idx) in otherRequirements" :key="idx" class="row">
                                            <div class="col-12 col-md-11 q-pa-sm">
                                                <q-input
                                                    outlined
                                                    v-model="itm.name" 
                                                    label="Key Parameter"
                                                    stack-label 
                                                    dense
                                                >
                                                </q-input>
                                            </div>
                                            <div class="col-12 col-md-1 q-pa-sm text-right">
                                                <q-btn @click="removeRequirementItem(idx)" size="sm" class=" q-mt-xs" color="red" icon="ti-trash" />
                                            </div>
                                            <div class="col-12 col-md-12 q-pa-sm">
                                                <q-input
                                                    type="textarea"
                                                    outlined
                                                    v-model="itm.label" 
                                                    label="Description" 
                                                    stack-label
                                                    dense
                                                >
                                                </q-input>
                                            </div>
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-actions>
                        <q-btn @click="updateScholarShip" color="positive" label="Update Details" />
                    </q-card-actions>
                </q-card>
            </q-scroll-area>
        </q-drawer>
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'PayrollPage',
    data(){
        return {
            itemsList: [],
            drawerRight: false,
            drawerRightDetails: false,
            step: 1,
            selectAllCourse: false,
            courseOpt: [],
            sId: null,
            form: {
                title: "",
                slot: "",
                provider: "",
                otherDetailsLink: "",
                qualification: [],
                coveredCourses: [],
                createdBy: 0,
                dueDate: "",
                status: 1,
                requirements: [
                    {
                        name: 'schoolCard',
                        label: 'Form 138/High School Card',
                        required: false,
                    },
                    {
                        name: 'lastCard',
                        label: 'Copy of grades last semester attended (for old student)',
                        required: false,
                    },
                    {
                        name: 'goodMoral',
                        label: 'Good Moral Character',
                        required: false,
                    },
                    {
                        name: 'psa',
                        label: 'Birth Certificate/PSA',
                        required: false,
                    },
                    {
                        name: 'regForm',
                        label: 'Registration Form/Proof of enrollment',
                        required: false,
                    },
                    {
                        name: 'entranceTest',
                        label: 'Entrance Test Result (for new student)',
                        required: false,
                    },
                    {
                        name: 'indigency',
                        label: 'Certificate of Indigency/Income Tax Return of Parents',
                        required: false,
                    },
                    {
                        name: 'picture',
                        label: '2X2 Picture With Clear Details',
                        required: false,
                    },
                ]
            },
            otherRequirements: [],
            providerOpt: []
        }
    },
    computed: {
        columns(){
            return [
                {
                    name: 'title',
                    required: true,
                    label: 'Program',
                    align: 'left',
                    field: row => row,
                    format: val => val.title,
                    sortable: true
                },
                { name: 'provider', align: 'left', label: 'Provider', field: 'provider' },
                { name: 'slotAvailable', align: 'left', label: 'Available Slot', field: 'slotAvailable', sortable: true },
                { name: 'dueDate', align: 'left', label: 'Validity', field: 'dueDate', sortable: true },
                { name: 'status', align: 'left', label: 'Status', field: 'status', sortable: true },
                { name: 'creator', align: 'left', label: 'Created By', field: 'creator', sortable: true },
                
            ]
        },
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        }
    },
    watch:{
        selectAllCourse(newVal){
            if(newVal){
                this.form.coveredCourses = this.courseOpt.map(el => el.value)
            } else {
                this.form.coveredCourses = []
            }
        }, 
        "form.coveredCourses": function(newVal) {
            // if(newVal.length !== this.courseOpt && newVal.length !== 0){
            //     this.selectAllCourse = false
            // }
        }   
    },
    created(){
        this.getCourses().then(() => {
            this.getProviders()
        })
        this.getList();
    },
    methods: {
        moment,
        closeDetails(){
            this.drawerRightDetails = !this.drawerRightDetails
            this.resetForm();
        },
        showDetails(data){
            let selected = data.original
            this.drawerRightDetails = true
            this.sId = selected.id
            for(const i in this.form){
                if(selected[i] !== undefined){
                    if(i === 'requirements'){
                        for (const r in selected[i]) {
                            this.form[i][r].required = selected[i][r].required
                        }
                    } else if(i === 'coveredCourses') {
                        this.form[i] = selected[i].split(",")
                    } else {
                        this.form[i] = selected[i] 
                    }
                    
                }
            }

        },
        async updateScholarStatus(data){
            this.$q.dialog({
                title: 'Update Application Status',
                message: 'Would you like to proceed with this transaction?',
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
                    scholarId: Number(data.original.id),
                    status: Number(data.original.status) === 0 ? 1 : 0
                }

                this.$api.post('scholarship/update/scholarship', payload).then(async (response) => {
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
        },
        async deleteScholarship(data){
            this.$q.dialog({
                title: 'Delete Program',
                message: 'Would you like to proceed with this transaction?',
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
                    scholarId: Number(data.original.id)
                }

                this.$api.post('scholarship/delete', payload).then(async (response) => {
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
        },
        async submitRegister(){
            this.$q.loading.show();
            this.loginLoad = true;
            let vm = this;
            let payload = {
                ...vm.form,
                // provider: this.form.provider.label,
                coveredCourses: this.form.coveredCourses.join(","),
                requirements: this.convertRequirements(this.form.requirements),
                createdBy: Number(this.user.userId),
            };

            this.$api.post('scholarship/create/new', payload).then(async (response) => {
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
        },
        async updateScholarShip(){
            this.$q.dialog({
                title: 'Update Scholarship Details',
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
                this.loginLoad = true;
                let vm = this;
                let payload = {
                    sId: this.sId,
                    details: {
                        ...vm.form,
                        // provider: this.form.provider.label,
                        coveredCourses: this.form.coveredCourses.join(","),
                        requirements: this.convertRequirements(this.form.requirements),
                        createdBy: Number(this.user.userId),
                    }
                };

                this.$api.post('scholarship/update/details', payload).then(async (response) => {
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
            })
        },
        convertCourses(courseArray){
            let result = ""
            let list = []
            list = courseArray.map((val, indx) => {
                const res = this.courseOpt.filter(el => el.value === val)
                return res[0].label
            });
            result = list.join(" , ")

            return result
        },
        convertRequirements(reqArray){
            let joinedRequirements = [
                ...reqArray,
                ...this.otherRequirements
            ]
            let result = []
            result = joinedRequirements.filter(el => el.required === true);

            return result
        },
        addQualificationItem(){
            this.form.qualification.push({
                description: "",
                targetValue: "",
            })
        },
        removeItem(index){
            this.form.qualification.splice(index, 1)
        },
        addRequirementItem(){
            this.otherRequirements.push({
                name: '',
                label: '',
                required: true,
            })
        },
        removeRequirementItem(index){
            this.otherRequirements.splice(index, 1)
        },
        async getList(){
            this.itemsList = []
            this.$api.get('scholarship/list/admin').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.itemsList = data.list
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
                    this.$q.notify({
                        position: 'top-left',
                        type: 'negative',
                        message: data.title,
                        caption: data.message,
                        icon: 'report_problem'
                    })
                }
            })
        },
        async getProviders(){
            this.providerOpt = [];
            this.$api.get('misc/providerList').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    let opt = data.list.map((el) => {
                        return {
                            label: el.name,
                            value: el.id
                        }
                    })
                    
                    this.providerOpt = opt
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
        },
        resetForm(){
            this.otherRequirements = []
            this.form = {
                title: "",
                slot: "",
                provider: "",
                otherDetailsLink: "",
                qualification: [],
                coveredCourses: [],
                createdBy: 0,
                dueDate: "",
                status: 1,
                requirements: [
                    {
                        name: 'schoolCard',
                        label: 'Form 138/High School Card',
                        required: false,
                    },
                    {
                        name: 'lastCard',
                        label: 'Copy of grades last semester attended (for old student)',
                        required: false,
                    },
                    {
                        name: 'goodMoral',
                        label: 'Good Moral Character',
                        required: false,
                    },
                    {
                        name: 'psa',
                        label: 'Birth Certificate/PSA',
                        required: false,
                    },
                    {
                        name: 'regForm',
                        label: 'Registration Form/Proof of enrollment',
                        required: false,
                    },
                    {
                        name: 'entranceTest',
                        label: 'Entrance Test Result (for new student)',
                        required: false,
                    },
                    {
                        name: 'indigency',
                        label: 'Certificate of Indigency/Income Tax Return of Parents',
                        required: false,
                    },
                    {
                        name: 'picture',
                        label: '2X2 Picture With Clear Details',
                        required: false,
                    },
                ]
            }
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
