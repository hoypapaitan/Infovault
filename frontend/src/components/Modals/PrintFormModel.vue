<template>
    <div>
        <q-dialog
            v-model="openModal"
            :maximized="maximizedToggle"
            transition-show="slide-up"
            transition-hide="slide-down"
            persistent
        >
            <q-card class="">
                <q-bar>
                    <q-space />
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section class="q-pt-none" style="height: 90vh;">
                    <iframe id="pdf" style="width: 100%; height: 100%; border: none;"></iframe>
                </q-card-section>
            </q-card>
        </q-dialog>
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib'
import { jwtDecode } from 'jwt-decode'

const dateNow = moment().format('MM/DD/YYYY');

export default {
    name: 'PrintModal',
    data(){
        return {
            openModal: false,
            maximizedToggle: true,
        }
    },
    watch:{
        modalStatus: function(newVal){
            this.openModal = newVal
            if(newVal){
                this.createPDF(this.appData)
            }
        }
    },
    props: {
        appData: {
            type: Object
        },
        modalStatus: {
            type: Boolean
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        }
    },
    methods: {
        moment,
        async closeModal(){
            this.$emit('updatePrintStatus', false);
        },
        async getBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        },
        async createPDF(data){
            const url = 'docs/ascots_form.pdf'
            // const url = 'files/draftInvoice.pdf'
            const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
            // Create a new PDFDocument
            const pdfDoc = await PDFDocument.load(existingPdfBytes)
            // Add a blank page to the document
            const pages = pdfDoc.getPages()
            const form = pdfDoc.getForm()
            const fpage = pages[0];
            // Get the width and height of the page
            const { width, height } = fpage.getSize()
            const fontSize = 9
            let curdateYear = moment().format("YY");
            pdfDoc.setTitle("ASCOTS Application Form")
            // console.log(data)

            // get the 2x2 picture
            let picture = data.scholar.requirements.filter(el => el.name === 'picture')

            if(picture.length > 0){
                // validate if png or jpg
                console.log(picture)
                // if the file is a File from Apply
                // if(picture[0].file){
                //     let convertedFile = await this.getBase64(picture[0].file)
                //     picture[0].uploadFile = convertedFile
                // }


                let validate = picture[0].uploadFile.match(/[^:]\w+\/[\w-+\d.]+(?=;|,)/)[0];
                if(validate === 'image/png'){
                    // print the picture
                    const profilePictureBytes = await fetch(picture[0].uploadFile).then(res => res.arrayBuffer())
                    const profileImage = await pdfDoc.embedPng(profilePictureBytes)

                    fpage.drawImage(profileImage, {
                        x: 473,
                        y: height - 105,
                        width: 100,
                        height: 100,
                    })
                } else {
                    // print the picture
                    const profilePictureBytes = await fetch(picture[0].uploadFile).then(res => res.arrayBuffer())
                    const profileImage = await pdfDoc.embedJpg(profilePictureBytes)

                    fpage.drawImage(profileImage, {
                        x: 473,
                        y: height - 105,
                        width: 110,
                        height: 100,
                    })
                }
                
            }

            // Draw a string of text toward the top of the page
            fpage.drawText(`${data.scholar.title}`, {
              x: 150,
              y: height - 140,
              size: 10,
              color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.student.lastName}, ${data.student.firstName} ${data.student.suffix} ${data.student.middleName}`, {
              x: 90,
              y: height - 190,
              size: 10,
              color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.student.dateOfBirth}`, {
              x: 130,
              y: height - 225,
              size: 10,
              color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.student.placeOfBirth}`, {
              x: 130,
              y: height - 240,
              size: 10,
              color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.student.sex}`, {
              x: 130,
              y: height - 255,
              size: 10,
              color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.student.contact}`, {
              x: 130,
              y: height - 270,
              size: 10,
              color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.student.email}`, {
              x: 130,
              y: height - 283,
              size: 10,
              color: rgb(0, 0, 0),
            })

            fpage.drawText(`${data.student.address}`, {
                x: 315,
                y: height - 235,
                size: 10,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 290,
                color: rgb(0, 0, 0),
            })

            fpage.drawText(`${data.student.schoolAttended}`, {
                x: 315,
                y: height - 270,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 290,
                color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.student.schoolAddress}`, {
                x: 315,
                y: height - 293,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })

            let fatherLiving = form.createCheckBox('father.live')
            let fatherDeceased = form.createCheckBox('father.deceased')
            fatherLiving.addToPage(fpage, { 
                x: 270, 
                y: height - 338,
                width: 10,
                height: 10,
            })
            fatherDeceased.addToPage(fpage, { 
                x: 327, 
                y: height - 338,
                width: 10,
                height: 10,
            })

            fatherLiving = form.getCheckBox('father.live')
            fatherDeceased = form.getCheckBox('father.deceased')
            fatherLiving.enableReadOnly()
            fatherDeceased.enableReadOnly()
            if(data.others.father.livingStatus === "living"){
                fatherLiving.check()
            } else if (data.others.father.livingStatus === "deceased") {
                fatherDeceased.check()
            }
            fpage.drawText(`${data.others.father.name}`, {
                x: 220,
                y: height - 360,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })
            
            fpage.drawText(`${data.others.father.address}`, {
                x: 220,
                y: height - 372,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })

            fpage.drawText(`${data.others.father.occupation}`, {
                x: 220,
                y: height - 383,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })

            fpage.drawText(`${data.others.father.educAttainment}`, {
                x: 220,
                y: height - 393,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })

            fpage.drawText(`${data.others.father.contact}`, {
                x: 220,
                y: height - 406,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })



            

            let motherLiving = form.createCheckBox('mother.live')
            let motherDeceased = form.createCheckBox('mother.deceased')
            
            motherLiving.addToPage(fpage, { 
                x: 453, 
                y: height - 338,
                width: 10,
                height: 10,
            })
            motherDeceased.addToPage(fpage, { 
                x: 506, 
                y: height - 338,
                width: 10,
                height: 10,
            })

            motherLiving = form.getCheckBox('mother.live')
            motherDeceased = form.getCheckBox('mother.deceased')
            motherLiving.enableReadOnly()
            motherDeceased.enableReadOnly()
            if(data.others.mother.livingStatus === "living"){
                motherLiving.check()
            } else if (data.others.mother.livingStatus === "deceased") {
                motherDeceased.check()
            }

            fpage.drawText(`${data.others.mother.name}`, {
                x: 402,
                y: height - 360,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })
            
            fpage.drawText(`${data.others.mother.address}`, {
                x: 402,
                y: height - 372,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })

            fpage.drawText(`${data.others.mother.occupation}`, {
                x: 402,
                y: height - 383,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })

            fpage.drawText(`${data.others.mother.educAttainment}`, {
                x: 402,
                y: height - 396,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })

            fpage.drawText(`${data.others.mother.contact}`, {
                x: 402,
                y: height - 406,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })
            

            fpage.drawText(`${data.others.totalIncome}`, {
                x: 220,
                y: height - 419,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.others.noOfSiblings}`, {
                x: 520,
                y: height - 419,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 200,
                color: rgb(0, 0, 0),
            })

            if(!data.others.notWithParents){
                fpage.drawText(`${data.others.guardian.name}`, {
                    x: 85,
                    y: height - 449,
                    size: 9,
                    spacing: 1,
                    lineHeight: 11,
                    maxWidth: 200,
                    color: rgb(0, 0, 0),
                })
                fpage.drawText(`${data.others.guardian.occupation}`, {
                    x: 260,
                    y: height - 449,
                    size: 9,
                    spacing: 1,
                    lineHeight: 11,
                    maxWidth: 200,
                    color: rgb(0, 0, 0),
                }) 
                fpage.drawText(`${data.others.guardian.relation}`, {
                    x: 420,
                    y: height - 449,
                    size: 9,
                    spacing: 1,
                    lineHeight: 11,
                    maxWidth: 200,
                    color: rgb(0, 0, 0),
                })
            
            }


            let stdTextHeight = height - 610;
            data.scholar.requirements.forEach((el, indx) => {
                let elemCheck = form.createCheckBox(`element.${el.name}`)
                elemCheck.addToPage(fpage, { 
                    x: 55, 
                    y: stdTextHeight,
                    width: 8,
                    height: 8,
                })
                elemCheck = form.getCheckBox(`element.${el.name}`)
                elemCheck.enableReadOnly()
                if(el.fileUploaded){
                    elemCheck.check()
                }

                fpage.drawText(`${indx+1}.      ${el.label}`, {
                    x: 45,
                    y: stdTextHeight,
                    size: 9,
                    spacing: 1,
                    lineHeight: 11,
                    maxWidth: 250,
                    color: rgb(0, 0, 0),
                })
                
                stdTextHeight -= 20
            });

            // let stdTextHeightReq = height - 610;
            // data.scholar.qualification.forEach((el, indx) => {
            //     fpage.drawText(`${indx+1}. ${el.description}`, {
            //         x: 315,
            //         y: stdTextHeightReq,
            //         size: 9,
            //         spacing: 1,
            //         lineHeight: 11,
            //         maxWidth: 250,
            //         color: rgb(0, 0, 0),
            //     })
                
            //     stdTextHeightReq -= 25
            // });

            let quali = data.scholar.qualification.map((el, idx) => `${idx+1}. ${el.description}`)
            
            fpage.drawText(quali.join("\r\n"), {
                x: 315,
                y: height - 600,
                size: 9,
                spacing: 1,
                lineHeight: 17,
                maxWidth: 270,
                color: rgb(0, 0, 0),
            })

            console.log('on here')
            const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
            document.getElementById('pdf').src = pdfDataUri;
        },

        // ending method
    },

}
</script>
