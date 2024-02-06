// useExcel.js
import * as XLSX from 'xlsx';

// export default function useExcel() {
//   const exportToExcel = (sheets, prefix, file) => {
//     const timestamp = new Date().toISOString().replace(/[-:]/g, '').split('.')[0]; // Generate a timestamp

//     const fileName = `${prefix}_${timestamp}.xlsx`;

//     // Create the workbook
//     const wb = XLSX.utils.book_new();

//     sheets.forEach((sheet, index) => {
//       const { data, columns } = sheet;

//       // Create the sheet
//       let ws = XLSX.utils.json_to_sheet(data, { header: columns });

//       // Define styles for the header
//       const headerStyle = {
//         font: { bold: true },
//         fill: { fgColor: { rgb: 'FFFF00' } }, // Background color (yellow in this example)
//       };

//       // Apply styles to the header
//       // Apply styles to the header
//       ws['!cols'] = columns.map(() => ({ wch: 15 })); // Set column width in characters (adjust as needed)

//     //   ws['!cols'] = columns.map(() => ({ width: 15 })); // Set column width (adjust as needed)

//       columns.forEach((col, i) => {
//         const headerCell = XLSX.utils.encode_cell({ c: i, r: 0 });
//         ws[headerCell].s = headerStyle;
//       });

//       // Append the sheet to the workbook
//       XLSX.utils.book_append_sheet(wb, ws, `Sheet ${index + 1}`);
//     });

//     // Save the file
//     XLSX.writeFile(wb, fileName);
//   };

//   return {
//     exportToExcel,
//   };
// }

// export default function useExcel() {

//   const exportToExcel = (data, columns, filename) => {
//     const worksheet = XLSX.utils.json_to_sheet(data, { header: columns });
//     const workbook = XLSX.utils.book_new();
//     XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet 1');
//     XLSX.writeFile(workbook, filename + '.xlsx');
//   };

//   return { exportToExcel };
// }

export default function useExcel(){
  const exportToExcel = (data, columns, filename) => {
    // If columns are null, use keys of the first object as headers
    if (!columns) {
      columns = Object.keys(data[0]);
    }

    const worksheet = XLSX.utils.json_to_sheet(data, { header: columns });
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet 1');
    XLSX.writeFile(workbook, filename + '.xlsx');
  };

  return { exportToExcel };
}
