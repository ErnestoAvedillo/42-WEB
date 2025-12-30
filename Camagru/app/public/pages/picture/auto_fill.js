document.getElementById('auto-fill').addEventListener('mouseenter', function () {
   this.style.cursor = 'pointer';
   this.style.backgroundColor = '#f0f0f0';
   this.innerText = 'Auto Filling...';
});

document.getElementById('auto-fill').addEventListener('mouseleave', function () {
   this.style.backgroundColor = '';
   this.innerText = 'Auto Fill';
});

document.getElementById('auto-fill').addEventListener('click', function () {

   var data_received = {
      csrf_token: document.querySelector('input[name="csrf_token"]').value,
      picture: document.getElementById('picture').currentSrc
   };
   if (typeof startWait === 'function') {
      startWait('Generando comentario...');
   }
   else {
      document.getElementById("waitOverlay").style.display = "flex";
   }

   var file = document.getElementById('picture').currentSrc;
   fetch('/pages/picture/auto_fill.php', {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json'
      },
      body: JSON.stringify(data_received)
   })
   .then(response => {
         return response.json();
      })
   .then(data => {
         if (data.success) {
            document.getElementById('caption').value = data.caption;
         } else {
            console.error('Error fetching caption:', data.error || data.message || 'Unknown error');
            console.error('Full error response:', data);
         }
      })
      .catch(error => {
         if (typeof stopWait === 'function') stopWait();
         else document.getElementById("waitOverlay").style.display = "none";
         console.error('Network/Parse error:', error);
         console.error('Full error object:', error);
         alert('Error de red o parsing: ' + error.message);
      })
      .finally(() => {
         if (typeof stopWait === 'function') stopWait();
         else document.getElementById("waitOverlay").style.display = "none";
      });
});