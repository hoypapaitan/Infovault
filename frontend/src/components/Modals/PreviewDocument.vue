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

                <q-card-section v-if="appData.type !== 'picture'" class="q-pt-none" style="height: 90vh;">
                    <iframe id="pdfPreview" style="width: 100%; height: 100%; border: none;"></iframe>
                </q-card-section>
                <q-card-section v-if="appData.type === 'picture'" class="q-pt-none" style="height: 90vh;">
                    <q-img :src="imgeSrc" />
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

export default {
    name: 'PrintModal',
    data(){
        return {
            openModal: false,
            maximizedToggle: true,
            imgeSrc: "",
        }
    },
    watch:{
        modalStatus: function(newVal){
            this.openModal = newVal
            if(newVal){
                if(this.appData.type === "picture"){
                    this.imgeSrc = this.appData.url
                } else {
                    this.createPDF(this.appData)
                }
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
        base64ToArrayBuffer(base64) {
            let binaryString = atob(base64);
            let bytes = new Uint8Array(binaryString.length);
            for (var i = 0; i < binaryString.length; i++) {
                bytes[i] = binaryString.charCodeAt(i);
            }
            return bytes.buffer;
        },
        async createPDF(data){
            const existingPdfBytes = await fetch(data.url).then(res => res.arrayBuffer())
            // Create a new PDFDocument
            const pdfDoc = await PDFDocument.load(existingPdfBytes)
            // Add a blank page to the document
            pdfDoc.setTitle(data.fileName)
            const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
            document.getElementById('pdfPreview').src = pdfDataUri;
        },

        // ending method
    },

}
</script>
