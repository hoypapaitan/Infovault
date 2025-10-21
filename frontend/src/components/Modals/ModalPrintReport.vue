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
            const itemPerPage = 40

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
                elpage.drawText(`GENDER EQUALITY DATA REPORT`, {
                    x: 20,
                    y: height - 40,
                    size: 12,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                
                elpage.drawText(`Course`, {
                    x: 15,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`School Year`, {
                    x: 210,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Report`, {
                    x: 270,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Year`, {
                    x: 320,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Term`, {
                    x: 370,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Male`, {
                    x: 470,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Female`, {
                    x: 520,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })

                let paginated = this.getPaginatedData(data, index+1, itemPerPage)
                let stdContentHeight = height - 230;
                for (let idx = 1; idx <= paginated.length; idx++) {
                    let edata = paginated[idx-1]
                    elpage.drawText(`${edata.course || '--'}`, {
                        x: 15,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 230,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.yearFrom || '--'}`, {
                        x: 210,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 230,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.reportType || '--'}`, {
                        x: 270,
                        y: stdContentHeight + 135,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.classYear || '--'}`, {
                        x: 320,
                        y: stdContentHeight + 135,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.term || '--'}`, {
                        x: 370,
                        y: stdContentHeight + 135,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.male || '--'}`, {
                        x: 470,
                        y: stdContentHeight + 135,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.female || '--'}`, {
                        x: 520,
                        y: stdContentHeight + 135,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })

                    stdContentHeight -= 20
                }
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