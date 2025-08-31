document.getElementById("fill_factura").addEventListener("mouseenter", function () {
    this.style.backgroundColor = "#37337eff";
    this.style.cursor = "pointer";
    this.innerText = "Click para rellenar...";
});
document.getElementById("fill_factura").addEventListener("mouseleave", function () {
    this.style.backgroundColor = "";
    this.innerText = "Rellenar facturaa";
});


/**
 * Fill factura input fields from returned JSON.
 * Supports keys like emisor, receptor, factura and flat keys.
 */
function normalizeResponse(data) {
    if (!data) return {};
    // JSON string?
    if (typeof data === 'string') {
        try { return JSON.parse(data); }
        catch (e) { return { raw: data }; }
    }
    // Array with object inside?
    if (Array.isArray(data)) {
        if (data.length === 0) return {};
        if (typeof data[0] === 'object') return data[0];
        return { array: data };
    }
    // Common wrapper keys
    if (typeof data === 'object') {
        return data.responseData || data.response || data.result || data.data || data.payload || data;
    }
    return {};
}

// Intercept the form submit so the browser doesn't navigate away and we can
// send the file via fetch as multipart/form-data.
const facturaForm = document.getElementById('facturaForm');
if (facturaForm) {
    facturaForm.addEventListener('submit', function (e) {
        e.preventDefault();
        // Show wait overlay using shared wait utils if available
        if (typeof startWait === 'function') startWait('Guardando factura... Rellenando campos...');
        else document.getElementById("waitOverlay").style.display = "flex";

        const formData = new FormData(facturaForm);

        fetch('/pages/facturas/factura_handler.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.redirect) {
                    console.log('Redirecting to:', data.redirect);
                    window.location.href = data.redirect;
                } else {
                    console.error('Error in response:', data.error);
                    window.location.href = data.redirect;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.href = '/pages/facturas/factura.php';
            })
            .finally(() => {
                // Hide wait overlay via shared utils if available
                console.log("Finally reached");
                if (typeof stopWait === 'function') stopWait();
                else document.getElementById("waitOverlay").style.display = "none";
            });
    });
}
document.getElementById("crea_demanda").addEventListener("mouseenter", function () {
    this.style.backgroundColor = "#38614cff";
    this.style.cursor = "pointer";
    this.innerText = "Click para crear demanda...";
});
document.getElementById("crea_demanda").addEventListener("mouseleave", function () {
    this.style.backgroundColor = "";
    this.innerText = "Crear demanda";
});

//uando creemos el filtro añadiremos esta parte del código
document.getElementById("crea_demanda").addEventListener("click", function () {
    const facturaId = this.getAttribute("user_uuid");
    fetch('/pages/facturas/crea_demanda.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ facturaId })
    })
        .then(response => response.json())
        .then(data => {
            alert("Paso 1");
            if (data.success) {
                alert('Demanda creada con éxito.');
                window.location.href = '/pages/facturas/factura.php';
            } else {
                alert('Error al crear la demanda: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al crear la demanda.');
        });
});
