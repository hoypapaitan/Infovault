<!-- 
	This is the dashboard page, it uses the dashboard layout in: 
	"./layouts/Dashboard.vue" .
 -->

 <template>
	<div>
		<a-row :gutter="24">
			<a-col :span="24" :lg="24" class="mb-15">
				Resource List
			</a-col>
			<a-col :span="24" :lg="6" >
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
					<a-form-item label="">
						<a-select
							v-model="resourceUrl"
							:options="docsOpt"
						/>
					</a-form-item>
				</a-form>
			</a-col>
			<a-col :span="24" :lg="6" >
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
					<a-form-item label="">
						<a-button-group>
							<a-button 
								:disabled="docsOpt.length === 1"
								@click="removeResource" 
								type="danger"
							> 
								<a-icon type="delete" /> Remove Resource
							</a-button>
							<a-button 
								@click="isNewUserModalOpen = true"
								type="primary"
							> 
								<a-icon type="file-add" /> Add Resource
							</a-button>
						</a-button-group>
					</a-form-item>
				</a-form>
			</a-col>
			<!-- PDF Content -->
			<a-col :span="24" :lg="24" >
				<iframe id="pdf" style="width: 100%; height: 80dvh; border: none;"></iframe>
			</a-col>
		</a-row>

		<a-modal
			v-model="isNewUserModalOpen"
			title="Add New Resource"
			centered
			@ok="onSubmit"
			ok-text="Submit"
		>
			<!-- <a-alert
				message="Notes"
				description="If you still not yet downloaded the csv format to add data please do ensure download the template below and fill."
				type="info"
				show-icon
			/> -->
			
			<a-upload-dragger
				name="file"
				accept=".pdf"
				@change="getFile"
			>
				<p class="ant-upload-drag-icon">
					<a-icon type="file-add" />
				</p>
				<p class="ant-upload-text">
					Click or drag file to this area to upload data
				</p>
				<p class="ant-upload-hint">
					This will automatically insert the data and close the form once the upload of data is complete
				</p>
			</a-upload-dragger>
		</a-modal>
	</div>
</template>

<script>
import { jwtDecode } from 'jwt-decode';
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib'
	
export default ({
	data() {
		return {
			// Counter Widgets Stats
			isNewUserModalOpen: false,
			resourceUrl: 'docs/resources.pdf',
			docList: [],
			docsOpt: [],
			evaluationList:[],
      selectedTitle: "",
			form: {
				title: '',
				content: ''
			}
		}
	},
	created(){
      this.previewPDF()
      this.getList()
    },
	watch:{
      resourceUrl(){
        this.previewPDF();
      },
    },
	computed: {
      user: function(){
        let token = localStorage.getItem('userToken')
        // token = JSON.parse(token);
        return jwtDecode(token);
      }
    },
	methods:{
    async changeResource(val){
      console.log(val)
    },
      async getFile(data){
        console.log(data.file)
        let filePath = data.file.originFileObj
        let fileBase = await this.getBase64(filePath)
        this.form.title = filePath.name.replace('.pdf', '')
        this.form.content = fileBase
      },
      async getBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
      },
      async readCSVFile(file){
        var reader = new FileReader();
        let filePath = file.target.files[0]
        reader.readAsText(new Blob(
          [filePath],
          {"type": file.type}
        ))
        const fileContent = await new Promise(resolve => {
          reader.onloadend = (event) => {
            resolve(event.target.result)
          }
        })
        let csvData = d3.csvParse(fileContent)
        

        csvData = csvData.map((el) => {
          return {
            ...el,
            createdBy: Number(this.user.userId)
          }
        })

        this.evaluationList = csvData
      },
      async previewPDF(){
        const existingPdfBytes = await fetch(this.resourceUrl).then(res => res.arrayBuffer())
        const pdfDoc = await PDFDocument.load(existingPdfBytes)

        // get the index of the selected
        let title = "Default File"
        let findTitle = this.docsOpt.filter(el => el.value === this.resourceUrl);
        if(findTitle.length > 0){
          title = findTitle[0].label
        } 

        pdfDoc.setTitle(title)

        const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
        document.getElementById('pdf').src = pdfDataUri;

      },
      async removeResource(){
        let filterDoc = this.docList.filter((el) => { return el.content === this.resourceUrl })
        
        let payload = {
          cId: filterDoc[0].id
        }

        this.$api.post("document/delete/content", payload).then((res) => {
          let response = {...res.data}
          if(!response.error){
            this.clearForm();
            this.getList();
          } else {
            console.log('there is some error')
          }
        })
      },
      async onSubmit(){
        if(this.form.content !== '' && this.form.title !== ""){
          let payload = {
            ...this.form,
            createdBy: Number(this.user.userId)
          }

          this.$api.post("document/create/content", payload).then((res) => {
            let response = {...res.data}
            if(!response.error){
              this.clearForm();
              this.getList();
              this.isNewUserModalOpen = false
            } else {
              // show Error
              console.log('there is some error')
            }
          })
        } else {
        //   toast.add({
        //     id: 'error_submit',
        //     title: 'Submit Failed.',
        //     description: 'Please fill the required fields.',
        //     icon: 'i-octicon-alert-24',
        //     color: "red",
        //     timeout: 1000,
        //   })
        }
      },
      async getList(){
        this.docsOpt = []
        this.$api.get("document/get/list").then((res) => {
          let response = {...res.data}
          if(!response.error){
            this.docList = response.list
            this.docsOpt = response.list.map(el => {
              return {
                label: el.title,
                value: el.content
              }
            })
            this.resourceUrl = this.docsOpt[0].value
            this.selectedTitle = this.docsOpt[0].tilte
          } else {
            // show Error
            console.log('there is some error')
          }
        })
      },
      clearForm(){
        this.form = {
          title: '',
          content: '',
        }
      },
    }
})
</script>

<style lang="scss">
</style>