document.getElementById('auto-fill').addEventListener('mouseenter', function () {
   this.style.cursor = 'pointer';
   this.style.backgroundColor = '#f0f0f0';
   this.innerText = 'Auto Filling...';
});
document.getElementById('auto-fill').addEventListener('mouseleave', function () {
   this.style.backgroundColor = '';
   this.innerText = 'Auto Fill';
});
document.getElementById('picture').addEventListener('click', function () {
   this.style.cursor = 'pointer';
   this.style.backgroundColor = '#f0f0f0';
   const file = this.currentSrc;
   if (file) {
      alert('First 10 chars: ' + file.substring(0, 10));
   }
});
// document.getElementById('auto-fill').addEventListener('click', function () {
//     startWait('Testing Auto Fill');
// });
document.getElementById('auto-fill').addEventListener('click', function () {

   console.log("Submitting form data...");
   if (typeof startWait === 'function') {
      startWait('Generando comentario...');
      console.log("startWait function called.");
   }
   else {
      document.getElementById("waitOverlay").style.display = "flex";
      console.log("waitOverlay displayed.");
   }

   console.log("Fetching...");
   var file = document.getElementById('picture').currentSrc;
   fetch('/pages/picture/auto_fill.php', {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json'
      },
      body: JSON.stringify({ picture: file })
   })

      .then(response => response.json())
      .then(data => {
         console.log("response received...", data);
         if (data.success) {
            document.getElementById('caption').value = data.caption;
            console.log('Caption set to:', data.caption);
         } else {
            console.error('Error fetching caption:', data.error);
         }
      })
      .catch(error => {
         if (typeof stopWait === 'function') stopWait();
         else document.getElementById("waitOverlay").style.display = "none";
         console.log('Error mi error es:', error);
         console.error('Error:', error);
      })
      .finally(() => {
         if (typeof stopWait === 'function') stopWait();
         else document.getElementById("waitOverlay").style.display = "none";
      });
});