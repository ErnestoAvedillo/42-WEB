const modal = document.getElementById("editDemandaModal");
const modalBody = document.getElementById("modalBody");
const closeButton = document.querySelector(".close-button");

Array.from(document.getElementsByClassName("edit_demanda")).forEach(td => {
  td.addEventListener("mouseover", function () {
    this.style.cursor = "pointer";
    this.style.backgroundColor = "green";
  });
});

Array.from(document.getElementsByClassName("edit_demanda")).forEach(td => {
  td.addEventListener("mouseout", function () {
    this.style.cursor = "default";
    this.style.backgroundColor = "";
  });
});

Array.from(document.getElementsByClassName("edit_demanda")).forEach(td => {
  td.addEventListener("click", async (event) => {
    const demandaId = event.currentTarget.getAttribute("data-id");
    const response = await fetch(`/pages/demandas/edit_demanda/edit_demanda.php?id=${demandaId}`);
    const content = await response.text();
    modalBody.innerHTML = content;
    modal.style.display = "block";
  });
});

closeButton.addEventListener("click", () => {
  modal.style.display = "none";
  modalBody.innerHTML = "";
});

window.addEventListener("click", (event) => {
  if (event.target === modal) {
    modal.style.display = "none";
    modalBody.innerHTML = "";
  }
});

modalBody.addEventListener("click", (event) => {
  const cancelButton = event.target.closest("#cancelEditButton");
  if (cancelButton) {
    modal.style.display = "none";
    modalBody.innerHTML = "";
  }
});

modalBody.addEventListener("click", async (event) => {
  const saveButton = event.target.closest("#saveDemandaButton");
  if (saveButton) {
    const form = document.getElementById("formularioDemanda");
    const formData = new FormData(form);
    try {
      const response = await fetch(`/pages/demandas/edit_demanda/edit_demanda_handler.php`, {
        method: "POST",
        body: formData
      });
      console.log("Response received:", response);
      const result = await response.text();
      console.log("Result:", result);
      const parsedData = JSON.parse(result);
      console.log("Parsed Data:", parsedData);
      if (parsedData.success) {
        window.location.href = parsedData.redirect;
      } else {
        alert("Error: " + (parsedData.message || "No se pudo guardar ❌"));
      }
    } catch (error) {
      console.error("Error en fetch:", error);
      alert("Error de red al guardar la demanda ⚠️");
    }
  };
});

modalBody.addEventListener("click", async (event) => {
  const generateButton = event.target.closest("#genera_concepto");
  if (generateButton) {
    const form = document.getElementById("formularioDemanda");
    const formData = new FormData(form);
    console.log("Form Data:", formData);
    try {
      const response = await fetch(`/pages/demandas/edit_demanda/genera_concepto.php`, {
        method: "POST",
        body: formData
      });
      const result = await response.text();
      const parsedData = JSON.parse(result);
      console.log("Parsed Data:", parsedData);
      const $entradaConcepto = document.getElementById("concepto");
      if ($entradaConcepto) {
        if (parsedData.success) {
          $entradaConcepto.value = parsedData.caption;
          alert("Concepto generado con éxito!");
        } else {
          alert("Error al generar concepto: " + (parsedData.message || "No se pudo generar"));
        }
      }
    } catch (error) {
      console.error("Error en fetch:", error);
      alert("Error de red al generar concepto");
    }
  }
});