/*=============================================
DESCARGAR TABLA NOTAS DE CREDITO Y DEBITO
=============================================*/
function notasExcel() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#notasExcel').DataTable();

    // Obtener todos los datos de la tabla, no solo los visibles
    const data = dataTable.rows().data().toArray();

    // Crear un nuevo libro de trabajo con ExcelJS
    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Facturas");

    // Obtener los encabezados de la tabla
    const headers = [];
    $('#notasExcel thead th').each(function() {
        headers.push($(this).text().trim());
    });
    headers.pop(); // Quitar la última columna
    // Agregar los encabezados como primera fila en el Excel
    ws.addRow(headers);

    // Agregar las filas de datos
    // Agregar las filas de datos, quitando la última columna
    data.forEach(row => {
        const rowData = Array.from(row);
        rowData.pop(); // Quitar la última columna
        ws.addRow(rowData);
    });


    // Exportar el archivo Excel
    wb.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = "notas_credito_debito.xlsx";
        link.click();
    });

    // Cerrar el mensaje de carga
    swal.close();
}

/*=============================================
DESCARGAR TABLA ANULADAS
=============================================*/
function anuladasExcel() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anuladasExcel').DataTable();

    // Obtener todos los datos de la tabla, no solo los visibles
    const data = dataTable.rows().data().toArray();

    // Crear un nuevo libro de trabajo con ExcelJS
    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Facturas");

    // Obtener los encabezados de la tabla
    const headers = [];
    $('#anuladasExcel thead th').each(function() {
        headers.push($(this).text().trim());
    });
    headers.pop(); // Quitar la última columna
    // Agregar los encabezados como primera fila en el Excel
    ws.addRow(headers);

    // Agregar las filas de datos
    // Agregar las filas de datos, quitando la última columna
    data.forEach(row => {
        const rowData = Array.from(row);
        rowData.pop(); // Quitar la última columna
        ws.addRow(rowData);
    });


    // Exportar el archivo Excel
    wb.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = "facturas_invalidadas.xlsx";
        link.click();
    });

    // Cerrar el mensaje de carga
    swal.close();
}

/*=============================================
DESCARGAR TABLA ELIMINADAS SIN TRANSMITIR
=============================================*/
function eliminadasExcel() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#eliminadasExcel').DataTable();

    // Obtener todos los datos de la tabla, no solo los visibles
    const data = dataTable.rows().data().toArray();

    // Crear un nuevo libro de trabajo con ExcelJS
    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Facturas");

    // Obtener los encabezados de la tabla
    const headers = [];
    $('#eliminadasExcel thead th').each(function() {
        headers.push($(this).text().trim());
    });

    // Agregar los encabezados como primera fila en el Excel
    ws.addRow(headers);

    // Agregar las filas de datos
    ws.addRows(data);

    // Exportar el archivo Excel
    wb.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = "eilimnadas_sin_transmitir.xlsx";
        link.click();
    });

    // Cerrar el mensaje de carga
    swal.close();
}

/*=============================================
DESCARGAR TABLA VENTAS AROMAZONE EN PDF
=============================================*/
function ventasAromazonePdf() {
	// Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando PDF",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentasAromazone').DataTable();

    // Guardar la configuración original
    const originalLength = dataTable.page.len();

    // Mostrar todos los registros en una sola página
    dataTable.page.len(-1).draw();

    // Crear el PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'landscape',
    });

    // Obtener los encabezados de las columnas, excepto la última
    const columns = [];
