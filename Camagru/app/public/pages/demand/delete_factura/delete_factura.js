Array.from(document.getElementsByClassName('delete_factura')).forEach(td => {
    td.addEventListener("mouseover", function () {
        this.style.cursor = "pointer";
        this.style.backgroundColor = "red";
    });
});
Array.from(document.getElementsByClassName('delete_factura')).forEach(td => {
    td.addEventListener("mouseout", function () {
        this.style.backgroundColor = "";
    });
});
Array.from(document.getElementsByClassName('delete_factura')).forEach(td => {
    td.addEventListener('click', function () {
        const facturaId = this.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this factura?')) {
            document.cookie = 'deleteConfirmed=true; path=/';
            window.location.href = '/pages/demand/delete_factura/delete_factura.php?id=' + facturaId;
        } else {
            document.cookie = 'deleteConfirmed=false; path=/';
        }
    });
});