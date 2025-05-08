<?php

require_once "../../../controladores/facturas.controlador.php";
require_once "../../../modelos/facturas.modelo.php";

require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";

require_once "../../../controladores/productos.controlador.php";
require_once "../../../modelos/productos.modelo.php";

require_once "../../../controladores/usuarios.controlador.php";
require_once "../../../modelos/usuarios.modelo.php";

require_once '../../phpqrcode/qrlib.php';


if(isset($_GET["idFactura"]) && isset($_GET["idFactura"])){

    $item = "id";
    $orden = "id";
    $valor = $_GET["idFactura"];
    $optimizacion = "no";

    // Obtiene los datos de la factura
    $factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $item = "id";
    $orden = "id";
    $valor = $factura["id_cliente"];

    // Obtiene los datos de la factura
    $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

    $item = "id";
    $orden = "id";
    $valor = "1";

    // Obtiene los datos de la factura
    $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);


  }

  function numeroALetras($numero) {
    $unidad = [
        "cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve",
        "diez", "once", "doce", "trece", "catorce", "quince", "dieciséis", "diecisiete", "dieciocho", "diecinueve"
    ];
    $decena = [
        "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"
    ];
    $centena = [
        "", "cien", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos"
    ];

    if ($numero == 0) {
        return "cero";
    }

    if ($numero < 20) {
        return $unidad[$numero];
    } elseif ($numero < 100) {
        return $decena[intval($numero / 10)] . ($numero % 10 == 0 ? "" : " y " . $unidad[$numero % 10]);
    } elseif ($numero < 1000) {
        return ($numero == 100 ? "cien" : $centena[intval($numero / 100)] . ($numero % 100 == 0 ? "" : " " . numeroALetras($numero % 100)));
    } elseif ($numero < 1000000) {
        return numeroALetras(intval($numero / 1000)) . " mil" . ($numero % 1000 == 0 ? "" : " " . numeroALetras($numero % 1000));
    } elseif ($numero < 1000000000) {
        return numeroALetras(intval($numero / 1000000)) . " millón" . ($numero % 1000000 == 0 ? "" : " " . numeroALetras($numero % 1000000));
    } else {
        return "Número demasiado grande";
    }
    }

    function numeroAmoneda($numero) {
        $partes = explode(".", number_format($numero, 2, ".", ""));
        $parteEntera = intval($partes[0]);
        $parteDecimal = intval($partes[1]);

        $texto = numeroALetras($parteEntera) . " dólares";
        if ($parteDecimal > 0) {
            $texto .= " con " . numeroALetras($parteDecimal) . " centavos";
        }

        return ucfirst($texto);
    }

  // URL que deseas codificar en el QR
  $url = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=" . $factura["codigoGeneracion"];
  $url .= "&fechaEmi=" . $factura["fecEmi"];  

// Nombre del archivo donde se guardará el QR
$archivoQR = 'codigo_qr.png';

// Genera el código QR y guárdalo como imagen
QRcode::png($url, $archivoQR, QR_ECLEVEL_L, 10);

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        
        $item = "id";
        $orden = "id";
        $valor = $_GET["idFactura"];
        $optimizacion = "no";

        // Obtiene los datos de la factura
        $factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $modoTexto = "";

        if($factura["modo"] != "Contingencia"){
            $modoTexto = "Transmisión normal";
        } else {
            $modoTexto = "Transmisión en contingencia";
        }

        switch ($factura["tipoDte"]) {
            case "01":
                $tipoFacturaTexto = "Factura";
                break;
            case "03":
                $tipoFacturaTexto = "Comprobante de crédito fiscal";
                break;
            case "04":
                $tipoFacturaTexto = "Nota de remisión";
                break;
            case "05":
                $tipoFacturaTexto = "Nota de crédito";
                break;
            case "06":
                $tipoFacturaTexto = "Nota de débito";
                break;
            case "07":
                $tipoFacturaTexto = "Comprobante de retención";
                break;
            case "08":
                $tipoFacturaTexto = "Comprobante de liquidación";
                break;
            case "09":
                $tipoFacturaTexto = "Documento contable de liquidación";
                break;
            case "11":
                $tipoFacturaTexto = "Factura de exportación";
                break;
            case "14":
                $tipoFacturaTexto = "Factura de sujeto excluido";
                break;
            case "15":
                $tipoFacturaTexto = "Comprobante de donación";
                break;

            default:
                echo "Factura no válida";
                break;
        }

        

        $this->Ln(15); // Agrega un espacio vertical de 10 unidades (puedes ajustar el valor)
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(250, 0, "DOCUMENTO TRIBUTARIO ELECTRÓNICO", 0, true, 'C', 0, ' ', 1, false, 'M', 'M');

        $this->Ln(15); // Agrega un espacio vertical de 15 unidades
        $this->SetFont('helvetica', 'B', 14);

        // Ruta de la imagen del código QR
        $archivoQR = 'codigo_qr.png'; // Ajusta según la ruta de tu archivo QR

        // Inserta el código QR en el PDF
        $this->Image($archivoQR, 50, 10, 30, 30, 'PNG', '', 'C', false, 300, '', false, false, 0, false, false, false);


        $this->Ln(5); // Agrega un espacio vertical de 10 unidades (puedes ajustar el valor)
        $this->SetFont('helvetica', '', 14);
        $this->Cell(275, 0, $modoTexto, 0, true, 'C', 0, ' ', 1, false, 'B', 'M');
        $this->Cell(275, 20, $tipoFacturaTexto.' - '.$factura["estado"], 0, true, 'C', 0, ' ', 1, false, 'B', 'M');

        
        // Logo
        $image_file = K_PATH_IMAGES.'tcpdf_logo.jpg';
        $this->Image($image_file, 10, 20, 35, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);


// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Rentaly El Salvador');
$pdf->setTitle('Factura '.$factura["codigoGeneracion"].'');
$pdf->setSubject('Reservación Rentaly El Salvador');
$pdf->setKeywords('	TCPDF, PDF, example, test, guide');


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);



// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->setFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// Obtener la fecha de ahora
$dia = date('d');
$mes = date('m');
$ano = date('Y');

function fechaEnEspanol($fecha) {
    $meses = array(
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    );

    $timestamp = strtotime($fecha);
    $dia = date('d', $timestamp);
    $mes = $meses[date('n', $timestamp)];
    $anio = date('Y', $timestamp);

    return "$dia de $mes del $anio";
}
if($factura == null){
    $html = 'Factura eliminada';
    $pdf->writeHTML($html, true, false, true, false, '');
    
    //Close and output PDF document
    $pdf->Output($factura["codigoGeneracion"].'.pdf', 'I');

    //============================================================+
    // END OF FILE
    //============================================================+

} else {

    $departamentos = [
        "00" => "Extranjero",
        "01" => "Ahuachapan",
        "02" => "Santa Ana",
        "03" => "Sonsonate",
        "04" => "Chalatenango",
        "05" => "La Libertad",
        "06" => "San Salvador",
        "07" => "Cuscatlán",
        "08" => "La Paz",
        "09" => "Cabañas",
        "10" => "San Vicente",
        "11" => "Usulután",
        "12" => "San Miguel",
        "13" => "Morazán",
        "14" => "La Unión"
    ];
    
    $municipios = [
        "01" => [ // Ahuachapan
            "13" => "Ahuachapan norte",
            "14" => "Ahuachapan centro",
            "15" => "Ahuachapan sur"
        ],
        "02" => [ // Santa Ana
            "14" => "Santa Ana norte",
            "15" => "Santa Ana centro",
            "16" => "Santa Ana este",
            "17" => "Santa Ana oeste"
        ],
        "03" => [ // Sonsonate
            "17" => "Sonsonate norte",
            "18" => "Sonsonate centro",
            "19" => "Sonsonate este",
            "20" => "Sonsonate oeste"
        ],
        "04" => [ // Chalatenango
            "34" => "Chalatenango norte",
            "35" => "Chalatenango centro",
            "36" => "Chalatenango sur"
        ],
        "05" => [ // La Libertad
            "23" => "La Libertad norte",
            "24" => "La Libertad centro",
            "25" => "La Libertad oeste",
            "26" => "La Libertad este",
            "27" => "La Libertad costa",
            "28" => "La Libertad sur"
        ],
        "06" => [ // San Salvador
            "20" => "San Salvador norte",
            "21" => "San Salvador oeste",
            "22" => "San Salvador este",
            "23" => "San Salvador centro",
            "24" => "San Salvador sur"
        ],
        "07" => [ // Cuscatlán
            "17" => "Cuscatlán norte",
            "18" => "Cuscatlán sur"
        ],
        "08" => [ // La Paz
            "23" => "La Paz oeste",
            "24" => "La Paz centro",
            "25" => "La Paz este"
        ],
        "09" => [ // Cabañas
            "10" => "Cabañas oeste",
            "11" => "Cabañas este"
        ],
        "10" => [ // San Vicente
            "14" => "San Vicente norte",
            "15" => "San Vicente sur"
        ],
        "11" => [ // Usulután
            "24" => "Usulután norte",
            "25" => "Usulután este",
            "26" => "Usulután oeste"
        ],
        "12" => [ // San Miguel
            "21" => "San Miguel norte",
            "22" => "San Miguel centro",
            "23" => "San Miguel oeste"
        ],
        "13" => [ // Morazán
            "27" => "Morazán norte",
            "28" => "Morazán sur"
        ],
        "14" => [ // La Unión
            "19" => "La Unión norte",
            "20" => "La Unión sur"
        ]
    ];

    $item = null;
    $valor = null;

    $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

    $nombreVendedor = "";
    $nombreFacturador = "";

    foreach ($usuarios as $key => $value){
        if($value["id"] == $factura["id_vendedor"]){
            $nombreVendedor = $value["nombre"];
        }

        if($value["id"] == $factura["id_usuario"]){
            $nombreFacturador = $value["nombre"];
        }
    }
// create some HTML content
$html = '<br><br><br><div style="font-family: Arial, sans-serif; font-size: 9px;">
<hr>
<table border="0" cellspacing="0" cellpadding="2">
    <tr>
        
        <td style="text-align:left" colspan="7">
            <br><br>
            <b>Código de generación:</b> '.$factura["codigoGeneracion"].'<br>
            <b>Número de control:</b> '.$factura["numeroControl"].'<br>
            <b>Sello de recepción:</b> '.$factura["sello"].'
        </td>
        <td style="text-align:left; border-left: 1px solid black; height: 60px;" colspan="7">
            <br><br>
            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sistema de facturación:</b> Fox Control<br>
            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tipo de transmisión:</b> Normal<br>
            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha y hora:</b> '.$factura["fecEmi"].' '.$factura["horEmi"].'
        </td>
    </tr>
</table>

<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #dddcdc" colspan="7">
            <b>EMISOR</b>
        </td>
        <td style="text-align:center; background-color: #dddcdc" colspan="7">
            <b>RECEPTOR</b>
        </td>
    </tr>
    <tr>
        <td style="text-align:left" colspan="7">
            <br><br>
            <b>Nombre o razón social:</b> '.$empresa["nombre"].'<br>
            <b>NIT:</b> '.$empresa["nit"].'<br>
            <b>NRC:</b> '.$empresa["nrc"].'<br>
            <b>Actividad Económica:</b> '.$empresa["desActividad"].'<br>
            <b>Dirección:</b> '.$departamentos[$empresa["departamento"]] . ', ' .$municipios[$empresa["departamento"]][$empresa["municipio"]] . ', ' .$empresa["direccion"].'<br>
            <b>Número de teléfono:</b> '.$empresa["telefono"].'<br>
            <b>Correo Electrónico:</b> '.$empresa["correo"].'<br>
            <b>Vendedor:</b> '.$nombreVendedor.'<br>
            <b>Facturador:</b> '.$nombreFacturador.'<br>
            <br>
        </td>
        <td style="text-align:left" colspan="7">
            <br><br><br>
            <b>Nombre o razón social:</b> '.$cliente["nombre"].'<br>';
            if($cliente["NIT"] != ""){
                $html .= '<b>&nbsp;&nbsp;NIT:</b> '.$cliente["NIT"].'<br>';
            }
            if($cliente["NRC"] != ""){
                $html .= '<b>&nbsp;&nbsp;NRC:</b> '.$cliente["NRC"].'<br>';
            }
            if($cliente["DUI"] != ""){
                $html .= '<b>&nbsp;&nbsp;DUI:</b> '.$cliente["DUI"].'<br>';
            }
            $html .= '
            <b>Dirección:</b> '.$cliente["direccion"].'<br>
            <b>Número de teléfono:</b> '.$cliente["telefono"].'<br>
            <b>Correo Electrónico:</b> '.$cliente["correo"].'<br>
            <b>Actividad Económica:</b> '.$cliente["descActividad"].'<br>
            <br>
        </td>
    </tr>
</table>
<hr>';


$condicionTexto = "";
if($factura["condicionOperacion"] == "1"){
    $condicionTexto = "Contado";
}
if($factura["condicionOperacion"] == "2"){
    $condicionTexto = "Crédito";
}
if($factura["condicionOperacion"] == "3"){
    $condicionTexto = "Otro";
}


if($factura["tipoDte"] === "01" && $cliente["tipo_cliente"] === "00"){// Factura, Persona normal y declarante de IVA
    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color:rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei) {
            $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioConIva"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]), 2, '.', ',').'</td>
                    </tr>
                    
            ';
            $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                     </tr>   ';

        }
    }

    $retencionGranContribuyente = 0.0;
    if($factura["gran_contribuyente"] == "Si"){
        $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $'.number_format(($factura["total"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sub-Total:</b> $'.number_format(($factura["total"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 13%:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 1%:</b> $'.$retencionGranContribuyente.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención Renta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monto total de la operación:</b> $'.number_format(((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))), 2, '.', ',').'<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))).':</b>
                <br>
            </td>
        </tr>';
        if($factura["orden_compra"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Orden de compra:</b>'.$factura["orden_compra"].'
                            </td>
                        </tr>';
        }
        if($factura["incoterm"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Incoterm:</b>'.$factura["incoterm"].'
                            </td>
                        </tr>';
        }
        if($factura["origen"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Origen:</b>'.$factura["origen"].'
                            </td>
                        </tr>';
        }
        $html .= '</table>';
}

if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "01"){// Factura, Persona normal y declarante de IVA
    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioConIva"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]), 2, '.', ',').'</td>                    
                    </tr>
                    
            ';
            $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                    </tr>   ';

        }
    }

    $retencionGranContribuyente = 0.0;
    if($factura["gran_contribuyente"] == "Si"){
        $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $'.number_format(($factura["total"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sub-Total:</b> $'.number_format(($factura["total"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 13%:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 1%:</b> $'.$retencionGranContribuyente.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención Renta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monto total de la operación:</b> $'.number_format(((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))), 2, '.', ',').'<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))).':</b>
                <br>
            </td>
        </tr>';
        if($factura["orden_compra"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Orden de compra:</b>'.$factura["orden_compra"].'
                            </td>
                        </tr>';
        }
        if($factura["incoterm"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Incoterm:</b>'.$factura["incoterm"].'
                            </td>
                        </tr>';
        }
        if($factura["origen"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Origen:</b>'.$factura["origen"].'
                            </td>
                        </tr>';
        }
        $html .= '</table>';
}

if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "02"){// Factura, Empresa con beneficios fiscales
    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta no sujeta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]), 2, '.', ',').'</td>                    
                </tr>
                
        ';
        $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                    </tr>   ';

        }
    }

    $retencionGranContribuyente = 0.0;
    if($factura["gran_contribuyente"] == "Si"){
        $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sub-Total:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 13%:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 1%:</b> $'.$retencionGranContribuyente.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención Renta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monto total de la operación:</b> $'.number_format(((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))), 2, '.', ',').'<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))).':</b>
                <br>
            </td>
        </tr>';
        if($factura["orden_compra"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Orden de compra:</b>'.$factura["orden_compra"].'
                            </td>
                        </tr>';
        }
        if($factura["incoterm"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Incoterm:</b>'.$factura["incoterm"].'
                            </td>
                        </tr>';
        }
        if($factura["origen"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Origen:</b>'.$factura["origen"].'
                            </td>
                        </tr>';
        }
        $html .= '</table>';
}

if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "03"){ // Factura, diplomáticos
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
        

        $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
        $totalProF = floatval(number_format($totalPro, 2, '.', ''));
        $cuerpoDocumento[] = [
            "numItem" => $numItem,
            "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
            "numeroDocumento" => null,
            "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
            "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
            "codTributo" => null,
            "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
            "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
            "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
            "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
            "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
            "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
            "ventaGravada" => 0.0, // Valor de venta gravada
            "tributos" => null,
            "psv" => 0,
            "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
            "ivaItem" => 0.0
        ];

        // Incrementar el número de ítem
        $numItem++;
        $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
    }

    $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));

    $ivaSacar = $factura["total"] - $factura["totalSinIva"];

    // Formatea el resultado a 8 decimales
    $ivaTotalF = floatval(number_format($ivaSacar, 2, '.', ''));

    
    function convertirMontoALetras($monto) {
        // Separar la parte entera y la parte decimal
        $partes = explode('.', number_format($monto, 2, '.', ''));
        $parteEntera = (int)$partes[0];
        $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
    
        // Convertir la parte entera a letras
        $parteEnteraLetras = convertirNumeroALetras($parteEntera);
    
        // Formato final "UNO 67/100"
        return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
    }
    
    function convertirNumeroALetras($numero) {
        $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
        $decenas = [
            "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
            "sesenta", "setenta", "ochenta", "noventa"
        ];
        $especiales = [
            10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
            14 => "catorce", 15 => "quince", 16 => "dieciséis", 
            17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
        ];
    
        if ($numero < 10) {
            return $unidades[$numero];
        } elseif ($numero < 20) {
            return $especiales[$numero];
        } elseif ($numero < 100) {
            $decena = (int)($numero / 10);
            $unidad = $numero % 10;
            return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
        } elseif ($numero < 1000) {
            $centena = (int)($numero / 100);
            $resto = $numero % 100;
            $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
            return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
        } elseif ($numero < 1000000) {
            $miles = (int)($numero / 1000);
            $resto = $numero % 1000;
            $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
            return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
        } else {
            return "Número demasiado grande";
        }
    }

    $retencionGranContribuyente = 0.0;
    if($factura["gran_contribuyente"] == "Si"){
        $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
    }

    $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"] - $retencionGranContribuyente));
    $ncrCliente = "";
    if($cliente["NRC"] == "") {
        $ncrCliente = null;
    } else {
        $ncrCliente = $cliente["NRC"];
    }
    // URL de la solicitud
    $url = "http://localhost:8113/firmardocumento/";

    // Configuración de los encabezados
    $headers = [
        'User-Agent: facturacion',
        'Content-Type: application/json'
    ];

    // Datos del JSON (estructura de ejemplo)
    $data = [
        "contentType" => "application/JSON",
        "nit" => $empresa["nit"],
        "activo" => true,
        "passwordPri" => $empresa["passwordPri"],
        "dteJson" => [
            "identificacion" => [
                "version" => 1,
                "ambiente" => "01",
                "tipoDte" => $factura["tipoDte"],
                "numeroControl" => $factura["numeroControl"],
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "tipoModelo" => 1,
                "tipoOperacion" => 1,
                "tipoContingencia" => null,
                "motivoContin" => null,
                "fecEmi" => $factura["fecEmi"],
                "horEmi" => $factura["horEmi"],
                "tipoMoneda" => "USD"
            ],
            "emisor" => [
                "nit" => $empresa["nit"],
                "nrc" => $empresa["nrc"],
                "nombre" => $empresa["nombre"],
                "codActividad" => $empresa["codActividad"],
                "descActividad" => $empresa["desActividad"],
                "nombreComercial" => null,
                "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                "direccion" => [
                    "departamento" => $empresa["departamento"],
                    "municipio" => $empresa["municipio"],
                    "complemento" => $empresa["direccion"]
                ],
                "telefono" => $empresa["telefono"],
                "codEstable" => null,
                "codEstableMH" => null,
                "codPuntoVentaMH" => null,
                "codPuntoVenta" => null,
                "correo" => $empresa["correo"]
            ],
            "receptor" => [
                "tipoDocumento" => "36",
                "numDocumento" => $cliente["NIT"],
                "nrc" => $ncrCliente,
                "nombre" => $cliente["nombre"],
                "codActividad" => null,
                "descActividad" => null,
                "direccion" => [
                    "departamento" => $cliente["departamento"],
                    "municipio" => $cliente["municipio"],
                    "complemento" => $cliente["direccion"]
                ],
                "telefono" => $cliente["telefono"],
                "correo" => $cliente["correo"]
            ],
            "otrosDocumentos" => null,
            "documentoRelacionado" => null,
            "ventaTercero" => null,
            "cuerpoDocumento" => $cuerpoDocumento,
            "resumen" => [
                "totalNoSuj" => 0.0,
                "totalExenta" => floatval($factura["totalSinIva"]),
                "totalGravada" => 0.0,

                "subTotalVentas" => floatval($factura["totalSinIva"]),
                "descuNoSuj" => 0.0,
                "descuExenta" => 0.0,
                "descuGravada" => 0.0,
                "porcentajeDescuento" => 0.0,
                "totalDescu" => $descuentoGobalF,
                "tributos" => null,
                "subTotal" => floatval($factura["totalSinIva"]),
                "ivaRete1" => $retencionGranContribuyente,
                "reteRenta" => 0.0,
                "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                "totalNoGravado" => 0.0,
                "totalPagar" => round(($factura["totalSinIva"] - $retencionGranContribuyente), 2),
                "totalLetras" => $totalLetras,
                "totalIva" => 0.0,
                "saldoFavor" => 0.0,
                "condicionOperacion" => floatval($factura["condicionOperacion"]),
                "pagos" => null,
                "numPagoElectronico" => null
            ],
            "extension" => [
                "nombEntrega" => null,
                "docuEntrega" => null,
                "nombRecibe" => null,
                "docuRecibe" => null,
                "observaciones" => null,
                "placaVehiculo" => null
            ],
            "apendice" => null
        ]
    ];


}

