<?php

    if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Vendedor"){
    } else {
        echo '<script>
        window.location = "inicio";
        </script>';
    return;
    }

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
    <?php

        $item = "id";
        $valor = $_GET["idFacturaEditar"];
        $orden = "fecEmi";
        $optimizacion = "no";

        $factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $item = "id";
        $valor = $factura["id_cliente"];
        $orden = "id";

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

        $tipoDteTaba = $factura["tipoDte"];

        $tipoFacturaTexto = "";

        switch ($tipoDteTaba) {
            case "01":
                $tipoFacturaTexto = "Factura ".$factura["numeroControl"];
                break;
            case "03":
                $tipoFacturaTexto = "Comprobante de crédito fiscal ".$factura["numeroControl"];
                break;
            case "04":
                $tipoFacturaTexto = "Nota de remisión ".$factura["numeroControl"];
                break;
            case "05":
                $tipoFacturaTexto = "Nota de crédito ".$factura["numeroControl"];
                break;
            case "06":
                $tipoFacturaTexto = "Nota de débito ".$factura["numeroControl"];
                break;
            case "07":
                $tipoFacturaTexto = "Comprobante de retención ".$factura["numeroControl"];
                break;
            case "08":
                $tipoFacturaTexto = "Comprobante de liquidación ".$factura["numeroControl"];
                break;
            case "09":
                $tipoFacturaTexto = "Documento contable de liquidación ".$factura["numeroControl"];
                break;
            case "11":
                $tipoFacturaTexto = "Factura de exportación ".$factura["numeroControl"];
                break;
            case "14":
                $tipoFacturaTexto = "Factura de sujeto excluido ".$factura["numeroControl"];
                break;
            case "15":
                $tipoFacturaTexto = "Comprobante de donación ".$factura["numeroControl"];
                break;

            default:
                echo "Factura no válida";
                break;
        }
        $url = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=" . urlencode($factura["codigoGeneracion"]) . "&fechaEmi=" . urlencode($factura["fecEmi"]);
    ?>
    <?php
        if($factura["modo"] === "Normal"){
            echo '
                <button class="btn btn-success" onclick="location.href=\'facturacion\'">
          
          Regresar
          
    </button>
            ';
        } else {
            echo '
                <button class="btn btn-success" onclick="location.href=\'facturacion-contingencia\'">
          
          Regresar
          
    </button>
            ';
        }
    ?>

    
    <button class="btn btn-warning" onclick="location.href='<?php echo $url; ?>'">
          
          Ver en Hacienda

    </button>
    <button class="btn btn-info" onclick="location.href='extensiones/TCPDF-main/examples/imprimir-factura.php?idFactura='+<?php echo $factura['id'] ?>">
          
          Generar pdf

    </button>
    
    <button class="btn btn-primary btnEnviarFacturaCorreo" idFactura="<?php echo $factura['id'] ?>">Enviar factura a cliente</button>
    <?php
            if($factura["tipoDte"] === "04" || $factura["tipoDte"] === "05" || $factura["tipoDte"] === "06"){

            } else{
                echo '<button class="btn btn-dark" onclick="location.href=\'extensiones/TCPDF-main/examples/imprimir-ticket.php?idFactura=' . $factura['id']. '\'">Generar ticket</button>
                    <button class="btn btn-secondary editarTicket" data-toggle="modal" data-target="#modalEditarTicket">Configuración ticket</button>';
            }
        

        date_default_timezone_set('America/El_Salvador');

        // Fecha y hora de emisión
        $fecEmi = $factura["fecEmi"] . ' ' . $factura["horEmi"];
        
        // Obtener la fecha y hora actual
        $fechaActual = new DateTime(); // Fecha y hora actual
        
        // Crear un objeto DateTime con la fecha de emisión
        $fechaEmision = new DateTime($fecEmi);
        
        // Clonar la fecha de emisión y sumar 3 meses (para la primera validación)
        $fechaLimiteTresMeses = clone $fechaEmision;
        $fechaLimiteTresMeses->modify('+3 months')->setTime(23, 59, 59); // 3 meses después a las 23:59:59
        
        // Clonar la fecha de emisión y agregar 1 día (para la segunda validación)
        $fechaLimiteUnDia = clone $fechaEmision;
        $fechaLimiteUnDia->modify('+1 day')->setTime(23, 59, 59); // Día siguiente a las 23:59:59
        
        // Verificar si la fecha actual es anterior a 3 meses
        if ($fechaActual <= $fechaLimiteTresMeses) {
            // Aún no han pasado 3 meses
            // Verificar si la fecha actual coincide con el día que mh da
            if ($fechaActual <= $fechaLimiteUnDia) {
            
              if($factura["firmaDigital"] === ""){
                echo '<button class="btn btn-danger btnEliminarFactura" idFactura="'.$factura["id"].'">Eliminar factura</button>';
                } else {
                  if($factura["sello"] === ""){
                      echo '<button class="btn btn-danger btnEliminarFactura" idFactura="'.$factura["id"].'">Eliminar factura</button>';
                  } else {

                    if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Facturación"){
                      if($factura["estado"] != "Anulada"){
                        echo '<button class="btn btn-danger btnEliminarFacturaHacienda" idFactura="'.$factura["id"].'">Anular factura</button>';
                      } 
                    }
                  }
                  
              }
              if($factura["sello"] != "" && $factura["estado"] != "Anulada"){

                if($factura["tipoDte"] == "03") {
                  if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Facturación"){
                    echo '<button class="btn btn-info btnNotaCredito" idFactura="'.$factura["id"].'">NC</button>
                    <button class="btn btn-success btnNotaDebito" idFactura="'.$factura["id"].'">ND</button>
                    <button class="btn btn-dark btnNotaRemision" idFactura="'.$factura["id"].'">NR</button>';
                  }
                }
              }

            } else {
                if($factura["sello"] != "" && $factura["estado"] != "Anulada"){
                  if($factura["tipoDte"] == "03") {
                    if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Facturación"){
                      echo '<button class="btn btn-info btnNotaCredito" idFactura="'.$factura["id"].'">NC</button>
                      <button class="btn btn-success btnNotaDebito" idFactura="'.$factura["id"].'">ND</button>
                      <button class="btn btn-dark btnNotaRemision" idFactura="'.$factura["id"].'">NR</button>';
                    }
                  }

                    if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Facturación"){
                      if($factura["estado"] != "Anulada" && ($factura["tipoDte"] == "01" || $factura["tipoDte"] == "11")){
                        echo '<button class="btn btn-danger btnEliminarFacturaHacienda" idFactura="'.$factura["id"].'">Anular factura</button>';
                      }
                    }                   
                  
                } else {
                  echo '<button class="btn btn-danger btnEliminarFactura" idFactura="'.$factura["id"].'">Eliminar factura</button>';
                }
            }
        } else {
            // Han pasado más de 3 meses no hacer nada
        }
    
    
    ?>

<?php
// Se recomienda iniciar el búfer de salida para evitar que cualquier contenido
// accidental se imprima antes o después del HTML.
ob_start();

