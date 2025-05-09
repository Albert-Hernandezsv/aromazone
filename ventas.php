<?php

  if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad"){
  } else {
      echo '<script>
      window.location = "inicio";
      </script>';
    return;
  }
// Configurar la zona horaria de El Salvador
date_default_timezone_set('America/El_Salvador');
?>

<div id="loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(148, 148, 148, 0.37); display: flex; justify-content: center; align-items: center; z-index: 9999;">
    <h2>Cargando...</h2>
</div>
<script>
    $(window).on('load', function() {
        $('#loader').fadeOut();
    });
</script>
<div class="main-content content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Sistema de ventas - F07
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">
    
      <div class="box-body">        
      <button class="btn btn-warning" data-toggle="modal" data-target="#modalCrearFolio">Generar folios</button>
        <?php
            // Verificar si los parámetros existen y asignarlos a variables
            $filtroFechaInicio = isset($_GET['filtroFechaInicio']) ? $_GET['filtroFechaInicio'] : '00-00-0000';
            $filtroFechaFin = isset($_GET['filtroFechaFin']) ? $_GET['filtroFechaFin'] : '00-00-0000';

            echo '
                <h4>Filtros aplicados actualmente</h4>
                <div class="row">
                  <div class="col-xl-2 col-xs-12">
                    Fecha inicio: '.$filtroFechaInicio.'
                  </div>
                  <div class="col-xl-2 col-xs-12">
                    Fecha fin: '.$filtroFechaFin.'
                  </div>
                </div>
            ';
        ?>
        <hr>
        <br>
        <h5>Filtrar facturas:</h5>
        <form role="form" method="get" action="index.php?ruta=ventas" enctype="multipart/form-data">
          <input type="hidden" name="ruta" value="ventas">

          <div class="row">

            <div class="col-xl-2 col-xs-12">

                <!-- ENTRADA PARA FILTRO DE FECHA DE INICIO -->
                <div class="form-group">
                <p>Filtrar por fecha inicio:</p>
                  <div class="input-group mb-3">
                        <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                          <input type="date" class="form-control" id="filtroFechaInicio" name="filtroFechaInicio">
                  </div>

                </div>

            </div>

            <div class="col-xl-2 col-xs-12">

                <!-- ENTRADA PARA FILTRO DE FECHA FINALIZACION -->
                <div class="form-group">
                <p>Filtrar por fecha fin:</p>
                  <div class="input-group mb-3">
                        <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="date" class="form-control" id="filtroFechaFin" name="filtroFechaFin" value="<?php echo $_GET["filtroFechaFin"]; ?>">
                  </div>

                </div>

            </div>



          </div>
          <button type="submit" class="btn btn-dark">Aplicar filtros</button>
        </form>

        
        <br>
        <style>
            .tablas {
                table-layout: fixed;
                width: 100%;
            }
    

    

            td {
                word-wrap: break-word; /* El texto en las celdas se puede dividir en líneas */
            }
        </style>

        <br>
        <?php
              $optimizacion;
              if(!isset($_GET["optimizar"])){
                $optimizacion = "si";
                echo "<div class='form-check form-switch'>
                  <input class='form-check-input' type='checkbox' role='switch' id='flexSwitchCheckChecked' onclick=\"location.href='index.php?ruta=ventas&filtroFechaInicio=".$filtroFechaInicio."&filtroFechaFin=".$filtroFechaFin."&optimizar=no'\" checked>
                  <label class='form-check-label' for='flexSwitchCheckChecked'>Optimizar tablas</label>
                </div>";
              } else {
                $optimizacion = "no";
                echo "<div class='form-check form-switch'>
                  <input class='form-check-input' type='checkbox' role='switch' id='flexSwitchCheckChecked' onclick=\"location.href='index.php?ruta=ventas&filtroFechaInicio=".$filtroFechaInicio."&filtroFechaFin=".$filtroFechaFin."'\">
                  <label class='form-check-label' for='flexSwitchCheckChecked'>Optimizar tablas</label>
                </div>";
              }
          ?>


        <br>
        <div class="navbar bg-dark" style="color: white">Ventas AROMAZONE</div>
        <br>
        <button class="btn btn-primary" onclick="ventasAromazonePdf()">Descargar reporte como pdf</button>
        <button class="btn btn-success" onclick="ventasAromazoneExcel()">Descargar reporte como Excel</button>
       <table class="table table-bordered table-striped dt-responsive tablaVentasAromazone" id="anexoVentasAromazone" width="100%" style="font-size: 60%">
         
        <thead>
         
         <tr>
           
           <th>Cliente</th>
           <th>PO</th>
           <th>Vendedor</th>
           <th>Línea</th>
           <th>Facturación</th>
           <th>Fecha</th>
           <th>Cantidad</th>
           <th>UM</th>
           <th>Producto</th>
           <th>Precio individual sin IVA</th>
           <th>Reten. 1%</th>
           <th>Total sin IVA</th>
           <th>Total</th>

         </tr> 

        </thead>
                            
        <tbody>

            <?php
              $pagoAcuentas = 0.0;
              $item = null;
              $valor = null;
              $orden = "fecEmi";

              if($optimizacion == "no"){
                $facturas = ControladorFacturas::ctrMostrarFacturasVentas($filtroFechaInicio, $filtroFechaFin);
              } else {
                $facturas = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
              }

              foreach ($facturas as $key => $value){
                if(($value["tipoDte"] == "01" || $value["tipoDte"] == "11" || $value["tipoDte"] == "03") && $value["sello"] != "" && $value["estado"] == "Activa") {

                    $item = "id";
                    $valor = $value["id_cliente"];
                    $orden = "id";

                    $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

                    $item = "id";
                    $valor = $value["id_vendedor"];

                    $usuario = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

                    if (
                      (
                          ($filtroFechaInicio == "todos" && $filtroFechaFin == "todos") || 
                          ($filtroFechaInicio != "todos" && $filtroFechaFin == "tod os" && $value["fecEmi"] >= $filtroFechaInicio) ||
                          ($filtroFechaInicio == "todos" && $filtroFechaFin != "todos" && $value["fecEmi"] <= $filtroFechaFin) ||
                          ($filtroFechaInicio != "todos" && $filtroFechaFin != "todos" && $value["fecEmi"] >= $filtroFechaInicio && $value["fecEmi"] <= $filtroFechaFin)
                      )
                  ){
                    
                    // Decodificamos los productos (arreglo JSON)
                    $productos = json_decode($value["productos"], true);
                    $unidadesMedida = array(
                      "59" => "Unidad",
                      "57" => "Ciento",
                      "58" => "Docena",
                      "1"  => "Metro",
                      "2"  => "Yarda",
                      "6"  => "Milímetro",
                      "9"  => "Kilómetro cuadrado",
                      "10" => "Hectárea",
                      "13" => "Metro cuadrado",
                      "15" => "Vara cuadrada",
                      "18" => "Metro cúbico",
                      "20" => "Barril",
                      "22" => "Galón",
                      "23" => "Litro",
                      "24" => "Botella",
                      "26" => "Mililitro",
                      "30" => "Tonelada",
                      "32" => "Quintal",
                      "33" => "Arroba",
                      "34" => "Kilogramo",
                      "36" => "Libra",
                      "37" => "Onza troy",
                      "38" => "Onza",
                      "39" => "Gramo",
                      "40" => "Miligramo",
                      "42" => "Megawatt",
                      "43" => "Kilowatt",
                      "44" => "Watt",
                      "45" => "Megavoltio-amperio",
                      "46" => "Kilovoltio-amperio",
                      "47" => "Voltio-amperio",
                      "49" => "Gigawatt-hora",
                      "50" => "Megawatt-hora",
                      "51" => "Kilowatt-hora",
                      "52" => "Watt-hora",
                      "53" => "Kilovoltio",
                      "54" => "Voltio",
                      "55" => "Millar",
                      "56" => "Medio millar",
                      "99" => "Otra"
                      );
                    // Si se decodificó bien y existen productos
                    if (!empty($productos) && is_array($productos)) {

                      // Recorremos cada producto
                      foreach ($productos as $productoFactura) {
                        $item = "id";
                        $valor = $productoFactura["idProducto"];
                
                        $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor);

                        $item1 = "id";
                        $valor1 = $productoLeido["categoria_id"];
                
                        $categoria = ControladorCategorias::ctrMostrarCategorias($item1, $valor1);

                        $retenGran = 0.0;
                        if($value["gran_contribuyente"] == "Si"){
                          $retenGran = ($productoFactura["precioSinImpuestos"]*$productoFactura["cantidad"]) * 0.01;
                        }

                        // Supongamos que tu código numérico de unidad viene en $productoFactura["unidadMedida"]
                        $codigoUM = $unidadesMedida[$productoLeido["unidadMedida"]]; // Ej. 56
                        echo ' <tr>
                                    <td>'.$cliente["nombre"].'</td>
                                    <td>'.$value["orden_compra"].'</td>
                                    <td>'.$usuario["nombre"].'</td>
                                    <td>'.$categoria["nombre"].'</td>
                                    <td>'.$value["numeroControl"].'</td>
                                    <td>'.$value["fecEmi"].'</td>
                                    <td>'.$productoFactura["cantidad"].'</td>
                                    <td>'.$codigoUM.'</td>
                                    <td>'.$productoLeido["nombre"].'</td>
                                    <td>'.$productoFactura["precioSinImpuestos"].'</td>
                                    <td>'.$retenGran.'</td>
                                    <td>'.($productoFactura["precioSinImpuestos"]*$productoFactura["cantidad"])-$retenGran.'</td>
                                    <td>'.($productoFactura["precioConIva"] * $productoFactura["cantidad"]) - ($retenGran + ($retenGran*0.13)).'</td>
                            </tr>';
                      }

                    }

                    }
                  
                 }
                
                          
              }


            ?> 

        </tbody>

       </table>


       <div class="navbar bg-dark" style="color: white">Ventas AROMAZONE Sujeto Excluido</div>
        <br>
        <button class="btn btn-primary" onclick="ventasSujetoAromazonePdf()">Descargar reporte como pdf</button>
        <button class="btn btn-success" onclick="ventasSujetoAromazoneExcel()">Descargar reporte como Excel</button>
       <table class="table table-bordered table-striped dt-responsive tablaVentasAromazone" id="anexoVentasSujetoAromazone" width="100%" style="font-size: 60%">
         
        <thead>
         
         <tr>
           
           <th>Fecha</th>
           <th>Número de control</th>
           <th>Nombre</th>
           <th>Concepto</th>
           <th>Monto</th>
           <th>Renta</th>
           <th>Total a recibir</th>

         </tr> 

        </thead>
                            
        <tbody>

            <?php
              $pagoAcuentas = 0.0;
              $item = null;
              $valor = null;
              $orden = "fecEmi";

              if($optimizacion == "no"){
                $facturas = ControladorFacturas::ctrMostrarFacturasVentas($filtroFechaInicio, $filtroFechaFin);
              } else {
                $facturas = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
              }

              foreach ($facturas as $key => $value){
                if(($value["tipoDte"] == "14") && $value["sello"] != "" && $value["estado"] == "Activa") {

                    $item = "id";
                    $valor = $value["id_cliente"];
                    $orden = "id";

                    $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

                    $item = "id";
                    $valor = $value["id_vendedor"];

                    $usuario = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

                    if (
                      (
                          ($filtroFechaInicio == "todos" && $filtroFechaFin == "todos") || 
                          ($filtroFechaInicio != "todos" && $filtroFechaFin == "tod os" && $value["fecEmi"] >= $filtroFechaInicio) ||
                          ($filtroFechaInicio == "todos" && $filtroFechaFin != "todos" && $value["fecEmi"] <= $filtroFechaFin) ||
                          ($filtroFechaInicio != "todos" && $filtroFechaFin != "todos" && $value["fecEmi"] >= $filtroFechaInicio && $value["fecEmi"] <= $filtroFechaFin)
                      )
                  ){
                    
                    // Decodificamos los productos (arreglo JSON)
                    $productos = json_decode($value["productos"], true);
                    $unidadesMedida = array(
                      "59" => "Unidad",
                      "57" => "Ciento",
                      "58" => "Docena",
                      "1"  => "Metro",
                      "2"  => "Yarda",
                      "6"  => "Milímetro",
                      "9"  => "Kilómetro cuadrado",
                      "10" => "Hectárea",
                      "13" => "Metro cuadrado",
                      "15" => "Vara cuadrada",
                      "18" => "Metro cúbico",
                      "20" => "Barril",
                      "22" => "Galón",
                      "23" => "Litro",
                      "24" => "Botella",
                      "26" => "Mililitro",
                      "30" => "Tonelada",
                      "32" => "Quintal",
                      "33" => "Arroba",
                      "34" => "Kilogramo",
                      "36" => "Libra",
                      "37" => "Onza troy",
                      "38" => "Onza",
                      "39" => "Gramo",
                      "40" => "Miligramo",
                      "42" => "Megawatt",
                      "43" => "Kilowatt",
                      "44" => "Watt",
                      "45" => "Megavoltio-amperio",
                      "46" => "Kilovoltio-amperio",
                      "47" => "Voltio-amperio",
                      "49" => "Gigawatt-hora",
                      "50" => "Megawatt-hora",
                      "51" => "Kilowatt-hora",
                      "52" => "Watt-hora",
                      "53" => "Kilovoltio",
                      "54" => "Voltio",
                      "55" => "Millar",
                      "56" => "Medio millar",
                      "99" => "Otra"
                    );
                    

                    $montoSinRenta = 0.00;
                    $renta = 0.00;
                    $conceptos = "";

                      // Si se decodificó bien y existen productos
                      if (!empty($productos) && is_array($productos)) {

                      // Recorremos cada producto
                      foreach ($productos as $productoFactura) {
                        $item = "id";
                        $valor = $productoFactura["idProducto"];
                
                        $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor);
                        $conceptos .= " - " . $productoLeido["nombre"];

                        $item1 = "id";
                        $valor1 = $productoLeido["categoria_id"];
                
                        $categoria = ControladorCategorias::ctrMostrarCategorias($item1, $valor1);

                        $retenGran = 0.0;
                        if($value["gran_contribuyente"] == "Si"){
                          $retenGran = ($productoFactura["precioSinImpuestos"]*$productoFactura["cantidad"]) * 0.01;
                        }

                        // Supongamos que tu código numérico de unidad viene en $productoFactura["unidadMedida"]
                        $codigoUM = $unidadesMedida[$productoLeido["unidadMedida"]]; // Ej. 56
                        $montoSinRenta += ($productoFactura["precioSinImpuestos"]*$productoFactura["cantidad"])-$retenGran;
                        $renta += (($productoFactura["precioSinImpuestos"]*$productoFactura["cantidad"])-$retenGran)*0.10;
                      }

                    }

                    echo ' <tr>
                                    <td>'.$value["fecEmi"].'</td>
                                    <td>'.$value["numeroControl"].'</td>
                                    <td>'.$cliente["nombre"].'</td>
                                    <td>'.$conceptos.'</td>
                                    <td>'.$montoSinRenta.'</td>
                                    <td>'.$renta.'</td>
                                    <td>'.$montoSinRenta - $renta.'</td>
                            </tr>';

                    }
                  
                 }
                
                          
              }


            ?> 

        </tbody>

       </table>













        <br>
        <div class="navbar bg-dark" style="color: white">Detalle de ventas a consumidor final</div>
        <br>
        <button class="btn btn-primary" onclick="ventasConsumidorFinalPdf()">Descargar reporte como pdf</button>
        <button class="btn btn-success" onclick="ventasConsumidorFinalExcel()">Descargar reporte como Excel</button>
        <button class="btn btn-info" onclick="ventasConsumidorFinalCsv()">Descargar reporte como CSV</button>
        <p style="color: red; font-size: 15px">¡Importante! Siempre revisa tu archivo CSV antes de subirlo al Ministerio de Hacienda - Qwerty Systems no es responsable de un mal manejo de las herramientas</p>
        <table class="table table-bordered table-striped dt-responsive tablas" id="anexoVentas" width="100%" style="font-size: 60%">
         
         <thead>
          
          <tr>
            
            <th>Fecha de emisión</th>
            <th>Clase de documento</th>
            <th>Tipo de documento</th>
            <th style="max-width: 35px; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; white-space: normal; vertical-align: middle;">Número de resolución</th>
            <th style="max-width: 35px; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; white-space: normal; vertical-align: middle;">Serie del documento</th>
            <th>Número de control interno del</th>
            <th>Número de control interno al</th>
            <th style="max-width: 35px; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; white-space: normal; vertical-align: middle;">Número de documento del</th>
            <th style="max-width: 35px; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; white-space: normal; vertical-align: middle;">Número de documento al</th>
            <th>Número de máquina registradora</th>
            <th>Ventas exentas</th>
            <th>Ventas internas exentas no sujetas a proporcionalidad</th>
            <th>Ventas no sujetas</th>
            <th>Ventas gravadas locales</th>
            <th>Exportaciones dentro del área de Centroamérica</th>
            <th>Exportaciones fuera del área de Centroámerica</th>
            <th>Exportaciones de servicio</th>
            <th>Ventas a zonas francas y DPA (tasa cero)</th>
            <th>Ventas a cuenta de terceros no domiciliados</th>
            <th>Total de ventas</th>
            <th>Tipo de operación (renta)</th>
            <th>Tipo de ingreso (renta)</th>
            <th>Número del anexo</th>
 
          </tr> 
 
         </thead>
                             
         <tbody>
 
             <?php
               $pagoAcuentasF = 0.0;
               $item = null;
               $valor = null;
               $orden = "fecEmi";
               if($optimizacion == "no"){
                 $facturas = ControladorFacturas::ctrMostrarFacturasVentas($filtroFechaInicio, $filtroFechaFin);
               } else {
                 $facturas = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
               }
               
               
 
               foreach ($facturas as $key => $value){
                 if(($value["tipoDte"] == "01" || $value["tipoDte"] == "11") && $value["sello"] != "" && $value["estado"] == "Activa") {
 
                     $item = "id";
                     $valor = $value["id_cliente"];
                     $orden = "id";
 
                     $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
 
                     if (
                       (
                           ($filtroFechaInicio == "todos" && $filtroFechaFin == "todos") || 
                           ($filtroFechaInicio != "todos" && $filtroFechaFin == "todos" && $value["fecEmi"] >= $filtroFechaInicio) ||
                           ($filtroFechaInicio == "todos" && $filtroFechaFin != "todos" && $value["fecEmi"] <= $filtroFechaFin) ||
                           ($filtroFechaInicio != "todos" && $filtroFechaFin != "todos" && $value["fecEmi"] >= $filtroFechaInicio && $value["fecEmi"] <= $filtroFechaFin)
                       )
                   ){
                           
                           // Suponiendo que $value["fecEmi"] tiene el valor '2024-10-19'
                           // Suponiendo que la fecha es '2024-10-19' (formato Y-m-d)
                           $fecha = $value["fecEmi"];
                           $fechaOriginal = new DateTime($fecha);
                           $fechaFormateada = $fechaOriginal->format('d/m/Y'); // Formato: 19/10/2024
 
                           $firmaDigi = "";
                           $sello = "";
                           if($value["firmaDigital"] == ""){
                             $firmaDigi = "No";
                           } else {
                             $firmaDigi = "Si";
                           }
 
                           if($value["sello"] ===  ""){
                             $sello = "No";
                           } else {
                             $sello = "Si";
                           }
 
                           $tipoFacturaTexto = "";
                           switch ($value["tipoDte"]) {
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
                                 $tipoFacturaTexto = "Factura no válida";
                                 break;
                         }
 
                         $tipo = "";
                         if($cliente["tipo_cliente"] == "00"){
                           $tipo = "Persona normal";
                         }
                         if($cliente["tipo_cliente"] == "01"){
                           $tipo = "Declarante IVA";
                         }
                         if($cliente["tipo_cliente"] == "02"){
                           $tipo = "Empresa con beneficios fiscales";
                         }
                         if($cliente["tipo_cliente"] == "03"){
                           $tipo = "Diplomático";
                         }
 
                         $ventaGravada = "0.00";
                         $ventaExenta = "0.00";
                         $ventaNoSujeta = "0.00";
                         $exportFuera = "0.00";
 
 
                         $productos = json_decode($value["productos"], true);
 
                         
 
                       if($cliente["tipo_cliente"] ==  "00" && $value["tipoDte"] == "01"){
                         // Recorremos el array de productos
                         foreach ($productos as $producto) {
                           $item = "id";
                           $valor = $producto["idProducto"];
                           $optimizacion = "no";
 
                           $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                           if($productoLeido["exento_iva"] == "si"){
                             $ventaExenta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             $pagoAcuentasF += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                           } else {
                             $ventaGravada += ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                             $pagoAcuentasF += ((($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]) / 1.13);
                           }
                         }  
                       }
 
                       if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "01"){
                         // Recorremos el array de productos
                         foreach ($productos as $producto) {
                           $item = "id";
                           $valor = $producto["idProducto"];
                           $optimizacion = "no";
 
                           $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                           if($productoLeido["exento_iva"] == "si"){
                             $ventaExenta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             $pagoAcuentasF += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                           } else {
                             $ventaGravada += ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                             $pagoAcuentasF += ((($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]) / 1.13);
                           }
                         }
                       }
 
                       if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "01"){
                         // Recorremos el array de productos
                         foreach ($productos as $producto) {
                           $item = "id";
                           $valor = $producto["idProducto"];
                           $optimizacion = "no";
 
                           $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                           if($productoLeido["exento_iva"] == "si"){
                             $ventaExenta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             $pagoAcuentasF += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                           } else {
                             $ventaNoSujeta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             $pagoAcuentasF += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                           }
                         }
                       }
 
                       if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "01"){
                         // Recorremos el array de productos
                         foreach ($productos as $producto) {
                           $item = "id";
                           $valor = $producto["idProducto"];
                           $optimizacion = "no";
 
                           $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                           if($productoLeido["exento_iva"] == "si"){
                             $ventaExenta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             $pagoAcuentasF += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                           } else {
                             $ventaExenta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             $pagoAcuentasF += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                           }
                         }
                       }
 
                       if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "11"){
                         // Recorremos el array de productos
                         foreach ($productos as $producto) {
                           $item = "id";
                           $valor = $producto["idProducto"];
                           $optimizacion = "no";
 
                           $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                           if($productoLeido["exento_iva"] == "si"){
                             $exportFuera += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                             $pagoAcuentasF += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                           } else {
                             $exportFuera += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                             $pagoAcuentasF += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                           }
                         }
                       }
   
                       
                       
                       if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "11"){
                         // Recorremos el array de productos
                         foreach ($productos as $producto) {
                           $item = "id";
                           $valor = $producto["idProducto"];
                           $optimizacion = "no";
 
                           $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                           if($productoLeido["exento_iva"] == "si"){
                             $exportFuera += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                             $pagoAcuentasF += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                           } else {
                             $exportFuera += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                             $pagoAcuentasF += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                           }
                         }
                       }
   
                       
                       if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "11"){
                         // Recorremos el array de productos
                         foreach ($productos as $producto) {
                           $item = "id";
                           $valor = $producto["idProducto"];
                           $optimizacion = "no";
 
                           $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                           if($productoLeido["exento_iva"] == "si"){
                             $exportFuera += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                             $pagoAcuentasF += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                           } else {
                             $exportFuera += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                             $pagoAcuentasF += (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]) + $value["seguro"] + $value["flete"];
                           }
                         }
                       }             
                         
                         $item = "id";
                         $valor = $value["id_vendedor"];
                 
                         $vendedor = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
 
                         $item = "id";
                         $valor = $value["id_usuario"];
                 
                         $usuario = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                         $tipoOperacion = "1";
                         if($ventaExenta != 0){
                           $tipoOperacion = "2";
                         }
 
                         if($value["sello"] == ""){
 
                         } else {
                           echo ' <tr>
                                     <td>'.$fechaFormateada.'</td>
                                     <td>4</td>
                                     <td>'.$value["tipoDte"].'</td>
                                     <td>'.str_replace('-', '', $value["numeroControl"]).'</td>
                                     <td>'.$value["sello"].'</td>
                                     <td>0</td>
                                     <td>0</td>
                                     <td>'.str_replace('-', '', $value["codigoGeneracion"]).'</td>
                                     <td>'.str_replace('-', '', $value["codigoGeneracion"]).'</td>
                                     <td></td>
                                     <td>'.$ventaExenta.'</td>
                                     <td>0.00</td>
                                     <td>'.$ventaNoSujeta.'</td>
                                     <td>'.$ventaGravada.'</td>
                                     <td>'.$exportFuera.'</td>
                                     <td>0.00</td>
                                     <td>0.00</td>
                                     <td>0.00</td>
                                     <td>0.00</td>
                                     <td>'.$ventaGravada + $ventaExenta + $ventaNoSujeta + $exportFuera.'</td>
                                     <td>'.$tipoOperacion.'</td>
                                     <td>03</td>
                                     <td>2</td>
                             </tr>';
                         }
                     }
                   
                  }
                 
                           
               }
 
 
             ?> 
 
         </tbody>
 
        </table>
        <div class="navbar bg-primary" style="color: white">Pago a cuentas del periodo <?php echo "$".number_format($pagoAcuentasF, 2, '.', ','); ?></div>
 
 
 
 
 
 
        <br>
         <div class="navbar bg-dark" style="color: white">Detalle de ventas a contribuyentes</div>
         <br>
         <button class="btn btn-primary" onclick="ventasContribuyentePdf()">Descargar reporte como pdf</button>
         <button class="btn btn-success" onclick="ventasContribuyenteExcel()">Descargar reporte como Excel</button>
         <button class="btn btn-info" onclick="ventasContribuyenteCsv()">Descargar reporte como CSV</button>
         <button class="btn btn-warning"  data-toggle="modal" data-target="#modalEscogerFechaContribuyente" onclick="generarDeclaracionContribuyentes()">Transmitir</button>
         <p style="color: red; font-size: 15px">¡Importante! Siempre revisa tu archivo CSV antes de subirlo al Ministerio de Hacienda - Qwerty Systems no es responsable de un mal manejo de las herramientas</p>
        <table class="table table-bordered table-striped dt-responsive tablas" id="anexoVentasContribuyentes" width="100%" style="font-size: 60%">
          
         <thead>
          
          <tr>
            
            <th>Fecha de emisión</th>
            <th>Clase de documento</th>
            <th>Tipo de documento</th>
            <th style="max-width: 35px; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; white-space: normal; vertical-align: middle;">Número de resolución</th>
            <th style="max-width: 35px; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; white-space: normal; vertical-align: middle;">Serie del documento</th>
            <th style="max-width: 35px; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; white-space: normal; vertical-align: middle;">Número de documento</th>
            <th>Número de control interno</th>
            <th>NIT o NRC cliente</th>
            <th>Razón social</th>
            <th>Ventas exentas</th>
            <th>Ventas no sujetas</th>
            <th>Ventas gravadas locales</th>
            <th>Débito fiscal</th>           
            <th>Ventas a cuenta de terceros no domiciliados</th>
            <th>Débito fiscal por venta a cuenta de terceros</th>
            <th>Total de ventas</th>
            <th>Dui del cliente</th>
            <th>Tipo de operación (renta)</th>
            <th>Tipo de ingreso (renta)</th>
            <th>Número del anexo</th>
 
          </tr> 
 
         </thead>
                             
         <tbody>
 
             <?php
               $pagoAcuentas = 0.0;
               $item = null;
               $valor = null;
               $orden = "fecEmi";
 
               if($optimizacion == "no"){
                 $facturas = ControladorFacturas::ctrMostrarFacturasVentas($filtroFechaInicio, $filtroFechaFin);
               } else {
                 $facturas = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
               }
 
               foreach ($facturas as $key => $value){
                 if(($value["tipoDte"] == "03" || $value["tipoDte"] == "05" || $value["tipoDte"] == "06") && $value["sello"] != "" && $value["estado"] == "Activa") {
 
                     $item = "id";
                     $valor = $value["id_cliente"];
                     $orden = "id";
 
                     $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
 
                     if (
                       (
                           ($filtroFechaInicio == "todos" && $filtroFechaFin == "todos") || 
                           ($filtroFechaInicio != "todos" && $filtroFechaFin == "todos" && $value["fecEmi"] >= $filtroFechaInicio) ||
                           ($filtroFechaInicio == "todos" && $filtroFechaFin != "todos" && $value["fecEmi"] <= $filtroFechaFin) ||
                           ($filtroFechaInicio != "todos" && $filtroFechaFin != "todos" && $value["fecEmi"] >= $filtroFechaInicio && $value["fecEmi"] <= $filtroFechaFin)
                       )
                   ){
                           
                           // Suponiendo que $value["fecEmi"] tiene el valor '2024-10-19'
                           // Suponiendo que la fecha es '2024-10-19' (formato Y-m-d)
                           $fecha = $value["fecEmi"];
                           $fechaOriginal = new DateTime($fecha);
                           $fechaFormateada = $fechaOriginal->format('d/m/Y'); // Formato: 19/10/2024
 
                           $firmaDigi = "";
                           $sello = "";
                           if($value["firmaDigital"] == ""){
                             $firmaDigi = "No";
                           } else {
                             $firmaDigi = "Si";
                           }
 
                           if($value["sello"] ===  ""){
                             $sello = "No";
                           } else {
                             $sello = "Si";
                           }
 
                           $tipoFacturaTexto = "";
                           switch ($value["tipoDte"]) {
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
                                 $tipoFacturaTexto = "Factura no válida";
                                 break;
                         }
 
                         $tipo = "";
                         if($cliente["tipo_cliente"] == "00"){
                           $tipo = "Persona normal";
                         }
                         if($cliente["tipo_cliente"] == "01"){
                           $tipo = "Declarante IVA";
                         }
                         if($cliente["tipo_cliente"] == "02"){
                           $tipo = "Empresa con beneficios fiscales";
                         }
                         if($cliente["tipo_cliente"] == "03"){
                           $tipo = "Diplomático";
                         }
 
                         $ventaGravada = "0.00";
                         $ventaExenta = "0.00";
                         $ventaNoSujeta = "0.00";
                         $iva = "0.00";
 
                         $productos = json_decode($value["productos"], true);
   
                         if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "03"){
                           // Recorremos el array de productos
                           foreach ($productos as $producto) {
                             $item = "id";
                             $valor = $producto["idProducto"];
                             $optimizacion = "no";
 
                             $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                             if($productoLeido["exento_iva"] == "si"){
                               $ventaExenta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             } else {
                               $ventaGravada += ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                               $pagoAcuentas += (($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]) / 1.13;
                               $iva += ((($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]) / 1.13) * 0.13;
                             }
                           }
                         }
 
                         if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "03"){
                           // Recorremos el array de productos
                           foreach ($productos as $producto) {
                             $item = "id";
                             $valor = $producto["idProducto"];
                             $optimizacion = "no";
 
                             $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                             if($productoLeido["exento_iva"] == "si"){
                               $ventaExenta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             } else {
                               $ventaNoSujeta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             }
                           }
                         }
                         if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "03"){
                           // Recorremos el array de productos
                           foreach ($productos as $producto) {
                             $item = "id";
                             $valor = $producto["idProducto"];
                             $optimizacion = "no";
 
                             $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                             if($productoLeido["exento_iva"] == "si"){
                               $ventaExenta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             } else {
                               $ventaExenta += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             }
                           }
                         }
     
                         if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "05"){
                           // Recorremos el array de productos
                           foreach ($productos as $producto) {
                             $item = "id";
                             $valor = $producto["idProducto"];
                             $optimizacion = "no";
 
                             $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                             if($productoLeido["exento_iva"] == "si"){
                               $ventaExenta += ($producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas -= ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             } else {
                               $ventaGravada += ($producto["descuentoConIva"]) * $producto["cantidad"];
                               $pagoAcuentas -= (($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]) / 1.13;
                             }
                           }
                         }
                         if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "05"){
                           // Recorremos el array de productos
                           foreach ($productos as $producto) {
                             $item = "id";
                             $valor = $producto["idProducto"];
                             $optimizacion = "no";
 
                             $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                             if($productoLeido["exento_iva"] == "si"){
                               $ventaExenta += ($producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas -= ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             } else {
                               $ventaNoSujeta += ($producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas -= ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             }
                           }
                         }
                         if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "05"){
                           // Recorremos el array de productos
                           foreach ($productos as $producto) {
                             $item = "id";
                             $valor = $producto["idProducto"];
                             $optimizacion = "no";
 
                             $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                             if($productoLeido["exento_iva"] == "si"){
                               $ventaExenta += ($producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas -= ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             } else {
                               $ventaExenta += ($producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas -= ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             }
                           }
                         }
 
                         if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "06"){
                           // Recorremos el array de productos
                           foreach ($productos as $producto) {
                             $item = "id";
                             $valor = $producto["idProducto"];
                             $optimizacion = "no";
 
                             $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                             if($productoLeido["exento_iva"] == "si"){
                               $ventaExenta += ($producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             } else {
                               $ventaGravada += ($producto["descuentoConIva"]) * $producto["cantidad"];
                               $iva += ((($producto["descuentoConIva"]) * $producto["cantidad"]) / 1.13) * 0.13;
                               $pagoAcuentas += (($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]) / 1.13;
                             }
                           }
                         }
                         if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "06"){
                           // Recorremos el array de productos
                           foreach ($productos as $producto) {
                             $item = "id";
                             $valor = $producto["idProducto"];
                             $optimizacion = "no";
 
                             $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                             if($productoLeido["exento_iva"] == "si"){
                               $ventaExenta += ($producto["descuento"]) * $producto["cantidad"]; 
                               $pagoAcuentas += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             } else {
                               $ventaNoSujeta += ($producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             }
                           }
                         }
                         if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "06"){
                           // Recorremos el array de productos
                           foreach ($productos as $producto) {
                             $item = "id";
                             $valor = $producto["idProducto"];
                             $optimizacion = "no";
 
                             $productoLeido = ControladorProductos::ctrMostrarProductos($item, $valor, $optimizacion);
                             if($productoLeido["exento_iva"] == "si"){
                               $ventaExenta += ($producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             } else {
                               $ventaExenta += ($producto["descuento"]) * $producto["cantidad"];
                               $pagoAcuentas += ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                             }
                           }
                         }  
                         
                         $item = "id";
                         $valor = $value["id_vendedor"];
                 
                         $vendedor = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
 
                         $item = "id";
                         $valor = $value["id_usuario"];
                 
                         $usuario = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                         
 
                         if($value["sello"] == ""){
 
                         } else {
 
                           echo ' <tr>
                                     <td>'.$fechaFormateada.'</td>
                                     <td>4</td>
                                     <td>'.$value["tipoDte"].'</td>
                                     <td>'.str_replace('-', '', $value["numeroControl"]).'</td>
                                     <td>'.$value["sello"].'</td>
                                     <td>'.str_replace('-', '', $value["codigoGeneracion"]).'</td>
                                     <td>'.str_replace('-', '', $value["numeroControl"]).'</td>
                                     <td>'.$cliente["NIT"].'</td>
                                     <td>'.$cliente["nombre"].'</td>
                                     <td>'.$ventaExenta.'</td>
                                     <td>'.$ventaNoSujeta.'</td>
                                     <td>'.$ventaGravada.'</td>
                                     <td>'.$iva.'</td>
                                     <td>0.00</td>
                                     <td>0.00</td>
                                     <td>'.$ventaGravada+$ventaExenta+$ventaNoSujeta.'</td>
                                     <td></td>
                                     <td>1</td>
                                     <td>03</td>
                                     <td>1</td>
                             </tr>';
                         }
                     }
                   
                  }
                 
                           
               }
 
 
             ?> 
 
         </tbody>
 
        </table>
       <div class="navbar bg-primary" style="color: white">Pago a cuentas del periodo <?php echo "$".number_format($pagoAcuentas, 2, '.', ','); ?></div>

      </div>

    </div>

  </section>

</div>

<!--=====================================
MODAL CREAR FOLO DE VENTA
======================================-->

<div id="modalCrearFolio" class="modal" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">
      <form role="form" method="get" action="extensiones/TCPDF-main/examples/imprimir-folio.php?" enctype="multipart/form-data">
        <input type="hidden" name="tipoFolio" value="todos">
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Filtros de folios</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
            
            
          <!-- ENTRADA PARA EL CORREO-->

          <div class="form-group">
            Seleccionar mes y año:
            <div class="input-group mb-3">
                  <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                  </div>
                  <input type="month" class="form-control" id="nuevaFechaFolio" name="nuevaFechaFolio" required>
            </div>
          </div>  


        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-dark pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Generar folio</button>
        </div>

      </form>
    </div>
      
    </div>

    </div>

  </div>

</div>