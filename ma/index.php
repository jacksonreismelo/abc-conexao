<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fotos PDF</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function updateProgress(evt) {
            if (evt.lengthComputable) {
                var percentComplete = (evt.loaded / evt.total) * 100;
                document.getElementById('progressBar').style.width = percentComplete + '%';
                document.getElementById('progressBar').innerText = Math.round(percentComplete) + '%';
            }
        }

        function uploadPhotos(event) {
            event.preventDefault();
            var form = document.getElementById('uploadForm');
            var formData = new FormData(form);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload.php', true);
            xhr.upload.addEventListener('progress', updateProgress, false);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert('PDF gerado com Sucesso!');
                        window.location.href = response.downloadUrl;
                    } else {
                        alert('An error occurred: ' + response.message);
                    }
                } else {
                    alert('Ocorreu um erro ao gerar PDF.');
                }
            };

            xhr.send(formData);
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Fotos em PDF</h1>
        <p>Agrupe fotos em PDF, até 9 por pagina.</p>
        <form id="uploadForm" onsubmit="uploadPhotos(event)" enctype="multipart/form-data">
           
            <label for="photos">Escolha as Fotos:</label>
            <input type="file" id="photos" name="photos[]" multiple required>
            
             <label for="pdfName">Nome do arquivo:</label>
            <input type="text" id="pdfName" name="pdfName" required>
            
            
            <input type="submit" value="Enviar e Gerar PDF">
        </form>
        <div class="progress">
            <div id="progressBar" class="progress-bar"></div>
        </div>
    </div>
</body>
</html>