$('#anexoVentasAromazone thead th').each(function(index) {
    if (index < 12) { // Incluye todas las columnas
        columns.push($(this).text());
    }
});


    // Obtener los datos de la tabla, excluyendo la última columna
    const rows = [];
    dataTable.rows().every(function() {
        const rowData = this.data();
        rows.push(rowData.slice(0, 12)); // Excluye la última columna
    });

    // Generar la tabla en el PDF
    doc.autoTable({
        head: [columns],
        body: rows,
        theme: 'grid',
        styles: { fontSize: 8 },
        columnStyles: {
            0: { cellWidth: 35 },  // Cliente
            1: { cellWidth: 25 },  // Número de control
            2: { cellWidth: 25 },  // Código de generación
            3: { cellWidth: 15 },  // Tipo de factura
            4: { cellWidth: 20 },  // Monto total
            5: { cellWidth: 20 },  // Monto abonado
            6: { cellWidth: 15 },  // Estado
            7: { cellWidth: 20 },  // Fecha
            8: { cellWidth: 15 },  // Días de atraso
            9: { cellWidth: 25 },  // Vendedor
            10: { cellWidth: 25 },  // Facturador
            11: { cellWidth: 25 },  // Facturador
        },
        margin: { top: 10, left: 2, right: 10 },
        pageBreak: 'auto',
        tableWidth: 'auto',
    });

    // Restaurar la configuración original de paginación
    dataTable.page.len(originalLength).draw();

    // Guardar el PDF
    doc.save('ventasAromazonePdf.pdf');
	swal.close()
}

/*=============================================
DESCARGAR TABLA GENERAL DE CXC EN EXCEL
=============================================*/
function ventasAromazoneExcel() {
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
    
    // Obtener la instancia del DataTable y todos los datos
    var dt = $('#anexoVentasAromazone').DataTable();
    var data = dt.rows().data().toArray();
    
    // Define tus encabezados deseados
    var headers = ["Cliente", "PO", "Vendedor", "Línea", "Facturación", "Fecha", "Cantidad", "UM", "Producto", "Precio individual sin IVA", "Reten. 1%", "Total sin IVA", "Total"];
    
    // Inserta los encabezados como la primera fila
    data.unshift(headers);
    
    // Crea la hoja de trabajo a partir del arreglo de arreglos
    var ws = XLSX.utils.aoa_to_sheet(data);
    
    // Crea un libro y añade la hoja
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "anexoVentasAromazone");
    
    // Exporta el archivo Excel
    XLSX.writeFile(wb, "anexoVentasAromazone.xlsx");
    
    swal.close();
}




/*=============================================
DESCARGAR TABLA GENERAL DE CXC EN PDF
=============================================*/
function descargarPDF() {
	// Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando PDF",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
    // Obtener la instancia de DataTable
    const dataTable = $('#facturasSinPagar').DataTable();

    // Guardar la configuración original
    const originalLength = dataTable.page.len();

    // Mostrar todos los registros en una sola página
    dataTable.page.len(-1).draw();

    // Crear el PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'landscape',
    });

    // Obtener los encabezados de las columnas, excepto la última
    const columns = [];
    $('#facturasSinPagar thead th').each(function(index) {
        if (index < 11) { // Excluye la última columna
            columns.push($(this).text());
        }
    });

    // Obtener los datos de la tabla, excluyendo la última columna
    const rows = [];
    dataTable.rows().every(function() {
        const rowData = this.data();
        rows.push(rowData.slice(0, 11)); // Excluye la última columna
    });

    // Generar la tabla en el PDF
    doc.autoTable({
        head: [columns],
        body: rows,
        theme: 'grid',
        styles: { fontSize: 8 },
        columnStyles: {
            0: { cellWidth: 40 },  // Cliente
            1: { cellWidth: 30 },  // Número de control
            2: { cellWidth: 30 },  // Código de generación
            3: { cellWidth: 20 },  // Tipo de factura
            4: { cellWidth: 25 },  // Monto total
            5: { cellWidth: 25 },  // Monto abonado
            6: { cellWidth: 20 },  // Estado
            7: { cellWidth: 25 },  // Fecha
            8: { cellWidth: 20 },  // Días de atraso
            9: { cellWidth: 30 }, // Vendedor
            10: { cellWidth: 30 }, // Facturador
        },
        margin: { top: 10, left: 2, right: 10 },
        pageBreak: 'auto',
        tableWidth: 'auto',
    });

    // Restaurar la configuración original de paginación
    dataTable.page.len(originalLength).draw();

    // Guardar el PDF
    doc.save('facturasSinPagar.pdf');
	swal.close()
}

