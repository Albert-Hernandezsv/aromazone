<?php

if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Facturación"){
} else {
    echo '<script>
    window.location = "inicio";
    </script>';
  return;
}

?>

<div class="main-content content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Sistema de facturación
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

      <button class="btn btn-primary btnEditarEmpresarial" data-toggle="modal" data-target="#modalDatosEmpresariales">
          
          Datos empresariales

        </button>

        <?php

            if($_SESSION["tokenInicioSesionMh"] == ""){
                echo '<button class="btn btn-danger btnIniciarSesionMh">
          
                          Iniciar sesión en Ministerio de Hacienda

                    </button>';
            } else {
              echo '<button class="btn btn-success">
          
                    Inicio de sesión MH correcto

                  </button>';
            }

        ?>
<br><br>
      </div>

      <div class="row">

          <div class="col-xl-3 col-xs-12">
              <?php
                  $optimizacion;
                  if(!isset($_GET["optimizar"]) && !isset($_GET["fechaOptimizada"])){
                    $optimizacion = "si";
                    echo "<div class='form-check form-switch'>
                      <input class='form-check-input' type='checkbox' role='switch' id='flexSwitchCheckChecked' onclick=\"location.href='index.php?ruta=contabilidad&optimizar=no'\" checked>
                      <label class='form-check-label' for='flexSwitchCheckChecked'>Optimizar tabla</label>
                    </div>";
                  } else {
                    $optimizacion = "no";
                    echo "<div class='form-check form-switch'>
                      <input class='form-check-input' type='checkbox' role='switch' id='flexSwitchCheckChecked' onclick=\"location.href='contabilidad'\">
                      <label class='form-check-label' for='flexSwitchCheckChecked'>Optimizar tabla</label>
                    </div>";
                  }
              ?>
          </div>

          <div class="col-xl-3 col-xs-12">

              <form method="GET" action="index.php">
                <input type="hidden" name="ruta" value="contabilidad">
                <label for="mes_anio">Seleccione mes y año:</label>
                <div class="d-flex align-items-center gap-2">
                  <input class="form-control" type="month" id="fechaOptimizada" name="fechaOptimizada" required>&nbsp;
                  <button class="btn btn-success" type="submit">Buscar</button>&nbsp;
                  <button class="btn btn-primary" onclick="location.href='contabilidad'" type="submit">Reestablecer</button>
                </div>
              </form>

          </div>

      </div>

      <div class="box-body">
      <button class="btn btn-success" onclick="notasExcel()">Descargar reporte como Excel</button><br><br>
       <table class="table table-bordered table-striped dt-responsive tablas" id="notasExcel" width="100%">
         
        <thead>
         
         <tr>
           
           <th style="width:10px">#</th>
           <th style="width:200px">Cliente</th>
           <th style="width:300px">Número de control</th>
           <th style="width:300px">Código de generación</th>
           <th style="width:20px !important">Firma digital</th>
           <th style="width:50px !important">Sellado por hacienda</th>
           <th>Tipo de factura</th>
           <th>Factura relacionada</th>
           <th>Monto</th>
           <th>Fecha</th>
           <th>Acciones</th>

         </tr> 

        </thead>

        <tbody>

        <?php

        $item = null;
        $valor = null;
        $orden = "fecEmi";

        if(isset($_GET["fechaOptimizada"])){
          $facturas = ControladorFacturas::ctrMostrarFacturasFechaOptimizada($_GET["fechaOptimizada"]);
        } else {
          $facturas = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
        }

        foreach ($facturas as $key => $value){
        if($value["tipoDte"] == "05" || $value["tipoDte"] == "06"){
          $item = "id";
                  $valor = $value["id_cliente"];
                  $orden = "id";

                  $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
                  // Suponiendo que $value["fecha"] tiene el valor '2024-10-19 22:36:44'
                  $fechaOriginal = new DateTime($value["fecEmi"]);
                  $fechaFormateada = $fechaOriginal->format('d \d\e F \d\e Y'); // Formato deseado
                  $firmaDigi = "";
                  $sello = "";
                  if($value["firmaDigital"] ===  ""){
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

                $totalF = "";
                
                $productos = json_decode($value["productos"], true); // true para obtener un array asociativo
                $contador = 1;
                $totalGravado = 0.0;
                $totalDes = 0.0;

                if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "05"){
                  // Decodificar los productos de la factura
                  
                  // Recorrer cada producto y mapear los datos
                  foreach ($productos as $producto) {
                      $item = "id";
                      $valor = $producto["idProducto"];
                  
                      $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                      $des = $producto["descuento"];
                      $desR = floatval(number_format($des, 2, '.', ''));

                      $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                      $totalProF = floatval(number_format($totalProD, 2, '.', ''));
                      
                      $totalGravado += $totalProF;
                      $totalDes += $desR;
                      $contador++;
                  }
                  $totalGravado = ($totalGravado*0.13) + $totalGravado;
                }

                if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "05"){
                  // Decodificar los productos de la factura
                  
                  // Recorrer cada producto y mapear los datos
                  foreach ($productos as $producto) {
                      $item = "id";
                      $valor = $producto["idProducto"];
                  
                      $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                      $des = $producto["descuento"];
                      $desR = floatval(number_format($des, 2, '.', ''));

                      $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                      $totalProF = floatval(number_format($totalProD, 2, '.', ''));
                      
                      $totalGravado += $totalProF;
                      $totalDes += $desR;
                      $contador++;
                  }
                }

                if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "05"){
                  // Decodificar los productos de la factura
                  
                  // Recorrer cada producto y mapear los datos
                  foreach ($productos as $producto) {
                      $item = "id";
                      $valor = $producto["idProducto"];
                  
                      $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                      $des = $producto["descuento"];
                      $desR = floatval(number_format($des, 2, '.', ''));

                      $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                      $totalProF = floatval(number_format($totalProD, 2, '.', ''));
                      
                      $totalGravado += $totalProF;
                      $totalDes += $desR;
                      $contador++;
                  }
                }

                if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "06"){
                  // Decodificar los productos de la factura
                  
                  // Recorrer cada producto y mapear los datos
                  foreach ($productos as $producto) {
                      $item = "id";
                      $valor = $producto["idProducto"];
                  
                      $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                      $des = $producto["descuento"];
                      $desR = floatval(number_format($des, 2, '.', ''));

                      $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                      $totalProF = floatval(number_format($totalProD, 2, '.', ''));
                      
                      $totalGravado += $totalProF;
                      $totalDes += $desR;
                      $contador++;
                  }
                  $totalGravado = ($totalGravado*0.13) + $totalGravado;
                }

                if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "06"){
                  // Decodificar los productos de la factura
                  
                  // Recorrer cada producto y mapear los datos
                  foreach ($productos as $producto) {
                      $item = "id";
                      $valor = $producto["idProducto"];
                  
                      $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                      $des = $producto["descuento"];
                      $desR = floatval(number_format($des, 2, '.', ''));

                      $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                      $totalProF = floatval(number_format($totalProD, 2, '.', ''));
                      
                      $totalGravado += $totalProF;
                      $totalDes += $desR;
                      $contador++;
                  }
                  
                }

                if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "06"){
                  // Decodificar los productos de la factura
                  
                  // Recorrer cada producto y mapear los datos
                  foreach ($productos as $producto) {
                      $item = "id";
                      $valor = $producto["idProducto"];
                  
                      $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                      $des = $producto["descuento"];
                      $desR = floatval(number_format($des, 2, '.', ''));

                      $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                      $totalProF = floatval(number_format($totalProD, 2, '.', ''));
                      
                      $totalGravado += $totalProF;
                      $totalDes += $desR;
                      $contador++;
                  }
                  
                }

                $item = "id";
                $orden = "id";
                $valor = $value["idFacturaRelacionada"];
            
                $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
                

          echo ' <tr>
                  <td>'.($key+1).'</td>
                  <td>'.$cliente["nombre"].' - '.$tipo.'</td>
                  <td>'.$value["numeroControl"].'</td>
                  <td>'.$value["codigoGeneracion"].'</td>
                  <td>'.$firmaDigi.'</td>
                  <td>'.$sello.'</td>
                  <td>'.$tipoFacturaTexto.'</td>
                  <td>'.$facturaOriginal["codigoGeneracion"].'</td>
                  <td>$'.$totalGravado.'</td>
                  <td>'.$fechaFormateada.'</td>
  
                  <td>

                    <div class="btn-group">
                      
                      <button class="btn btn-warning btnVerFactura" idFactura="'.$value["id"].'"><i class="fa fa-eye"></i></button>';
                      // Configurar la zona horaria de El Salvador
                      date_default_timezone_set('America/El_Salvador');

                      // Fecha de emisión
                      $fecEmi = $value["fecEmi"];

                      // Obtener la fecha actual en el formato "Y-m-d"
                      $fechaActual = date('Y-m-d');

                      // Verificar si la fecha actual coincide con `fecEmi`
                      if ($fechaActual === $fecEmi) {
                          // Ejecutar la acción si la fecha coincide
                          
                          if($value["firmaDigital"] === ""){
                            echo '<button class="btn btn-info btnFirmarDte" idFactura="'.$value["id"].'" ><i class="fa fa-pencil-square-o"></i></button>';
                          } else {
                            
                          }

                          if($value["sello"] === ""){
                            if($value["firmaDigital"] === ""){

                            } else {
                              echo '<button class="btn btn-success btnSellarDte" idFactura="'.$value["id"].'" ><i class="fa fa-arrow-circle-right"></i></button>';
                            }
                            
                            echo '<button class="btn btn-danger btnEliminarFactura" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                          } else {
                            if($value["estado"] != "Anulada" ){
                              echo '<button class="btn btn-danger btnEliminarFacturaHacienda" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                            }
                          }

                      } else {}

                      if($value["sello"] != "" && ($value["tipoDte"] == "03" || $value["tipoDte"] == "11")){

                        if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                          echo '<button class="btn btn-info btnNotaCredito" idFactura="'.$value["id"].'">NC</button>';
                          echo '<button class="btn btn-success btnNotaDebito" idFactura="'.$value["id"].'">ND</button>';
                        }
                        
                      } else {

                      }

              echo '</div>  

                  </td>

                </tr>';
        }
         
                  
        }


        ?> 

        </tbody>

       </table>

        <br><h2>Facturas anuladas</h2><br>
        <button class="btn btn-success" onclick="anuladasExcel()">Descargar reporte como Excel</button><br><br>
        <table class="table table-bordered table-striped dt-responsive tablas" id="anuladasExcel" width="100%">
         
          <thead>
            
            <tr>
              
              <th style="width:10px">#</th>
              <th style="width:200px">Cliente</th>
              <th style="width:300px">Número de control factura anulada</th>
              <th style="width:300px">Código de generación</th>
              <th style="width:300px">Código de generación factura anulada</th>
              <th style="width:20px !important">Firma digital</th>
              <th style="width:50px !important">Sellado por hacienda</th>
              <th>Total</th>
              <th>Fecha anulación</th>
              <th>Acciones</th>
  
            </tr> 
  
          </thead>
  
          <tbody>
  
          <?php
  
          $item = null;
          $valor = null;
          $orden = "fecEmi";
  
          if(isset($_GET["fechaOptimizada"])){
            $facturas = ControladorFacturas::ctrMostrarAnulacionesFechaOptimizada($_GET["fechaOptimizada"]);
          } else {
            $facturas = ControladorFacturas::ctrMostrarAnulaciones($item, $valor, $orden, $optimizacion);
          }
  
          foreach ($facturas as $key => $value){
  
            $item = "id";
            $valor = $value["facturaRelacionada"];
            $orden = "fecEmi";
  
            $factura1 = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
  
            $item = "id";
            $valor = $factura1["id_cliente"];
            $orden = "id";
  
            $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
            // Suponiendo que $value["fecha"] tiene el valor '2024-10-19 22:36:44'
            $fechaOriginal = new DateTime($value["fecEmi"]);
            $fechaFormateada = $fechaOriginal->format('d \d\e F \d\e Y'); // Formato deseado
            $firmaDigi = "";
            $sello = "";
            if($factura1["firmaDigital"] ===  ""){
              $firmaDigi = "No";
            } else {
              $firmaDigi = "Si";
            }
  
            if($factura1["sello"] ===  ""){
              $sello = "No";
            } else {
              $sello = "Si";
            }
  
            $tipoFacturaTexto = "";
            switch ($factura1["tipoDte"]) {
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
  
                  $totalF = "";
                  
                  $productos = json_decode($factura1["productos"], true); // true para obtener un array asociativo
                  $contador = 1;
                  $totalF = "";
                  if($cliente["tipo_cliente"] ==  "00" && $factura1["tipoDte"] == "01"){
                    $totalF = $factura1["total"];
                  }
                  if($cliente["tipo_cliente"] ==  "00" && $factura1["tipoDte"] == "03"){
                    $totalF = $factura1["total"];
                  }
                  if($cliente["tipo_cliente"] ==  "00" && $factura1["tipoDte"] == "11"){
                    $totalF = $factura1["total"] + $factura1["flete"] + $factura1["seguro"];
                  }
                  if($cliente["tipo_cliente"] ==  "00" && $factura1["tipoDte"] == "14"){
                    $totalF = $factura1["total"];
                  }
  
                  if($cliente["tipo_cliente"] ==  "01" && $factura1["tipoDte"] == "01"){
                    $totalF = $factura1["total"];
                  }
                  if($cliente["tipo_cliente"] ==  "01" && $factura1["tipoDte"] == "03"){
                    $totalF = $factura1["total"];
                  }
                  if($cliente["tipo_cliente"] ==  "01" && $factura1["tipoDte"] == "11"){
                    $totalF = $factura1["totalSinIva"] + $factura1["flete"] + $factura1["seguro"];
                  }
  
                  if($cliente["tipo_cliente"] ==  "02" && $factura1["tipoDte"] == "01"){
                    $totalF = $factura1["totalSinIva"];
                  }
                  if($cliente["tipo_cliente"] ==  "02" && $factura1["tipoDte"] == "03"){
                    $totalF = $factura1["totalSinIva"];
                  }
                  if($cliente["tipo_cliente"] ==  "02" && $factura1["tipoDte"] == "11"){
                    $totalF = $factura1["totalSinIva"] + $factura1["flete"] + $factura1["seguro"];
                  }
  
                  if($cliente["tipo_cliente"] ==  "03" && $factura1["tipoDte"] == "01"){
                    $totalF = $factura1["totalSinIva"];
                  }
                  if($cliente["tipo_cliente"] ==  "03" && $factura1["tipoDte"] == "03"){
                    $totalF = $factura1["totalSinIva"];
                  }
                  if($cliente["tipo_cliente"] ==  "03" && $factura1["tipoDte"] == "11"){
                    $totalF = $factura1["totalSinIva"] + $factura1["flete"] + $factura1["seguro"];
                  }
  
                  if($cliente["tipo_cliente"] ==  "01" && $factura1["tipoDte"] == "05"){
                    $totalF = $factura1["total"];
                  }
                  if($cliente["tipo_cliente"] ==  "02" && $factura1["tipoDte"] == "05"){
                    $totalF = $factura1["totalSinIva"];
                  }
                  if($cliente["tipo_cliente"] ==  "03" && $factura1["tipoDte"] == "05"){
                    $totalF = $factura1["totalSinIva"];
                  }
  
                  if($cliente["tipo_cliente"] ==  "01" && $factura1["tipoDte"] == "04"){
                    $totalF = $factura1["total"];
                  }
                  if($cliente["tipo_cliente"] ==  "02" && $factura1["tipoDte"] == "04"){
                    $totalF = $factura1["totalSinIva"];
                  }
                  if($cliente["tipo_cliente"] ==  "03" && $factura1["tipoDte"] == "04"){
                    $totalF = $factura1["totalSinIva"];
                  }
  
                  $item = "id";
                  $orden = "id";
                  $valor = $factura1["idFacturaRelacionada"];
              
                  $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
                  
  
            echo ' <tr>
                    <td>'.($key+1).'</td>
                    <td>'.$cliente["nombre"].' - '.$tipo.'</td>
                    <td>'.$factura1["numeroControl"].'</td>
                    <td>'.$value["codigoGeneracion"].'</td>
                    <td>'.$factura1["codigoGeneracion"].'</td>
                    <td>'.$firmaDigi.'</td>
                    <td>'.$sello.'</td>
                    <td>$'.$totalF.'</td>
                    <td>'.$fechaFormateada.'</td>
    
                    <td>
  
                      <div class="btn-group">
                        
                        <button class="btn btn-warning btnVerFactura" idFactura="'.$factura1["id"].'"><i class="fa fa-eye"></i></button>';
                        // Configurar la zona horaria de El Salvador
                        date_default_timezone_set('America/El_Salvador');
  
                        // Fecha de emisión
                        $fecEmi = $value["fecEmi"];
  
                        // Obtener la fecha actual en el formato "Y-m-d"
                        $fechaActual = date('Y-m-d');
  
                        // Crear un objeto DateTime con la fecha de emisión
                        $fechaEmision = new DateTime($fecEmi);
  
                        // Sumar 3 meses a la fecha de emisión
                        $fechaLimite = $fechaEmision->modify('+3 months');
  
                        // Verificar si la fecha actual es anterior a la fecha límite
                        if ($fechaActual <= $fechaLimite) {
                          // Aún no han pasado 3 meses
                          // Verificar si la fecha actual coincide con `fecEmi`
                          if ($fechaActual === $fecEmi) {
                            // Ejecutar la acción si la fecha coincide
                            
                            if($value["firmaDigital"] === ""){
                              echo '<button class="btn btn-info btnFirmarDteAnulacion" idFactura="'.$value["id"].'" ><i class="fa fa-pencil-square-o"></i></button>';
                              echo '<button class="btn btn-danger btnCancelarAnulacion" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                            } else {
                                if($value["sello"] === ""){
                                    echo '<button class="btn btn-success btnSellarDteAnulacion" idFactura="'.$value["id"].'" ><i class="fa fa-arrow-circle-right"></i></button>';
                                    echo '<button class="btn btn-danger btnCancelarAnulacion" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                }
                            }
                          }
                        }
  
                echo '</div>  
  
                    </td>
  
                  </tr>';         
                    
          }
  
  
          ?> 
  
          </tbody>
  
        </table>

        <br><h2>Facturas eliminadas sin transmitir</h2><br>
        <button class="btn btn-success" onclick="eliminadasExcel()">Descargar reporte como Excel</button><br><br>
       <table class="table table-bordered table-striped dt-responsive tablas" id="eliminadasExcel" width="100%">
         
            <thead>
            
            <tr>
              
              <th style="width:10px">#</th>
              <th style="width:200px">Número de control</th>
              <th style="width:300px">Código de generación</th>
              <th style="width:300px">Fecha</th>

            </tr> 

            </thead>

            <tbody>

            <?php

                $tabla = "eliminadas";
                $item = null;
                $valor = null;
                $orden = "fecEmi";

                if(isset($_GET["fechaOptimizada"])){
                  $facturasEliminadas = ModeloFacturas::mdlMostrarFacturasEliminadasFechaOptimizada($tabla, $_GET["fechaOptimizada"]);
                } else {
                  if($optimizacion == "no"){
                    $facturasEliminadas = ModeloFacturas::mdlMostrarFacturasEliminadas($tabla, $item, $valor, $orden);
                  } else {
                    $facturasEliminadas = ModeloFacturas::mdlMostrarFacturasEliminadasOptimizadas($tabla, $item, $valor, $orden);
                  }
                }

                  foreach ($facturasEliminadas as $key => $value){

                    echo ' <tr>
                            <td>'.($key+1).'</td>
                            <td>'.$value["numero_control"].'</td>
                            <td>'.$value["codigo_generacion"].'</td>
                            <td>'.$value["fecha"].'</td>';      
                  }

            ?> 

        </tbody>

       </table>

      </div>

    </div>

  </section>

</div>

<?php

  $cancelarAnulacion = new ControladorFacturas();
  $cancelarAnulacion -> ctrCancelarAnulacion();

?>

<!--=====================================
MODAL DATOS EMISOR
======================================-->

<div id="modalDatosEmpresariales" class="modal fade" role="dialog">
  
  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Editar datos empresariales</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
            <div class="row">

                <div class="col-xl-6 col-xs-12">

                    <!-- ENTRADA PARA EL NOMBRE -->

                      <div class="form-group">
                        <p>Nombre de la empresa</p>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                          </div>
                          <input type="text" class="form-control" id="editarNombreEmpresa" name="editarNombreEmpresa" value="" placeholder="Nombre de la empresa" required>
                          <input type="text" class="form-control" id="editarIdEmpresa" name="editarIdEmpresa" value="1" hidden>
                        </div>

                        </div>

                        <!-- ENTRADA PARA EL DEPARTAMENTO-->

                        <div class="form-group">
                        <p>Seleccionar departamento</p>
                        <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                              </div>
                              <select class="form-control" name="editarDepartamentoEmpresa" value="" required>
                              <option value="" id="editarDepartamentoEmpresa"></option>
                                <option value="00">EXTRANJERO</option>
                                <option value="01">AHUACHAPAN</option>
                                <option value="02">SANTA ANA</option>
                                <option value="03">SONSONATE</option>
                                <option value="04">CHALATENANGO</option>
                                <option value="05">LA LIBERTAD</option>
                                <option value="06">SAN SALVADOR</option>
                                <option value="07">CUSCATLAN</option>
                                <option value="08">LA PAZ</option>
                                <option value="09">CABAÑAS</option>
                                <option value="10">SAN VICENTE</option>
                                <option value="11">USULUTAN</option>>
                                <option value="12">SAN MIGUEL</option>
                                <option value="13">MORAZAN</option>
                                <option value="14">LA UNION</option>
                              </select>
                        </div>

                        </div>

                        <!-- ENTRADA PARA EL MUNICIPIO-->

                        <div class="form-group">
                        <p>Seleccionar municipio</p>
                        <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                              </div>
                              <select class="form-control" name="editarMunicipioEmpresa" value="" required>
                              <option value="" id="editarMunicipioEmpresa"></option>
                                <option value="00">EXTRANJERO</option>
                                <option value="13">AHUACHAPAN NORTE</option>
                                <option value="14">AHUACHAPAN CENTRO</option>
                                <option value="15">AHUACHAPAN SUR</option>
                                <option value="14">SANTA ANA NORTE</option>
                                <option value="15">SANTA ANA CENTRO</option>
                                <option value="16">SANTA ANA ESTE</option>
                                <option value="17">SANTA ANA OESTE</option>
                                <option value="17">SONSONATE NORTE</option>
                                <option value="18">SONSONATE CENTRO</option>
                                <option value="19">SONSONATE ESTE</option>
                                <option value="20">SONSONATE OESTE</option>
                                <option value="34">CHALATENANGO NORTE</option>
                                <option value="35">CHALATENANGO CENTRO</option>
                                <option value="36">CHALATENANGO SUR</option>
                                <option value="23">LA LIBERTAD NORTE</option>
                                <option value="24">LA LIBERTAD CENTRO</option>
                                <option value="25">LA LIBERTAD OESTE</option>
                                <option value="26">LA LIBERTAD ESTE</option>
                                <option value="27">LA LIBERTAD COSTA</option>
                                <option value="28">LA LIBERTAD SUR</option>
                                <option value="20">SAN SALVADOR NORTE</option>
                                <option value="21">SAN SALVADOR OESTE</option>
                                <option value="22">SAN SALVADOR ESTE</option>
                                <option value="23">SAN SALVADOR CENTRO</option>
                                <option value="24">SAN SALVADOR SUR</option>
                                <option value="17">CUSCATLAN NORTE</option>
                                <option value="18">CUSCATLAN SUR</option>
                                <option value="23">LA PAZ OESTE</option>
                                <option value="24">LA PAZ CENTRO</option>
                                <option value="25">LA PAZ ESTE</option>
                                <option value="10">CABAÑAS OESTE</option>
                                <option value="11">CABAÑAS ESTE</option>
                                <option value="14">SAN VICENTE NORTE</option>
                                <option value="15">SAN VICENTE SUR</option>
                                <option value="24">USULUTAN NORTE</option>
                                <option value="25">USULUTAN ESTE</option>
                                <option value="26">USULUTAN OESTE</option>
                                <option value="21">SAN MIGUEL NORTE</option>
                                <option value="22">SAN MIGUEL CENTRO</option>
                                <option value="23">SAN MIGUEL OESTE</option>
                                <option value="27">MORAZAN NORTE</option>
                                <option value="28">MORAZAN SUR</option>
                                <option value="19">LA UNION NORTE</option>
                                <option value="20">LA UNION SUR</option>
                              </select>
                        </div>

                        </div>

                        <!-- ENTRADA PARA LA DIRECCION -->

                        <div class="form-group">
                        <p>Dirección complementaria</p>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                          </div>
                          <input type="text" class="form-control" id="editarDireccionEmpresa" name="editarDireccionEmpresa" placeholder="Dirección complementaria" required>
                        </div>

                        </div>

                        <!-- ENTRADA PARA EL CORREO-->

                        <div class="form-group">
                        <p>Correo electrónico</p>
                        <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope"></i></span>
                              </div>
                              <input type="email" class="form-control" id="editarCorreoEmpresa" name="editarCorreoEmpresa" placeholder="Ingresar correo electrónico" required>
                        </div>

                        </div>

                        <!-- ENTRADA PARA EL NUMERO -->

                        <div class="form-group">
                        <p>Número telefónico</p>
                        <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-phone"></i></span>
                              </div>
                              <input type="text" class="form-control" id="editarNumeroEmpresa" name="editarNumeroEmpresa" placeholder="Ingresar número telefónico" required>
                        </div>

                        </div>
                </div>
                
                <div class="col-xl-6 col-xs-12">
                  <!-- ENTRADA PARA EL NIT -->

                    <div class="form-group">
                      <P>Colocarlo sin guiones</P>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="editarNITEmpresa" name="editarNITEmpresa" placeholder="NIT de la empresa" required>
                      </div>

                    </div>

                    <!-- ENTRADA PARA EL NRC -->

                    <div class="form-group">
                      <P>Colocarlo sin guiones</P>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="editarNRCEmpresa" name="editarNRCEmpresa" placeholder="NRC de la empresa">
                      </div>

                    </div>

                    <!-- ENTRADA PARA LA CONTRASEÑA PRI -->

                    <div class="form-group">
                      <P>Clave privada</P>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="editarPasswordPriEmpresa" name="editarPasswordPriEmpresa" placeholder="Password Pri de la empresa">
                      </div>

                    </div>

                    <!-- ENTRADA PARA EL CODIGO ACTIVIDAD -->

                    <div class="form-group">
                      <P>Código de actividad económica primaria</P>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="editarCodigoActividadEmpresa" name="editarCodigoActividadEmpresa" placeholder="Código actividad de la empresa">
                      </div>

                    </div>

                    <!-- ENTRADA PARA LA ACTIVIDAD -->

                    <div class="form-group">
                      <P>Actividad económica primaria</P>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="editarActividadEmpresa" name="editarActividadEmpresa" placeholder="Actividad de la empresa">
                      </div>

                    </div>

                    <!-- ENTRADA PARA EL TIPO ESTABLECIMIENTO -->

                    <div class="form-group">
                      <P>Código tipo establecimiento</P>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="editarEstablecimientoEmpresa" name="editarEstablecimientoEmpresa" placeholder="Tipo de Estable. de la empresa">
                      </div>

                    </div>

                    <!-- ENTRADA PARA CONTRASEÑA DE DESCUENTOS -->

                    <div class="form-group">
                      <P>Contraseña para autorización de descuentos</P>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="password" class="form-control" id="editarContraDescuento" name="editarContraDescuento" placeholder="Contraseña para descuentos">
                      </div>

                    </div>

                </div>

            </div>

            

            
            </div>
            </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Modificar Empresa</button>

        </div>

     <?php
          $editarEmpresa = new ControladorClientes();
          $editarEmpresa -> ctrEditarEmpresa();
        ?> 

      </form>

    </div>
    </div>
    </div>

  </div>

</div>