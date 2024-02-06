import axios from 'axios';
import Swal from 'sweetalert2';

export default function useFileDownload() {
  async function downloadFile(requestData, url) {
    try {
        console.log(requestData)
      const response = await axios({url,method:'post',data:{...{requestData}},responseType: 'blob'});

      const contentDisposition = response.headers['content-disposition'];
      const filename = extractFilenameFromContentDisposition(contentDisposition);
      Swal.fire('Success!','Download Successful','success')

      const downloadUrl = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.setAttribute('download', filename);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    } catch (error) {
        Swal.fire('Error!',`An error occurred during the download ${error}`,'error')
      console.error(error);
    }
  }

  function extractFilenameFromContentDisposition(contentDisposition) {
    const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
    const matches = filenameRegex.exec(contentDisposition);
    if (matches != null && matches[1]) {
      return matches[1].replace(/['"]/g, '');
    }
    return 'download';
  }

  return {
    downloadFile,
  };
}