/*=============================================
DESCARGAR TABLA GENERAL DE CXC EN EXCEL
=============================================*/
function exportarExcel() {
	// Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
	// Obtener la tabla
	const tabla = document.getElementById("facturasSinPagar");
	// Convertir la tabla HTML en una hoja de trabajo
	const ws = XLSX.utils.table_to_sheet(tabla);
	// Crear un nuevo libro de trabajo
	const wb = XLSX.utils.book_new();
	XLSX.utils.book_append_sheet(wb, ws, "Facturas sin pagar");
	// Exportar el libro de trabajo a un archivo
	XLSX.writeFile(wb, "facturas_sin_pagar.xlsx");
	swal.close()
}

/*=============================================
DESCARGAR TABLA VENTAS CONSUMIDOR FINAL EN PDF
=============================================*/
function ventasConsumidorFinalPdf() {
	// Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando PDF",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentas').DataTable();

    // Guardar la configuración original
    const originalLength = dataTable.page.len();

    // Mostrar todos los registros en una sola página
    dataTable.page.len(-1).draw();

    // Crear el PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'landscape',
    });

    // Obtener los encabezados de las columnas
    // Obtener los encabezados de las columnas
const columns = [];
const totalColumns = $('#anexoVentas thead th').length;

$('#anexoVentas thead th').each(function(index) {
    let text = $(this).text();
    if (index === totalColumns - 2) {
        text = "Renta"; // Cambiar el penúltimo encabezado
    } else if (index === totalColumns - 1) {
        text = "Anexo"; // Cambiar el último encabezado
    }
    columns.push(text);
});



    // Obtener los datos de la tabla
    const rows = [];
    dataTable.rows().every(function() {
        rows.push(this.data());
    });

    doc.autoTable({
        head: [columns],
        body: rows,
        theme: 'grid',
        styles: { fontSize: 6 },
        columnStyles: {
            0: { cellWidth: 10 },
            1: { cellWidth: 10 },
            2: { cellWidth: 10 },
            3: { cellWidth: 20 },
            4: { cellWidth: 20 },
            5: { cellWidth: 10 },
            6: { cellWidth: 10 },
            7: { cellWidth: 20 },
            8: { cellWidth: 20 },
            9: { cellWidth: 10 },
            10: { cellWidth: 10 },
            11: { cellWidth: 15 },
            12: { cellWidth: 10 },
            13: { cellWidth: 15 },
            14: { cellWidth: 15 },
            15: { cellWidth: 15 },
            16: { cellWidth: 10 },
            17: { cellWidth: 15 },
            18: { cellWidth: 20 },
            19: { cellWidth: 10 },
            20: { cellWidth: 10 },
        },
        margin: { top: 10, left: 2, right: 10 },
        pageBreak: 'auto',
        tableWidth: 'auto',
    });

    // Restaurar la configuración original de paginación
    dataTable.page.len(originalLength).draw();

    // Guardar el PDF
    doc.save('ANEXO-VENTAS-FINAL.pdf');
	swal.close()
}

/*=============================================
DESCARGAR TABLA VENTAS CONSUMIDOR FINAL EN EXCEL
=============================================*/
function ventasConsumidorFinalExcel() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentas').DataTable();

    // Obtener todos los datos de la tabla, no solo los visibles
    const data = dataTable.rows().data().toArray();

    // Crear un nuevo libro de trabajo con ExcelJS
    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Facturas");

    // Obtener los encabezados de la tabla
    const headers = [];
    $('#anexoVentas thead th').each(function() {
        headers.push($(this).text().trim());
    });

    // Agregar los encabezados como primera fila en el Excel
    ws.addRow(headers);

    // Agregar las filas de datos
    ws.addRows(data);

    // Exportar el archivo Excel
    wb.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = "anexo_de_ventas_final.xlsx";
        link.click();
    });

    // Cerrar el mensaje de carga
    swal.close();
}

