<template>
    <div>
        <!-- Profile Header -->
        <div class="profile-nav-bg" style="background-image: url('images/coverbg.png')"></div>

        <a-card :bordered="false" class="card-profile-head" :bodyStyle="{padding: 0,}">
            <template #title>
                <a-row type="flex" align="middle">
                    <a-col :span="24" :md="12" class="col-info">
                        <a-avatar shape="square" :size="74" icon="idcard" :style="{ backgroundColor: '#1e9ecf' }" />
                        <div class="avatar-info">
                            <h4 class="font-semibold m-0">Graduate Records</h4>
                            <p>Manage graduate records and analytical representation</p>
                        </div>
                    </a-col>
                    <a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
                        <a-space size="small">
                            <a-button v-if="isAdmin" type="primary" @click="addSingleModal = true">
                                <a-icon type="plus" />Add Entry
                            </a-button>
                            <a-button v-if="isAdmin" type="default" @click="addUserModal = true">
                                <a-icon type="upload" />Import CSV
                            </a-button>
                            
                            <a-button :type="showFilter ? 'primary' : 'dashed'" @click="showFilter = !showFilter">
                                <a-icon type="filter" /> {{ showFilter ? 'Hide Filters' : 'Filter & Reports' }}
                            </a-button>
                        </a-space>
                    </a-col>
                </a-row>
            </template>
        </a-card>

        <!-- Filters Section -->
        <transition name="slide-fade">
            <a-card v-if="showFilter" :bordered="false" class="mb-24" style="margin-top: 20px;">
                <a-row :gutter="[24, 24]">
                    <a-col :span="24" :md="8">
                        <a-form-item label="Filter by Course" style="margin-bottom: 0;">
                            <a-select 
                                mode="multiple" 
                                v-model="selectedCourseFilter" 
                                placeholder="Select courses..."
                                :options="courseFilter"
                                style="width: 100%"
                                allowClear
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="24" :md="8">
                        <a-form-item label="Filter by Year" style="margin-bottom: 0;">
                            <a-select 
                                mode="multiple" 
                                v-model="selectedSchoolYearFilter" 
                                placeholder="Select years..."
                                :options="schoolYearFilter"
                                style="width: 100%"
                                allowClear
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="24" :md="6">
                        <a-form-item label="Filter by Sex" style="margin-bottom: 0;">
                            <a-select
                                mode="multiple"
                                v-model="selectedSexFilter"
                                placeholder="Select sex..."
                                :options="sexFilter"
                                style="width: 100%"
                                allowClear
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="24" :md="6">
                        <a-form-item label="Filter by Student ID" style="margin-bottom: 0;">
                            <a-input
                                v-model="studentIdFilter"
                                placeholder="Enter student ID..."
                                allowClear
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="24" :md="6">
                        <a-form-item label="Filter by Major" style="margin-bottom: 0;">
                            <a-select
                                mode="multiple"
                                v-model="selectedMajorFilter"
                                placeholder="Select majors..."
                                :options="majorFilter"
                                style="width: 100%"
                                allowClear
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="24" :md="6" style="display: flex; align-items: flex-end;">
                        <a-button-group style="width: 100%">
                            <a-button @click="exportCSV" icon="cloud-download" block>Export CSV</a-button>
                            <a-button @click="generatePrint" icon="printer" block>Print Report</a-button>
                        </a-button-group>
                    </a-col>
                </a-row>
            </a-card>
        </transition>

        <!-- Main Table -->
        <a-card :bordered="false" class="header-solid table-card mb-24" style="margin-top: 24px;">
            <div slot="title" style="display: flex; align-items: center;">
                <a-avatar shape="square" style="background-color: #f6ffed; color: #52c41a" icon="table" class="mr-2" />
                <div>
                    <h5 class="font-semibold m-0">Master List</h5>
                    <small class="text-muted">Total: {{ filteredUser.length }} students</small>
                </div>
            </div>

            <!-- FIX: Added @change="handleTableChange" -->
            <a-table
                :dataSource="filteredUser"
                :columns="columns"
                :pagination="pagination"
                @change="handleTableChange"
                :scroll="{ x: 1000 }"
                rowKey="id"
                class="ant-table-striped"
            >
                <template slot="name" slot-scope="text, record">
                    <div class="d-flex align-items-center">
                        <a-avatar :size="40" class="mr-3" :style="{ backgroundColor: getAvatarColor(text), verticalAlign: 'middle' }">
                            {{ text && text.length > 0 ? text.charAt(0).toUpperCase() : '?' }}
                        </a-avatar>
                        <div style="display: inline-block; vertical-align: middle;">
                            <h6 class="m-0 text-dark font-semibold">{{ text }}</h6>
                        </div>
                    </div>
                </template>

                <template slot="studentId" slot-scope="text">
                    <span style="font-family: 'Fira Mono', 'Consolas', 'monospace'; font-size: 13px; padding: 2px 8px; background: #f6f6f6; border-radius: 4px; display: inline-block; min-width: 90px; text-align: center; letter-spacing: 1px;">{{ text }}</span>
                </template>

                <template slot="gender" slot-scope="text">
                    <span>
                        <a-icon :type="text === 'Male' ? 'man' : 'woman'" :style="{ color: text === 'Male' ? '#1890ff' : '#eb2f96', marginRight: '5px' }" />
                        {{ text }}
                    </span>
                </template>

                <template slot="course" slot-scope="text">
                    <a-tooltip :title="text">
                        <span><a-icon type="book" theme="twoTone" class="mr-2" /> {{ text }}</span>
                    </a-tooltip>
                </template>

                <template slot="achievement" slot-scope="text">
                    <div v-if="text">
                         <a-tag :color="getAchievementColor(text)" style="border-radius: 15px; padding: 2px 12px;">
                            <a-icon v-if="text !== 'Regular Graduate'" type="trophy" class="mr-1" /> {{ text }}
                         </a-tag>
                    </div>
                    <span v-else class="text-muted" style="opacity: 0.6;">—</span>
                </template>

                <template slot="yearGraduated" slot-scope="text">
                    <a-tag color="blue">{{ text }}</a-tag>
                </template>

                <template slot="action" slot-scope="text, record">
                    <div class="action-buttons">
                        <a-tooltip title="View Details">
                            <a-button shape="circle" icon="eye" size="small" @click="handleView(record)" />
                        </a-tooltip>
                        <a-tooltip v-if="isAdmin" title="Edit Record">
                            <a-button type="primary" shape="circle" icon="edit" size="small" class="ml-2" @click="handleEdit(record)" />
                        </a-tooltip>
                    </div>
                </template>

                <template slot="major" slot-scope="text">
                    <span>{{ text || '—' }}</span>
                </template>

                <template slot="achievements" slot-scope="record">
                    <div>
                        <a-tag color="blue" v-if="record.achievement">{{ record.achievement }}</a-tag>
                        <a-tag color="purple" v-for="ach in (record.additionalAchievement || [])" :key="ach">{{ ach }}</a-tag>
                        <span v-if="!record.achievement && (!record.additionalAchievement || record.additionalAchievement.length === 0)" class="text-muted" style="opacity: 0.6;">—</span>
                    </div>
                </template>
            </a-table>
        </a-card>

        <!-- Import Modal -->
        <a-modal v-model="addUserModal" title="Upload CSV Data" centered @ok="uploadCSVData(csvData)" :width="900" bodyStyle="max-height:80vh;overflow-y:auto;">
            <a-alert message="Notes" description="Duplicate entries (Same Name, Year, Course OR Student ID) will be automatically skipped." type="info" show-icon class="mb-24" />
            <a-upload-dragger name="file" accept=".csv" :before-upload="getFile" :showUploadList="false">
                <p class="ant-upload-drag-icon"><a-icon type="inbox" /></p>
                <p class="ant-upload-text">Click or drag file to this area</p>
            </a-upload-dragger>
            <div style="margin-top: 15px; text-align: center;">
                <a href="/docs/analytics_data-format.csv" download>
                    <a-icon type="download" /> Download CSV Template
                </a>
            </div>
            <div v-if="csvData.length > 0" style="margin-top: 24px;">
                <h5 class="font-semibold mb-2">Preview Imported Data</h5>
                <div style="max-height:400px;overflow-y:auto;">
                    <a-table
                        :dataSource="csvData"
                        :columns="columns.filter(col => col.dataIndex)"
                        :pagination="{ pageSize: 5 }"
                        rowKey="studentId"
                        size="small"
                    />
                </div>
            </div>
            <div v-if="invalidCsvData.length > 0" style="margin-top: 24px;">
                <h5 class="font-semibold mb-2" style="color: #d4380d;">Records with Missing Required Fields (Not Uploaded)</h5>
                <a-button type="dashed" icon="cloud-download" @click="exportInvalidCSV" style="margin-bottom: 10px;">
                    Export Invalid Data as CSV
                </a-button>
                <div style="max-height:400px;overflow-y:auto;">
                    <a-table
                        :dataSource="invalidCsvData"
                        :columns="columns.filter(col => col.dataIndex)"
                        :pagination="{ pageSize: 5 }"
                        rowKey="studentId"
                        size="small"
                    />
                </div>
                <div class="text-muted" style="margin-top: 8px;">Please fill in all required fields and re-upload these records.</div>
            </div>
        </a-modal>

        <!-- Add Single Entry Modal -->
        <a-modal v-model="addSingleModal" title="Add Single Graduate Entry" centered @ok="saveSingleEntry" :confirmLoading="singleSaving" :width="700">
            <a-form :label-col="{ span: 6 }" :wrapper-col="{ span: 16 }">
                <a-form-item label="Student ID" required>
                    <a-input
                        v-model="singleRecord.studentId"
                        maxlength="10"
                        placeholder="00-00-0000"
                        @input="onStudentIdInput"
                    />
                </a-form-item>
                <a-form-item label="Name" required><a-input v-model="singleRecord.name" /></a-form-item>
                <a-form-item label="Address" required><a-input v-model="singleRecord.address" /></a-form-item>
                <a-form-item label="Class of" required>
                    <a-select v-model="singleRecord.yearGraduated" placeholder="Select Year" allowClear>
                        <a-select-option v-for="year in yearOptions" :key="year" :value="year">{{ year }}</a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Program Covered" required>
                    <a-select v-model="singleRecord.program" placeholder="Academic Program" allowClear :dropdownMatchSelectWidth="false">
                        <a-select-option v-for="item in programsOpt" :key="item" :value="item">
                            {{ item }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Course" required>
                    <a-select v-model="singleRecord.course" placeholder="Courses" allowClear :dropdownMatchSelectWidth="false">
                        <a-select-option v-for="item in coursesOpt[singleRecord.program]" :key="item" :value="item">
                            {{ item }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Major In" required>
                    <a-select v-model="singleRecord.major" placeholder="Majors" allowClear :dropdownMatchSelectWidth="false">
                        <a-select-option v-for="item in majorOptions[singleRecord.course]" :key="item" :value="item">
                            {{ item }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Achievement" required>
                    <a-select v-model="singleRecord.achievement" placeholder="Select Achievement" allowClear :dropdownMatchSelectWidth="false">
                        <a-select-option v-for="item in achievementOptions" :key="item" :value="item">
                            {{ item }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Additional Achievement">
                    <a-select
                        v-model="singleRecord.additionalAchievement"
                        mode="multiple"
                        placeholder="Select Additional Achievement(s)"
                        allowClear
                        :dropdownMatchSelectWidth="false"
                    >
                        <a-select-option value="With Honor">With Honor</a-select-option>
                        <a-select-option value="With High Honor">With High Honor</a-select-option>
                        <a-select-option value="With Highest Honor">With Highest Honor</a-select-option>
                        <a-select-option value="Leadership Awardee">Leadership Awardee</a-select-option>
                        <a-select-option value="Cultural Awardee">Cultural Awardee</a-select-option>
                        <a-select-option value="Player of the Year">Player of the Year</a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Sex" required>
                        <a-select v-model="singleRecord.gender">
                                <a-select-option value="Male">Male</a-select-option>
                                <a-select-option value="Female">Female</a-select-option>
                        </a-select>
                </a-form-item>

                
            </a-form>
        </a-modal>

        <!-- View Details Modal -->
          <!-- View Modal -->
        <a-modal
            v-model="viewModal"
            title="Graduate Details"
            :width="600"
            :footer="null"
            centered
        >
            <div v-if="viewRecord">
                <a-descriptions :column="1" bordered size="small">
                    <a-descriptions-item label="Name"><strong>{{ viewRecord.name }}</strong></a-descriptions-item>
                    <a-descriptions-item label="Student ID">{{ viewRecord.studentId }}</a-descriptions-item>
                    <a-descriptions-item label="Address">{{ viewRecord.address }}</a-descriptions-item>
                    <a-descriptions-item label="Year"><a-tag color="green">{{ viewRecord.yearGraduated }}</a-tag></a-descriptions-item>
                    <a-descriptions-item label="Course">{{ viewRecord.course }}</a-descriptions-item>
                    <a-descriptions-item label="Achievement">{{ viewRecord.achievement || 'Regular Graduate' }}</a-descriptions-item>
                </a-descriptions>
            </div>
        </a-modal>

        <!-- Edit Modal -->
        <a-modal v-model="editModal" title="Edit Graduate Record" @ok="saveEdit" :confirmLoading="editSaving" :width="700">
            <a-form :label-col="{ span: 6 }" :wrapper-col="{ span: 16 }">
                <a-form-item label="Student ID" required>
                    <a-input
                        v-model="editRecord.studentId"
                        maxlength="10"
                        placeholder="00-00-0000"
                        @input="onEditStudentIdInput"
                    />
                </a-form-item>
                <a-form-item label="Name" required><a-input v-model="editRecord.name" /></a-form-item>
                <a-form-item label="Address" required><a-input v-model="editRecord.address" /></a-form-item>
                <a-form-item label="Class of" required>
                    <a-select v-model="editRecord.yearGraduated" placeholder="Select Year" allowClear>
                        <a-select-option v-for="year in yearOptions" :key="year" :value="year">{{ year }}</a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Program Covered" required>
                    <a-select v-model="editRecord.program" placeholder="Academic Program" allowClear :dropdownMatchSelectWidth="false">
                        <a-select-option v-for="item in programsOpt" :key="item" :value="item">
                            {{ item }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Course" required>
                    <a-select v-model="editRecord.course" placeholder="Courses" allowClear :dropdownMatchSelectWidth="false">
                        <a-select-option v-for="item in coursesOpt[editRecord.program]" :key="item" :value="item">
                            {{ item }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Major In" required>
                    <a-select v-model="editRecord.major" placeholder="Majors" allowClear :dropdownMatchSelectWidth="false">
                        <a-select-option v-for="item in majorOptions[editRecord.course]" :key="item" :value="item">
                            {{ item }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Achievement" required>
                    <a-select v-model="editRecord.achievement" placeholder="Select Achievement" allowClear :dropdownMatchSelectWidth="false">
                        <a-select-option v-for="item in achievementOptions" :key="item" :value="item">
                            {{ item }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Additional Achievement">
                    <a-select
                        v-model="editRecord.additionalAchievement"
                        mode="multiple"
                        placeholder="Select Additional Achievement(s)"
                        allowClear
                        :dropdownMatchSelectWidth="false"
                    >
                        <a-select-option value="With Honor">With Honor</a-select-option>
                        <a-select-option value="With High Honor">With High Honor</a-select-option>
                        <a-select-option value="With Highest Honor">With Highest Honor</a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Sex" required>
                    <a-select v-model="editRecord.gender">
                        <a-select-option value="Male">Male</a-select-option>
                        <a-select-option value="Female">Female</a-select-option>
                    </a-select>
                </a-form-item>
            </a-form>
        </a-modal>
        <ModalPrintReport :openPrint="openPrint" :dataVal="filteredUser" @closePrint="openPrint = false"></ModalPrintReport>
    </div>
</template>

<script>
import { jwtDecode } from 'jwt-decode';
import * as d3 from "d3"
import ModalPrintReport from '../components/Modals/ModalPrintReport';

export default {
    components: {
        ModalPrintReport
    },
    computed: {
        yearOptions() {
            const start = 1997;
            const end = new Date().getFullYear();
            const years = [];
            for (let y = end; y >= start; y--) years.push(y);
            return years;
        },
        filteredUser() {
            return this.users.filter(el =>
                (this.selectedCourseFilter.length ? this.selectedCourseFilter.includes(el.course) : true) &&
                (this.selectedSchoolYearFilter.length ? this.selectedSchoolYearFilter.includes(el.yearGraduated) : true) &&
                (this.selectedSexFilter.length ? this.selectedSexFilter.includes(el.gender) : true) &&
                (this.selectedMajorFilter.length ? this.selectedMajorFilter.includes(el.major) : true) &&
                (this.studentIdFilter ? String(el.studentId).includes(this.studentIdFilter) : true)
            )
        },
        columns() {
            return [
                { 
                    title: 'STUDENT NUMBER', 
                    dataIndex: 'studentId', 
                    key: 'studentId', 
                    scopedSlots: { customRender: 'studentId' }, 
                    sorter: (a, b) => a.studentId.localeCompare(b.studentId), 
                    width: 180 
                },
                { 
                    title: 'STUDENT', 
                    dataIndex: 'name', 
                    key: 'name', 
                    scopedSlots: { customRender: 'name' }, 
                    sorter: (a, b) => a.name.localeCompare(b.name), 
                    width: 280 
                },
                { 
                    title: 'ADDRESS', 
                    dataIndex: 'address', 
                    key: 'address', 
                    scopedSlots: { customRender: 'address' }, 
                    sorter: (a, b) => a.address.localeCompare(b.address), 
                    width: 280 
                },
                { 
                    title: 'SEX', 
                    dataIndex: 'gender', 
                    key: 'gender', 
                    width: 120,
                    sorter: (a, b) => a.gender.localeCompare(b.gender), 
                    scopedSlots: { customRender: 'gender' } 
                },
                { 
                    title: 'COURSE', 
                    dataIndex: 'course', 
                    key: 'course',
                    sorter: (a, b) => a.course.localeCompare(b.course), 
                    scopedSlots: { customRender: 'course' },
                    width: 230 
                },
                { 
                    title: 'CLASS OF', 
                    dataIndex: 'yearGraduated', 
                    key: 'yearGraduated',
                    sorter: (a, b) => a.yearGraduated.localeCompare(b.yearGraduated), 
                    scopedSlots: { customRender: 'yearGraduated' } ,
                    width: 130 
                },
                { 
                    title: 'MAJOR', 
                    dataIndex: 'major', 
                    key: 'major',
                    sorter: (a, b) => (a.major || '').localeCompare(b.major || ''),
                    scopedSlots: { customRender: 'major' },
                    width: 180 
                },
                { 
                    title: 'ACHIEVEMENTS', 
                    key: 'achievements',
                    scopedSlots: { customRender: 'achievements' },
                    width: 220 
                },
                { 
                    title: 'ACTION', 
                    key: 'action',
                    scopedSlots: { customRender: 'action' },
                    fixed: 'right',
                    width: 120
                },
            ];
        },
        user: function () {
            let raw = localStorage.getItem('userToken')
            if (!raw) return null
            let tokenString = raw
            try {
                const parsed = JSON.parse(raw)
                if (parsed && parsed.value) tokenString = parsed.value
            } catch (e) { }
            try {
                return jwtDecode(tokenString)
            } catch (e) {
                console.error('Failed to decode JWT token', e)
                return null
            }
        },
        isAdmin: function () {
            return this.user && this.user.aud === 'admin'
        },
    },
    data() {
        return {
            showFilter: false, 
            // Pagination settings
            pagination: {
                current: 1,
                pageSize: 10,
                showSizeChanger: true,
                pageSizeOptions: ['5', '10', '20', '30', '40', '50', '10000'], 
                showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`
            },
            viewModal: false,
            viewRecord: {},
            editModal: false,
            editRecord: {},
            editSaving: false,
            addUserModal: false, // Fixed spelling
            addSingleModal: false,
            singleRecord: {
                additionalAchievement: [],
            },
            singleSaving: false,
            openPrint: false,
            users: [],
            usersOrig: [],
            csvData: [],
            invalidCsvData: [],
            courseFilter: [],
            selectedCourseFilter: [],
            schoolYearFilter: [],
            selectedSchoolYearFilter: [],
            achievementOptions: [
                "Cum Laude",
                "Magna Cum Laude",
                "Summa Cum Laude",
                "With Honor",
                "With High Honor",
                "With Highest Honor",
                "None"
            ],
            programsOpt: [
                'GRADUATES',
                'UNDERGRADUATE',
                'DIPLOMA or CERTIFICATE',
            ],
            coursesOpt: {
                GRADUATES: [
                    'MASTER OF ARTS IN EDUCATION',
                    'MASTER IN MANAGEMENT',
                    'MASTER OF SCIENCE IN AGRICULTURE',
                    'MASTER OF SCIENCE IN ENVIRONMENTAL MANAGEMENT'
                ],
                UNDERGRADUATE: [
                    'SCHOOL OF AGRICULTURE AND AQUATIC SCIENCES',
                    'SCHOOL OF EDUCATION',
                    'SCHOOL OF ARTS AND SCIENCES',
                    'SCHOOL OF ENGINEERING',
                    'SCHOOL OF FORESTRY AND ENVIRONMENTAL SCIENCES',
                    'SCHOOL OF INDUSTRIAL TECHNOLOGY',
                    'SCHOOL OF INFORMATION TECHNOLOGY',
                ],
                "DIPLOMA or CERTIFICATE": [
                    'SCHOOL OF FORESTRY AND ENVIRONMENTAL SCIENCES',
                    'SCHOOL OF INDUSTRIAL TECHNOLOGY',
                    'SCHOOL OF INFORMATION TECHNOLOGY'
                ],
            },
            majorOptions: {
                'MASTER OF ARTS IN EDUCATION': [
                    'major in Science Education',
                    'major in Filipino Language Teaching',
                    'major in Guidance Counseling',
                    'major in Educational Management'
                ],
                'MASTER IN MANAGEMENT': [
                    'major in Public Management',
                    'major in Business Management'
                ],
                'MASTER OF SCIENCE IN AGRICULTURE': [
                    'major in Animal Science',
                    'major in Crop Science'
                ],
                'MASTER OF SCIENCE IN ENVIRONMENTAL MANAGEMENT': [],
                'SCHOOL OF AGRICULTURE AND AQUATIC SCIENCES': [
                    'major in Animal Science',
                    'major in Crop Science',
                    'major in Fisheries'
                ],
                'SCHOOL OF ARTS AND SCIENCES': [
                    'in Hospitality Management',
                    'in Tourism Management',
                    'in Political Science'
                ],
                'SCHOOL OF EDUCATION': [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education major in English',
                    'Bachelor of Secondary Education major in Filipino',
                    'Bachelor of Secondary Education major in Science',
                    'Bachelor of Secondary Education major in Social Studies',
                    'Bachelor of Secondary Education major in Mathematics',
                    'Bachelor of Physical Education',
                    'Bachelor of Technology and Livelihood Education major in Home Economics',
                    'Bachelor of Technology and Livelihood Education major in Information and Communication Technology'
                ],
                'SCHOOL OF ENGINEERING': [
                    'Bachelor of Science in Civil Engineering major in Construction Engineering and Management',
                    'Bachelor of Science in Electrical Engineering',
                    'Bachelor of Science in Mechanical Engineering'
                ],
                'SCHOOL OF FORESTRY AND ENVIRONMENTAL SCIENCES': [
                    'Bachelor of Science in Forestry',
                    'Diploma in Forest Technology'
                ],
                'SCHOOL OF INDUSTRIAL TECHNOLOGY': [
                    'Bachelor in Industrial Technology major in Automotive Engineering Technology',
                    'Diploma in Automotive Engineering Technician Course',
                    'Associate in Automotive Engineering Technician Course',
                    'Certificate in Automotive Engineering Technician Course',
                    'Bachelor in Industrial Technology major in Electrical Engineering Technology',
                    'Diploma in Electrical Engineering Technician Course',
                    'Associate in Electrical Engineering Technician Course',
                    'Certificate in Electrical Engineering Technician Course',
                    'Bachelor in Industrial Technology major in Civil Engineering Technology',
                    'Diploma in Civil Engineering Technician Course',
                    'Associate in Senior Civil Engineering Technician Course',
                    'Certificate in Junior Civil Engineering Technician Course',
                    'Bachelor in Industrial Technology major in Food Technology',
                    'Diploma in Food Technology',
                    'Certificate in Food Technology'
                ],
                'SCHOOL OF INFORMATION TECHNOLOGY': [
                    'Bachelor of Science in Information Technology with Specialization in Application Programming',
                    'Bachelor of Science in Information Technology with Specialization in Digital Design',
                    'Associate in Computer Technology'
                ],

            },
            sexFilter: [
                { label: 'Male', value: 'Male' },
                { label: 'Female', value: 'Female' }
            ],
            selectedSexFilter: [],
            studentIdFilter: '',
            majorFilter: [],
            selectedMajorFilter: [],
        }
    },
    // FIX: Auto-reset pagination to page 1 when filtering
    watch: {
        selectedCourseFilter() {
            this.pagination = { ...this.pagination, current: 1 };
        },
        selectedSchoolYearFilter() {
            this.pagination = { ...this.pagination, current: 1 };
        }
    },
    created() {
        this.getList();
    },
    methods: {
        onEditStudentIdInput(e) {
            // Enforce format 00-00-0000, max 10 chars
            let value = e.target.value.replace(/[^0-9-]/g, '').slice(0, 10);
            // Optionally auto-insert dashes
            if (value.length > 2 && value[2] !== '-') value = value.slice(0, 2) + '-' + value.slice(2);
            if (value.length > 5 && value[5] !== '-') value = value.slice(0, 5) + '-' + value.slice(5);
            this.editRecord.studentId = value;
        },
        /**
         * Mask and format studentId as 00-00-0000, max 10 chars
         */
        onStudentIdInput(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            let masked = value;
            if (value.length > 4) masked = value.slice(0,2) + '-' + value.slice(2,4) + '-' + value.slice(4);
            else if (value.length > 2) masked = value.slice(0,2) + '-' + value.slice(2);
            this.singleRecord.studentId = masked;
        },
        // FIX: The core pagination method
        handleTableChange(pagination, filters, sorter) {
            const pager = { ...this.pagination };
            pager.current = pagination.current;
            pager.pageSize = pagination.pageSize;
            this.pagination = pager;
        },

        getAchievementColor(achievement) {
            const colorMap = { 
                'Summa Cum Laude': 'gold', 
                'Magna Cum Laude': 'orange', 
                'Cum Laude': 'green', 
                'Regular Graduate': 'blue' 
            };
            return colorMap[achievement] || 'skyblue';
        },
        getAvatarColor(name) {
            const colors = ['#f56a00', '#7265e6', '#ffbf00', '#00a2ae', '#87d068', '#1890ff', '#eb2f96'];
            let hash = 0;
            if(name) {
                for (let i = 0; i < name.length; i++) {
                    hash = name.charCodeAt(i) + ((hash << 5) - hash);
                }
            }
            return colors[Math.abs(hash) % colors.length];
        },

        generatePrint() {
            this.openPrint = true
        },
        async exportCSV() {
            // Exclude Action column from CSV
            const exportableColumns = this.columns.filter(col => col.dataIndex);
            
            const content = [exportableColumns.map(col => this.wrapCsvValue(col.title))].concat(
                this.filteredUser.map(row => exportableColumns.map(col => this.wrapCsvValue(
                    row[col.dataIndex]
                )).join(','))
            ).join('\n')

            const anchor = document.createElement('a');
            anchor.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(content);
            anchor.target = '_blank';
            anchor.download = 'DataReport.csv';
            anchor.click();
        },
        wrapCsvValue(val) {
            let formatted = val === void 0 || val === null ? '' : String(val)
            formatted = formatted.split('"').join('""')
            return `"${formatted}"`
        },
        async getFile(data) {
            var reader = new FileReader();
            let filePath = data
            reader.readAsText(new Blob([filePath], { type: filePath.type }))
            const fileContent = await new Promise(resolve => {
                reader.onloadend = (event) => {
                    resolve(event.target.result)
                }
            })
            let csvData = d3.csvParse(fileContent)
            
            // Safety check for user
            const creatorId = this.user ? Number(this.user.userId) : 0;

            let requiredFields = [
                'studentId', 'name', 'yearGraduated', 'school', 'course', 'major', 'program', 'gender'
            ];
            let validRows = [], invalidRows = [];
            csvData.forEach(rec => {
                let mapped = {
                    studentId: rec.studentId || rec.student_id || rec['Student ID'] || '',
                    name: rec.name || '',
                    address: rec.address || '',
                    batch: rec.batch || '',
                    section: rec.section || '',
                    yearGraduated: rec.yearGraduated || rec.year_graduated || rec['Year Graduated'] || '',
                    school: rec.school || '',
                    course: rec.course || '',
                    major: rec.major || '',
                    program: rec.program || '',
                    category: rec.category || 'Local Graduate',
                    achievement: rec.achievement || '',
                    gender: rec.gender || '',
                    status: rec.status || 'Alumnus',
                    createdBy: creatorId
                };
                let hasBlank = requiredFields.some(f => !mapped[f] || String(mapped[f]).trim() === '');
                if (hasBlank) invalidRows.push(mapped);
                else validRows.push(mapped);
            });
            this.csvData = validRows;
            this.invalidCsvData = invalidRows;

            console.log(this.csvData)
            return false 
        },
        async uploadCSVData(data) {
            if (!this.user) {
                this.$notification.error({ message: 'Error', description: 'User session not found. Please relogin.' });
                return;
            }
            try {
                const { cleanData, duplicatesFound } = this.filterDuplicatesFromCSV(data);

                if (duplicatesFound.length > 0 || cleanData.length === 0) {
                    this.$notification.warning({
                        message: 'Duplicate Data Found or No New Data Can be Uploaded',
                        description: `${duplicatesFound.length} duplicate entries were found and skipped. Please review the invalid data section for details.`
                    });

                    return;
                }

                const res = await this.$api.post('graduates/create/multiple', { csv: cleanData })
                const response = res.data
                if (!response.error) {
                    this.getList()
                    this.addUserModal = false
                    this.$notification.success({
                        message: 'Upload Successful',
                        description: `${cleanData.length} new graduate records have been uploaded successfully.`
                    });
                } else {
                    this.$notification.error({
                        message: 'Upload Failed',
                        description: response.message || 'Failed to upload CSV data'
                    });
                }
            } catch (err) {
                console.error(err)
                this.$notification.error({
                    message: 'Upload Error',
                    description: 'An error occurred while uploading the CSV data'
                });
            }
        },
        async getList() {
            this.users = []
            this.usersOrig = []
            this.$api.get("graduates/get/list").then((res) => {
                let response = { ...res.data }
                if (!response.error) {
                    let grads = response.list
                    // Sort descending by ID
                    grads.sort((a, b) => b.id - a.id);
                    
                    this.users = grads
                    this.usersOrig = grads

                    let courses = []
                    let sy = []
                    let majors = [];
                    grads.forEach(element => {
                        if(element.course) courses.push({ label: element.course, value: element.course })
                        if(element.yearGraduated) sy.push({ label: element.yearGraduated, value: element.yearGraduated })
                        if(element.major) majors.push({ label: element.major, value: element.major });
                    });
                    
                    this.courseFilter = courses.filter((e, i, self) => i === self.findIndex((t) => t.label === e.label));
                    this.schoolYearFilter = sy.filter((e, i, self) => i === self.findIndex((t) => t.label === e.label));
                    this.majorFilter = majors.filter((e, i, self) => i === self.findIndex((t) => t.label === e.label));
                }
            })
        },
        handleDelete(rec) {
             const payload = { id: rec.id }
             return this.$api.post('graduates/delete', payload).then((res) => {
                if (!res.data.error) {
                    this.$notification.success({ message: 'Deleted', description: 'Record deleted successfully' })
                    this.getList()
                } else {
                    this.$notification.error({ message: 'Error', description: res.data.message || 'Failed to delete record' })
                }
            })
        },
        handleView(rec) {
            this.viewRecord = rec
            this.viewModal = true
        },
        handleEdit(rec) {
            console.log('Editing record:', rec);
            this.editRecord = Object.assign({}, rec)
            this.editModal = true
        },
        async saveEdit() {
            this.editSaving = true
            const payload = Object.assign({}, this.editRecord)
            this.$api.post('graduates/update', payload).then((res) => {
                this.editSaving = false
                if (!res.data.error) {
                    this.$notification.success({ message: 'Success', description: 'Record updated successfully!' });
                    this.editModal = false
                    this.getList()
                } else {
                    this.$notification.error({ message: 'Error', description: res.data.message || 'Update failed' });
                }
            }).catch(err => { 
                this.editSaving = false; 
                console.error(err);
                this.$notification.error({ message: 'Error', description: 'Server error' });
            })
        },
        async saveSingleEntry() {
            if (!this.singleRecord.name || !this.singleRecord.gender || !this.singleRecord.studentId) {
                this.$notification.error({ message: 'Validation Error', description: 'Student ID, Name and Gender are required' });
                return;
            }
            if (!this.user) {
                this.$notification.error({ message: 'Error', description: 'User session invalid.' });
                return;
            }

            const duplicate = this.checkForDuplicateLocally(
                this.singleRecord.name,
                this.singleRecord.yearGraduated,
                this.singleRecord.course,
                this.singleRecord.studentId
            );
            
            if (duplicate) {
                this.$notification.warning({ message: 'Duplicate Entry', description: 'Record (or Student ID) already exists.' });
                return;
            }
            
            this.singleSaving = true;
            const payload = {
                ...this.singleRecord,
                createdBy: Number(this.user.userId)
            };
            
            try {
                const res = await this.$api.post('graduates/create', payload);
                this.singleSaving = false;
                if (!res.data.error) {
                    this.addSingleModal = false;
                    this.singleRecord = {};
                    this.getList();
                    this.$notification.success({ message: 'Success', description: 'Graduate record added!' });
                }
            } catch (err) {
                this.singleSaving = false;
                if (err.response && err.response.status === 409) {
                    this.$notification.warning({ message: 'Duplicate Entry', description: err.response.data.message });
                } else {
                    this.$notification.error({ message: 'Error', description: 'Failed to create record' });
                }
            }
        },
        checkForDuplicateLocally(name, yearGraduated, course, studentId) {
            return this.users.some(user => {
                const nameMatch = user.name?.toLowerCase().trim() === (name || '').toLowerCase().trim();
                const yearMatch = user.yearGraduated === yearGraduated;
                const courseMatch = user.course?.toLowerCase().trim() === (course || '').toLowerCase().trim();
                const idMatch = studentId && user.studentId && (String(user.studentId).trim() === String(studentId).trim());
                
                return (nameMatch && yearMatch && courseMatch) || idMatch;
            });
        },
        filterDuplicatesFromCSV(csvData) {
            const cleanData = [];
            const duplicatesFound = [];
            const seenEntries = new Set();
            
            csvData.forEach((entry, index) => {
                const safeName = (entry.name || '').toLowerCase().trim();
                const safeCourse = (entry.course || '').toLowerCase().trim();
                const safeId = (entry.studentId || '').trim();
                
                // Key for checking duplicates within the CSV itself
                const key = `${safeName}_${entry.yearGraduated}_${safeCourse}_${safeId}`;
                
                const isDuplicateInExisting = this.checkForDuplicateLocally(entry.name, entry.yearGraduated, entry.course, entry.studentId);
                const isDuplicateInCSV = seenEntries.has(key);
                
                if (isDuplicateInExisting || isDuplicateInCSV) {
                    duplicatesFound.push({ row: index + 1, name: entry.name });
                } else {
                    seenEntries.add(key);
                    cleanData.push(entry);
                }
            });
            return { cleanData, duplicatesFound };
        },
        // ...existing code...
        exportInvalidCSV() {
            if (!this.invalidCsvData.length) return;
            const exportableColumns = this.columns.filter(col => col.dataIndex);
            const content = [
                exportableColumns.map(col => this.wrapCsvValue(col.title))
            ].concat(
                this.invalidCsvData.map(row =>
                    exportableColumns.map(col => this.wrapCsvValue(row[col.dataIndex])).join(',')
                )
            ).join('\n');
            const anchor = document.createElement('a');
            anchor.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(content);
            anchor.target = '_blank';
            anchor.download = 'InvalidData.csv';
            anchor.click();
        },
    }
}
</script>

<style lang="scss" scoped>
.slide-fade-enter-active, .slide-fade-leave-active {
  transition: all 0.3s ease;
}
.slide-fade-enter, .slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}
.mb-24 {
    margin-bottom: 24px;
}

.table-card { 
    border-top: 3px solid #52c41a; 
}
.mr-2 { margin-right: 8px; }
.mr-3 { margin-right: 12px; }
.ml-2 { margin-left: 8px; }
.text-muted { color: #8c8c8c; }
.text-dark { color: #262626; }
.d-flex { display: flex; align-items: center; }

.action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Deep selector to target Ant Design Table internals */
::v-deep .ant-table-thead > tr > th {
    background-color: #fafafa;
    font-weight: 700;
    color: #595959;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.5px;
}
::v-deep .ant-table-row:hover {
    background-color: #f6ffed !important;
    cursor: pointer;
}
</style>