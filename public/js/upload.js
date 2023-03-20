const form = document.querySelector('form');
form.addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      fetch('upload.php', {
            method: 'POST',
            body: formData
      })
      .then((response) => response.json())
      .then((response) => {
            if (response.estatus) {
                  alert(response.mensaje)
                  window.location.href = 'existencias.php'
            } else {
                  alert(response.mensaje)
            }
      })
      .catch(error => {
            console.error('Error al subir el archivo:', error);
      });
});