<?php
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	use \Firebase\JWT\JWT;
	date_default_timezone_set('America/El_Salvador');

class ControladorFacturas{

	/*=============================================
	MOSTRAR FACTURAS
	=============================================*/

	static public function ctrMostrarFacturas($item, $valor, $orden, $optimizacion){

		$tabla = "facturas_locales";

		if($optimizacion == "si"){
			$respuesta = ModeloFacturas::MdlMostrarFacturasOptimizadas($tabla, $item, $valor, $orden);
		} else {
			$respuesta = ModeloFacturas::MdlMostrarFacturas($tabla, $item, $valor, $orden);
		}		

		return $respuesta;
	}

	static public function ctrMostrarFacturasVarias($item, $valor, $orden, $optimizacion){

		$tabla = "facturas_locales";

		$respuesta = ModeloFacturas::MdlMostrarFacturasVarias($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	static public function ctrMostrarFacturasCortes($item, $valor, $orden, $optimizacion){

		$tabla = "facturas_locales";

		$respuesta = ModeloFacturas::MdlMostrarFacturasCortes($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	static public function ctrMostrarFacturasDash($item, $valor, $orden, $optimizacion){

		$tabla = "facturas_locales";

		$respuesta = ModeloFacturas::MdlMostrarFacturasDash($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	static public function ctrMostrarFacturasAsc($item, $valor, $orden, $optimizacion){

		$tabla = "facturas_locales";

		if($optimizacion == "si"){
			$respuesta = ModeloFacturas::MdlMostrarFacturasOptimizadas($tabla, $item, $valor, $orden);
		} else {
			$respuesta = ModeloFacturas::MdlMostrarFacturasAsc($tabla, $item, $valor, $orden);
		}		

		return $respuesta;
	}

	static public function ctrMostrarFacturasVentas($fechaInicio, $fechaFin){

		$tabla = "facturas_locales";

		$respuesta = ModeloFacturas::MdlMostrarFacturasVentas($tabla, $fechaInicio, $fechaFin);

		return $respuesta;
	}

	static public function ctrMostrarFacturasFechaOptimizada($fechaOptimizada){

		$tabla = "facturas_locales";

		$respuesta = ModeloFacturas::MdlMostrarFacturasFechaOptimizada($tabla, $fechaOptimizada);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR ABONOS
	=============================================*/

	static public function ctrMostrarAbonos($item, $valor, $orden){

		$tabla = "formas_pago";

		$respuesta = ModeloFacturas::MdlMostrarAbonos($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR CORTES
	=============================================*/

	static public function ctrMostrarCortes($item, $valor, $orden){

		$tabla = "cortes_caja";

		$respuesta = ModeloFacturas::MdlMostrarCortes($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR EVENTOS CONTINGENCAS
	=============================================*/

	static public function ctrMostrarEventosContingencias($item, $valor, $orden){

		$tabla = "contingencias";

		$respuesta = ModeloFacturas::MdlMostrarEventosContingencias($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR ANULACIONES
	=============================================*/

	static public function ctrMostrarAnulaciones($item, $valor, $orden, $optimizacion){

		$tabla = "anuladas";

		if($optimizacion == "si"){
			$respuesta = ModeloFacturas::mdlMostrarFacturasOptimizadas($tabla, $item, $valor, $orden);
		} else {
			$respuesta = ModeloFacturas::MdlMostrarFacturas($tabla, $item, $valor, $orden);
		}

		return $respuesta;
	}

	static public function ctrMostrarAnulacionesFechaOptimizada($fechaOptimizada){

		$tabla = "anuladas";

		$respuesta = ModeloFacturas::MdlMostrarFacturasFechaOptimizada($tabla, $fechaOptimizada);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR COMPRAS
	=============================================*/

	static public function ctrMostrarCompras($item, $valor, $orden, $optimizacion){

		$tabla = "compras";

		if($optimizacion == "si"){
			$respuesta = ModeloFacturas::MdlMostrarComprasOptimizadas($tabla, $item, $valor, $orden);
		} else {
			$respuesta = ModeloFacturas::MdlMostrarCompras($tabla, $item, $valor, $orden);
		}

		return $respuesta;
	}

	/*=============================================
	REGISTRO DE FACTURA LOCAL
	=============================================*/

	static public function ctrCrearFactura(){

		if(isset($_POST["nuevoClienteFactura"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoClienteFactura"])){

				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = $empresarial["numeroControlGeneral"];

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = 'S001P001';

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = "";
				
				if($_POST["tipoDte"] == "01"){ // Factura
					// Construir el nuevo número de control
					$numeroControl = 'DTE-01-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}
				if($_POST["tipoDte"] == "03"){ // CCF
					// Construir el nuevo número de control
					$numeroControl = 'DTE-03-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}
				
				$recintoFiscal = "";
				$regimen = "";
				$modoTransporte = "";
				$seguro = 0.0;
				$flete = 0.0;
				$idMotorista = "";

				if($_POST["tipoDte"] == "11"){ // Exportación
					// Construir el nuevo número de control
					$numeroControl = 'DTE-11-' . $parteAleatoria . '-' . $parteNumericaIncrementada;

					$recintoFiscal = $_POST["recintoFiscal"];
					$regimen = $_POST["regimen"];
					$modoTransporte = $_POST["modoTransporte"];
					$seguro = $_POST["seguro"];
					$flete = $_POST["flete"];
					$idMotorista = $_POST["idMotorista"];
				}

				if($_POST["tipoDte"] == "14"){ // Sujeto excluido
					// Construir el nuevo número de control
					$numeroControl = 'DTE-14-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}

				$idFacturaRelacionada = "";

				if($_POST["tipoDte"] == "05"){ // Nota de crédito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-05-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}

				if($_POST["tipoDte"] == "06"){ // Nota de débito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-06-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}
			
				$tabla = "emisor";
				$item1 = "numeroControlGeneral";
				$valor1 = $numeroControl;
				$item2 = "id";
				$valor2 = "1";
				

				$actualizarNumeroControl = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
				
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			
				$notaRemi = "";
				$estado = "Activa";

				$ordenCompra = "";
				$incoterm = "";
				$origen = "";
				if(isset($_POST["orden_compra"])){
					$ordenCompra = $_POST["orden_compra"];
				}
				if(isset($_POST["incoterm"])){
					$incoterm = $_POST["incoterm"];
				}
				if(isset($_POST["origen"])){
					$origen = $_POST["origen"];
				}

				$granContribuyente = "No";
				if($_POST["granContribuyente"]){
					$granContribuyente = $_POST["granContribuyente"];
				}

				$datos = array("id_cliente" => $_POST["nuevoClienteFactura"],
							   "productos" => $_POST["productos"],
							   "total" => $_POST["nuevoTotalFactura"],
							   "totalSinIva" => $_POST["nuevoTotalFacturaSin"],
							   "tipoDte" => $_POST["tipoDte"],

							   "gran_contribuyente" => $granContribuyente,

							   "recintoFiscal" => $recintoFiscal,
							   "regimen" => $regimen,
							   "modoTransporte" => $modoTransporte,
							   "seguro" => $seguro,
							   "flete" => $flete,
							   "idMotorista" => $idMotorista,

							   "idFacturaRelacionada" => $idFacturaRelacionada,
							   "notaRemision" => $notaRemi,
							   "estado" => $estado,

							   "modo" => "Normal",

							   "id_vendedor" => $_POST["nuevoVendedorId"],
							   "id_usuario" => $_POST["nuevoFacturadorId"],

							   "condicionOperacion" => $_POST["condicionOperacion"],
							   "numeroControl" => $numeroControl,
								"codigoGeneracion" => $codigoGeneracion,
								"horEmi" => $horEmi,
								"fecEmi" => $fecEmi,
								"orden_compra" => $ordenCompra,
								"incoterm" => $incoterm,
								"origen" => $origen);

				

				$productos = json_decode($_POST["productos"], true);

				if($_POST["tipoDte"] == "05" || $_POST["tipoDte"] == "06"){
					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFactura($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){
	
						echo '<script>
	
						swal({
	
							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
	
						}).then(function(result){
	
							if(result.value){
							
								window.location = "contabilidad";
	
							}
	
						});
					
	
						</script>';
	
	
					}
						
				} else {
					foreach ($productos as $producto) {

						// DESCARGAR STOCK FACTURADO
						$item = "id";
						$valor = $producto["idProducto"];
				
						$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
				
						$stockActual = $productoTraido["stock"];
						$stockNuevo = $stockActual - $producto["cantidad"];
				
						$tabla = "inventario";
						$item1 = "stock";
						$valor1 = $stockNuevo;
						$item2 = "id";
						$valor2 = $producto["idProducto"];
				
						$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
					}

					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFactura($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){

						echo '<script>

						swal({

							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"

						}).then(function(result){

							if(result.value){
							
								window.location = "facturacion";

							}

						});
					

						</script>';


					}
				}
				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La factura no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	REGISTRO DE FACTURA LOCAL CONTINGENCIA
	=============================================*/

	static public function ctrCrearFacturaContingencia(){

		if(isset($_POST["nuevoClienteFactura"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoClienteFactura"])){

				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = $empresarial["numeroControlGeneral"];

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = 'S001P001';
				

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = "";
				
				if($_POST["tipoDte"] == "01"){ // Factura
					// Construir el nuevo número de control
					$numeroControl = 'DTE-01-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}
				if($_POST["tipoDte"] == "03"){ // CCF
					// Construir el nuevo número de control
					$numeroControl = 'DTE-03-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}
				
				$recintoFiscal = "";
				$regimen = "";
				$modoTransporte = "";
				$seguro = 0.0;
				$flete = 0.0;
				$idMotorista = "";

				if($_POST["tipoDte"] == "11"){ // Exportación
					// Construir el nuevo número de control
					$numeroControl = 'DTE-11-' . $parteAleatoria . '-' . $parteNumericaIncrementada;

					$recintoFiscal = $_POST["recintoFiscal"];
					$regimen = $_POST["regimen"];
					$modoTransporte = $_POST["modoTransporte"];
					$seguro = $_POST["seguro"];
					$flete = $_POST["flete"];
					$idMotorista = $_POST["idMotorista"];
				}

				if($_POST["tipoDte"] == "14"){ // Sujeto excluido
					// Construir el nuevo número de control
					$numeroControl = 'DTE-14-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}

				$idFacturaRelacionada = "";

				if($_POST["tipoDte"] == "05"){ // Nota de crédito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-05-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}

				if($_POST["tipoDte"] == "06"){ // Nota de débito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-06-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}
			
				$tabla = "emisor";
				$item1 = "numeroControlGeneral";
				$valor1 = $numeroControl;
				$item2 = "id";
				$valor2 = "1";
				

				$actualizarNumeroControl = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
				
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			
				$notaRemi = "";
				$estado = "Activa";

				$ordenCompra = "";
				$incoterm = "";
				$origen = "";
				if(isset($_POST["orden_compra"])){
					$ordenCompra = $_POST["orden_compra"];
				}
				if(isset($_POST["incoterm"])){
					$incoterm = $_POST["incoterm"];
				}
				if(isset($_POST["origen"])){
					$origen = $_POST["origen"];
				}

				$granContribuyente = "No";
				if($_POST["granContribuyente"]){
					$granContribuyente = $_POST["granContribuyente"];
				}
								
				$datos = array("id_cliente" => $_POST["nuevoClienteFactura"],
							   "productos" => $_POST["productos"],
							   "total" => $_POST["nuevoTotalFactura"],
							   "totalSinIva" => $_POST["nuevoTotalFacturaSin"],
							   "tipoDte" => $_POST["tipoDte"],

							   "gran_contribuyente" => $granContribuyente,

							   "recintoFiscal" => $recintoFiscal,
							   "regimen" => $regimen,
							   "modoTransporte" => $modoTransporte,
							   "seguro" => $seguro,
							   "flete" => $flete,
							   "idMotorista" => $idMotorista,

							   "idFacturaRelacionada" => $idFacturaRelacionada,
							   "notaRemision" => $notaRemi,
							   "estado" => $estado,

							   "modo" => "Contingencia",
							   "id_vendedor" => $_POST["nuevoVendedorId"],
							   "id_usuario" => $_POST["nuevoFacturadorId"],

							   "tipo_contingencia" => $_POST["tipoContingencia"],
							   "motivo_contingencia" => $_POST["motivoContingencia"],

							   "condicionOperacion" => $_POST["condicionOperacion"],
							   "numeroControl" => $numeroControl,
								"codigoGeneracion" => $codigoGeneracion,
								"horEmi" => $horEmi,
								"fecEmi" => $fecEmi,
								"orden_compra" => $ordenCompra,
								"incoterm" => $incoterm,
								"origen" => $origen);

				$productos = json_decode($_POST["productos"], true);

				if($_POST["tipoDte"] == "05" || $_POST["tipoDte"] == "06"){
					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFacturaContingencia($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){
	
						echo '<script>
	
						swal({
	
							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
	
						}).then(function(result){
	
							if(result.value){
							
								window.location = "contabilidad";
	
							}
	
						});
					
	
						</script>';
	
	
					}
						
				} else {
					foreach ($productos as $producto) {

						// DESCARGAR STOCK FACTURADO
						$item = "id";
						$valor = $producto["idProducto"];
				
						$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
				
						$stockActual = $productoTraido["stock"];
						$stockNuevo = $stockActual - $producto["cantidad"];
				
						$tabla = "inventario";
						$item1 = "stock";
						$valor1 = $stockNuevo;
						$item2 = "id";
						$valor2 = $producto["idProducto"];
				
						$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
					}

					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFacturaContingencia($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){

						echo '<script>

						swal({

							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"

						}).then(function(result){

							if(result.value){
							
								window.location = "facturacion-contingencia";

							}

						});
					

						</script>';


					}
				}
				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La factura no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	REGISTRO DE EVENTO DE CONTINGENCIA
	=============================================*/

	static public function ctrCrearEventoContingencia(){

		if(isset($_POST["nuevaFechaInicio"])){

			if(isset($_POST["nuevaFechaInicio"])){

				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = $empresarial["numeroControlGeneral"];

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = '';
				$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				for ($i = 0; $i < 8; $i++) {
					$parteAleatoria .= $caracteres[rand(0, strlen($caracteres) - 1)];
				}

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = "";
				
				
				
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			
				$notaRemi = "";
				$estado = "Activa";

				$ids_facturas = $_POST['ids_facturas'] ?? [];
				$ids_facturas_json = json_encode($ids_facturas); // Convertir a JSON

				$datos = array("fecha_inicio" => $_POST["nuevaFechaInicio"],
							   "fecha_fin" => $_POST["nuevaFechaFin"],
							   "hora_inicio" => $_POST["nuevaHoraInicio"],
							   "hora_fin" => $_POST["nuevaHoraFin"],
							   "tipo_contingencia" => $_POST["tipoContingencia"],
							   "motivo_contingencia" => $_POST["motivoContingencia"],
							   "ids_facturas" => $ids_facturas_json,
								"codigoGeneracion" => $codigoGeneracion);
				
					$tabla = "contingencias";

					$respuesta = ModeloFacturas::mdlIngresarEventoContingencia($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){
	
						echo '<script>
	
						swal({
	
							type: "success",
							title: "¡El evento ha sido creado correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
	
						}).then(function(result){
	
							if(result.value){
							
								window.location = "facturacion-contingencia";
	
							}
	
						});
					
	
						</script>';
	
	
					}

				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡El evento no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion-contingencia";

						}

					});
				

				</script>';

			}


		}


	}
	
	/*=============================================
	REGISTRO DE NOTA DE REMISION EN FACTURA LOCAL
	=============================================*/

	static public function ctrCrearNotaRemision(){

		if(isset($_GET["idFacturaNotaRemision"])){

			if(isset($_GET["idFacturaNotaRemision"])){

				$item = "id";
				$orden = "id";
				$valor = $_GET["idFacturaNotaRemision"];
				$optimizacion = "no";

				$facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
				
				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = $empresarial["numeroControlGeneral"];

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = 'S001P001';

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = "";

				// Construir el nuevo número de control
				$numeroControl = 'DTE-04-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				
				$recintoFiscal = "";
				$regimen = "";
				$modoTransporte = "";
				$seguro = 0.0;
				$flete = 0.0;
				$idMotorista = "";

				if($facturaOriginal["tipoDte"] == "11"){ // Exportación

					$recintoFiscal = $facturaOriginal["recintoFiscal"];
					$regimen = $facturaOriginal["regimen"];
					$modoTransporte = $facturaOriginal["modoTransporte"];
					$seguro = $facturaOriginal["seguro"];
					$flete = $facturaOriginal["flete"];
					$idMotorista = $facturaOriginal["idMotorista"];
				}


				$idFacturaRelacionada = "";

				if($facturaOriginal["tipoDte"] == "05"){ // Nota de crédito
					$idFacturaRelacionada = $facturaOriginal["idFacturaRelacionada"];
				}

				if($facturaOriginal["tipoDte"] == "06"){ // Nota de débito
					$idFacturaRelacionada = $facturaOriginal["idFacturaRelacionada"];
				}
				$estado = "Activa";
				$notaRemi = "";
				if($facturaOriginal["tipoDte"] == "04"){ // Nota de remisión
					$idFacturaRelacionada = $facturaOriginal["idFacturaRelacionada"];
					$notaRemi = "si";
				}
			
				$tabla = "emisor";
				$item1 = "numeroControlGeneral";
				$valor1 = $numeroControl;
				$item2 = "id";
				$valor2 = "1";
				

				$actualizarNumeroControl = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
				
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			
				$granContribuyente = "No";
				if($_POST["granContribuyente"]){
					$granContribuyente = $_POST["granContribuyente"];
				}

				$datos = array("id_cliente" => $facturaOriginal["id_cliente"],
								"id_vendedor" => $_SESSION["id"],
								"id_usuario" => $_SESSION["id"],
							   "productos" => $facturaOriginal["productos"],
							   "total" => $facturaOriginal["total"],
							   "totalSinIva" => $facturaOriginal["totalSinIva"],
							   "tipoDte" => "04",
								"orden_compra" => "",
								"incoterm" => "",
								"gran_contribuyente" => $granContribuyente,
								"origen" => "",
							   "recintoFiscal" => $facturaOriginal["recintoFiscal"],
							   "regimen" => $facturaOriginal["regimen"],
							   "modoTransporte" => $facturaOriginal["modoTransporte"],
							   "seguro" => $facturaOriginal["seguro"],
							   "flete" => $facturaOriginal["flete"],
							   "idMotorista" =>$facturaOriginal["idMotorista"],

							   "idFacturaRelacionada" => $facturaOriginal["id"],

							   "notaRemision" => $notaRemi,
							   "estado" => $estado,

							   "modo" => "Normal",

							   "condicionOperacion" => $facturaOriginal["condicionOperacion"],
							   "numeroControl" => $numeroControl,
								"codigoGeneracion" => $codigoGeneracion,
								"horEmi" => $horEmi,
								"fecEmi" => $fecEmi);


					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFactura($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){

						$tabla = "facturas_locales";
						$item1 = "notaRemision";
						$valor1 = "si";
						$item2 = "id";
						$valor2 = $facturaOriginal["id"];
				
						$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

						echo '<script>
	
						swal({
	
							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
	
						}).then(function(result){
	
							if(result.value){
							
								window.location = "facturacion";
	
							}
	
						});
					
	
						</script>';
	
	
					}	
				
			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La factura no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	REGISTRO DE NOTA DE REMISION EN FACTURA MANUAL
	=============================================*/

	static public function ctrNotaRemisionManual(){

		if(isset($_POST["nuevoClienteFactura"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoClienteFactura"])){

				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = $empresarial["numeroControlGeneral"];

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = 'S001P001';

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = "";
				
				if($_POST["tipoDte"] == "01"){ // Factura
					// Construir el nuevo número de control
					$numeroControl = 'DTE-01-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}
				if($_POST["tipoDte"] == "03"){ // CCF
					// Construir el nuevo número de control
					$numeroControl = 'DTE-03-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idMotorista = $_POST["idMotorista"];
				}
				
				$recintoFiscal = "";
				$regimen = "";
				$modoTransporte = "";
				$seguro = 0.0;
				$flete = 0.0;
				$idMotorista = "";

				if($_POST["tipoDte"] == "04"){ // Exportación
					// Construir el nuevo número de control
					$numeroControl = 'DTE-04-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idMotorista = $_POST["idMotorista"];
				}

				if($_POST["tipoDte"] == "11"){ // Exportación
					// Construir el nuevo número de control
					$numeroControl = 'DTE-11-' . $parteAleatoria . '-' . $parteNumericaIncrementada;

					$recintoFiscal = $_POST["recintoFiscal"];
					$regimen = $_POST["regimen"];
					$modoTransporte = $_POST["modoTransporte"];
					$seguro = $_POST["seguro"];
					$flete = $_POST["flete"];
					$idMotorista = $_POST["idMotorista"];
				}

				if($_POST["tipoDte"] == "14"){ // Sujeto excluido
					// Construir el nuevo número de control
					$numeroControl = 'DTE-14-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}

				$idFacturaRelacionada = "";

				if($_POST["tipoDte"] == "05"){ // Nota de crédito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-05-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}

				if($_POST["tipoDte"] == "06"){ // Nota de débito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-06-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}
			
				$tabla = "emisor";
				$item1 = "numeroControlGeneral";
				$valor1 = $numeroControl;
				$item2 = "id";
				$valor2 = "1";
				

				$actualizarNumeroControl = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
				
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			
				$notaRemi = "";
				$estado = "Activa";

				$granContribuyente = "No";
				if($_POST["granContribuyente"]){
					$granContribuyente = $_POST["granContribuyente"];
				}

				$terminoVentaCif = "";
				$terminoVentaFob = "";

				if(isset($_POST["terminoVentaCif"])){
					$terminoVentaCif = $_POST["terminoVentaCif"];
				}
				if(isset($_POST["terminoVentaFob"])){
					$terminoVentaFob = $_POST["terminoVentaFob"];
				}


				$datos = array("id_cliente" => $_POST["nuevoClienteFactura"],
							   "productos" => $_POST["productos"],
							   "total" => $_POST["nuevoTotalFactura"],
							   "totalSinIva" => $_POST["nuevoTotalFacturaSin"],
							   "tipoDte" => $_POST["tipoDte"],

							   "gran_contribuyente" => $granContribuyente,

							   "recintoFiscal" => $recintoFiscal,
							   "regimen" => $regimen,
							   "modoTransporte" => $modoTransporte,
							   "seguro" => $seguro,
							   "flete" => $flete,
							   "idMotorista" => $idMotorista,

							   "idFacturaRelacionada" => $idFacturaRelacionada,
							   "notaRemision" => $notaRemi,
							   "estado" => $estado,

							   "modo" => "Normal",

							   "id_vendedor" => $_POST["nuevoVendedorId"],
							   "id_usuario" => $_POST["nuevoFacturadorId"],

							   "condicionOperacion" => $_POST["condicionOperacion"],
							   "numeroControl" => $numeroControl,
								"codigoGeneracion" => $codigoGeneracion,
								"horEmi" => $horEmi,
								"fecEmi" => $fecEmi);

				

				$productos = json_decode($_POST["productos"], true);

				if($_POST["tipoDte"] == "05" || $_POST["tipoDte"] == "06"){
					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFactura($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){
	
						echo '<script>
	
						swal({
	
							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
	
						}).then(function(result){
	
							if(result.value){
							
								window.location = "contabilidad";
	
							}
	
						});
					
	
						</script>';
	
	
					}
						
				} else {
					foreach ($productos as $producto) {

						// DESCARGAR STOCK FACTURADO
						$item = "id";
						$valor = $producto["idProducto"];
				
						$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
				
						$stockActual = $productoTraido["stock"];
						$stockNuevo = $stockActual - $producto["cantidad"];
				
						$tabla = "inventario";
						$item1 = "stock";
						$valor1 = $stockNuevo;
						$item2 = "id";
						$valor2 = $producto["idProducto"];
				
						$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
						if($producto["peso"]){
							$pesoNuevo = $producto["peso"];

							$tabla = "inventario";
							$item1 = "peso";
							$valor1 = $pesoNuevo;
							$item2 = "id";
							$valor2 = $producto["idProducto"];
					
							$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
						}
						
					}

					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFactura($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){

						echo '<script>

						swal({

							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"

						}).then(function(result){

							if(result.value){
							
								window.location = "facturacion";

							}

						});
					

						</script>';


					}
				}
				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La factura no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	REGISTRO DE COMPRA
	=============================================*/

	static public function ctrCrearCompra(){

		if(isset($_POST["numero_documento"])){

			if(isset($_POST["numero_documento"])){

				// Obtener los datos del formulario
				$fechas = $_POST['fecha'];
				$clases_documento = $_POST['clase_documento'];
				$tipos_documento = $_POST['tipo_documento'];
				$numeros_documento = $_POST['numero_documento'];
				$nits_nrc = $_POST["nit_nrc"];
				$proveedores = $_POST['nombre_proveedor'];
				$comprass_internas_exentas = $_POST["compras_internas_exentas"];
				$internacioness_exentas_y_no_sujetas = $_POST["internaciones_exentas_y_no_sujetas"];
				$importacioness_exentas_y_no_sujetas = $_POST["importaciones_exentas_y_no_sujetas"];
				$comprass_internas_gravadas = $_POST["compras_internas_gravadas"];
				$internacioness_gravadas_de_bienes = $_POST["internaciones_gravadas_de_bienes"];
				$importacioness_gravadas_de_bienes = $_POST["importaciones_gravadas_de_bienes"];
				$importacioness_gravadas_de_servicios = $_POST["importaciones_gravadas_de_servicios"];
				$creditos_fiscal = $_POST["credito_fiscal"];
				$totals_de_compras = $_POST["total_de_compras"];
				$duis_del_proveedor = $_POST["dui_del_proveedor"];
				$tipos_de_operacion = $_POST["tipo_de_operacion"];
				$clasificacions = $_POST["clasificacion"];
				$sectors = $_POST["sector"];
				$tipos = $_POST["tipo"];
				$anexos = $_POST["anexo"];

				// Recorrer los registros y guardarlos uno por uno
				for ($i = 0; $i < count($numeros_documento); $i++) {
					$datos = array("fecha" => $fechas[$i],
								"clase_documento" => $clases_documento[$i],
								"tipo_documento" => $tipos_documento[$i],
								"numero_documento" => $numeros_documento[$i],
								"nit_nrc" => $nits_nrc[$i],
								"nombre_proveedor" => $proveedores[$i],
								"compras_internas_exentas" => $comprass_internas_exentas[$i],
								"internaciones_exentas_y_no_sujetas" => $internacioness_exentas_y_no_sujetas[$i],
								"importaciones_exentas_y_no_sujetas" => $importacioness_exentas_y_no_sujetas[$i],
								"compras_internas_gravadas" => $comprass_internas_gravadas[$i],
								"internaciones_gravadas_de_bienes" => $internacioness_gravadas_de_bienes[$i],
								"importaciones_gravadas_de_bienes" => $importacioness_gravadas_de_bienes[$i],
								"importaciones_gravadas_de_servicios" => $importacioness_gravadas_de_servicios[$i],
								"credito_fiscal" => $creditos_fiscal[$i],
								"total_de_compras" => $totals_de_compras[$i],
								"dui_del_proveedor" => $duis_del_proveedor[$i],
								"tipo_de_operacion" => $tipos_de_operacion[$i],
								"clasificacion" => $clasificacions[$i],
								"sector" => $sectors[$i],
								"tipo" => $tipos[$i],
								"anexo" => $anexos[$i]);

					$tabla = "compras";

					$respuesta = ModeloFacturas::mdlIngresarCompra($tabla, $datos);
				}

						echo '<script>

						swal({

							type: "success",
							title: "¡Las compras han sido creadas correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"

						}).then(function(result){

							if(result.value){
							
								window.location = "compras";

							}

						});
					

						</script>';

				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡Las compras no se pudieron crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "compras";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	BORRAR FACTURA
	=============================================*/

	static public function ctrBorrarFactura(){

		if(isset($_GET["idFacturaEliminar"])){

			/*=============================================
			REGRESAR EL STOCK
			=============================================*/

			$item = "id";
			$valor = $_GET["idFacturaEliminar"];
			$orden = "fecEmi";
			$optimizacion = "no";

			$facturas = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

			$productos = json_decode($facturas["productos"], true);

			if($facturas["tipoDte"] == "01" || $facturas["tipoDte"] == "03" || $facturas["tipoDte"] == "11" || $facturas["tipoDte"] == "14"){

				foreach ($productos as $producto) {

					// DESCARGAR STOCK FACTURADO
					$item = "id";
					$valor = $producto["idProducto"];
			
					$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
			
					$stockActual = $productoTraido["stock"];
					$stockNuevo = $stockActual + $producto["cantidad"];
			
					$tabla = "inventario";
					$item1 = "stock";
					$valor1 = $stockNuevo;
					$item2 = "id";
					$valor2 = $producto["idProducto"];
			
					$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

				}

				$tabla ="facturas_locales";
				$datos = $_GET["idFacturaEliminar"];
	
				$respuesta = ModeloFacturas::mdlBorrarFactura($tabla, $datos);

				$tabla = "eliminadas";

				$datos = array("numero_control" => $facturas["numeroControl"],
								"codigo_generacion" => $facturas["codigoGeneracion"]
							   );

				$guardarElimina = ModeloFacturas::mdlIngresarEliminada($tabla, $datos);

				if($respuesta == "ok"){

					echo'<script>
	
					swal({
							type: "success",
							title: "La factura ha sido borrada correctamente y el stock ha sido regresado",
							showConfirmButton: true,
							confirmButtonText: "Cerrar",
							closeOnConfirm: false
							}).then(function(result) {
									if (result.value) {
	
									window.location = "facturacion";
	
									}
								})
	
					</script>';
	
				}				
			} else {
				if($facturas["tipDte"] == "04"){
					$item = "id";
					$orden = "id";
					$valor = $_GET["idFacturaEliminar"];
					$optimizacion = "no";

					$facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

					$tabla = "facturas_locales";
					$item1 = "notaRemision";
					$valor1 = "";
					$item2 = "id";
					$valor2 = $facturaOriginal["idFacturaRelacionada"];
			
					$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
				}


				

				$tabla ="facturas_locales";
				$datos = $_GET["idFacturaEliminar"];
	
				$respuesta = ModeloFacturas::mdlBorrarFactura($tabla, $datos);

				$tabla = "eliminadas";

				$datos = array("numero_control" => $facturas["numeroControl"],
								"codigo_generacion" => $facturas["codigoGeneracion"]
							   );

				$guardarElimina = ModeloFacturas::mdlIngresarEliminada($tabla, $datos);
	
				if($respuesta == "ok"){
					
						echo'<script>
	
							swal({
								type: "success",
								title: "La factura ha sido borrado correctamente",
								showConfirmButton: true,
								confirmButtonText: "Cerrar",
								closeOnConfirm: false
								}).then(function(result) {
									if (result.value) {

									window.location = "contabilidad";

									}
								})
			
							</script>';
					
				}
			}

		}

	}

	/*=============================================
	BORRAR EVENTO CONTINGENCIA
	=============================================*/

	static public function ctrBorrarEvento(){

		if(isset($_GET["idEventoEliminar"])){

			/*=============================================
			REGRESAR EL STOCK
			=============================================*/	

			$tabla ="contingencias";
			$datos = $_GET["idEventoEliminar"];

			$respuesta = ModeloFacturas::mdlBorrarFactura($tabla, $datos);

			if($respuesta == "ok"){
				
					echo'<script>

						swal({
							type: "success",
							title: "El evento ha sido borrado correctamente",
							showConfirmButton: true,
							confirmButtonText: "Cerrar",
							closeOnConfirm: false
							}).then(function(result) {
								if (result.value) {

								window.location = "facturacion-contingencia";

								}
							})
		
						</script>';
				
			}
			

		}

	}

	/*=============================================
	ANULAR DTE
	=============================================*/

	static public function ctrCrearAnulacion(){

		if(isset($_POST["facturaRelacionadaAnular"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["facturaRelacionadaAnular"])){

				$item = "id";
				$valor = $_POST["facturaRelacionadaAnular"];
				$orden = "fecEmi";
				$optimizacion = "no";

				$facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = $empresarial["numeroControlGeneral"];

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = 'S001P001';
				

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = 'DTE-01-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				
			
				$tabla = "emisor";
				$item1 = "numeroControlGeneral";
				$valor1 = $numeroControl;
				$item2 = "id";
				$valor2 = "1";
				

				//$actualizarNumeroControl = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
				
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			

				$datos = array("codigoGeneracion" => $codigoGeneracion,
							   "fecEmi" => $fecEmi,
							   "horEmi" => $horEmi,
							   "facturaRelacionada" => $facturaOriginal["id"],
							   "motivoAnulacion" => $_POST["motivoAnulacion"],);

				

				$tabla = "anuladas";

				$respuesta = ModeloFacturas::mdlIngresarAnulacion($tabla, $datos);
				
			
				if($respuesta == "ok"){

					if($facturaOriginal["tipoDte"] == "01" || $facturaOriginal["tipoDte"] == "03" || $facturaOriginal["tipoDte"] == "11" || $facturaOriginal["tipoDte"] == "14"){
						$productos = json_decode($facturaOriginal["productos"], true);

						foreach ($productos as $producto) {

							// DESCARGAR STOCK FACTURADO
							$item = "id";
							$valor = $producto["idProducto"];
					
							$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
					
							$stockActual = $productoTraido["stock"];
							$stockNuevo = $stockActual + $producto["cantidad"];
					
							$tabla = "inventario";
							$item1 = "stock";
							$valor1 = $stockNuevo;
							$item2 = "id";
							$valor2 = $producto["idProducto"];
					
							$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
						}

						$tabla = "facturas_locales";
						$item1 = "estado";
						$valor1 = "Anulada";
						$item2 = "id";
						$valor2 = $facturaOriginal["id"];
				
						$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

						if($respuesta == "ok"){
		
							echo'<script>
							
							swal({
								type: "success",
								title: "El DTE de anulación ha sido creado correctamente y el stock ha sido regresado",
								showConfirmButton: true,
								confirmButtonText: "Cerrar",
								closeOnConfirm: false
								}).then(function(result) {
											if (result.value) {
			
											window.location = "contabilidad";
			
											}
										})
			
							</script>';
			
						}

						
						
					} else {
						if($facturaOriginal["tipoDte"] == "04" || $facturaOriginal["tipoDte"] == "05" || $facturaOriginal["tipoDte"] == "06" || $facturaOriginal["tipoDte"] == "14"){
							$tabla = "facturas_locales";
							$item1 = "estado";
							$valor1 = "Anulada";
							$item2 = "id";
							$valor2 = $facturaOriginal["id"];
					
							$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
						}

						if($facturaOriginal["tipoDte"] == "04"){
							$tabla = "facturas_locales";
							$item1 = "notaRemision";
							$valor1 = "";
							$item2 = "id";
							$valor2 = $facturaOriginal["id"];
					
							$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
						}
							echo '<script>
	
							swal({
		
								type: "success",
								title: "¡La factura local ha sido creada correctamente!",
								showConfirmButton: true,
								confirmButtonText: "Cerrar"
		
							}).then(function(result){
		
								if(result.value){
								
									window.location = "contabilidad";
		
								}
		
							});
						
		
							</script>';						
					}	
				}
					
					

								

			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La factura no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}
		
	}

	/*=============================================
	CANCELAR ANULACIÓN
	=============================================*/

	static public function ctrCancelarAnulacion(){

		if(isset($_GET["idFacturaCancelarAnulacion"])){

			/*=============================================
			RESTAR EL STOCK
			=============================================*/

			$item = "id";
			$valor = $_GET["idFacturaCancelarAnulacion"];
			$orden = "fecEmi";

			$facturas = ControladorFacturas::ctrMostrarAnulaciones($item, $valor, $orden, "");

			$item = "id";
			$valor = $facturas["facturaRelacionada"];
			$orden = "fecEmi";
			$optimizacion = "no";

			$facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

			$productos = json_decode($facturaOriginal["productos"], true);

			if($facturaOriginal["tipoDte"] == "01" || $facturaOriginal["tipoDte"] == "03" || $facturaOriginal["tipoDte"] == "11" || $facturaOriginal["tipoDte"] == "14"){

				foreach ($productos as $producto) {

					// DESCARGAR STOCK FACTURADO
					$item = "id";
					$valor = $producto["idProducto"];
			
					$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
			
					$stockActual = $productoTraido["stock"];
					$stockNuevo = $stockActual - $producto["cantidad"];
			
					$tabla = "inventario";
					$item1 = "stock";
					$valor1 = $stockNuevo;
					$item2 = "id";
					$valor2 = $producto["idProducto"];
			
					$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

				}

				$tabla = "facturas_locales";
				$item1 = "estado";
				$valor1 = "Activa";
				$item2 = "id";
				$valor2 = $facturaOriginal["id"];
		
				$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);	
	
				$tabla ="anuladas";
				$datos = $_GET["idFacturaCancelarAnulacion"];

				$respuesta = ModeloFacturas::mdlBorrarFactura($tabla, $datos);

				if($respuesta == "ok"){

					echo'<script>
	
					swal({
							type: "success",
							title: "El DTE de anulación ha sido borrado correctamente y el stock ha sido regresado",
							showConfirmButton: true,
							confirmButtonText: "Cerrar",
							closeOnConfirm: false
							}).then(function(result) {
									if (result.value) {
	
									window.location = "contabilidad";
	
									}
								})
	
					</script>';
	
				}				
			} else {
				if($facturas["tipDte"] == "04"){

					$tabla = "facturas_locales";
					$item1 = "notaRemision";
					$valor1 = "";
					$item2 = "id";
					$valor2 = $facturaOriginal["idFacturaRelacionada"];
			
					$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
				}

				$tabla = "facturas_locales";
				$item1 = "estado";
				$valor1 = "Activa";
				$item2 = "id";
				$valor2 = $facturaOriginal["id"];
		
				$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);	

				$tabla ="anuladas";
				$datos = $_GET["idFacturaCancelarAnulacion"];
	
				$respuesta = ModeloFacturas::mdlBorrarFactura($tabla, $datos);
	
				if($respuesta == "ok"){
					
						echo'<script>
	
							swal({
								type: "success",
								title: "El DTE de anulación ha sido borrado correctamente",
								showConfirmButton: true,
								confirmButtonText: "Cerrar",
								closeOnConfirm: false
								}).then(function(result) {
									if (result.value) {

									window.location = "contabilidad";

									}
								})
			
							</script>';
					
				}
			}

		}

	}

	/*=============================================
	ENVIAR FACTURA CORREO
	=============================================*/

	static public function ctrEnviarFacturaCorreo(){
		
		if (isset($_GET["idFacturaEnviarCorreo"])) {

			require 'extensiones/phpmailer/src/PHPMailer.php';
			require 'extensiones/phpmailer/src/SMTP.php';
			require 'extensiones/phpmailer/src/Exception.php';

			ini_set('display_errors', 1);
			error_reporting(E_ALL);

			// Obtener datos de la empresa, factura y cliente
			$empresa = ControladorClientes::ctrMostrarEmpresas("id", "1", "id");
			$factura = ControladorFacturas::ctrMostrarFacturas("id", $_GET["idFacturaEnviarCorreo"], "fecEmi", "no");
			$cliente = ControladorClientes::ctrMostrarClientes("id", $factura["id_cliente"], "id");
			
			// Determinar tipo de cliente y tipo de factura
			$tipoCliente = [
				"00" => "Consumidor final",
				"01" => "Contribuyente",
				"02" => "Empresa con beneficios fiscales",
				"03" => "Diplomático"
			][$cliente["tipo_cliente"]] ?? "Desconocido";
			
			$tipoFacturaTexto = [
				"01" => "Factura",
				"03" => "Comprobante de crédito fiscal",
				"04" => "Nota de remisión",
				"05" => "Nota de crédito",
				"06" => "Nota de débito",
				"07" => "Comprobante de retención",
				"08" => "Comprobante de liquidación",
				"09" => "Documento contable de liquidación",
				"11" => "Factura de exportación",
				"14" => "Factura de sujeto excluido",
				"15" => "Comprobante de donación"
			][$factura["tipoDte"]] ?? "Factura no válida";

			// Configuración para correos
			$subject = "Emisión de Documento Tributario Electrónico - {$cliente['nombre']}";
			$message = "Estimado cliente: {$cliente['nombre']} - $tipoCliente \n";
			$message .= "Adjunto encontrará su documento $tipoFacturaTexto número:\n";
			$message .= "{$factura['codigoGeneracion']} \n\n";
			$message .= "Para nosotros es un placer servirle \n";
		
			
			// Enviar correos
			try {
				// URL del PDF
				$pdfUrl = "http://localhost/FOX-CONTROL/extensiones/TCPDF-main/examples/imprimir-factura.php?idFactura={$_GET['idFacturaEnviarCorreo']}";
				$file = file_get_contents($pdfUrl);

				if ($file === false) {
					error_log("No se pudo acceder al archivo PDF en: $pdfUrl");
					throw new Exception("No se pudo acceder al archivo PDF en: $pdfUrl");
				} else {
					echo "Archivo PDF encontrado y cargado correctamente.";
				}

				function decodeJWT($jwt) {
					// Dividir el JWT en sus tres partes: Header, Payload y Signature
					$parts = explode('.', $jwt);
				
					// Decodificar la carga útil (Payload) desde Base64URL a texto
					$payload = base64_url_decode($parts[1]);
				
					// Devolver la carga útil decodificada
					return json_decode($payload, true);
				}
				
				function base64_url_decode($data) {
					// Base64URL es una variante de Base64, se debe hacer un pequeño ajuste
					$data = str_replace(['-', '_'], ['+', '/'], $data);  // Reemplazar los caracteres URL-safe
					$padding = strlen($data) % 4;  // Agregar el relleno necesario (=)
					if ($padding) {
						$data .= str_repeat('=', 4 - $padding);
					}
				
					return base64_decode($data);  // Decodificar Base64
				}
				
				$jwt = $factura["firmaDigital"]; // Firma digital (JWT)
				$decoded = decodeJWT($jwt);      // Decodificamos el JWT

				// Agregamos la firma y el sello como campos adicionales
				$decoded["selloRecibido"] = $factura["sello"];
				$decoded["firmaElectronica"] = $jwt;
				

				// Codificamos el JSON con los campos añadidos
				$jsonConFirma = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			

				self::enviarCorreo($empresa["correo"], $empresa["correo"], $subject, $message, $pdfUrl, $factura["codigoGeneracion"], $jsonConFirma);
				self::enviarCorreo($empresa["correo"], $cliente["correo"], $subject, $message, $pdfUrl, $factura["codigoGeneracion"], $jsonConFirma);

				

				// Redirigir
				echo '<script>
					swal({
						type: "success",
						title: "La factura ha sido enviada correctamente",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					}).then(function(result) {
						if (result.value) {
							window.location.href = "index.php?ruta=ver-factura&idFacturaEditar=' . $factura["id"] . '";
						}
					});
				</script>';
			
				exit();
			} catch (Exception $e) {
				error_log("Error al enviar correo: {$e->getMessage()}");
			}
		}
	}

	private static function enviarCorreo($from, $to, $subject, $message, $pdfUrl, $codigoGenera, $jsonContent){
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true;
		//$mail->SMTPDebug = 1;  // Nivel 3 de depuración para obtener más detalles
		// Establecer la codificación a UTF-8
		$mail->CharSet = 'UTF-8';

		$mail->Username = $from;
		$mail->Password = 'nfwotivxggzrrcta'; // Usa variables seguras para contraseñas
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port = 465;

		$mail->setFrom($from, 'Aromazone');
		$mail->addAddress($to);
		$mail->Subject = $subject;
		$mail->Body = $message;

		 // Descargar y adjuntar el PDF si se proporciona una URL
		 try {
			if ($pdfUrl) {
				$pdfContent = file_get_contents($pdfUrl);
				if ($pdfContent === false) {
					throw new Exception("No se pudo descargar el archivo PDF desde la URL: $pdfUrl");
				}
		
				$tempDir = sys_get_temp_dir();
				$pdfFilePath = tempnam($tempDir, $codigoGenera) . '.pdf';
		
				$writeResult = file_put_contents($pdfFilePath, $pdfContent);
				if ($writeResult === false) {
					throw new Exception("No se pudo guardar el archivo PDF temporal en: $pdfFilePath");
				}
		
				if (!file_exists($pdfFilePath)) {
					throw new Exception("El archivo PDF temporal no existe en: $pdfFilePath");
				}
		
				// Adjuntar el PDF
				if (file_exists($pdfFilePath)) {
					$mail->addAttachment($pdfFilePath, 'Factura.pdf');
				} else {
					throw new Exception("El archivo PDF no se ha creado correctamente o no se encuentra.");
				}
			}

			 // Aquí agregamos el JSON recibido como archivo adjunto
			 $tempJsonDir = sys_get_temp_dir();
			 $jsonFilePath = tempnam($tempJsonDir, 'factura_') . '.json';
	 
			 // Escribimos el contenido del JSON en el archivo temporal
			 file_put_contents($jsonFilePath, $jsonContent);
	 
			 // Adjuntamos el archivo JSON
			 $mail->addAttachment($jsonFilePath, $codigoGenera . '.json');  // Adjuntar el archivo JSON
			
			// Enviar correo
			$mail->send();
		} catch (Exception $e) {
			error_log("Error al enviar el correo: {$e->getMessage()}");
			echo "Hubo un problema al enviar el correo. Verifique los logs.";
		}
		
	}

	/*=============================================
	REGISTRO DE ABONO A FACTURA
	=============================================*/

	static public function ctrCrearAbono(){

		if(isset($_POST["nuevaFormaAbono"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaFormaAbono"])){

				$item = "id";
				$valor = $_POST["nuevoIdFacturaAbono"];
				$orden = "fecEmi";
				$optimizacion = "no";

				$factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

				$abonoViejo = $factura["abonado"];
				$abonoIngresado = $_POST["nuevoMontoAbono"];
				$abonoNuevo = $abonoViejo + $abonoIngresado;
			
				$tabla = "facturas_locales";
				$item1 = "abonado";
				$valor1 = $abonoNuevo;
				$item2 = "id";
				$valor2 = $factura["id"];
				

				$actualizarFactura = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');

				// Obtener la fecha y hora actual en un solo valor
				$fecha = date("Y-m-d H:i:s"); // Formato: YYYY-MM-DD HH:MM:SS

				$datos = array("id_factura" => $factura["id"],
							   "forma_abono" => $_POST["nuevaFormaAbono"],
							   "fecha_abono" => $fecha,
							   "gestion_abono" => $_POST["nuevaGestion"],
							   "banco" => $_POST["nuevoBanco"],
							   "monto" => $_POST["nuevoMontoAbono"]);


				$tabla = "formas_pago";

				$respuesta = ModeloFacturas::mdlIngresarAbono($tabla, $datos);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡El monto ha sido abonado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=ver-factura&idFacturaEditar='.$factura["id"].'";

						}

					});
				

					</script>';


				}
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡El abono no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	EDITAR COMPRA
	=============================================*/

	static public function ctrEditarCompra(){

		if(isset($_POST["editarnumero_documentoCompra"])){  // Verifica que el campo exista

			if(trim($_POST["editarnumero_documentoCompra"]) === ""){  // Verifica si el campo está vacío
				// Si el campo está vacío, muestra un error y evita el guardado
				echo '<script>
					swal({
						type: "error",
						title: "¡El número de documento no puede ir vacío!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					}).then(function(result) {
						if (result.value) {
							window.location = "index.php?ruta=ver-compras&filtroFactura=todos&filtroFechaInicio=&filtroFechaFin=' . date('Y-m-d') . '";
						}
					})
				</script>';

		
			} else {  // Si el campo no está vacío, procede a guardar los datos

				// Obtener los datos del formulario
				$fechas = $_POST['editarFechaCompra'];
				$clases_documento = $_POST['editarclase_documentoCompra'];
				$tipos_documento = $_POST['editartipo_documentoCompra'];
				$numeros_documento = $_POST['editarnumero_documentoCompra'];
				$nits_nrc = $_POST["editarnit_nrcCompra"];
				$proveedores = $_POST['editarnombre_proveedorCompra'];
				$comprass_internas_exentas = $_POST["editarcompras_internas_exentasCompra"];
				$internacioness_exentas_y_no_sujetas = $_POST["editarinternaciones_exentas_y_no_sujetasCompra"];
				$importacioness_exentas_y_no_sujetas = $_POST["editarimportaciones_exentas_y_no_sujetasCompra"];
				$comprass_internas_gravadas = $_POST["editarcompras_internas_gravadasCompra"];
				$internacioness_gravadas_de_bienes = $_POST["editarinternaciones_gravadas_de_bienesCompra"];
				$importacioness_gravadas_de_bienes = $_POST["editarimportaciones_gravadas_de_bienesCompra"];
				$importacioness_gravadas_de_servicios = $_POST["editarimportaciones_gravadas_de_serviciosCompra"];
				$creditos_fiscal = $_POST["editarcredito_fiscalCompra"];
				$totals_de_compras = $_POST["editartotal_de_comprasCompra"];
				$duis_del_proveedor = $_POST["editardui_del_proveedorCompra"];
				$tipos_de_operacion = $_POST["editartipo_de_operacionCompra"];
				$clasificacions = $_POST["editarclasificacionCompra"];
				$sectors = $_POST["editarsectorCompra"];
				$tipos = $_POST["editartipoCompra"];
				$anexos = $_POST["editaranexoCompra"];

				$tabla = "compras";

				$datos = array("fecha" => $fechas,
							"clase_documento" => $clases_documento,
							"tipo_documento" => $tipos_documento,
							"numero_documento" => $numeros_documento,
							"nit_nrc" => $nits_nrc,
							"nombre_proveedor" => $proveedores,
							"compras_internas_exentas" => $comprass_internas_exentas,
							"internaciones_exentas_y_no_sujetas" => $internacioness_exentas_y_no_sujetas,
							"importaciones_exentas_y_no_sujetas" => $importacioness_exentas_y_no_sujetas,
							"compras_internas_gravadas" => $comprass_internas_gravadas,
							"internaciones_gravadas_de_bienes" => $internacioness_gravadas_de_bienes,
							"importaciones_gravadas_de_bienes" => $importacioness_gravadas_de_bienes,
							"importaciones_gravadas_de_servicios" => $importacioness_gravadas_de_servicios,
							"credito_fiscal" => $creditos_fiscal,
							"total_de_compras" => $totals_de_compras,
							"dui_del_proveedor" => $duis_del_proveedor,
							"tipo_de_operacion" => $tipos_de_operacion,
							"clasificacion" => $clasificacions,
							"sector" => $sectors,
							"tipo" => $tipos,
							"anexo" => $anexos,
							"id" => $_POST["editarIdCompra"]);
		
				$respuesta = ModeloFacturas::mdlEditarCompra($tabla, $datos);
		
				if($respuesta == "ok"){
		
					echo'<script>
		
								window.location = "index.php?ruta=ver-compras&filtroFactura=todos&filtroFechaInicio=&filtroFechaFin=' . date('Y-m-d') . '";
		
					</script>';
				}
			}
		}
		

	}

	/*=============================================
	BORRAR COMPRA
	=============================================*/

	static public function ctrBorrarCompra(){

		if(isset($_GET["idCompraEliminar"])){

			$tabla ="compras";
			$datos = $_GET["idCompraEliminar"];

			$respuesta = ModeloClientes::mdlBorrarCliente($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

								window.location = "index.php?ruta=ver-compras&filtroFactura=todos&filtroFechaInicio=&filtroFechaFin=' . date('Y-m-d') . '";

				</script>';

			}		

		}

	}

	/*=============================================
	CREAR CORTE DE CAJA
	=============================================*/

	static public function ctrCrearCorteCaja(){

		if(isset($_GET["crearCorte"])){

			if(isset($_GET["crearCorte"])){
				date_default_timezone_set('America/El_Salvador');
				// Obtener la fecha y hora actual
				$fechaActual = new DateTime(); // Fecha y hora actual
				$fechaFormateada = $fechaActual->format('Y-m-d');  // Formato '2025-03-08'
	
				$item = "fecEmi";
				$valor = $fechaFormateada;
				$orden = "id";
				$optimizacion = "no";
		
				$facturas = ControladorFacturas::ctrMostrarFacturasCortes($item, $valor, $orden, $optimizacion);

				// Crear un array para almacenar los IDs de las facturas
				$idsFacturas = [];

				foreach ($facturas as $key => $factura){
					if($factura["id_usuario"] == $_SESSION["id"] && $factura["sello"] != ""){
					   	// Agregar el ID de la factura al array
        				$idsFacturas[] = $factura["id"];
					}
				  }
				// Convertir el array de IDs a formato JSON
				$jsonIds = json_encode($idsFacturas);
				$datos = array("ids_facturas" => $jsonIds,
							   "id_facturador" => $_SESSION["id"]
							);


				$tabla = "cortes_caja";

				$respuesta = ModeloFacturas::mdlIngresarCorte($tabla, $datos);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡El corte ha sido creado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=facturacion";

						}

					});
				

					</script>';


				}
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡No se pudo crear el corte!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	EDITAR CORTE
	=============================================*/

	static public function ctrEditarCorte(){

		if(isset($_POST["nuevoMontoTotalCorte"])){  // Verifica que el campo exista

			if(!isset($_POST["nuevoMontoTotalCorte"])){  // Verifica si el campo está vacío
				// Si el campo está vacío, muestra un error y evita el guardado
				echo '<script>
					swal({
						type: "error",
						title: "¡El corte no se pudo autorizar!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					}).then(function(result) {
						if (result.value) {
							window.location = "index.php?ruta=facturacion";
						}
					})
				</script>';

		
			} else {  // Si el campo no está vacío, procede a guardar los datos

				

				$tabla = "cortes_caja";

				$datos = array("id" => $_POST["idCorte"],
							"autorizacion" => $_POST["nuevaAutorizacionCorte"],
							"cuadrada" => $_POST["nuevoCuadradaCorte"],
							"comentarios" => $_POST["comentariosCorte"],
							"total" => $_POST["nuevoMontoTotalCorte"]);
		
				$respuesta = ModeloFacturas::mdlEditarCorte($tabla, $datos);
		
				if($respuesta == "ok"){
		
					echo '<script>

					swal({

						type: "success",
						title: "¡El corte ha sido modificado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

					</script>';
				}
			}
		}
		

	}

}