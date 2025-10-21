<template>

    <a-modal
		v-model="openModal"
        :dialog-style="{ top: '0px' }"
		title="Report Preview"
        width="100%"
        :closable="false"
        @cancel="handleCancel"
        @ok="handleCancel"
	>
        <iframe id="pdf" style="width: 100%; height: 80dvh; border: none;"></iframe>
	</a-modal>

</template>

<script>
import moment from 'moment'
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib'

export default ({
	props:{
        dataVal: {
            type: Object,
            default: {
                from: "",
                to: ""
            } || "2024 - 2025"
        },
        openPrint: {
            type: Boolean,
            default: false
        },
    },
	data() {
		return {
            openModal: false
		}
	},
    watch:{
        openPrint(newVal){
            this.openModal = newVal
            if(newVal){
                this.generatePdf(this.dataVal)
            }
        }
    },
	computed:{
		user: function(){
			let token = localStorage.getItem('userToken')
			token = JSON.parse(token);
			return jwtDecode(token.value);
		},
        getGADTotal(){
			let gadTot = this.dataVal.reduce((a, b) => Number(a) + Number(b.scoreCol), 0)
			return gadTot
		},
	},
	methods:{
        moment,
        handleCancel(){
            this.$emit('closePrint')
        },
        async generatePdf(data){
            const url = '/docs/legalformat.pdf'
            const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
            // Create a new PDFDocument
            const firstDoc = await PDFDocument.load(existingPdfBytes)
            const pdfDoc = await PDFDocument.create();
            const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold)
            let dataCount = data.length
            const itemPerPage = 15

            // loop how many pages
            for (let index = 1; index <= Math.ceil(dataCount / itemPerPage); index++) {
                const [firstDonorPage] = await pdfDoc.copyPages(firstDoc, [0])
                pdfDoc.addPage(firstDonorPage)
            }

            const pages = pdfDoc.getPages()
            pages.forEach((elpage, index) => {
                const { width, height } = elpage.getSize()
                elpage.drawText(`ASCOT GENDER ANALYTICS DASHBOARD`, {
                    x: 20,
                    y: height - 25,
                    size: 14,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`GENDER EQUALITY SURVEY RESPONSE`, {
                    x: 20,
                    y: height - 40,
                    size: 12,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                
                elpage.drawText(`Dimension and question`, {
                    x: 15,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Response`, {
                    x: 350,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Score for the item/element`, {
                    x: 420,
                    y: height - 80,
                    size: 9,
                    lineHeight: 10,
                    maxWidth: 100,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Result or comment`, {
                    x: 490,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                // <p v-if="question.title !== '' && question.question === ''"><strong>
				// 		{{ `${question.order} ${question.title}` }}
				// 	</strong></p>
				// 	<p v-if="question.title !== '' && question.question !== ''">
				// 		<strong>{{ `${question.order} ${question.title}` }}</strong> <br />
				// 		{{ `${question.question}` }}
				// 	</p>
				// 	<p v-else>{{ `${question.order} ${question.question} (${question.scoring})` }}</p>
                let paginated = this.getPaginatedData(data, index+1, itemPerPage)
                let stdContentHeight = height - 260;
                for (let idx = 1; idx <= paginated.length; idx++) {
                    let edata = paginated[idx-1]
                    
                    elpage.drawText(`${edata.order || ''} ${edata.title || edata.question}`, {
                        x: 15,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 330,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.responseCol || ''}`, {
                        x: 350,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 230,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.scoreCol || ''}`, {
                        x: 420,
                        y: stdContentHeight + 135,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.remarks || ''}`, {
                        x: 490,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 1300,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    stdContentHeight -= 70
                }

                let totalText = `TOTAL GAD SCORE— PROJECT IDENTIFICATION AND DESIGN STAGES (Add the score for each of the 10 elements, or the figures in thickly bordered cells.)`
                elpage.drawText(`${totalText}`, {
                    x: 15,
                    y: stdContentHeight + 135,
                    lineHeight: 10,
                    maxWidth: 390,
                    size: 9,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`${this.getGADTotal || '0'}`, {
                    x: 420,
                    y: stdContentHeight + 135,
                    lineHeight: 10,
                    maxWidth: 1300,
                    size: 9,
                    color: rgb(0, 0, 0),
                })
            })

            const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
            document.getElementById('pdf').src = pdfDataUri;
        },
        getPaginatedData(array, page, limit) {
            const offset = limit * (page - 1);
            const paginatedItems = array.slice(offset, limit * page);
            return paginatedItems
        }
    }
})

</script>