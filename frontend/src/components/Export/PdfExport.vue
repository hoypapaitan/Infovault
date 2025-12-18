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

      // return
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

        // Set A4 size in points (portrait, 1pt = 1/72 inch)
        const A4_WIDTH = 841.89; // 210mm (portrait width)
        const A4_HEIGHT = 595.28; // 297mm (portrait height)

        // Column Positions (X coordinates) - Adjusted for all required fields
        const xPos = {
          studentId: 40,
          name: 110,
          gender: 250,
          address: 310,
          classOf: 420,
          course: 470,
          achievement: 700 // merged achievement & additional
        };

        // Helper: Add New Page (A4 size)
        const addNewPage = () => {
          // Always use portrait: width < height
          const page = pdfDoc.addPage([A4_WIDTH, A4_HEIGHT]);
          const width = A4_WIDTH;
          const height = A4_HEIGHT;
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
          page.drawText('Achievement(s)', { x: xPos.achievement, y: headerY, size: 11, font: fontBold });
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
          // Helper for word wrapping any text in a column
          function wrapText(text, maxWidth) {
            if (!text) return [''];
            if (font.widthOfTextAtSize(text, fontSize) <= maxWidth) return [text];
            let words = text.split(' ');
            let lines = [];
            let line = '';
            for (let i = 0; i < words.length; i++) {
              let testLine = line ? line + ' ' + words[i] : words[i];
              if (font.widthOfTextAtSize(testLine, fontSize) > maxWidth) {
                if (line) lines.push(line);
                line = words[i];
              } else {
                line = testLine;
              }
            }
            if (line) lines.push(line);
            return lines;
          }

          // Define max widths for each column
          const colWidths = {
            studentId: 60,
            name: 120,
            gender: 60,
            address: 100,
            classOf: 60,
            course: 180,
            achievement: 100
          };

          // Prepare all column values and their wrapped lines
          const rowData = {
            studentId: String(grad.studentId || ''),
            name: grad.name || '',
            gender: String(grad.gender || ''),
            address: grad.address || '',
            classOf: String(grad.yearGraduated || ''),
            course: grad.course || '',
            achievement: (() => {
              let achievements = [];
              if (grad.achievement) achievements.push(grad.achievement);
              if (Array.isArray(grad.additionalAchievement)) {
                achievements = achievements.concat(grad.additionalAchievement);
              } else if (grad.additionalAchievement) {
                achievements.push(grad.additionalAchievement);
              }
              return achievements.filter(Boolean).join(', ');
            })()
          };

          // Wrap all columns
          const wrappedCols = {};
          let maxLines = 1;
          for (const key in rowData) {
            wrappedCols[key] = wrapText(rowData[key], colWidths[key]);
            if (wrappedCols[key].length > maxLines) maxLines = wrappedCols[key].length;
          }

          // Draw each line for the row, stacking vertically
          for (let lineIdx = 0; lineIdx < maxLines; lineIdx++) {
            page.drawText(wrappedCols.studentId[lineIdx] || '', { x: xPos.studentId, y: currentY, size: fontSize, font: font });
            page.drawText(wrappedCols.name[lineIdx] || '', { x: xPos.name, y: currentY, size: fontSize, font: font });
            page.drawText(wrappedCols.gender[lineIdx] || '', { x: xPos.gender, y: currentY, size: fontSize, font: font });
            page.drawText(wrappedCols.address[lineIdx] || '', { x: xPos.address, y: currentY, size: fontSize, font: font });
            page.drawText(wrappedCols.classOf[lineIdx] || '', { x: xPos.classOf, y: currentY, size: fontSize, font: font });
            page.drawText(wrappedCols.course[lineIdx] || '', { x: xPos.course, y: currentY, size: fontSize, font: font });
            page.drawText(wrappedCols.achievement[lineIdx] || '', { x: xPos.achievement, y: currentY, size: fontSize, font: font });
            currentY -= fontSize + 2;
          }
          // Adjust for extra lines (so next row doesn't overlap)
          currentY += (fontSize + 2) * (1 - maxLines);
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