// JavaScript for modal functionality
const modal = document.getElementById('editFacturaModal');
const modalBody = document.getElementById('modalBody');
const closeButton = document.querySelector('.close-button');
Array.from(document.getElementsByClassName('edit_factura')).forEach(td => {
  td.addEventListener('mouseover', function () {
    this.style.cursor = "pointer";
    this.style.backgroundColor = "green";
  });
});

Array.from(document.getElementsByClassName('edit_factura')).forEach(td => {
  td.addEventListener('mouseout', function () {
    this.style.backgroundColor = "";
  });
});

Array.from(document.getElementsByClassName('edit_factura')).forEach(td => {
  td.addEventListener('click', async (event) => {
    const facturaId = event.currentTarget.getAttribute('data-id');
    const response = await fetch(`/pages/facturas/edit_factura/edit_factura.php?id=${facturaId}`);
    const content = await response.text();
    modalBody.innerHTML = content;
    modal.style.display = 'block';
  });
});

closeButton.addEventListener('click', () => {
  console.log("Close button clicked");
  modal.style.display = 'none';
  modalBody.innerHTML = '';
});

window.addEventListener('click', (event) => {
  if (event.target === modal) {
    console.log("Modal background clicked");
    modal.style.display = 'none';
    modalBody.innerHTML = '';
  }
});

// Attach event listener after dynamic content is loaded
modalBody.addEventListener('click', (event) => {
  const cancelButton = event.target.closest('#cancelEditButton');
  if (cancelButton) {
    console.log("Cancel button clicked");
    modal.style.display = 'none';
    modalBody.innerHTML = '';
  }
});

modalBody.addEventListener('click', async (event) => {
  const saveButton = event.target.closest('#saveFacturaButton');
  if (saveButton) {
    const form = document.getElementById('formularioFactura');
    const formData = new FormData(form);
    // const jsonData = {};
    // formData.forEach((value, key) => {
    //     jsonData[key] = value ?? "";
    // });
    // console.log("JSON Data to be sent:", jsonData);
    try {
      const response = await fetch('/pages/facturas/edit_factura/edit_factura_handler.php', {
        method: 'POST',
        body: formData
      });
      console.log("Fetch completed", response);
      const data = await response.text();
      console.log("Response from server:", data);
      const parsedData = JSON.parse(data);

      if (parsedData.success) {
        window.location.href = parsedData.redirect;
      } else {
        alert("Error: " + (data.error || "No se pudo guardar ❌"));
      }
    } catch (err) {
      console.error("Error en fetch:", err);
      alert("Error de red al guardar la factura ⚠️");
    }
  };
});