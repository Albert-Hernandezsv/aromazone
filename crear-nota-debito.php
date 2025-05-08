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
    <?php
        $item = "id";
        $valor = $_GET["idFactura"];
        $orden = "id";
        $optimizacion = "no";

        $factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $item = "id";
        $valor = $factura["id_cliente"];
        $orden = "id";

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

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
    <button class="btn btn-primary" onclick="location.href='facturacion'">
          
          Regresar

    </button>
    <br><br>
    <h1>
      
      <?php
      $tipoFacturaTexto = "";
        switch ($factura["tipoDte"]) {
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

        echo ("Nota de débito para ".$tipoFacturaTexto);
      ?>
    
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
            CABEZA DEL MODAL
            ======================================-->

            <div class="modal-header" style="background:grey; color:white">
                <h4 class="modal-title"><?php echo($tipoFacturaTexto); ?></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!--=====================================
            FORMULARIO CREAR FACTURA
            ======================================-->

            <form role="form" method="post" id="enviarFacturaLoca" enctype="multipart/form-data">

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
                ?>

                <div class="row">

                        <div class="col-xl-2 col-xs-12">
                            <p style="font-weight: bold;">Condición de la operación:</p>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p><?php echo $condicionTexto ?></p>
                        </div>

                </div>
                    <!-- ENTRADA PARA EL CLIENTE -->
                    <div class="form-group">
                        Seleccionar cliente
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <input type="text" name="tipoDte" id="tipoDte" value="06" hidden>
                            <input type="text" id="productos" name="productos" hidden>
                            <input name="condicionOperacion" id="condicionOperacion" value="<?php echo $factura["condicionOperacion"] ?>" class="form-control" hidden>
                            <input name="idFacturaRelacionada" id="idFacturaRelacionada" value="<?php echo $factura["id"] ?>" class="form-control" hidden>
                            <select name="nuevoClienteFactura" id="nuevoClienteFactura" class="form-control" required readonly>
                                <?php

                                    $item = "id";
                                    $valor = $factura["id_cliente"];
                                    $orden = "id";

                                    $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
                                    echo '<option value="'.$cliente["id"].'">'.$cliente["nombre"].' - '.$tipo.'</option>';
                                ?> 
                            </select>
                        </div>
                    </div>

                    <!-- ENTRADA PARA EL VENDEDOR Y QUIEN FACTURA -->
                    <div class="form-group">
                        <p>Vendedor que realiza la venta:</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                            </div>
                            <input type="text" name="nuevoFacturadorId" value="<?php echo $_SESSION["id"]?>" hidden>
                            <select name="nuevoVendedorId" class="form-control" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                                <?php
                                     $item = null;
                                     $valor = null;
                             
                                     $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                             
                                    foreach ($usuarios as $key => $value){
                                        echo '
                                            <option value="'.$value["id"].'">'.$value["nombre"]." - ".$value["rol"].'</option>
                                        ';
                                    }
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
                                // Recorrer e imprimir cada producto
                                foreach ($productos as $producto) {
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
                                                            <select name="" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                
                                                                    $item = "id";
                                                                    $valor = $producto["idProducto"];
                                                                    
                                                                    $product = ControladorProductos::ctrMostrarProductos($item, $valor);

                                                                    echo '<option data-exento="'.$product["exento_iva"].'"  data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'" data-codigo="'.$product["codigo"].'">'.$product["nombre"].' - '.$product["codigo"].'</option>';
                                                                
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
                                                            <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'" min="0" max="'.$producto["cantidad"].'">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Precio unitario sin impuestos
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly value="'.($producto["precioSinImpuestos"] - $producto["descuento"]).'">
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Venta grabada individual (más IVA)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.($producto["precioConIva"] - $producto["descuentoConIva"]).'">
                                                    </div>
                                                </div>

                                                <!-- Total -->
                                                <div class="col-xl-2 col-xs-12">
                                                    <br><br>
                                                    Total sin IVA
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividualSin[]" readonly value="'.($producto["precioSinImpuestos"] - $producto["descuento"])*$producto["cantidad"].'">
                                                    </div>
                                                </div>

                                                <!-- Total -->
                                                <div class="col-xl-2 col-xs-12">
                                                <br><br>
                                                    Total
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" value="'.($producto["precioConIva"] - $producto["descuentoConIva"])*$producto["cantidad"].'" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-3 col-xs-12">
                                                    Total a aumentar a cada item individualmente - colocar sin iva (si lleva iva se suma automaticamente) y si es gran contribuyente se calcula solo (ver en pdf)
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control descuentoItem" name="descuentoItem[]" min="0" max="'.$producto["precioSinImpuestos"].'" step="0.01">
                                                    </div>
                                                </div>
                                            </div>';
                                }
                            } else {
                                echo "Error: El formato de los datos de productos es incorrecto.";
                            }

                        ?>
                    </div>

                    <div class="row">

                        <div class="col-xl-2 col-xs-12 ml-auto">
                            <p>Total factura sin IVA:</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                </div>
                                <input type="number" class="form-control" name="nuevoTotalFacturaSin" id="" readonly value="<?php echo $factura["totalSinIva"]+$factura["flete"]+$factura["seguro"] ?>">
                            </div>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p>Total factura:</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                </div>
                                <input type="number" class="form-control" name="nuevoTotalFactura" id="" readonly value="<?php echo $factura["total"]+$factura["flete"]+$factura["seguro"] ?>">
                            </div>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p>Total aumento sin IVA:</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                </div>
                                <input type="number" class="form-control" name="totalDescuentoSin" id="" readonly>
                            </div>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p>Total aumento con IVA:</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                </div>
                                <input type="number" class="form-control" name="totalDescuento" id="" readonly>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <!--=====================================
            PIE DEL MODAL
            ======================================-->


            <div class="modal-footer">
                <button type="submit" class="btn btn-dark">Crear factura localmente</button>
            </div>

        </form>

        <?php

          $crearFactura = new ControladorFacturas();
          $crearFactura -> ctrCrearFactura();

        ?>
        <p>En la nota de débito el total reflejado si es el total a sumar a la factura original</p>
        </div>

    </div>


  </section>

</div>