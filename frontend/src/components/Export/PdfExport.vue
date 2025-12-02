<template>
  <a-button 
    type="primary" 
    icon="download" 
    @click="generatePdf" 
    :loading="isGenerating"
    :disabled="disabled || graduates.length === 0"
  >
    <slot>
      Export {{ year ? 'Batch ' + year : 'All' }}
    </slot>
  </a-button>
</template>

<script>
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib';

export default {
  name: 'GraduatePdfExport',
  props: {
    // Array of graduate objects
    graduates: {
      type: Array,
      required: true,
      default: () => []
    },
    // The selected year (string or number)
    year: {
      type: [String, Number],
      default: null
    },
    // Optional disable prop
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      isGenerating: false
    };
  },
  methods: {
    async generatePdf() {
      if (this.graduates.length === 0) {
        this.$message.warning("No data to export");
        return;
      }

      this.isGenerating = true;

      try {
        // 1. Create Document & Fonts
        const pdfDoc = await PDFDocument.create();
        const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
        const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold);

        // 2. Settings
        const fontSize = 10;
        const rowHeight = 20;
        const margin = 50;
        
        // Column Positions (X coordinates) - Adjusted to fit Gender
        const xPos = {
            id: 40,
            name: 80,
            gender: 220, // New Position for Gender
            year: 270,
            course: 320,
            achievement: 470
        };

        // Helper: Add New Page
        const addNewPage = () => {
            const page = pdfDoc.addPage();
            const { width, height } = page.getSize();
            
            // Title
            const titleText = this.year 
                ? `Graduate List - Batch ${this.year}` 
                : 'Graduate List - All Records';
                
            page.drawText(titleText, {
                x: margin,
                y: height - margin,
                size: 18,
                font: fontBold,
                color: rgb(0, 0, 0),
            });

            // Table Headers
            const headerY = height - margin - 40;
            page.drawText('ID', { x: xPos.id, y: headerY, size: 12, font: fontBold });
            page.drawText('Name', { x: xPos.name, y: headerY, size: 12, font: fontBold });
            page.drawText('Gender', { x: xPos.gender, y: headerY, size: 12, font: fontBold }); // Added Header
            page.drawText('Year', { x: xPos.year, y: headerY, size: 12, font: fontBold });
            page.drawText('Course', { x: xPos.course, y: headerY, size: 12, font: fontBold });
            page.drawText('Achievement', { x: xPos.achievement, y: headerY, size: 12, font: fontBold });

            return { page, currentY: headerY - 10, height };
        };

        // Initialize first page
        let { page, currentY, height } = addNewPage();

        // 3. Draw Rows
        for (const grad of this.graduates) {
            currentY -= rowHeight;

            // Page Break Check
            if (currentY < margin) {
                const newPageData = addNewPage();
                page = newPageData.page;
                currentY = newPageData.currentY - rowHeight;
                height = newPageData.height;
            }

            // Draw Separator Line
            page.drawLine({
                start: { x: margin, y: currentY + 12 },
                end: { x: page.getWidth() - margin, y: currentY + 12 },
                thickness: 0.5,
                color: rgb(0.8, 0.8, 0.8),
            });

            // Draw Data
            page.drawText(String(grad.id || ''), { x: xPos.id, y: currentY, size: fontSize, font: font });
            
            // Truncate name slightly more to fit gender
            const safeName = (grad.name && grad.name.length > 22) ? grad.name.substring(0, 20) + '...' : (grad.name || '');
            page.drawText(safeName, { x: xPos.name, y: currentY, size: fontSize, font: font });

            // Draw Gender Data
            page.drawText(String(grad.gender || ''), { x: xPos.gender, y: currentY, size: fontSize, font: font });

            page.drawText(String(grad.yearGraduated || ''), { x: xPos.year, y: currentY, size: fontSize, font: font });
            
            const safeCourse = (grad.course && grad.course.length > 25) ? grad.course.substring(0, 23) + '...' : (grad.course || '');
            page.drawText(safeCourse, { x: xPos.course, y: currentY, size: fontSize, font: font });
            
            page.drawText(String(grad.achievement || ''), { x: xPos.achievement, y: currentY, size: fontSize, font: font });
        }

        // 4. Save & Download
        const pdfBytes = await pdfDoc.save();
        const blob = new Blob([pdfBytes], { type: 'application/pdf' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `Graduates_${this.year || 'All'}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        this.$message.success('PDF Downloaded');
        this.$emit('export-success'); 

      } catch (error) {
        console.error('Export Error:', error);
        this.$message.error('Failed to generate PDF');
        this.$emit('export-error', error);
      } finally {
        this.isGenerating = false;
      }
    }
  }
}
</script>