export default function useFileDownload() {
    async function downloadFile(requestData, url) {
      try {
        const response = await fetch(url, {
          method: 'POST',
          ...requestData,
        });

        const contentDisposition = response.headers.get('content-disposition');
        const filename = extractFilenameFromContentDisposition(contentDisposition);

        const blob = await response.blob();
        const downloadUrl = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = downloadUrl;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (error) {
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
