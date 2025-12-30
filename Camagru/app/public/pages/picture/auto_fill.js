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

   console.log("Submitting form data...");
   var data_received = {
      csrf_token: document.querySelector('input[name="csrf_token"]').value,
      picture: document.getElementById('picture').currentSrc
   };
   if (typeof startWait === 'function') {
      startWait('Generando comentario...');
      console.log("startWait function called.");
   }
   else {
      document.getElementById("waitOverlay").style.display = "flex";
      console.log("waitOverlay displayed.");
   }

   console.log("Fetching...");
   console.log("Data received:", data_received);
   var file = document.getElementById('picture').currentSrc;
   fetch('/pages/picture/auto_fill.php', {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json'
      },
      body: JSON.stringify(data_received)
   })
   .then(response => {
         console.log("Raw response status:", response.status);
         console.log("Raw response headers:", response.headers);
         return response.json();
      })
   .then(data => {
         console.log("response received...", data);
         if (data.success) {
            document.getElementById('caption').value = data.caption;
            console.log('Caption set to:', data.caption);
         } else {
            console.error('Error fetching caption:', data.error || data.message || 'Unknown error');
            console.error('Full error response:', data);
         }
      })
      .catch(error => {
         if (typeof stopWait === 'function') stopWait();
         else document.getElementById("waitOverlay").style.display = "none";
         console.log('Network/Parse error:', error);
         console.error('Full error object:', error);
         alert('Error de red o parsing: ' + error.message);
      })
      .finally(() => {
         if (typeof stopWait === 'function') stopWait();
         else document.getElementById("waitOverlay").style.display = "none";
      });
});