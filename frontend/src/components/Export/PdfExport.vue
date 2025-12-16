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
        
        // Column Positions (X coordinates) - Adjusted for all required fields
        const xPos = {
          studentId: 40,
          name: 110,
          gender: 250,
          address: 310,
          classOf: 420,
          course: 470,
          achievement: 600,
          additional: 700
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
          page.drawText('Student No.', { x: xPos.studentId, y: headerY, size: 11, font: fontBold });
          page.drawText('Name', { x: xPos.name, y: headerY, size: 11, font: fontBold });
          page.drawText('Sex', { x: xPos.gender, y: headerY, size: 11, font: fontBold });
          page.drawText('Address', { x: xPos.address, y: headerY, size: 11, font: fontBold });
          page.drawText('Class of', { x: xPos.classOf, y: headerY, size: 11, font: fontBold });
          page.drawText('Course', { x: xPos.course, y: headerY, size: 11, font: fontBold });
          page.drawText('Achievement', { x: xPos.achievement, y: headerY, size: 11, font: fontBold });
          page.drawText('Additional', { x: xPos.additional, y: headerY, size: 11, font: fontBold });
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
          page.drawText(String(grad.studentId || ''), { x: xPos.studentId, y: currentY, size: fontSize, font: font });
          // Name (wrap if too long)
          let safeName = grad.name || '';
          if (safeName.length > 22) safeName = safeName.substring(0, 20) + '...';
          page.drawText(safeName, { x: xPos.name, y: currentY, size: fontSize, font: font });
          // Gender
          page.drawText(String(grad.gender || ''), { x: xPos.gender, y: currentY, size: fontSize, font: font });
          // Address (wrap if too long)
          let safeAddress = grad.address || '';
          if (safeAddress.length > 22) safeAddress = safeAddress.substring(0, 20) + '...';
          page.drawText(safeAddress, { x: xPos.address, y: currentY, size: fontSize, font: font });
          // Class of
          page.drawText(String(grad.yearGraduated || ''), { x: xPos.classOf, y: currentY, size: fontSize, font: font });
          // Course (wrap if too long)
          let safeCourse = grad.course || '';
          if (safeCourse.length > 22) safeCourse = safeCourse.substring(0, 20) + '...';
          page.drawText(safeCourse, { x: xPos.course, y: currentY, size: fontSize, font: font });
          // Achievement
          let safeAch = grad.achievement || '';
          if (safeAch.length > 18) safeAch = safeAch.substring(0, 16) + '...';
          page.drawText(safeAch, { x: xPos.achievement, y: currentY, size: fontSize, font: font });
          // Additional Achievements (array or string)
          let addl = grad.additionalAchievement;
          if (Array.isArray(addl)) addl = addl.join(', ');
          if (addl && addl.length > 18) addl = addl.substring(0, 16) + '...';
          page.drawText(String(addl || ''), { x: xPos.additional, y: currentY, size: fontSize, font: font });
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