// Supongamos que ya tienes definido $factura con sus respectivos campos.
$jwt = $factura["firmaDigital"];
$codigoGeneracion = $factura["codigoGeneracion"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Descargar JSON</title>
</head>
<body>
    <!-- Botón que desencadena la descarga -->
    <button class="btn btn-success" onclick="descargarJSON()">Descargar JSON</button>
    <script>
        const jwt = "<?= addslashes($jwt) ?>";
        const selloRecibido = "<?= addslashes($factura['sello']) ?>";

        function descargarJSON() {
            const partes = jwt.split('.');
            if (partes.length < 2) {
                alert('El formato del JWT no es válido.');
                return;
            }

            const payloadBase64 = partes[1]
                .replace(/-/g, '+')
                .replace(/_/g, '/')
                .padEnd(Math.ceil(partes[1].length / 4) * 4, '=');

            try {
                const jsonStr = atob(payloadBase64);
                const data = JSON.parse(jsonStr);

                // Agregamos los campos extra
                data.firmaElectronica = jwt;
                data.selloRecibido = selloRecibido;

                const finalJsonStr = JSON.stringify(data, null, 4);
                const blob = new Blob([finalJsonStr], { type: 'application/json' });
                const url = URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = "<?= addslashes($codigoGeneracion) ?>.json";
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            } catch (error) {
                alert('Error al decodificar el JWT o generar el archivo JSON.');
                console.error(error);
            }
        }
    </script>
</body>
</html>
<?php
// Limpiamos el búfer de salida para evitar contenido residual
ob_end_flush();
?>



    <br><br>
    <h1>
      
      <?php echo($tipoFacturaTexto); ?>
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        Datos de facturación
<br><br>
      </div>

      
        
      <div class="box-body">
        
       <!--=====================================
        FORMULARIO CREAR FACTURA
        ======================================-->

        <form role="form" method="post" id="enviarFacturaLoca" enctype="multipart/form-data">

            <!--=====================================
            CABEZA DEL MODAL
            ======================================-->

            <div class="modal-header" style="background:grey; color:white">
                <h4 class="modal-title"><?php echo($tipoFacturaTexto); ?></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!--=====================================
            CUERPO DEL MODAL
            ======================================-->

            <div class="modal-body">

                <div class="box-body">

                <div class="row">

                    <div class="col-xl-1 col-xs-12">
                        <p style="font-weight: bold;">Número de control:</p>
                    </div>

                    <div class="col-xl-3 col-xs-12">
                        <p><?php echo $factura["numeroControl"] ?></p>
                    </div>

                    <div class="col-xl-2 col-xs-12">
                        <p style="font-weight: bold;">Código de generación:</p>
                    </div>

                    <div class="col-xl-2 col-xs-12">
                        <p><?php echo $factura["codigoGeneracion"] ?></p>
                    </div>

                    <div class="col-xl-2 col-xs-12">
                        <p style="font-weight: bold;">Firma digital:</p>
                    </div>

                    <div class="col-xl-2 col-xs-12">
                        <p><?php
                                if($factura["firmaDigital"] != ""){
                                    echo "Firmado";
                                } else {
                                    echo "Sin firmar";
                                }
                            ?>
                        </p>
                    </div>

                </div>
                <div class="row">

                        <div class="col-xl-1 col-xs-12">
                            <p style="font-weight: bold;">Sello HM:</p>
                        </div>

                        <div class="col-xl-4 col-xs-12">
                            <p><?php echo $factura["sello"] ?></p>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p style="font-weight: bold;">Fecha de emisión:</p>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p><?php echo $factura["fecEmi"] ?></p>
                        </div>

                        <div class="col-xl-1 col-xs-12">
                            <p style="font-weight: bold;">Hora de emisión:</p>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p><?php echo $factura["horEmi"] ?></p>
                        </div>

                </div>
                
                <?php
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
                ?>

                <div class="row">

                        <div class="col-xl-2 col-xs-12">
                            <p style="font-weight: bold;">Condición de la operación:</p>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p><?php echo $condicionTexto ?></p>
                        </div>
                        <?php
                            $estado = "";
                            if($factura["estado"] != "Anulada"){
                                $estado = "Activa";
                            } else {
                                $estado = "Anulada";
                            }
                        ?>
                        <div class="col-xl-1 col-xs-12">
                            <p style="font-weight: bold;">Estado:</p>
                        </div>

                        <div class="col-xl-1 col-xs-12">
                            <p><?php echo $estado ?></p>
                        </div>

                        <?php
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
                        ?>

                        <div class="col-xl-1 col-xs-12">
                            <p style="font-weight: bold;">Vendedor:</p>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p><?php echo $nombreVendedor ?></p>
                        </div>

                        <div class="col-xl-1 col-xs-12">
                            <p style="font-weight: bold;">Facturador:</p>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p><?php echo $nombreFacturador ?></p>
                        </div>
                        

                </div>

                    <!-- ENTRADA PARA EL CLIENTE -->
                    <div class="form-group">
                        Seleccionar cliente
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <input type="text" name="editarTipoDte" id="tipoDte" value="<?php echo $factura["tipoDte"] ?>" hidden>
                            <input type="text" id="productos" name="productos" hidden>
                            <select name="editarClienteFactura" id="nuevoClienteFactura" class="form-control" required readonly>
                                <?php

                                    $item = "id";
                                    $valor = $factura["id_cliente"];
                                    $orden = "id";

                                    $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
                                    echo '<option value="'.$cliente["id"].'">'.$cliente["nombre"].' '.$tipo.' </option>';
                                ?> 
                            </select>
                        </div>
                    </div>
                    

                    <!-- Contenedor donde se agregarán los productos -->
                    <div id="productosContainer">
                        <?php
                            // JSON en $factura["id_cliente"]
                            $jsonProductos = $factura["productos"];

                            // Decodificar el JSON en un array PHP
                            $productos = json_decode($jsonProductos, true); // true convierte el JSON en un array asociativo
                            $contador = count($productos);

                            echo '<input type="number" id="contador" hidden value="'.$contador.'">';
                            // Verificar si la decodificación fue exitosa
                            if (is_array($productos)) {
                                $totalGravado = 0.0;
                                // Recorrer e imprimir cada producto
                                foreach ($productos as $producto) {
                                    if($factura["tipoDte"] == "01" && ($cliente["tipo_cliente"] == "00" || $cliente["tipo_cliente"] == "01")){ //Factura, Persona normal y declarante de IVA
                                        echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                            <div class="row" id="productosContainer">
                                                <div class="col-xl-6 col-xs-12">
                                                    <div class="form-group">
                                                        Producto
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1">
                                                                    <span>1</span>
                                                                </span>
                                                            </div>
                                                            <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                
                                                                    $item = "id";
                                                                    $valor = $producto["idProducto"];
                                                                    
                                                                    $product = ControladorProductos::ctrMostrarProductos($item, $valor);

                                                                    echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                
                                                            echo '</select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Cantidad
                                                    
                                                    <div class="form-group">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Precio unitario sin impuestos
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Venta grabada individual (más IVA)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Total sin IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["totalProducto"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Total a disminuir por cada uno de los items - con iva
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control descuentoItem" name="descuentoItem[]" min="0" value="'.(($producto["descuento"]*0.13)+$producto["descuento"]).'" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Porcentaje de descuento según lo ingresado (ejemplo 40, 33, SIN EL PORCENTAJE)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" value="'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%" readonly>
                                                    </div>
                                                </div>

                                                <!-- Total -->
                                                <div class="col-xl-3 col-xs-12">
                                                    Total con IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioConIva"] - $producto["descuentoConIva"])*$producto["cantidad"].'">
                                                    </div>
                                                </div>

                                            </div>';
                                    }

                                    if($factura["tipoDte"] == "01" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ //Factura, Beneficios y Diplomático
                                        echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                            <div class="row" id="productosContainer">
                                                <div class="col-xl-6 col-xs-12">
                                                    <div class="form-group">
                                                        Producto
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1">
                                                                    <span>1</span>
                                                                </span>
                                                            </div>
                                                            <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                
                                                                    $item = "id";
                                                                    $valor = $producto["idProducto"];
                                                                    
                                                                    $product = ControladorProductos::ctrMostrarProductos($item, $valor);

                                                                    echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                
                                                            echo '</select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Cantidad
                                                    
                                                    <div class="form-group">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Precio unitario sin impuestos
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Descuento por cada item sin IVA
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["descuento"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Venta grabada individual (más IVA)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Porcentaje de descuento según lo ingresado (ejemplo 40, 33, SIN EL PORCENTAJE)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" value="'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Total sin IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioSinImpuestos"] - $producto["descuento"])*$producto["cantidad"].'">
                                                    </div>
                                                </div>

                                                <!-- Total -->
                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Total con IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioSinImpuestos"] - $producto["descuento"])*$producto["cantidad"].'">
                                                    </div>
                                                </div>

                                            </div>';
                                    }

                                    if($factura["tipoDte"] == "03" && ($cliente["tipo_cliente"] == "01")){ // CCF Contribuyente
                                        echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                            <div class="row" id="productosContainer">
                                                <div class="col-xl-6 col-xs-12">
                                                    <div class="form-group">
                                                        Producto
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1">
                                                                    <span>1</span>
                                                                </span>
                                                            </div>
                                                            <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                
                                                                    $item = "id";
                                                                    $valor = $producto["idProducto"];
                                                                    
                                                                    $product = ControladorProductos::ctrMostrarProductos($item, $valor);

                                                                    echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                
                                                            echo '</select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Cantidad
                                                    
                                                    <div class="form-group">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Precio unitario sin impuestos
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Precio unitario con IVA
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioConIva"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Total a disminuir por cada uno de los items con iva
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control descuentoItem" name="descuentoItem[]" min="0" value="'.$producto["descuentoConIva"].'" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Porcentaje de descuento según lo ingresado (ejemplo 40, 33, SIN EL PORCENTAJE)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" value="'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Venta grabada individual (más IVA)
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>';
                                                        if($product["exento_iva"] == "no"){
                                                            echo '<input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"]-$producto["descuentoConIva"].'">';
                                                        } else {
                                                            echo '<input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioSinImpuestos"]-$producto["descuento"].'">';
                                                        }
                                                        
                                                echo '</div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Total sin IVA
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioSinImpuestos"]*$producto["cantidad"].'">
                                                    </div>
                                                </div>

                                                <!-- Total -->
                                                <div class="col-xl-2 col-xs-12">
                                                    Total con IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>';
                                                        if($product["exento_iva"] == "no"){
                                                            echo '<input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioConIva"] - $producto["descuentoConIva"])*$producto["cantidad"].'">';
                                                        } else {
                                                            echo '<input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioSinImpuestos"] - $producto["descuento"])*$producto["cantidad"].'">';
                                                        }
                                                        
                                                echo '</div>
                                                </div>
                                                
                                                

                                            </div>';
                                    }

                                    if($factura["tipoDte"] == "03" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // CCF Beneficios y diploma
                                        echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                            <div class="row" id="productosContainer">
                                                <div class="col-xl-6 col-xs-12">
                                                    <div class="form-group">
                                                        Producto
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1">
                                                                    <span>1</span>
                                                                </span>
                                                            </div>
                                                            <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                
                                                                    $item = "id";
                                                                    $valor = $producto["idProducto"];
                                                                    
                                                                    $product = ControladorProductos::ctrMostrarProductos($item, $valor);

                                                                    echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                
                                                            echo '</select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Cantidad
                                                    
                                                    <div class="form-group">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Precio unitario sin impuestos
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Total a disminuir por cada uno de los items sin iva
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control descuentoItem" name="descuentoItem[]" min="0" value="'.$producto["descuento"].'" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Porcentaje de descuento según lo ingresado (ejemplo 40, 33, SIN EL PORCENTAJE)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" value="'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Venta grabada individual (más IVA)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Total sin IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioSinImpuestos"]-$producto["descuento"])*$producto["cantidad"].'">
                                                    </div>
                                                </div>

                                                <!-- Total -->
                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Total con IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioConIva"]*$producto["cantidad"].'">
                                                    </div>
                                                </div>

                                                

                                            </div>';
                                    }

                                    if($factura["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Expor Contribuyentes, Beneficios y diploma
                                        echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                            <div class="row" id="productosContainer">
                                                <div class="col-xl-6 col-xs-12">
                                                    <div class="form-group">
                                                        Producto
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1">
                                                                    <span>1</span>
                                                                </span>
                                                            </div>
                                                            <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                
                                                                    $item = "id";
                                                                    $valor = $producto["idProducto"];
                                                                    
                                                                    $product = ControladorProductos::ctrMostrarProductos($item, $valor);

                                                                    echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                
                                                            echo '</select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Cantidad
                                                    
                                                    <div class="form-group">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Precio unitario sin impuestos
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Total a disminuir por cada uno de los items sin iva
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control descuentoItem" name="descuentoItem[]" min="0" value="'.$producto["descuento"].'" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Porcentaje de descuento según lo ingresado (ejemplo 40, 33, SIN EL PORCENTAJE)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" value="'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Venta grabada individual (más IVA)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Total sin IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioSinImpuestos"]-$producto["descuento"])*$producto["cantidad"].'">
                                                    </div>
                                                </div>

                                                <!-- Total -->
                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Total con IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioConIva"]*$producto["cantidad"].'">
                                                    </div>
                                                </div>

                                            </div>';
                                    }
                                    
                                    if($factura["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){ // Sujeto excluido normal
                                        echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                            <div class="row" id="productosContainer">
                                                <div class="col-xl-6 col-xs-12">
                                                    <div class="form-group">
                                                        Producto
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1">
                                                                    <span>1</span>
                                                                </span>
                                                            </div>
                                                            <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                
                                                                    $item = "id";
                                                                    $valor = $producto["idProducto"];
                                                                    
                                                                    $product = ControladorProductos::ctrMostrarProductos($item, $valor);

                                                                    echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                
                                                            echo '</select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Cantidad
                                                    
                                                    <div class="form-group">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Precio unitario sin impuestos
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Total a disminuir por cada uno de los items - sin iva (si lleva iva se suma automaticamente)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control descuentoItem" name="descuentoItem[]" min="0" value="'.$producto["descuento"].'" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Porcentaje de descuento según lo ingresado (ejemplo 40, 33, SIN EL PORCENTAJE)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" value="'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Venta grabada individual (más IVA)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Total sin IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioSinImpuestos"]-$producto["descuento"])*$producto["cantidad"].'">
                                                    </div>
                                                </div>

                                                <!-- Total -->
                                                <div class="col-xl-2 col-xs-12" hidden>
                                                    Total con IVA
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioConIva"]*$producto["cantidad"].'">
                                                    </div>
                                                </div>


                                            </div>';
                                    }

                                    if($factura["tipoDte"] == "04"){

                                        if($factura["idFacturaRelacionada"] != ""){
                                            $item = "id";
                                            $orden = "id";
                                            $valor = $factura["idFacturaRelacionada"];
                                            $optimizacion = "no";

                                            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

                                            if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // Nota de remisión, ccf contribuyente
                                                echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                                    <div class="row" id="productosContainer">
                                                        <div class="col-xl-6 col-xs-12">
                                                            <div class="form-group">
                                                                Producto
                                                                
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1">
                                                                            <span>1</span>
                                                                        </span>
                                                                    </div>
                                                                    <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                        
                                                                            $item = "id";
                                                                            $valor = $producto["idProducto"];
                                                                            
                                                                            $product = ControladorProductos::ctrMostrarProductos($item, $valor);
        
                                                                            echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                        
                                                                    echo '</select>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Cantidad
                                                            
                                                            <div class="form-group">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                    </div>
                                                                    <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Precio unitario sin impuestos
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Venta grabada individual (más IVA)
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-2 col-xs-12">
                                                            Descuento sin IVA
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["descuento"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Total sin IVA
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioSinImpuestos"]*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
                                                        
        
                                                        <!-- Total -->
                                                        <div class="col-xl-2 col-xs-12">
                                                            Total con IVA
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioConIva"]*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }

                                            if($facturaOriginal["tipoDte"] == "03" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Nota de remisión, ccf beneficios y diplomas
                                                echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                                    <div class="row" id="productosContainer">
                                                        <div class="col-xl-6 col-xs-12">
                                                            <div class="form-group">
                                                                Producto
                                                                
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1">
                                                                            <span>1</span>
                                                                        </span>
                                                                    </div>
                                                                    <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                        
                                                                            $item = "id";
                                                                            $valor = $producto["idProducto"];
                                                                            
                                                                            $product = ControladorProductos::ctrMostrarProductos($item, $valor);
        
                                                                            echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                        
                                                                    echo '</select>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Cantidad
                                                            
                                                            <div class="form-group">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                    </div>
                                                                    <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Precio unitario sin impuestos
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Venta grabada individual (más IVA)
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-2 col-xs-12">
                                                            Descuento sin IVA
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["descuento"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Total sin IVA
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioSinImpuestos"]*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
        
                                                        <!-- Total -->
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Total con IVA
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioConIva"]*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }

                                            if($facturaOriginal["tipoDte"] == "11" && $cliente["tipo_cliente"] == "01"){ // Nota de remisión, export contribuyentes
                                                echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                                    <div class="row" id="productosContainer">
                                                        <div class="col-xl-6 col-xs-12">
                                                            <div class="form-group">
                                                                Producto
                                                                
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1">
                                                                            <span>1</span>
                                                                        </span>
                                                                    </div>
                                                                    <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                        
                                                                            $item = "id";
                                                                            $valor = $producto["idProducto"];
                                                                            
                                                                            $product = ControladorProductos::ctrMostrarProductos($item, $valor);
        
                                                                            echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                        
                                                                    echo '</select>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Cantidad
                                                            
                                                            <div class="form-group">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                    </div>
                                                                    <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Precio unitario sin impuestos
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Venta grabada individual (más IVA)
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-2 col-xs-12">
                                                            Descuento
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["descuento"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Total sin IVA
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioSinImpuestos"] - $producto["descuento"])*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
        
                                                        <!-- Total -->
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Total con IVA
                                                            <br><br>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioConIva"] - $producto["descuentoConIva"])*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }

                                            if($facturaOriginal["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Nota de remisión, export beneficios y diplomas
                                                echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                                    <div class="row" id="productosContainer">
                                                        <div class="col-xl-6 col-xs-12">
                                                            <div class="form-group">
                                                                Producto
                                                                
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1">
                                                                            <span>1</span>
                                                                        </span>
                                                                    </div>
                                                                    <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                        
                                                                            $item = "id";
                                                                            $valor = $producto["idProducto"];
                                                                            
                                                                            $product = ControladorProductos::ctrMostrarProductos($item, $valor);
        
                                                                            echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                        
                                                                    echo '</select>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Cantidad
                                                            
                                                            <div class="form-group">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                    </div>
                                                                    <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Precio unitario sin impuestos
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-2 col-xs-12">
                                                            Descuento sin IVA
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["descuento"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Venta grabada individual (más IVA)
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Total sin IVA
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioSinImpuestos"]*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
        
                                                        <!-- Total -->
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Total con IVA
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioConIva"]*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }
                                        }

                                        if($cliente["tipo_cliente"] == "01"){ // Nota de remisión, ccf contribuyente
                                            echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                                <div class="row" id="productosContainer">
                                                    <div class="col-xl-6 col-xs-12">
                                                        <div class="form-group">
                                                            Producto
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1">
                                                                        <span>1</span>
                                                                    </span>
                                                                </div>
                                                                <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                    
                                                                        $item = "id";
                                                                        $valor = $producto["idProducto"];
                                                                        
                                                                        $product = ControladorProductos::ctrMostrarProductos($item, $valor);
    
                                                                        echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                    
                                                                echo '</select>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <div class="col-xl-2 col-xs-12">
                                                        Cantidad
                                                        
                                                        <div class="form-group">
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <div class="col-xl-2 col-xs-12" hidden>
                                                        Precio unitario sin impuestos
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                        </div>
                                                    </div>
    
                                                    <div class="col-xl-2 col-xs-12">
                                                        Venta grabada individual (más IVA)
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-2 col-xs-12">
                                                        Descuento sin IVA
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["descuento"].'">
                                                        </div>
                                                    </div>
    
                                                    <div class="col-xl-2 col-xs-12" hidden>
                                                        Total sin IVA
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioSinImpuestos"]*$producto["cantidad"].'">
                                                        </div>
                                                    </div>
                                                    
    
                                                    <!-- Total -->
                                                    <div class="col-xl-2 col-xs-12">
                                                        Total con IVA
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioConIva"]*$producto["cantidad"].'">
                                                        </div>
                                                    </div>
                                                </div>';
                                        }

                                        if(($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Nota de remisión, ccf beneficios y diplomas
                                            echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                                <div class="row" id="productosContainer">
                                                    <div class="col-xl-6 col-xs-12">
                                                        <div class="form-group">
                                                            Producto
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1">
                                                                        <span>1</span>
                                                                    </span>
                                                                </div>
                                                                <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                    
                                                                        $item = "id";
                                                                        $valor = $producto["idProducto"];
                                                                        
                                                                        $product = ControladorProductos::ctrMostrarProductos($item, $valor);
    
                                                                        echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                    
                                                                echo '</select>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <div class="col-xl-2 col-xs-12">
                                                        Cantidad
                                                        
                                                        <div class="form-group">
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <div class="col-xl-2 col-xs-12">
                                                        Precio unitario sin impuestos
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["precioSinImpuestos"].'">
                                                        </div>
                                                    </div>
    
                                                    <div class="col-xl-2 col-xs-12" hidden>
                                                        Venta grabada individual (más IVA)
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-2 col-xs-12">
                                                        Descuento sin IVA
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["descuento"].'">
                                                        </div>
                                                    </div>
    
                                                    <div class="col-xl-2 col-xs-12">
                                                        Total sin IVA
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioSinImpuestos"]*$producto["cantidad"].'">
                                                        </div>
                                                    </div>
    
                                                    <!-- Total -->
                                                    <div class="col-xl-2 col-xs-12" hidden>
                                                        Total con IVA
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioConIva"]*$producto["cantidad"].'">
                                                        </div>
                                                    </div>
                                                </div>';
                                        }

                                                  
                                    }
                                    
                                    if($factura["tipoDte"] == "05"){
                                        
                                        if($producto["descuento"] != "0"){

                                            $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                                            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
                                            $totalGravado += $totalProF;

                                            $item = "id";
                                            $orden = "id";
                                            $valor = $factura["idFacturaRelacionada"];
                                            $optimizacion = "no";

                                            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

                                            if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // Nota de credito, ccf contribuyente
                                                echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                                    <div class="row" id="productosContainer">
                                                        <div class="col-xl-6 col-xs-12">
                                                            <div class="form-group">
                                                                Producto
                                                                
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1">
                                                                            <span>1</span>
                                                                        </span>
                                                                    </div>
                                                                    <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                        
                                                                            $item = "id";
                                                                            $valor = $producto["idProducto"];
                                                                            
                                                                            $product = ControladorProductos::ctrMostrarProductos($item, $valor);
        
                                                                            echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                        
                                                                    echo '</select>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Cantidad
                                                            
                                                            <div class="form-group">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                    </div>
                                                                    <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Precio unitario sin impuestos
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["descuento"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Venta grabada individual (más IVA)
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["descuento"]+($producto["descuento"]*0.13).'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Total sin IVA
                                                            <br><br>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioSinImpuestos"]*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
        
                                                        <!-- Total -->
                                                        <div class="col-xl-2 col-xs-12">
                                                            Total con IVA
                                                            <br><br>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["descuento"]+($producto["descuento"]*0.13))*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }

                                            if($facturaOriginal["tipoDte"] == "03" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Nota de credito, ccf beneficios y diplomas
                                                echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                                    <div class="row" id="productosContainer">
                                                        <div class="col-xl-6 col-xs-12">
                                                            <div class="form-group">
                                                                Producto
                                                                
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1">
                                                                            <span>1</span>
                                                                        </span>
                                                                    </div>
                                                                    <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                        
                                                                            $item = "id";
                                                                            $valor = $producto["idProducto"];
                                                                            
                                                                            $product = ControladorProductos::ctrMostrarProductos($item, $valor);
        
                                                                            echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                        
                                                                    echo '</select>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Cantidad
                                                            
                                                            <div class="form-group">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                    </div>
                                                                    <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Precio unitario sin impuestos
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["descuento"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Venta grabada individual (más IVA)
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["descuento"]+($producto["descuento"]*0.13).'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Total sin IVA
                                                            <br><br>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["descuento"]*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
        
                                                        <!-- Total -->
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Total con IVA
                                                            <br><br>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["descuento"]+($producto["descuento"]*0.13))*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }
                                            
                                        }
                                        
                                    }

                                    if($factura["tipoDte"] == "06"){
                                        
                                        if($producto["descuento"] != "0"){

                                            $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                                            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
                                            $totalGravado += $totalProF;

                                            $item = "id";
                                            $orden = "id";
                                            $valor = $factura["idFacturaRelacionada"];
                                            $optimizacion = "no";

                                            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

                                            if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // Nota de credito, ccf contribuyente
                                                echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                                    <div class="row" id="productosContainer">
                                                        <div class="col-xl-6 col-xs-12">
                                                            <div class="form-group">
                                                                Producto
                                                                
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1">
                                                                            <span>1</span>
                                                                        </span>
                                                                    </div>
                                                                    <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                        
                                                                            $item = "id";
                                                                            $valor = $producto["idProducto"];
                                                                            
                                                                            $product = ControladorProductos::ctrMostrarProductos($item, $valor);
        
                                                                            echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                        
                                                                    echo '</select>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Cantidad
                                                            
                                                            <div class="form-group">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                    </div>
                                                                    <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Precio unitario sin impuestos
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["descuento"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Venta grabada individual (más IVA)
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["descuento"]+($producto["descuento"]*0.13).'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Total sin IVA
                                                            <br><br>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["precioSinImpuestos"]*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
        
                                                        <!-- Total -->
                                                        <div class="col-xl-2 col-xs-12">
                                                            Total con IVA
                                                            <br><br>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["descuento"]+($producto["descuento"]*0.13))*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }

                                            if($facturaOriginal["tipoDte"] == "03" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Nota de credito, ccf beneficios y diplomas
                                                echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                                    <div class="row" id="productosContainer">
                                                        <div class="col-xl-6 col-xs-12">
                                                            <div class="form-group">
                                                                Producto
                                                                
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1">
                                                                            <span>1</span>
                                                                        </span>
                                                                    </div>
                                                                    <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                        
                                                                            $item = "id";
                                                                            $valor = $producto["idProducto"];
                                                                            
                                                                            $product = ControladorProductos::ctrMostrarProductos($item, $valor);
        
                                                                            echo '<option data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].'</option>';
                                                                        
                                                                    echo '</select>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Cantidad
                                                            
                                                            <div class="form-group">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                    </div>
                                                                    <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Precio unitario sin impuestos
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.$producto["descuento"].'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Venta grabada individual (más IVA)
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["descuento"]+($producto["descuento"]*0.13).'">
                                                            </div>
                                                        </div>
        
                                                        <div class="col-xl-2 col-xs-12">
                                                            Total sin IVA
                                                            <br><br>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["descuento"]*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
        
                                                        <!-- Total -->
                                                        <div class="col-xl-2 col-xs-12" hidden>
                                                            Total con IVA
                                                            <br><br>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["descuento"]+($producto["descuento"]*0.13))*$producto["cantidad"].'">
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }
                                            
                                        }
                                        
                                    }
                                    
                                }
                            } else {
                                echo "Error: El formato de los datos de productos es incorrecto.";
                            }

                        ?>
                    </div>

                    <div class="row">

                            <div class="col-xl-2 col-xs-12 ml-auto">
                                <p>Flete:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" class="form-control" readonly value="<?php echo $factura["flete"] ?>">
                                </div>

                                <p>Seguro:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" class="form-control" readonly value="<?php echo $factura["seguro"] ?>">
                                </div>
                            </div>

                    </div>

                    <?php
                        if($factura["tipoDte"] == "01" && ($cliente["tipo_cliente"] == "00" || $cliente["tipo_cliente"] == "01")){ //Factura, Persona normal y declarante de IVA
                            echo '<div class="row">

                                    <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                        <p>Total factura sin IVA:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="<?php echo $factura["totalSinIva"]+$factura["flete"]+$factura["seguro"] ?>">
                                        </div>
                                    </div>
            
                                    <div class="col-xl-2 col-xs-12 ml-auto">
                                        <p>Total factura con IVA:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["total"]+$factura["flete"]+$factura["seguro"].'">
                                        </div>
                                    </div>
            
                                </div>


                                <form role="form" method="post" enctype="multipart/form-data">

                                    <!--=====================================
                                    CABEZA DEL MODAL
                                    ======================================-->

                                    <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonar factura</h4>

                                    </div>

                                    <!--=====================================
                                    CUERPO DEL MODAL
                                    ======================================-->

                                    <div class="modal-body">

                                    <div class="box-body">

                                        <div class="row">
                                            <div class="col-xl-4 col-xs-12">
                                                Forma de pago:
                                                <br><br>
                                                <!-- ENTRADA PARA LA FORMA DE ABONO -->            
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoIdFacturaAbono" value="'.$factura["id"].'" hidden>
                                                        <select class="form-control" name="nuevaFormaAbono" value="" required>
                                                            <option value="" disabled selected>Seleccione una opción de pago</option>
                                                            <option value="Efectivo">Efectivo</option>
                                                            <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                                                            <option value="Tarjeta de débito">Tarjeta de débito</option>
                                                            <option value="Cheque">Cheque</option>
                                                            <option value="Transferencia">Transferencia</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-xs-12">
                                                <!-- ENTRADA PARA LA GESTIÓN FINANCIERA -->            
                                                <div class="form-group">
                                                    Gestión financiera, si es efectivo dejar en blanco, si tarjeta colocar el # de gestión, si es cheque el # de cheque y si es transferencia colocar el # de confirmación
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevaGestion" placeholder="Ingresar gestión financiera">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL BANCO -->
                                                Nombre del banco, si es efectivo dejar en blanco           
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoBanco" placeholder="Ingresar nombre del banco">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL MONTO -->            
                                                Monto a abonar
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoAbono" id="nuevoMontoAbono" placeholder="Ingresar monto a abonar" value="'.$factura["total"]+$factura["flete"]+$factura["seguro"].'" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL DINERO -->            
                                                Monto dinero con que el cliente paga
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoDinero" id="nuevoMontoDinero" placeholder="Ingresar monto a abonar" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL VUELTO -->            
                                                Vuelto
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoVuelto" id="nuevoMontoVuelto" placeholder="Ingresar monto a abonar" required readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                
                                    <!--=====================================
                                    PIE DEL MODAL
                                    ======================================-->

                                    <div class="modal-footer">

                                    <button type="submit" class="btn btn-dark">Guardar abono</button>

                                    </div>
                                </form>';

                                $crearAbono = new ControladorFacturas();
                                $crearAbono -> ctrCrearAbono();
                            ?>
                                <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonos a la factura</h4>
                                </div>
                                <br>

                                <table class="table table-bordered table-striped dt-responsive tablas" width="100%" style="font-size: 80%">
         
                                    <thead>
                                    
                                    <tr>
                                    
                                    <th style="width:10px">#</th>
                                    <th style="width:200px">Forma de abono</th>
                                    <th style="width:300px">Fecha y hora</th>
                                    <th style="width:300px">Gestión de financiera</th>
                                    <th style="width:20px !important">Banco</th>
                                    <th style="width:50px !important">Monto</th>
                            
                                    </tr> 
                            
                                    </thead>
                            
                                    <tbody>
                            
                                        <?php
                            
                                        $item = null;
                                        $valor = null;
                                        $orden = "fecha_abono";
                            
                                        $abonos = ControladorFacturas::ctrMostrarAbonos($item, $valor, $orden);

                                        $abonado = 0;
                                        if($abonos){
                                            foreach ($abonos as $key => $value){
                                                if($value["id_factura"] == $factura["id"]){
                                                    $abonado += $value["monto"];

                                                    echo ' <tr>
                                                        <td>'.($key+1).'</td>
                                                        <td>'.$value["forma_abono"].'</td>
                                                        <td>'.$value["fecha_abono"].'</td>
                                                        <td>'.$value["gestion_abono"].'</td>
                                                        <td>'.$value["banco"].'</td>
                                                        <td>$'.$value["monto"].'</td>
                                                        </tr>';
                                                    }
                                                }
                                        }
                                        
                            
                                        ?> 
                            
                                    </tbody>

                                    <h1>Monto abonado $<?php echo $abonado ?></h1>
                                    <h1>Monto restante $<?php echo (($factura["total"]+$factura["flete"]+$factura["seguro"]) - $abonado) ?></h1>
                            
                                </table>
                            <?php
                                
                        }

                        if($factura["tipoDte"] == "01" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ //Factura, Beneficios y diplomáticos
                            echo '<div class="row">

                                    <div class="col-xl-2 col-xs-12 ml-auto">
                                        <p>Total factura sin IVA:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                        </div>
                                    </div>
            
                                    <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                        <p>Total factura:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["total"]+$factura["flete"]+$factura["seguro"]).'">
                                        </div>
                                    </div>
                                </div>


                                <form role="form" method="post" enctype="multipart/form-data">

                                    <!--=====================================
                                    CABEZA DEL MODAL
                                    ======================================-->

                                    <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonar factura</h4>

                                    </div>

                                    <!--=====================================
                                    CUERPO DEL MODAL
                                    ======================================-->

                                    <div class="modal-body">

                                    <div class="box-body">

                                        <div class="row">
                                            <div class="col-xl-4 col-xs-12">
                                                Forma de pago:
                                                <br><br>
                                                <!-- ENTRADA PARA LA FORMA DE ABONO -->            
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoIdFacturaAbono" value="'.$factura["id"].'" hidden>
                                                        <select class="form-control" name="nuevaFormaAbono" value="" required>
                                                            <option value="" disabled selected>Seleccione una opción de pago</option>
                                                            <option value="Efectivo">Efectivo</option>
                                                            <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                                                            <option value="Tarjeta de débito">Tarjeta de débito</option>
                                                            <option value="Cheque">Cheque</option>
                                                            <option value="Transferencia">Transferencia</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-xs-12">
                                                <!-- ENTRADA PARA LA GESTIÓN FINANCIERA -->            
                                                <div class="form-group">
                                                    Gestión financiera, si es efectivo dejar en blanco, si tarjeta colocar el # de gestión, si es cheque el # de cheque y si es transferencia colocar el # de confirmación
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevaGestion" placeholder="Ingresar gestión financiera">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL BANCO -->
                                                Nombre del banco, si es efectivo dejar en blanco           
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoBanco" placeholder="Ingresar nombre del banco">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL MONTO -->            
                                                Monto a abonar
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoAbono" placeholder="Ingresar monto a abonar" required value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL DINERO -->            
                                                Monto dinero con que el cliente paga
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoDinero" id="nuevoMontoDinero" placeholder="Ingresar monto a abonar" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL VUELTO -->            
                                                Vuelto
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoVuelto" id="nuevoMontoVuelto" placeholder="Ingresar monto a abonar" required readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                
                                    <!--=====================================
                                    PIE DEL MODAL
                                    ======================================-->

                                    <div class="modal-footer">

                                    <button type="submit" class="btn btn-dark">Guardar abono</button>

                                    </div>
                                </form>';

                                $crearAbono = new ControladorFacturas();
                                $crearAbono -> ctrCrearAbono();
                            ?>
                                <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonos a la factura</h4>
                                </div>
                                <br>

                                <table class="table table-bordered table-striped dt-responsive tablas" width="100%" style="font-size: 80%">
         
                                    <thead>
                                    
                                    <tr>
                                    
                                    <th style="width:10px">#</th>
                                    <th style="width:200px">Forma de abono</th>
                                    <th style="width:300px">Fecha y hora</th>
                                    <th style="width:300px">Gestión de financiera</th>
                                    <th style="width:20px !important">Banco</th>
                                    <th style="width:50px !important">Monto</th>
                            
                                    </tr> 
                            
                                    </thead>
                            
                                    <tbody>
                            
                                        <?php
                            
                                        $item = null;
                                        $valor = null;
                                        $orden = "fecha_abono";
                            
                                        $abonos = ControladorFacturas::ctrMostrarAbonos($item, $valor, $orden);

                                        $abonado = 0;
                                        if($abonos){
                                            foreach ($abonos as $key => $value){
                                                if($value["id_factura"] == $factura["id"]){
                                                    $abonado += $value["monto"];

                                                    echo ' <tr>
                                                        <td>'.($key+1).'</td>
                                                        <td>'.$value["forma_abono"].'</td>
                                                        <td>'.$value["fecha_abono"].'</td>
                                                        <td>'.$value["gestion_abono"].'</td>
                                                        <td>'.$value["banco"].'</td>
                                                        <td>$'.$value["monto"].'</td>
                                                        </tr>';
                                                    }
                                                }
                                        }
                                        
                            
                                        ?> 
                            
                                    </tbody>

                                    <h1>Monto abonado $<?php echo $abonado ?></h1>
                                    <h1>Monto restante $<?php echo (($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"]) - $abonado) ?></h1>
                            
                                </table>
                            <?php
                        }

                        if($factura["tipoDte"] == "03" && ($cliente["tipo_cliente"] == "01")){ // CCF, Contribuyentes
                            echo '<div class="row">

                                    <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                        <p>Total factura sin IVA:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                        </div>
                                    </div>
            
                                    <div class="col-xl-2 col-xs-12 ml-auto">
                                        <p>Total factura:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["total"]+$factura["flete"]+$factura["seguro"]).'">
                                        </div>
                                    </div>
            
                                </div>


                                <form role="form" method="post" enctype="multipart/form-data">

                                    <!--=====================================
                                    CABEZA DEL MODAL
                                    ======================================-->

                                    <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonar factura</h4>

                                    </div>

                                    <!--=====================================
                                    CUERPO DEL MODAL
                                    ======================================-->

                                    <div class="modal-body">

                                    <div class="box-body">

                                        <div class="row">
                                            <div class="col-xl-4 col-xs-12">
                                                Forma de pago:
                                                <br><br>
                                                <!-- ENTRADA PARA LA FORMA DE ABONO -->            
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoIdFacturaAbono" value="'.$factura["id"].'" hidden>
                                                        <select class="form-control" name="nuevaFormaAbono" value="" required>
                                                            <option value="" disabled selected>Seleccione una opción de pago</option>
                                                            <option value="Efectivo">Efectivo</option>
                                                            <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                                                            <option value="Tarjeta de débito">Tarjeta de débito</option>
                                                            <option value="Cheque">Cheque</option>
                                                            <option value="Transferencia">Transferencia</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-xs-12">
                                                <!-- ENTRADA PARA LA GESTIÓN FINANCIERA -->            
                                                <div class="form-group">
                                                    Gestión financiera, si es efectivo dejar en blanco, si tarjeta colocar el # de gestión, si es cheque el # de cheque y si es transferencia colocar el # de confirmación
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevaGestion" placeholder="Ingresar gestión financiera">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL BANCO -->
                                                Nombre del banco, si es efectivo dejar en blanco           
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoBanco" placeholder="Ingresar nombre del banco">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL MONTO -->            
                                                Monto a abonar
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoAbono" placeholder="Ingresar monto a abonar" value="'.($factura["total"]+$factura["flete"]+$factura["seguro"]).'" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL DINERO -->            
                                                Monto dinero con que el cliente paga
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoDinero" id="nuevoMontoDinero" placeholder="Ingresar monto a abonar" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL VUELTO -->            
                                                Vuelto
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoVuelto" id="nuevoMontoVuelto" placeholder="Ingresar monto a abonar" required readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                
                                    <!--=====================================
                                    PIE DEL MODAL
                                    ======================================-->

                                    <div class="modal-footer">

                                    <button type="submit" class="btn btn-dark">Guardar abono</button>

                                    </div>
                                </form>';

                                $crearAbono = new ControladorFacturas();
                                $crearAbono -> ctrCrearAbono();
                            ?>
                                <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonos a la factura</h4>
                                </div>
                                <br>

                                <table class="table table-bordered table-striped dt-responsive tablas" width="100%" style="font-size: 80%">
         
                                    <thead>
                                    
                                    <tr>
                                    
                                    <th style="width:10px">#</th>
                                    <th style="width:200px">Forma de abono</th>
                                    <th style="width:300px">Fecha y hora</th>
                                    <th style="width:300px">Gestión de financiera</th>
                                    <th style="width:20px !important">Banco</th>
                                    <th style="width:50px !important">Monto</th>
                            
                                    </tr> 
                            
                                    </thead>
                            
                                    <tbody>
                            
                                        <?php
                            
                                        $item = null;
                                        $valor = null;
                                        $orden = "fecha_abono";
                            
                                        $abonos = ControladorFacturas::ctrMostrarAbonos($item, $valor, $orden);

                                        $abonado = 0;
                                        if($abonos){
                                            foreach ($abonos as $key => $value){
                                                if($value["id_factura"] == $factura["id"]){
                                                    $abonado += $value["monto"];

                                                    echo ' <tr>
                                                        <td>'.($key+1).'</td>
                                                        <td>'.$value["forma_abono"].'</td>
                                                        <td>'.$value["fecha_abono"].'</td>
                                                        <td>'.$value["gestion_abono"].'</td>
                                                        <td>'.$value["banco"].'</td>
                                                        <td>$'.$value["monto"].'</td>
                                                        </tr>';
                                                    }
                                                }
                                        }
                                        
                            
                                        ?> 
                            
                                    </tbody>

                                    <h1>Monto abonado $<?php echo $abonado ?></h1>
                                    <h1>Monto restante $<?php echo (($factura["total"]+$factura["flete"]+$factura["seguro"]) - $abonado) ?></h1>
                            
                                </table>
                            <?php
                        }

                        if($factura["tipoDte"] == "03" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // CCF, Beneficios y diplomaicos
                            echo '<div class="row">

                                    <div class="col-xl-2 col-xs-12 ml-auto">
                                        <p>Total factura sin IVA:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                        </div>
                                    </div>
            
                                    <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                        <p>Total factura:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["total"]+$factura["flete"]+$factura["seguro"]).'">
                                        </div>
                                    </div>
            
                                </div>


                                <form role="form" method="post" enctype="multipart/form-data">

                                    <!--=====================================
                                    CABEZA DEL MODAL
                                    ======================================-->

                                    <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonar factura</h4>

                                    </div>

                                    <!--=====================================
                                    CUERPO DEL MODAL
                                    ======================================-->

                                    <div class="modal-body">

                                    <div class="box-body">

                                        <div class="row">
                                            <div class="col-xl-4 col-xs-12">
                                                Forma de pago:
                                                <br><br>
                                                <!-- ENTRADA PARA LA FORMA DE ABONO -->            
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoIdFacturaAbono" value="'.$factura["id"].'" hidden>
                                                        <select class="form-control" name="nuevaFormaAbono" value="" required>
                                                            <option value="" disabled selected>Seleccione una opción de pago</option>
                                                            <option value="Efectivo">Efectivo</option>
                                                            <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                                                            <option value="Tarjeta de débito">Tarjeta de débito</option>
                                                            <option value="Cheque">Cheque</option>
                                                            <option value="Transferencia">Transferencia</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-xs-12">
                                                <!-- ENTRADA PARA LA GESTIÓN FINANCIERA -->            
                                                <div class="form-group">
                                                    Gestión financiera, si es efectivo dejar en blanco, si tarjeta colocar el # de gestión, si es cheque el # de cheque y si es transferencia colocar el # de confirmación
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevaGestion" placeholder="Ingresar gestión financiera">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL BANCO -->
                                                Nombre del banco, si es efectivo dejar en blanco           
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoBanco" placeholder="Ingresar nombre del banco">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL MONTO -->            
                                                Monto a abonar
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoAbono" placeholder="Ingresar monto a abonar" value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL DINERO -->            
                                                Monto dinero con que el cliente paga
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoDinero" id="nuevoMontoDinero" placeholder="Ingresar monto a abonar" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL VUELTO -->            
                                                Vuelto
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoVuelto" id="nuevoMontoVuelto" placeholder="Ingresar monto a abonar" required readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                
                                    <!--=====================================
                                    PIE DEL MODAL
                                    ======================================-->

                                    <div class="modal-footer">

                                    <button type="submit" class="btn btn-dark">Guardar abono</button>

                                    </div>
                                </form>';

                                $crearAbono = new ControladorFacturas();
                                $crearAbono -> ctrCrearAbono();
                            ?>
                                <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonos a la factura</h4>
                                </div>
                                <br>

                                <table class="table table-bordered table-striped dt-responsive tablas" width="100%" style="font-size: 80%">
         
                                    <thead>
                                    
                                    <tr>
                                    
                                    <th style="width:10px">#</th>
                                    <th style="width:200px">Forma de abono</th>
                                    <th style="width:300px">Fecha y hora</th>
                                    <th style="width:300px">Gestión de financiera</th>
                                    <th style="width:20px !important">Banco</th>
                                    <th style="width:50px !important">Monto</th>
                            
                                    </tr> 
                            
                                    </thead>
                            
                                    <tbody>
                            
                                        <?php
                            
                                        $item = null;
                                        $valor = null;
                                        $orden = "fecha_abono";
                            
                                        $abonos = ControladorFacturas::ctrMostrarAbonos($item, $valor, $orden);

                                        $abonado = 0;
                                        if($abonos){
                                            foreach ($abonos as $key => $value){
                                                if($value["id_factura"] == $factura["id"]){
                                                    $abonado += $value["monto"];

                                                    echo ' <tr>
                                                        <td>'.($key+1).'</td>
                                                        <td>'.$value["forma_abono"].'</td>
                                                        <td>'.$value["fecha_abono"].'</td>
                                                        <td>'.$value["gestion_abono"].'</td>
                                                        <td>'.$value["banco"].'</td>
                                                        <td>$'.$value["monto"].'</td>
                                                        </tr>';
                                                    }
                                                }
                                        }
                                        
                            
                                        ?> 
                            
                                    </tbody>

                                    <h1>Monto abonado $<?php echo $abonado ?></h1>
                                    <h1>Monto restante $<?php echo (($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"]) - $abonado) ?></h1>
                            
                                </table>
                            <?php
                        }

                        if($factura["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // EXPOR, Contribuyentes, Beneficios y diplomaicos
                            echo '<div class="row">

                                    <div class="col-xl-2 col-xs-12 ml-auto">
                                        <p>Total factura sin IVA:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                        </div>
                                    </div>
            
                                    <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                        <p>Total factura:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["total"]+$factura["flete"]+$factura["seguro"]).'">
                                        </div>
                                    </div>
            
                                </div>


                                <form role="form" method="post" enctype="multipart/form-data">

                                    <!--=====================================
                                    CABEZA DEL MODAL
                                    ======================================-->

                                    <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonar factura</h4>

                                    </div>

                                    <!--=====================================
                                    CUERPO DEL MODAL
                                    ======================================-->

                                    <div class="modal-body">

                                    <div class="box-body">

                                        <div class="row">
                                            <div class="col-xl-4 col-xs-12">
                                                Forma de pago:
                                                <br><br>
                                                <!-- ENTRADA PARA LA FORMA DE ABONO -->            
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoIdFacturaAbono" value="'.$factura["id"].'" hidden>
                                                        <select class="form-control" name="nuevaFormaAbono" value="" required>
                                                            <option value="" disabled selected>Seleccione una opción de pago</option>
                                                            <option value="Efectivo">Efectivo</option>
                                                            <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                                                            <option value="Tarjeta de débito">Tarjeta de débito</option>
                                                            <option value="Cheque">Cheque</option>
                                                            <option value="Transferencia">Transferencia</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-xs-12">
                                                <!-- ENTRADA PARA LA GESTIÓN FINANCIERA -->            
                                                <div class="form-group">
                                                    Gestión financiera, si es efectivo dejar en blanco, si tarjeta colocar el # de gestión, si es cheque el # de cheque y si es transferencia colocar el # de confirmación
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevaGestion" placeholder="Ingresar gestión financiera">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL BANCO -->
                                                Nombre del banco, si es efectivo dejar en blanco           
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoBanco" placeholder="Ingresar nombre del banco">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL MONTO -->            
                                                Monto a abonar
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoAbono" placeholder="Ingresar monto a abonar" value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL DINERO -->            
                                                Monto dinero con que el cliente paga
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoDinero" id="nuevoMontoDinero" placeholder="Ingresar monto a abonar" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL VUELTO -->            
                                                Vuelto
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoVuelto" id="nuevoMontoVuelto" placeholder="Ingresar monto a abonar" required readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                
                                    <!--=====================================
                                    PIE DEL MODAL
                                    ======================================-->

                                    <div class="modal-footer">

                                    <button type="submit" class="btn btn-dark">Guardar abono</button>

                                    </div>
                                </form>';

                                $crearAbono = new ControladorFacturas();
                                $crearAbono -> ctrCrearAbono();
                            ?>
                                <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonos a la factura</h4>
                                </div>
                                <br>

                                <table class="table table-bordered table-striped dt-responsive tablas" width="100%" style="font-size: 80%">
         
                                    <thead>
                                    
                                    <tr>
                                    
                                    <th style="width:10px">#</th>
                                    <th style="width:200px">Forma de abono</th>
                                    <th style="width:300px">Fecha y hora</th>
                                    <th style="width:300px">Gestión de financiera</th>
                                    <th style="width:20px !important">Banco</th>
                                    <th style="width:50px !important">Monto</th>
                            
                                    </tr> 
                            
                                    </thead>
                            
                                    <tbody>
                            
                                        <?php
                            
                                        $item = null;
                                        $valor = null;
                                        $orden = "fecha_abono";
                            
                                        $abonos = ControladorFacturas::ctrMostrarAbonos($item, $valor, $orden);

                                        $abonado = 0;
                                        if($abonos){
                                            foreach ($abonos as $key => $value){
                                                if($value["id_factura"] == $factura["id"]){
                                                    $abonado += $value["monto"];

                                                    echo ' <tr>
                                                        <td>'.($key+1).'</td>
                                                        <td>'.$value["forma_abono"].'</td>
                                                        <td>'.$value["fecha_abono"].'</td>
                                                        <td>'.$value["gestion_abono"].'</td>
                                                        <td>'.$value["banco"].'</td>
                                                        <td>$'.$value["monto"].'</td>
                                                        </tr>';
                                                    }
                                                }
                                        }
                                        
                            
                                        ?> 
                            
                                    </tbody>

                                    <h1>Monto abonado $<?php echo $abonado ?></h1>
                                    <h1>Monto restante $<?php echo (($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"]) - $abonado) ?></h1>
                            
                                </table>
                            <?php
                        }

                        if($factura["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){ // Sujeto excluido normal  
                            echo '<div class="row">

                                    <div class="col-xl-2 col-xs-12 ml-auto">
                                        <p>Total factura sin Renta:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-xs-12 ml-auto">
                                        <p>Renta:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"])*0.10.'">
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-xs-12 ml-auto">
                                        <p>Total factura con renta:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"])-(($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"])*0.10).'">
                                        </div>
                                    </div>
            
                                    <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                        <p>Total factura:</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["total"]+$factura["flete"]+$factura["seguro"]).'">
                                        </div>
                                    </div>
            
                                </div>


                                <form role="form" method="post" enctype="multipart/form-data">

                                    <!--=====================================
                                    CABEZA DEL MODAL
                                    ======================================-->

                                    <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonar factura</h4>

                                    </div>

                                    <!--=====================================
                                    CUERPO DEL MODAL
                                    ======================================-->

                                    <div class="modal-body">

                                    <div class="box-body">

                                        <div class="row">
                                            <div class="col-xl-4 col-xs-12">
                                                Forma de pago:
                                                <br><br>
                                                <!-- ENTRADA PARA LA FORMA DE ABONO -->            
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoIdFacturaAbono" value="'.$factura["id"].'" hidden>
                                                        <select class="form-control" name="nuevaFormaAbono" value="" required>
                                                            <option value="" disabled selected>Seleccione una opción de pago</option>
                                                            <option value="Efectivo">Efectivo</option>
                                                            <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                                                            <option value="Tarjeta de débito">Tarjeta de débito</option>
                                                            <option value="Cheque">Cheque</option>
                                                            <option value="Transferencia">Transferencia</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-xs-12">
                                                <!-- ENTRADA PARA LA GESTIÓN FINANCIERA -->            
                                                <div class="form-group">
                                                    Gestión financiera, si es efectivo dejar en blanco, si tarjeta colocar el # de gestión, si es cheque el # de cheque y si es transferencia colocar el # de confirmación
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevaGestion" placeholder="Ingresar gestión financiera">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL BANCO -->
                                                Nombre del banco, si es efectivo dejar en blanco           
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoBanco" placeholder="Ingresar nombre del banco">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL MONTO -->            
                                                Monto a abonar
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoAbono" placeholder="Ingresar monto a abonar" value="'.($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"])-(($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"])*0.10).'" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL DINERO -->            
                                                Monto dinero con que el cliente paga
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoDinero" id="nuevoMontoDinero" placeholder="Ingresar monto a abonar" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-xs-12">
                                                <!-- ENTRADA PARA EL VUELTO -->            
                                                Vuelto
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nuevoMontoVuelto" id="nuevoMontoVuelto" placeholder="Ingresar monto a abonar" required readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                
                                    <!--=====================================
                                    PIE DEL MODAL
                                    ======================================-->

                                    <div class="modal-footer">

                                    <button type="submit" class="btn btn-dark">Guardar abono</button>

                                    </div>
                                </form>';

                                $crearAbono = new ControladorFacturas();
                                $crearAbono -> ctrCrearAbono();
                            ?>
                                <div class="modal-header" style="background:grey; color:white">
                                    <h4 class="modal-title">Abonos a la factura</h4>
                                </div>
                                <br>

                                <table class="table table-bordered table-striped dt-responsive tablas" width="100%" style="font-size: 80%">
         
                                    <thead>
                                    
                                    <tr>
                                    
                                    <th style="width:10px">#</th>
                                    <th style="width:200px">Forma de abono</th>
                                    <th style="width:300px">Fecha y hora</th>
                                    <th style="width:300px">Gestión de financiera</th>
                                    <th style="width:20px !important">Banco</th>
                                    <th style="width:50px !important">Monto</th>
                            
                                    </tr> 
                            
                                    </thead>
                            
                                    <tbody>
                            
                                        <?php
                            
                                        $item = null;
                                        $valor = null;
                                        $orden = "fecha_abono";
                            
                                        $abonos = ControladorFacturas::ctrMostrarAbonos($item, $valor, $orden);

                                        $abonado = 0;
                                        if($abonos){
                                            foreach ($abonos as $key => $value){
                                                if($value["id_factura"] == $factura["id"]){
                                                    $abonado += $value["monto"];

                                                    echo ' <tr>
                                                        <td>'.($key+1).'</td>
                                                        <td>'.$value["forma_abono"].'</td>
                                                        <td>'.$value["fecha_abono"].'</td>
                                                        <td>'.$value["gestion_abono"].'</td>
                                                        <td>'.$value["banco"].'</td>
                                                        <td>$'.$value["monto"].'</td>
                                                        </tr>';
                                                    }
                                                }
                                        }
                                        
                            
                                        ?> 
                            
                                    </tbody>

                                    <h1>Monto abonado $<?php echo $abonado ?></h1>
                                    <h1>Monto restante $<?php echo ((($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"]) - $abonado)-($factura["totalSinIva"]*0.1)) ?></h1>
                            
                                </table>
                            <?php
                        }

                        if($factura["tipoDte"] == "04"){
                            if($factura["idFacturaRelacionada"] != ""){
                                $item = "id";
                                $orden = "id";
                                $valor = $factura["idFacturaRelacionada"];
                                $optimizacion = "no";

                                $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
                                
                                if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // Nota de remisión, ccf contribuyente
                                    echo '<div class="row">
        
                                            <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                                <p>Total factura sin IVA:</p>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                                </div>
                                            </div>
                    
                                            <div class="col-xl-2 col-xs-12 ml-auto">
                                                <p>Total factura:</p>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["total"]+$factura["flete"]+$factura["seguro"]).'">
                                                </div>
                                            </div>
                    
                                        </div>';
                                }

                                if($facturaOriginal["tipoDte"] == "03" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Nota de remisión, ccf beneficios y diplomas
                                    echo '<div class="row">
        
                                            <div class="col-xl-2 col-xs-12 ml-auto">
                                                <p>Total factura sin IVA:</p>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                                </div>
                                            </div>
                    
                                            <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                                <p>Total factura:</p>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["total"]+$factura["flete"]+$factura["seguro"]).'">
                                                </div>
                                            </div>
                    
                                        </div>';
                                }

                                if($facturaOriginal["tipoDte"] == "11" && $cliente["tipo_cliente"] == "01"){ // Nota de remisión, export contribuyente
                                    echo '<div class="row">
        
                                            <div class="col-xl-2 col-xs-12 ml-auto">
                                                <p>Total factura sin IVA:</p>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"]).'">
                                                </div>
                                            </div>
                    
                                            <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                                <p>Total factura:</p>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"]).'">
                                                </div>
                                            </div>
                    
                                        </div>';
                                }

                                if($facturaOriginal["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Nota de remisión, export beneficios y diplomas
                                    echo '<div class="row">
        
                                            <div class="col-xl-2 col-xs-12 ml-auto">
                                                <p>Total factura sin IVA:</p>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"]).'">
                                                </div>
                                            </div>
                    
                                            <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                                <p>Total factura:</p>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["totalSinIva"]+$factura["flete"]+$factura["seguro"]).'">
                                                </div>
                                            </div>
                    
                                        </div>';
                                }
                            }
                            
                            if($cliente["tipo_cliente"] == "01"){ // Nota de remisión, ccf contribuyente
                                echo '<div class="row">
    
                                        <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                            <p>Total factura sin IVA:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                            </div>
                                        </div>
                
                                        <div class="col-xl-2 col-xs-12 ml-auto">
                                            <p>Total factura:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["total"]+$factura["flete"]+$factura["seguro"]).'">
                                            </div>
                                        </div>
                
                                    </div>';
                            }

                            if(($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Nota de remisión, ccf beneficios y diplomas
                                echo '<div class="row">
    
                                        <div class="col-xl-2 col-xs-12 ml-auto">
                                            <p>Total factura sin IVA:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                            </div>
                                        </div>
                
                                        <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                            <p>Total factura:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.($factura["total"]+$factura["flete"]+$factura["seguro"]).'">
                                            </div>
                                        </div>
                
                                    </div>';
                            }
                        }

                        if($factura["tipoDte"] == "05"){
                            $item = "id";
                            $orden = "id";
                            $valor = $factura["idFacturaRelacionada"];
                            $optimizacion = "no";

                            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
                            
                            if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // Nota de crédito, ccf contribuyente
                                echo '<div class="row">
    
                                        <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                            <p>Total factura sin IVA:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                            </div>
                                        </div>
                
                                        <div class="col-xl-2 col-xs-12 ml-auto">
                                            <p>Total factura:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$totalGravado+($totalGravado*0.13).'">
                                            </div>
                                        </div>
                
                                    </div>';
                            }

                            if($facturaOriginal["tipoDte"] == "03" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Nota de crédito, ccf beneficios diplomas
                                echo '<div class="row">
    
                                        <div class="col-xl-2 col-xs-12 ml-auto">
                                            <p>Total factura sin IVA:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$totalGravado.'">
                                            </div>
                                        </div>
                
                                        <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                            <p>Total factura:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$totalGravado+($totalGravado*0.13).'">
                                            </div>
                                        </div>
                
                                    </div>';
                            }
                        }

                        if($factura["tipoDte"] == "06"){
                            $item = "id";
                            $orden = "id";
                            $valor = $factura["idFacturaRelacionada"];
                            $optimizacion = "no";

                            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
                            
                            if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // Nota de crédito, ccf contribuyente
                                echo '<div class="row">
    
                                        <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                            <p>Total factura sin IVA:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$factura["totalSinIva"]+$factura["flete"]+$factura["seguro"].'">
                                            </div>
                                        </div>
                
                                        <div class="col-xl-2 col-xs-12 ml-auto">
                                            <p>Total factura:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$totalGravado+($totalGravado*0.13).'">
                                            </div>
                                        </div>
                
                                    </div>';
                            }

                            if($facturaOriginal["tipoDte"] == "03" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Nota de crédito, ccf beneficios diplomas
                                echo '<div class="row">
    
                                        <div class="col-xl-2 col-xs-12 ml-auto">
                                            <p>Total factura sin IVA:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$totalGravado.'">
                                            </div>
                                        </div>
                
                                        <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                            <p>Total factura:</p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="nuevoTotalFactura" id="nuevaCantidadProductoFactura" readonly value="'.$totalGravado+($totalGravado*0.13).'">
                                            </div>
                                        </div>
                
                                    </div>';
                            }
                        }
                    ?>
                    
                    <div style="background-color: grey">
                        <h5 style="color: white; padding: 15px">Json de la factura</h5>
                        <?php
                            
                            // Decodificar JSON
                            $json_data = json_decode($factura["json_guardado"], true);
                            
                            // Si el JSON es válido
                            if ($json_data !== null) {
                                $json_pretty = json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                                $file_name = "factura.json";
                                file_put_contents($file_name, $json_pretty);
                                
                                // Convertimos el JSON en un array de líneas
                                $lines = explode("\n", $json_pretty);
                                $total_lines = count($lines);
                                
                                // Dividimos en 3 columnas equilibradas
                                $col1 = array_slice($lines, 0, ceil($total_lines / 3));
                                $col2 = array_slice($lines, ceil($total_lines / 3), ceil($total_lines / 3));
                                $col3 = array_slice($lines, 2 * ceil($total_lines / 3));
                            
                                echo "<style>
                                    .container { display: flex; gap: 10px; }
                                    .column { width: 33%; background: #f4f4f4; padding: 10px; white-space: pre-wrap; font-family: monospace; }
                                </style>";
                            
                                echo "<div class='container'>";
                                echo "<div class='column'>" . implode("\n", $col1) . "</div>";
                                echo "<div class='column'>" . implode("\n", $col2) . "</div>";
                                echo "<div class='column'>" . implode("\n", $col3) . "</div>";
                                echo "</div> <br>";
                                 // Botón de descarga
                                echo "<br><a style='padding: 20px' href='$file_name' download><button class='btn btn-info' type='button'>Descargar JSON</button></a><br><br>";
                            } else {
                                echo "Factura no firmada";
                            }
                                                    
                        ?>
                    </div> 
                    

                </div>
            </div>
            <!--=====================================
            PIE DEL MODAL
            ======================================-->

            
        </form>

        </div>

    </div>


  </section>

</div>

<!--=====================================
MODAL EDITAR MOTORISTA
======================================-->

<div id="modalEditarTicket" class="modal fade" role="dialog">
  
  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Editar ancho del ticket</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
            Ancho del ticket en milimetros:
            <!-- ENTRADA PARA EL NOMBRE -->            
            <div class="form-group">
              <div class="input-group mb-3">
                
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                </div>
                <input type="number" class="form-control" name="editarAnchoTicket" id="editarAnchoTicket" min="1" required>
              </div>
            </div>
        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-dark">Guardar configuración</button>

        </div>

        <?php

          $editarTicket = new ControladorClientes();
          $editarTicket -> ctrEditarTicket();

        ?>

      </form>

      </div>

    </div>

    </div>

  </div>

</div>

<?php

  $envarFactura = new ControladorFacturas();
  $envarFactura -> ctrEnviarFacturaCorreo();

?>