/*=============================================
DESCARGAR TABLA VENTAS CONSUMIDOR FINAL EN EXCEL CSV
=============================================*/
function ventasConsumidorFinalCsv() {
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    const dataTable = $('#anexoVentas').DataTable();
    const allData = dataTable.rows().data();

    let csv = '';
    for (let i = 0; i < allData.length; i++) {
        const row = allData[i];
        const rowData = [];

        for (let j = 0; j < row.length; j++) {
            rowData.push(String(row[j]).replace(/;/g, ',')); // Cambia ; por , si está en el contenido
        }

        csv += rowData.join(';') + '\n';
    }

    const link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    link.download = 'anexo_de_ventas_final.csv';
    link.click();

    swal.close();
}

/*=============================================
DESCARGAR TABLA VENTAS CONTRIBUYENTES EN PDF
=============================================*/
function ventasContribuyentePdf() {
	// Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando PDF",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentasContribuyentes').DataTable();

    // Guardar la configuración original
    const originalLength = dataTable.page.len();

    // Mostrar todos los registros en una sola página
    dataTable.page.len(-1).draw();

    // Crear el PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'landscape',
    });

    // Obtener los encabezados de las columnas
    // Obtener los encabezados de las columnas
const columns = [];
const totalColumns = $('#anexoVentasContribuyentes thead th').length;

$('#anexoVentasContribuyentes thead th').each(function(index) {
    let text = $(this).text();
    if (index === totalColumns - 2) {
        text = "Renta"; // Cambiar el penúltimo encabezado
    } else if (index === totalColumns - 1) {
        text = "Anexo"; // Cambiar el último encabezado
    }
    columns.push(text);
});



    // Obtener los datos de la tabla
    const rows = [];
    dataTable.rows().every(function() {
        rows.push(this.data());
    });

    doc.autoTable({
        head: [columns],
        body: rows,
        theme: 'grid',
        styles: { fontSize: 6 },
        columnStyles: {
            0: { cellWidth: 10 },
            1: { cellWidth: 10 },
            2: { cellWidth: 10 },
            3: { cellWidth: 20 },
            4: { cellWidth: 20 },
            5: { cellWidth: 10 },
            6: { cellWidth: 10 },
            7: { cellWidth: 20 },
            8: { cellWidth: 20 },
            9: { cellWidth: 10 },
            10: { cellWidth: 10 },
            11: { cellWidth: 15 },
            12: { cellWidth: 10 },
            13: { cellWidth: 15 },
            14: { cellWidth: 15 },
            15: { cellWidth: 15 },
            16: { cellWidth: 10 },
            17: { cellWidth: 15 },
            18: { cellWidth: 20 },
            19: { cellWidth: 10 },
        },
        margin: { top: 10, left: 2, right: 10 },
        pageBreak: 'auto',
        tableWidth: 'auto',
    });

    // Restaurar la configuración original de paginación
    dataTable.page.len(originalLength).draw();

    // Guardar el PDF
    doc.save('ANEXO-VENTAS-CONTRIBUYENTES.pdf');
	swal.close()
}

/*=============================================
DESCARGAR TABLA VENTAS CONTRIBUYENTES EN EXCEL
=============================================*/
function ventasContribuyenteExcel() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentasContribuyentes').DataTable();

    // Obtener todos los datos de la tabla, no solo los visibles
    const data = dataTable.rows().data().toArray();

    // Crear un nuevo libro de trabajo con ExcelJS
    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Facturas");

    // Obtener los encabezados de la tabla
    const headers = [];
    $('#anexoVentasContribuyentes thead th').each(function() {
        headers.push($(this).text().trim());
    });

    // Agregar los encabezados como primera fila en el Excel
    ws.addRow(headers);

    // Agregar las filas de datos
    ws.addRows(data);

    // Exportar el archivo Excel
    wb.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = "anexo_de_ventas_contribuyentes.xlsx";
        link.click();
    });

    // Cerrar el mensaje de carga
    swal.close();
}


