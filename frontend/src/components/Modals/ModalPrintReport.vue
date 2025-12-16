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
// Note: You used jwtDecode in computed but it wasn't imported in your snippet. 
// Ensure 'jwt-decode' is imported if not handled globally.

export default ({
    props:{
        dataVal: {
            type: Array,      // FIXED: Changed from Object to Array
            default: () => [] // FIXED: Default must be a function for Arrays/Objects
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
            // Only generate PDF if modal is opening and data exists
            if(newVal && this.dataVal){ 
                this.generatePdf(this.dataVal)
            }
        }
    },
    computed:{
        user: function(){
            let token = localStorage.getItem('userToken')
            if (!token) return null; // Added safety check
            token = JSON.parse(token);
            // Ensure jwtDecode is defined in your imports or global scope
            return jwtDecode(token.value); 
        },
    },
    methods:{
        moment,
        handleCancel(){
            this.$emit('closePrint')
        },
        async generatePdf(data){
            try {
                const url = '/docs/legalformat.pdf'
                const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
                
                // Create a new PDFDocument
                const firstDoc = await PDFDocument.load(existingPdfBytes)
                const pdfDoc = await PDFDocument.create();
                const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold)
                
                // Safety check for data length
                let dataCount = data ? data.length : 0 
                const itemPerPage = 40

                // loop how many pages
                // Added Math.max to ensure at least 1 loop if data is empty but we want to show headers
                const pageCount = Math.max(1, Math.ceil(dataCount / itemPerPage)); 
                
                for (let index = 1; index <= pageCount; index++) {
                    const [firstDonorPage] = await pdfDoc.copyPages(firstDoc, [0])
                    pdfDoc.addPage(firstDonorPage)
                }

                const pages = pdfDoc.getPages()
                pages.forEach((elpage, index) => {
                    const { width, height } = elpage.getSize()
                    // --- HEADER TEXT ---
                    elpage.drawText(`ASCOT YEARBOOK GRADUATE MASTERLIST`, {
                        x: 20,
                        y: height - 25,
                        size: 14,
                        font: fontBold,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`Generated: ${moment().format('MMMM D, YYYY')}` , {
                        x: 20,
                        y: height - 40,
                        size: 10,
                        font: fontBold,
                        color: rgb(0, 0, 0),
                    })
                    // --- TABLE HEADERS ---
                    elpage.drawText(`Student No.`, { x: 15, y: height - 80, size: 9, font: fontBold, color: rgb(0, 0, 0) })
                    elpage.drawText(`Name`, { x: 90, y: height - 80, size: 9, font: fontBold, color: rgb(0, 0, 0) })
                    elpage.drawText(`Address`, { x: 210, y: height - 80, size: 9, font: fontBold, color: rgb(0, 0, 0) })
                    elpage.drawText(`Sex`, { x: 320, y: height - 80, size: 9, font: fontBold, color: rgb(0, 0, 0) })
                    elpage.drawText(`Course`, { x: 370, y: height - 80, size: 9, font: fontBold, color: rgb(0, 0, 0) })
                    elpage.drawText(`Major`, { x: 470, y: height - 80, size: 9, font: fontBold, color: rgb(0, 0, 0) })
                    elpage.drawText(`Class Of`, { x: 570, y: height - 80, size: 9, font: fontBold, color: rgb(0, 0, 0) })
                    elpage.drawText(`Achievements`, { x: 640, y: height - 80, size: 9, font: fontBold, color: rgb(0, 0, 0) })

                    // --- CONTENT LOOP ---
                    if (dataCount > 0) {
                        let paginated = this.getPaginatedData(data, index+1, itemPerPage)
                        let stdContentHeight = height - 230;
                        for (let idx = 1; idx <= paginated.length; idx++) {
                            let edata = paginated[idx-1]
                            elpage.drawText(`${edata.studentId || '--'}`, { x: 15, y: stdContentHeight + 135, size: 9, color: rgb(0, 0, 0) })
                            elpage.drawText(`${edata.name || '--'}`, { x: 90, y: stdContentHeight + 135, size: 9, color: rgb(0, 0, 0) })
                            elpage.drawText(`${edata.address || '--'}`, { x: 210, y: stdContentHeight + 135, size: 9, color: rgb(0, 0, 0) })
                            elpage.drawText(`${edata.gender || '--'}`, { x: 320, y: stdContentHeight + 135, size: 9, color: rgb(0, 0, 0) })
                            elpage.drawText(`${edata.course || '--'}`, { x: 370, y: stdContentHeight + 135, size: 9, color: rgb(0, 0, 0) })
                            elpage.drawText(`${edata.major || '--'}`, { x: 470, y: stdContentHeight + 135, size: 9, color: rgb(0, 0, 0) })
                            elpage.drawText(`${edata.yearGraduated || '--'}`, { x: 570, y: stdContentHeight + 135, size: 9, color: rgb(0, 0, 0) })
                            let achievements = edata.achievement || ''
                            if (edata.additionalAchievement && Array.isArray(edata.additionalAchievement)) {
                                achievements += (achievements ? ', ' : '') + edata.additionalAchievement.join(', ')
                            }
                            elpage.drawText(`${achievements || '--'}`, { x: 640, y: stdContentHeight + 135, size: 9, color: rgb(0, 0, 0), maxWidth: 120 })
                            stdContentHeight -= 20
                        }
                    }
                })

                const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
                const iframe = document.getElementById('pdf');
                if(iframe) iframe.src = pdfDataUri;

            } catch (error) {
                console.error("Error generating PDF:", error);
            }
        },
        getPaginatedData(array, page, limit) {
            const offset = limit * (page - 1);
            const paginatedItems = array.slice(offset, limit * page);
            return paginatedItems
        }
    }
})
</script>