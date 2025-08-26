document.getElementById("fill_demand").addEventListener("mouseenter", function () {
    this.style.backgroundColor = "#f0f0f0";
    this.style.cursor = "pointer";
    this.innerText = "Click para rellenar...";
});
document.getElementById("fill_demand").addEventListener("mouseleave", function () {
    this.style.backgroundColor = "";
    this.innerText = "Rellenar demanda";
});

/**
 * Fill demand input fields from returned JSON.
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

// Uso dentro de fillDemandForm:
// function fillDemandForm(data) {
//     console.log('raw response:', data);
//     const obj = normalizeResponse(data);
//     console.log('normalized obj:', obj);
//     const caption = obj.caption || {}; // aquí están los datos reales

//     const emisor = caption.Emisor || {};
//     const receptor = caption.Receptor || {};
//     const factura = caption.Factura || {};
//     const concepto = caption.concepto || "";

//     function setIfExists(id, value) {
//         console.log(`Setting ${id} to ${value}`);
//         if (typeof value === 'undefined' || value === null) return;
//         const el = document.getElementById(id);
//         if (el) el.value = value;
//     }
//     // Emisor
//     console.log('fillDemandForm called with emisor:', emisor);
//     setIfExists('emisor_nombre', emisor.nombre || emisor.name || obj.emisor_nombre || obj.issuer_name);
//     setIfExists('emisor_cif', emisor.CIF || emisor.cif || emisor.nif || obj.emisor_cif);
//     setIfExists('emisor_domicilio', emisor.domicilio || emisor.address || obj.emisor_domicilio);
//     setIfExists('emisor_telefono', emisor.telefono || emisor.phone || obj.emisor_telefono);
//     setIfExists('emisor_fax', emisor.FAX || emisor.fax || obj.emisor_fax);
//     setIfExists('emisor_email', emisor.email || emisor.emailAddress || obj.emisor_email);

//     // Receptor
//     console.log('fillDemandForm called with receptor:', receptor);
//     setIfExists('receptor_nombre', receptor.nombre || receptor.name || obj.receptor_nombre || obj.receiver_name);
//     setIfExists('receptor_cif', receptor.CIF || receptor.cif || obj.receptor_cif);
//     setIfExists('receptor_domicilio', receptor.domicilio || receptor.address || obj.receptor_domicilio);
//     setIfExists('receptor_telefono', receptor.telefono || receptor.phone || obj.receptor_telefono);
//     setIfExists('receptor_fax', receptor.FAX || receptor.fax || obj.receptor_fax);
//     setIfExists('receptor_email', receptor.email || receptor.emailAddress || obj.receptor_email);

//     // Factura
//     console.log('fillDemandForm called with factura:', factura);
//     setIfExists('factura_numero', factura.numero || factura.number || obj.factura_numero || obj.invoice_number);
//     setIfExists('factura_fecha', factura.fecha || factura.date || obj.factura_fecha || obj.invoice_date);
//     setIfExists('factura_vencimiento', factura.vencimiento || factura.due_date || obj.factura_vencimiento);
//     setIfExists('factura_importe_total', factura.importe_total || factura.total || obj.factura_importe_total);
//     setIfExists('factura_importe_iva', factura.importe_IVA || factura.vat || obj.factura_importe_iva);
//     setIfExists('factura_importe_sin_iva', factura.importe_sin_IVA || factura.total_excl_vat || obj.factura_importe_sin_iva);

//     console.log('fillDemandForm called with concepto:', concepto);
//     setIfExists('concepto', concepto);

// }


// Intercept the form submit so the browser doesn't navigate away and we can
// send the file via fetch as multipart/form-data.
const demandForm = document.getElementById('demandForm');
if (demandForm) {
    demandForm.addEventListener('submit', function (e) {
        e.preventDefault();
        // Show wait overlay using shared wait utils if available
        if (typeof startWait === 'function') startWait('Guardando factura... Rellenando campos...');
        else document.getElementById("waitOverlay").style.display = "flex";

        const formData = new FormData(demandForm);

        fetch('/pages/demand/demand_handler.php', {
            method: 'POST',
            body: formData
        })
            // .then(response => response.json())
            // .then(data => {
            //     // Debug info
            //     console.log('Response JSON:', data);
            //     console.log('data.success:', data && data.success, 'typeof:', typeof (data && data.success));
            //     try {
            //         console.log('Response keys:', data ? Object.keys(data) : 'no data');
            //     } catch (e) { console.warn('Could not list keys', e); }

            //     // For debugging: always attempt to call fillDemandForm and report errors
            //     try {
            //         if (typeof fillDemandForm === 'function') {
            //             fillDemandForm(data);
            //             console.log('fillDemandForm executed');
            //         } else {
            //             console.warn('fillDemandForm is not defined');
            //         }
            //     } catch (e) {
            //         console.error('Error inside fillDemandForm:', e);
            //     }

            //     if (data && data.success) {
            //         alert('Demanda rellenada con éxito');
            //     } else {
            //         alert('Error al rellenar la demanda');
            //     }
            // })
            // .catch(error => {
            //     console.error('Error:', error);
            // })
            .then(() => {
                // Hide wait overlay via shared utils if available
                if (typeof stopWait === 'function') stopWait();
                else document.getElementById("waitOverlay").style.display = "none";
            });
    });
}