/*=============================================
DESCARGAR TABLA VENTAS CONTRIBUYENTES EN EXCEL CSV
=============================================*/
function ventasContribuyenteCsv() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anexoVentasContribuyentes').DataTable();

    // Obtener todos los datos cargados en DataTable
    const data = dataTable.rows().data().toArray();

    // Convertir los datos a formato CSV (delimitado por ";")
    let csv = "";
    data.forEach(row => {
        csv += row.join(";") + "\n"; // Filas de datos sin encabezados
    });

    // Crear un enlace para descargar el archivo CSV
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = "anexo_de_ventas_contribuyentes.csv";

    // Descargar el archivo
    link.click();

    // Cerrar el mensaje de carga
    swal.close();
}

/*=============================================
DESCARGAR TABLA COMPRAS EN EXCEL
=============================================*/
function comprasExcel() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anexoCompras').DataTable();

    // Obtener todos los datos de la tabla, no solo los visibles
    const data = dataTable.rows().data().toArray();

    // Crear un nuevo libro de trabajo con ExcelJS
    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Facturas");

    // Obtener los encabezados de la tabla
    const headers = [];
    $('#anexoCompras thead th').each(function() {
        headers.push($(this).text().trim());
    });

    // Agregar los encabezados como primera fila en el Excel
    ws.addRow(headers);

    // Agregar las filas de datos
    ws.addRows(data);

    // Exportar el archivo Excel
    wb.xlsx.writeBuffer().then(function(buffer) {
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = "anexo_de_compras.xlsx";
        link.click();
    });

    // Cerrar el mensaje de carga
    swal.close();
}


/*=============================================
DESCARGAR TABLA VENTAS CONTRIBUYENTES EN EXCEL CSV
=============================================*/
function comprasCsv() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#anexoCompras').DataTable();

    // Obtener todos los datos cargados en DataTable
    const data = dataTable.rows().data().toArray();

    // Convertir los datos a formato CSV (delimitado por ";") excluyendo la última columna
    let csv = "";
    data.forEach(row => {
        csv += row.slice(0, -1).join(";") + "\n"; // Eliminar la última columna antes de unir
    });

    // Crear un enlace para descargar el archivo CSV
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = "anexo_de_compras.csv";

    // Descargar el archivo
    link.click();

    // Cerrar el mensaje de carga
    swal.close();
}

/*=============================================
DESCARGAR TABLA INVENTARIO EN PDF
=============================================*/