if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){// CCF, Declarantes de IVA
    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                    
                </tr>
                
            ';
            $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                    </tr>   ';

        }
        
    }

    $retencionGranContribuyente = 0.0;
    if($factura["gran_contribuyente"] == "Si"){
        $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sub-Total:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 13%:</b> $'.number_format((($factura["total"] - $factura["totalSinIva"])), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 1%:</b> $'.$retencionGranContribuyente.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención Renta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monto total de la operación:</b> $'.number_format(($factura["total"] - $retencionGranContribuyente), 2, '.', ',').'<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($factura["total"] - $retencionGranContribuyente), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($factura["total"] - $retencionGranContribuyente).':</b>
                <br>
            </td>
        </tr>';
        if($factura["orden_compra"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Orden de compra:</b>'.$factura["orden_compra"].'
                            </td>
                        </tr>';
        }
        if($factura["incoterm"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Incoterm:</b>'.$factura["incoterm"].'
                            </td>
                        </tr>';
        }
        if($factura["origen"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Origen:</b>'.$factura["origen"].'
                            </td>
                        </tr>';
        }
        $html .= '</table>';
}

if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "02"){// CCF, Empresa con beneficios fiscales
    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta no sujeta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                    
                </tr>
                
            ';
            $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                    </tr>   ';

        }
        
    }

    $retencionGranContribuyente = 0.0;
    if($factura["gran_contribuyente"] == "Si"){
        $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $'.$factura["totalSinIva"].'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sub-Total:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 13%:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 1%:</b> $'.$retencionGranContribuyente.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención Renta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monto total de la operación:</b> $'.number_format(($factura["totalSinIva"] - $retencionGranContribuyente), 2, '.', ',').'<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($factura["totalSinIva"] - $retencionGranContribuyente), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($factura["totalSinIva"] - $retencionGranContribuyente).':</b>
                <br>
            </td>
        </tr>';
        if($factura["orden_compra"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Orden de compra:</b>'.$factura["orden_compra"].'
                            </td>
                        </tr>';
        }
        if($factura["incoterm"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Incoterm:</b>'.$factura["incoterm"].'
                            </td>
                        </tr>';
        }
        if($factura["origen"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Origen:</b>'.$factura["origen"].'
                            </td>
                        </tr>';
        }
        $html .= '</table>';
}

if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "03"){// CCF, Diplomáticos
    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>sUnidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta exenta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
            ';
            $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                    </tr>   ';

        }
        
    }

    $retencionGranContribuyente = 0.0;
    if($factura["gran_contribuyente"] == "Si"){
        $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sub-Total:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 13%:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA 1%:</b> $'.$retencionGranContribuyente.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención Renta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monto total de la operación:</b> $'.number_format(($factura["totalSinIva"] - $retencionGranContribuyente), 2, '.', ',').'<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($factura["totalSinIva"] - $retencionGranContribuyente), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($factura["totalSinIva"] - $retencionGranContribuyente).':</b>
                <br>
            </td>
        </tr>';
        if($factura["orden_compra"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Orden de compra:</b>'.$factura["orden_compra"].'
                            </td>
                        </tr>';
        }
        if($factura["incoterm"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Incoterm:</b>'.$factura["incoterm"].'
                            </td>
                        </tr>';
        }
        if($factura["origen"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Origen:</b>'.$factura["origen"].'
                            </td>
                        </tr>';
        }
        $html .= '</table>';
}

if($factura["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){// Exportación, Declarantes de IVA
    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei) {
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
            ';
            $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                     </tr>   ';

        }
        
    }
    $totalOpera = $factura["flete"] + $factura["seguro"] + $factura["totalSinIva"];
    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Flete:</b> $'.number_format(($factura["flete"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Seguro:</b> $'.number_format(($factura["seguro"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monto total de la operación:</b> $'.number_format(($totalOpera), 2, '.', ',').'<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalOpera), 2, '.', ',').'<br>
                </p>
                
                <br>
            </td>
        </tr>';
        if($factura["orden_compra"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Orden de compra:</b>'.$factura["orden_compra"].'
                            </td>
                        </tr>';
        }
        if($factura["incoterm"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Incoterm:</b>'.$factura["incoterm"].'
                            </td>
                        </tr>';
        }
        if($factura["origen"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Origen:</b>'.$factura["origen"].'
                            </td>
                        </tr>';
        }
        $html .= '</table>';
}

if($factura["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){// Sujeto no excluido, persona normal
    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta efecto renta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                    </tr>   ';

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sumas:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Renta retenida:</b> $'.number_format((($factura["totalSinIva"] * 0.10)), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total:</b> $'.number_format(($factura["totalSinIva"]-($factura["totalSinIva"] * 0.10)), 2, '.', ',').'<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($factura["totalSinIva"]-($factura["totalSinIva"] * 0.10)), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($factura["totalSinIva"]-($factura["totalSinIva"] * 0.10)).':</b>
                <br>
            </td>
        </tr>';
        if($factura["orden_compra"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Orden de compra:</b>'.$factura["orden_compra"].'
                            </td>
                        </tr>';
        }
        if($factura["incoterm"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Incoterm:</b>'.$factura["incoterm"].'
                            </td>
                        </tr>';
        }
        if($factura["origen"] != ""){
            $html .= '<tr>
                            <td colspan="4">
                                <b>Origen:</b>'.$factura["origen"].'
                            </td>
                        </tr>';
        }
        $html .= '</table>';
}

if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "01"){// Nota de crédito, Declarantes de IVA
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));

            $totalProD = (($producto["descuento"] * $producto["cantidad"]));
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["descuento"] * $producto["cantidad"])), 2, '.', ',').'</td>                    
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';
    
            }
            
        }
        
    }

    $retencionGran = 0.00;
    if($facturaOriginal["gran_contribuyente"] != "No"){
        $retencionGran = number_format(($totalGravado * 0.01), 2);
    }
    

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención 1%:</b> $'.$retencionGran.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $'.number_format(($totalGravado * 0.13), 2, '.', ',').'<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado + ($totalGravado * 0.13) - $retencionGran), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado + ($totalGravado * 0.13) - $retencionGran).':</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "02"){// Nota de crédito, Beneficios fiscales
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta no sujeta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
    
            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));
    
            $totalProD = (($producto["descuento"] * $producto["cantidad"]));
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
             // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"] * $producto["cantidad"]), 2, '.', ',').'</td>                    
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';
    
            }
            
        }
        
    }

    $retencionGran = 0.00;
    if($facturaOriginal["gran_contribuyente"] != "No"){
        $retencionGran = number_format(($totalGravado * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención 1%:</b> $'.$retencionGran.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.00<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado - $retencionGran), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado - $retencionGran).':</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "03"){// Nota de crédito, Diplomático
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta exenta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));

            $totalProD = (($producto["descuento"] * $producto["cantidad"]));
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["descuento"] * $producto["cantidad"])), 2, '.', ',').'</td>
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';
    
            }
            
        }
        
    }

    $retencionGran = 0.00;
    if($facturaOriginal["gran_contribuyente"] != "No"){
        $retencionGran = number_format(($totalGravado * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención 1%:</b> $'.$retencionGran.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.00<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado - $retencionGran), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado - $retencionGran).':</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "01"){// Nota de dédito, Declarantes de IVA
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Aumento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
    
            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));
    
            $totalProD = ($producto["descuento"] * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
             // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["descuento"] * $producto["cantidad"])), 2, '.', ',').'</td>
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';
    
            }
            
        }
        
    }

    $retencionGran = 0.00;
    if($facturaOriginal["gran_contribuyente"] != "No"){
        $retencionGran = number_format(($totalGravado * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención 1%:</b> $'.$retencionGran.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $'.number_format(($totalGravado * 0.13), 2, '.', ',').'<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado + ($totalGravado * 0.13) - $retencionGran), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado + ($totalGravado * 0.13) - $retencionGran).':</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "02"){// Nota de dédito, beneficios fiscales
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Aumento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta no sujeta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));

            $totalProD = ($producto["descuento"] * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["descuento"] * $producto["cantidad"])), 2, '.', ',').'</td>
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';
    
            }
            
        }
        
    }

    $retencionGran = 0.00;
    if($facturaOriginal["gran_contribuyente"] != "No"){
        $retencionGran = number_format(($totalGravado * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> '.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención 1%:</b> $'.$retencionGran.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.0<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado - $retencionGran), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado - $retencionGran).':</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "03"){// Nota de dédito, Diplomáticos
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Aumento</b>
        </td>
        <td style="text-align:center; background-color: rgb(176, 255, 174)" colspan="3">
            <br><br><b>Venta exenta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
    
            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));
    
            $totalProD = ($producto["descuento"] * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
             // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["descuento"] * $producto["cantidad"])), 2, '.', ',').'</td>
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';
    
            }
            
        }
        
    }

    $retencionGran = 0.00;
    if($facturaOriginal["gran_contribuyente"] != "No"){
        $retencionGran = number_format(($totalGravado * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: rgb(176, 255, 174); border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                
                <br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> '.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retención 1%:</b> $'.$retencionGran.'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.0<br>
                <p style="background-color: rgb(176, 255, 174); line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado - $retencionGran), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado - $retencionGran).':</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["idFacturaRelacionada"] != ""){
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] == "03"){// Nota de remisión, ccf contribuyente
        

        $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:center; background-color: #abebff" colspan="2">
                <br><br><b>N°</b><br>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="4">
                <br><br><b>Código</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Cant.</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="4">
                <br><br><b>Unidad</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="7">
                <br><br><b>Descripción</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Precio unitario</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Porcentaje descuento</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Descuento</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Venta gravada</b>
            </td>
        </tr>';

        // Decodificar los productos de la factura
        $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
        $contador = 1;
        $totalGravado = 0.0;
        // Recorrer cada producto y mapear los datos
        foreach ($productos as $producto) {
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            
            $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';

            }
            
        }

        $html .= '</table>
        <br><br>
        <table border="0" cellspacing="0" cellpadding="2">
            <tr>

                <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                    <b>DETALLES</b>
                </td>
                <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                    <b>SUMA TOTAL DE OPERACIONES</b>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;" colspan="7">
                    <br><br>
                    
                    <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                </td>
                <td style="text-align:left" colspan="7">
                    <br><br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> '.number_format(($totalGravado), 2, '.', ',').'<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $'.number_format(($totalGravado*0.13), 2, '.', ',').'<br>
                    <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                        <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado+($totalGravado*0.13)), 2, '.', ',').'<br>
                    </p>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado + ($totalGravado * 0.13)).':</b>
                    <br>
                </td>
            </tr>
        </table>';
    }

    if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "02" && $facturaOriginal["tipoDte"] == "03"){// Nota de remisión, ccf beneficios
        $item = "id";
        $orden = "id";
        $valor = $factura["idFacturaRelacionada"];
        $optimizacion = "no";

        $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:center; background-color: #abebff" colspan="2">
                <br><br><b>N°</b><br>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="4">
                <br><br><b>Código</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Cant.</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="4">
                <br><br><b>Unidad</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="7">
                <br><br><b>Descripción</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Precio unitario</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Porcentaje descuento</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Descuento</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Venta no sujeta</b>
            </td>
        </tr>';

        // Decodificar los productos de la factura
        $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
        $contador = 1;
        $totalGravado = 0.0;
        // Recorrer cada producto y mapear los datos
        foreach ($productos as $producto) {
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            
            $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';

            }
            
        }

        $html .= '</table>
        <br><br>
        <table border="0" cellspacing="0" cellpadding="2">
            <tr>

                <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                    <b>DETALLES</b>
                </td>
                <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                    <b>SUMA TOTAL DE OPERACIONES</b>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;" colspan="7">
                    <br><br>
                    
                    <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                </td>
                <td style="text-align:left" colspan="7">
                    <br><br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.0<br>
                    <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                        <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                    </p>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado).':</b>
                    <br>
                </td>
            </tr>
        </table>';
    }

    if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "03" && $facturaOriginal["tipoDte"] == "03"){// Nota de remisión, ccf diplomas
        $item = "id";
        $orden = "id";
        $valor = $factura["idFacturaRelacionada"];
        $optimizacion = "no";

        $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:center; background-color: #abebff" colspan="2">
                <br><br><b>N°</b><br>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="4">
                <br><br><b>Código</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Cant.</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="4">
                <br><br><b>Unidad</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="7">
                <br><br><b>Descripción</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Precio unitario</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Porcentaje descuento</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Descuento</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Venta exenta</b>
            </td>
        </tr>';

        // Decodificar los productos de la factura
        $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
        $contador = 1;
        $totalGravado = 0.0;
        // Recorrer cada producto y mapear los datos
        foreach ($productos as $producto) {
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            
            $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';

            }
            
        }

        $html .= '</table>
        <br><br>
        <table border="0" cellspacing="0" cellpadding="2">
            <tr>

                <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                    <b>DETALLES</b>
                </td>
                <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                    <b>SUMA TOTAL DE OPERACIONES</b>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;" colspan="7">
                    <br><br>
                    
                    <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                </td>
                <td style="text-align:left" colspan="7">
                    <br><br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.0<br>
                    <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                        <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                    </p>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado).':</b>
                    <br>
                </td>
            </tr>
        </table>';
    }

    if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] == "11"){// Nota de remisión, export contribuyente
        $item = "id";
        $orden = "id";
        $valor = $factura["idFacturaRelacionada"];
        $optimizacion = "no";

        $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:center; background-color: #abebff" colspan="2">
                <br><br><b>N°</b><br>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="4">
                <br><br><b>Código</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Cant.</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="4">
                <br><br><b>Unidad</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="7">
                <br><br><b>Descripción</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Precio unitario</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Porcentaje descuento</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Descuento</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Venta gravada</b>
            </td>
        </tr>';

        // Decodificar los productos de la factura
        $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
        $contador = 1;
        $totalGravado = 0.0;
        // Recorrer cada producto y mapear los datos
        foreach ($productos as $producto) {
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            
            $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';

            }
            
        }

        $html .= '</table>
        <br><br>
        <table border="0" cellspacing="0" cellpadding="2">
            <tr>

                <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                    <b>DETALLES</b>
                </td>
                <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                    <b>SUMA TOTAL DE OPERACIONES</b>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;" colspan="7">
                    <br><br>
                    
                    <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                </td>
                <td style="text-align:left" colspan="7">
                    <br><br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                    <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                        <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                    </p>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado).':</b>
                    <br>
                </td>
            </tr>
        </table>';
    }

    if($factura["tipoDte"] == "04" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03") && $facturaOriginal["tipoDte"] == "11"){// Nota de remisión, export beneficios diplomas
        $item = "id";
        $orden = "id";
        $valor = $factura["idFacturaRelacionada"];
        $optimizacion = "no";

        $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:center; background-color: #abebff" colspan="2">
                <br><br><b>N°</b><br>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="4">
                <br><br><b>Código</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Cant.</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="4">
                <br><br><b>Unidad</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="7">
                <br><br><b>Descripción</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Precio unitario</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Porcentaje descuento</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Descuento</b>
            </td>
            <td style="text-align:center; background-color: #abebff" colspan="3">
                <br><br><b>Ventas</b>
            </td>
        </tr>';

        // Decodificar los productos de la factura
        $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
        $contador = 1;
        $totalGravado = 0.0;
        // Recorrer cada producto y mapear los datos
        foreach ($productos as $producto) {
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            
            $totalProD = ($producto["precioSinImpuestos"] * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] * $producto["cantidad"])), 2, '.', ',').'</td>
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
                $html .= '<tr style="background-color: '.$bgColor.'">
                                <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                        </tr>   ';

            }
            
        }

        $html .= '</table>
        <br><br>
        <table border="0" cellspacing="0" cellpadding="2">
            <tr>

                <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                    <b>DETALLES</b>
                </td>
                <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                    <b>SUMA TOTAL DE OPERACIONES</b>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;" colspan="7">
                    <br><br>
                    
                    <b>Condición de la operación:</b> '.$condicionTexto.'<br>
                </td>
                <td style="text-align:left" colspan="7">
                    <br><br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                    <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                        <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                    </p>
                    <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado).':</b>
                    <br>
                </td>
            </tr>
        </table>';
    }
}

if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "01"){// Nota de remisión, ccf contribuyente
        

    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

        
        $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
        $totalProF = floatval(number_format($totalProD, 2, '.', ''));
        // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $totalGravado += $totalProF;
        $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                    </tr>   ';

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> '.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado).':</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "02"){// Nota de remisión, ccf beneficios
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta no sujeta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

        
        $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
        $totalProF = floatval(number_format($totalProD, 2, '.', ''));
        // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $totalGravado += $totalProF;
        $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                    </tr>   ';

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.0<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado).':</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "03"){// Nota de remisión, ccf diplomas
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta exenta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

        
        $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
        $totalProF = floatval(number_format($totalProD, 2, '.', ''));
        // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $totalGravado += $totalProF;
        $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                    </tr>   ';

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.0<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.numeroAmoneda($totalGravado).':</b>
                <br>
            </td>
        </tr>
    </table>';
}
    




$pdf->writeHTML($html, true, false, true, false, '');


// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Factura '.$factura["codigoGeneracion"].'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
}