function descargarPdf2() {
    // Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando PDF",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        buttons: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Obtener la instancia de DataTable
    const dataTable = $('#inventario').DataTable();

    // Guardar la configuración original
    const originalLength = dataTable.page.len();

    // Mostrar todos los registros en una sola página
    dataTable.page.len(-1).draw();

    // Crear el PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'landscape',
    });

    // Obtener los encabezados de las columnas excluyendo la última
    const columns = [];
    $('#inventario thead th').each(function(index) {
        if (index < $('#inventario thead th').length - 1) { // Omitir la última columna
            columns.push($(this).text());
        }
    });

    // Obtener los datos de la tabla excluyendo la última columna y convertir imágenes a base64
    const rows = [];
    const imagePromises = [];
    dataTable.rows().every(function() {
        const row = this.data();
        const imgElement = $(row.pop()); // Obtener la última columna (imagen)
        
        const rowData = row.slice(0, row.length);
        const imageUrl = imgElement.attr('src');

        imagePromises.push(
            new Promise((resolve) => {
                getBase64Image(imageUrl, (base64) => {
                    rowData.push({ image: base64 });
                    rows.push(rowData);
                    resolve();
                });
            })
        );
    });

    // Esperar a que todas las imágenes se conviertan a base64
    Promise.all(imagePromises).then(() => {
        doc.autoTable({
            head: [columns],
            body: rows,
            theme: 'grid',
            styles: { fontSize: 6 },
            columnStyles: {
                0: { cellWidth: 'auto' },
                1: { cellWidth: 'auto' },
                2: { cellWidth: 'auto' },
                3: { cellWidth: 'auto' },
                4: { cellWidth: 'auto' },
                5: { cellWidth: 'auto' },
                6: { cellWidth: 'auto' },
                7: { cellWidth: 'auto' },
                8: { cellWidth: 'auto' },
                9: { cellWidth: 35 } // Ajustar el ancho de la columna de la imagen
            },
            margin: { top: 10, left: 2, right: 10 },
            pageBreak: 'auto',
            tableWidth: 'wrap',
            didParseCell: function (data) {
                if (data.column.index === 9 && data.cell.raw.image) { // La última columna (ajusta el índice si es necesario)
                    data.cell.text = ''; // Vaciar el texto de la celda
                    data.cell.styles.cellPadding = 10; // Ajustar el padding de la celda
                    data.cell.styles.minCellHeight = 28; // Ajustar la altura mínima de la celda
                }
            },
            didDrawCell: function (data) {
                if (data.column.index === 9 && data.cell.raw.image) { // La última columna (ajusta el índice si es necesario)
                    doc.addImage(data.cell.raw.image, 'JPEG', data.cell.x + 1, data.cell.y + 1, 24, 24); // Ajustar el tamaño y posición de la imagen
                }
            }
        });

        // Restaurar la configuración original de paginación
        dataTable.page.len(originalLength).draw();

        // Guardar el PDF
        doc.save('inventario.pdf');
        swal.close();
    });
}

// Simular doble clic llamando a la función dos veces
document.getElementById("imprimirInventario").addEventListener("click", function() {
    descargarPdf2();
    setTimeout(descargarPdf2, 100); // Llamar la función nuevamente después de un breve retraso
	swal.close()
});


// Función para convertir la imagen a base64
function getBase64Image(url, callback) {
	
    var img = new Image();
    img.crossOrigin = 'Anonymous';
    img.onload = function() {
        var canvas = document.createElement('CANVAS');
        var ctx = canvas.getContext('2d');
        var dataURL;
        canvas.height = this.naturalHeight;
        canvas.width = this.naturalWidth;
        ctx.drawImage(this, 0, 0);
        dataURL = canvas.toDataURL('image/jpeg');
        callback(dataURL);
    };
    img.src = url;
}


/*=============================================
DESCARGAR TABLA INVENTARIO EN EXCEL
=============================================*/
		
function exportarExcel2() {
	// Mostrar el mensaje de carga con SweetAlert
    swal({
        title: "Generando archivo",
        text: "Por favor espera mientras se genera el archivo.",
        icon: "info",
        showConfirmButton: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
	// Obtener la instancia de DataTable
	const dataTable = $('#inventario').DataTable();

	// Crear el libro de Excel
	const wb = XLSX.utils.book_new();
	const ws_data = [];

	// Obtener los encabezados de las columnas excluyendo la última (para no incluir imágenes)
	const headers = [];
	$('#inventario thead th').each(function(index) {
		if (index < $('#inventario thead th').length - 2) { // Omitir la última columna (imagen)
			headers.push($(this).text());
		}
	});
	ws_data.push(headers); // Agregar los encabezados a la tabla

	// Obtener los datos de la tabla excluyendo la última columna (imagen)
	dataTable.rows().every(function() {
		const row = this.data();
		const rowData = row.slice(0, row.length - 2); // Omitir la última columna (imagen)
		ws_data.push(rowData); // Agregar fila sin la imagen
	});

	// Crear la hoja de Excel con los datos
	const ws = XLSX.utils.aoa_to_sheet(ws_data);

	// Crear el libro y agregar la hoja
	XLSX.utils.book_append_sheet(wb, ws, "Inventario");

	// Descargar el archivo Excel
	XLSX.writeFile(wb, 'inventario_sin_imagenes.xlsx');
	swal.close()
}