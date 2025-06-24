<?php

require_once "../controladores/facturas.controlador.php";
require_once "../modelos/facturas.modelo.php";

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

session_start();

class AjaxFacturas {

    /*=============================================
	SELLAR FACTURA
	=============================================*/	
	public $idFacturaS;

	public function ajaxSellarFactura() {
    
        $item = "id";
        $orden = "id";
        $valor = $this->idFacturaS;
        $optimizacion = "no";
    
        // Obtiene los datos de la factura
        $factura1 = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
    
        // Asegúrate de que el token esté en la sesión
        if (!isset($_SESSION["tokenInicioSesionMh"])) {
            echo json_encode("Token no encontrado en la sesión");
            return;
        }
    
        // Datos de la factura
        $factura = [
            "codigoGeneracion" => $factura1["codigoGeneracion"],
            "firmaDigital" => $factura1["firmaDigital"],
            "tipoDte" => $factura1["tipoDte"]
        ];
    
        // URL de la API a la que estás haciendo el posteo
        $url = "https://api.dtes.mh.gob.sv/fesv/recepciondte";
    
        // Configuración de los headers
        $headers = [
            "Authorization: " . $_SESSION["tokenInicioSesionMh"],
            "User-Agent: facturacion",
            "Content-Type: application/json"
        ];
    
        if($factura["tipoDte"] == "01"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 1
            ];
        }
        if($factura["tipoDte"] == "03"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 3
            ];
        }

        if($factura["tipoDte"] == "11"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 1
            ];
        }
        
        if($factura["tipoDte"] == "14"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 1
            ];
        }

        if($factura["tipoDte"] == "05"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 3
            ];
        }

        if($factura["tipoDte"] == "06"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 3
            ];
        }

        if($factura["tipoDte"] == "04"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 3
            ];
        }
    
        // Inicialización de cURL
        $ch2 = curl_init($url);
    
        // Configuración de cURL
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data));
    
        // Ejecución de la solicitud y captura de la respuesta
        $response = curl_exec($ch2);
        
        // Verifica si hubo algún error
        if (curl_errno($ch2)) {
            echo json_encode(['error' => curl_error($ch2)]);
            curl_close($ch2);
            return;
        }
        
        // Decodifica la respuesta JSON a un array asociativo
        $decoded_response = json_decode($response, true);
    
        // Verifica si el campo "selloRecibido" existe en la respuesta
        if (isset($decoded_response["selloRecibido"])) {
            $selloRecibido = $decoded_response["selloRecibido"];
    
            $tabla = "facturas_locales";
            $item1 = "sello";
            $valor1 = $selloRecibido;
            $item2 = "id";
            $valor2 = $this->idFacturaS;
    
            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
    
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no");
            }
    
        } else {
            echo json_encode($response);
        }
    
        // Cierre de la conexión cURL
        curl_close($ch2);
    }

    /*=============================================
	SELLAR EVENTO CONTINGENCIA
	=============================================*/	
	public $idEventoH;

	public function ajaxSellarEvento() {
    
        
        $item = "id";
        $orden = "id";
        $valor = $this->idEventoH;
    
        // Obtiene los datos de la factura
        $factura = ControladorFacturas::ctrMostrarEventosContingencias($item, $valor, $orden);
        
        $item = "id";
        $orden = "id";
        $valor = "1";

        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        
    
        // Asegúrate de que el token esté en la sesión
        if (!isset($_SESSION["tokenInicioSesionMh"])) {
            echo json_encode("Token no encontrado en la sesión");
            return;

        }
    
        
        
    
        // URL de la API a la que estás haciendo el posteo
        $url = "https://api.dtes.mh.gob.sv/fesv/contingencia";
    
        // Configuración de los headers
        $headers = [
            "Authorization: " . $_SESSION["tokenInicioSesionMh"],
            "User-Agent: facturacion",
            "Content-Type: application/json"
        ];
        
        
        // Cuerpo de la solicitud en JSON
        $data = [
            "nit" => $empresa["nit"],
            "documento" => $factura["firmaDigital"]
        ];

        
        
    
        // Inicialización de cURL
        $ch2 = curl_init($url);
    
        // Configuración de cURL
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data));
    
        // Ejecución de la solicitud y captura de la respuesta
        $response = curl_exec($ch2);
        
        // Verifica si hubo algún error
        if (curl_errno($ch2)) {
            echo json_encode(['error' => curl_error($ch2)]);
            curl_close($ch2);
            return;
        }
        
        // Decodifica la respuesta JSON a un array asociativo
        $decoded_response = json_decode($response, true);
    
        // Verifica si el campo "selloRecibido" existe en la respuesta
        if (isset($decoded_response["selloRecibido"])) {
            $selloRecibido = $decoded_response["selloRecibido"];
    
            $tabla = "contingencias";
            $item1 = "sello";
            $valor1 = $selloRecibido;
            $item2 = "id";
            $valor2 = $this->idEventoH;
    
            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
    
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no");
            }
    
        } else {
            echo json_encode($response);
        }
    
        // Cierre de la conexión cURL
        curl_close($ch2);
    }


    /*=============================================
	SELLAR ANULACION
	=============================================*/	
	public $idFacturaSA;

	public function ajaxSellarAnulacion() {

    
        $item = "id";
        $orden = "id";
        $valor = $this->idFacturaSA;
    
        // Obtiene los datos de la factura
        $factura1 = ControladorFacturas::ctrMostrarAnulaciones($item, $valor, $orden, "no");
    
        // Asegúrate de que el token esté en la sesión
        if (!isset($_SESSION["tokenInicioSesionMh"])) {
            echo json_encode("Token no encontrado en la sesión");
            return;
        }

    
        // URL de la API a la que estás haciendo el posteo
        $url = "https://api.dtes.mh.gob.sv/fesv/anulardte";
    
        // Configuración de los headers
        $headers = [
            "Authorization: " . $_SESSION["tokenInicioSesionMh"],
            "User-Agent: facturacion",
            "Content-Type: application/json"
        ];
    
        // Cuerpo de la solicitud en JSON
        $data = [
            "ambiente" => "01",
            "idEnvio" => 1,
            "version" => 2,
            "documento" => $factura1["firmaDigital"]
        ];
        
        // Inicialización de cURL
        $ch2 = curl_init($url);
    
        // Configuración de cURL
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data));
    
        // Ejecución de la solicitud y captura de la respuesta
        $response = curl_exec($ch2);
        
        // Verifica si hubo algún error
        if (curl_errno($ch2)) {
            echo json_encode(['error' => curl_error($ch2)]);
            curl_close($ch2);
            return;
        }
        
        // Decodifica la respuesta JSON a un array asociativo
        $decoded_response = json_decode($response, true);
    
        // Verifica si el campo "selloRecibido" existe en la respuesta
        if (isset($decoded_response["selloRecibido"])) {
            $selloRecibido = $decoded_response["selloRecibido"];
    
            $tabla = "anuladas";
            $item1 = "sello";
            $valor1 = $selloRecibido;
            $item2 = "id";
            $valor2 = $this->idFacturaSA;
    
            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
    
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no");
            }
    
        } else {
            echo json_encode($response);
        }
    
        // Cierre de la conexión cURL
        curl_close($ch2);
    }


    /*=============================================
	FIRMAR FACTURA ANULAR DTE
	=============================================*/	
	public $idFacturaA;

	public function ajaxAnularDTE() {
        
        
        $item = "id";
        $orden = "id";
		$valor = $this->idFacturaA;

		$factura = ControladorFacturas::ctrMostrarAnulaciones($item, $valor, $orden, "no");

        $item = "id";
        $orden = "id";
        $valor = $factura["facturaRelacionada"];
        $optimizacion = "no";

        $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $item = "id";
        $orden = "id";
        $valor = "1";

        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        
        
        $item = "id";
        $orden = "id";
        $valor = $facturaOriginal["id_cliente"];

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);


        // Decodificar los productos de la factura
        $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo

        // Inicializar el array cuerpoDocumento
        $cuerpoDocumento = [];

        // Número de ítem inicial
        $numItem = 1;

        $montoIva = 0.0;
        $tipoDocu = "";
        $docu = "";
        if((($facturaOriginal["tipoDte"] == "01" || $facturaOriginal["tipoDte"] == "14") && $cliente["tipo_cliente"] == "00")){
            $tipoDocu = "13";
            $docu = $cliente["DUI"];
        } else {
            $tipoDocu = "36";
            $docu = $cliente["NIT"];
        }

        if($facturaOriginal["tipoDte"] == "01" && ($cliente["tipo_cliente"] == "00" || $cliente["tipo_cliente"] == "01")){ // Factura, persona normal y persona que declara IVA - empresa
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));

        }

        if($facturaOriginal["tipoDte"] == "01" && $cliente["tipo_cliente"] == "02"){ // Factura, empresa con beneficios fiscales

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;


        }

        if($facturaOriginal["tipoDte"] == "01" && $cliente["tipo_cliente"] == "03"){ // Factura, diplomáticos
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;


        }

        if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // CCF, Declarante IVA - Empresa
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));


        }

        if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "02"){ // CCF, Empresa con beneficios fiscales
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;


        }

        if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "03"){ // CCF, Diplomáticos
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;


        }

        if($facturaOriginal["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Exportación, Declarante IVA - Empresa
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;


        }

        if($facturaOriginal["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){ // Factura sujeto excluido, persona normal
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));


        }

        if($facturaOriginal["tipoDte"] == "05" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] != "11"){ // Nota de crédito, CCF Declarante IVA - Empresa

            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));
            
        }

        if($facturaOriginal["tipoDte"] == "05" && $cliente["tipo_cliente"] == "02"){ // Nota de crédito, CCF Beneficios fiscales
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] == "05" && $cliente["tipo_cliente"] == "03"){ // Nota de crédito, CCF Diplomáticos

            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
        }

        if($facturaOriginal["tipoDte"] == "06" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] != "11"){ // Nota de débito, CCF Declarante IVA - Empresa

            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));
            
        }

        if($facturaOriginal["tipoDte"] == "06" && $cliente["tipo_cliente"] == "02"){ // Nota de débito, CCF Empresa con beneficios fiscales

            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] == "06" && $cliente["tipo_cliente"] == "03"){ // Nota de débito, CCF Diplomáticos

            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] == "04" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] == "03"){ // Nota de remisión, CCF Declarante IVA - Empresa
            
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));
            
        }

        if($facturaOriginal["tipoDte"] == "04" && $cliente["tipo_cliente"] == "02" && $facturaOriginal["tipoDte"] == "03"){ // Nota de remisión, CCF Empresa con beneficios fiscales
            
           // Recorrer cada producto y mapear los datos
           $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

           // Formatea el resultado a 8 decimales
           $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] == "04" && $cliente["tipo_cliente"] == "03" && $facturaOriginal["tipoDte"] == "03"){ // Nota de remisión, CCF Diplomaticos
            
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] === "04" && $cliente["tipo_cliente"] === "01" && $facturaOriginal["tipoDte"] === "11"){ // Nota de remisión, XPORT Declarante IVA - Empresa
            
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
        }

        if($facturaOriginal["tipoDte"] === "04" && $cliente["tipo_cliente"] === "02" && $facturaOriginal["tipoDte"] === "11"){ // Nota de remisión, XPORT Empresa beneficios fiscales
            
           // Recorrer cada producto y mapear los datos
           $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

           // Formatea el resultado a 8 decimales
           $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] === "04" && $cliente["tipo_cliente"] === "03" && $facturaOriginal["tipoDte"] === "11"){ // Nota de remisión, XPORT Diplomaticos
            
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
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
                        "version" => 2,
                        "ambiente" => "01",
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "fecAnula" => $factura["fecEmi"],
                        "horAnula" => $factura["horEmi"]
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nombre" => $empresa["nombre"],
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "nomEstablecimiento" => $empresa["nombre"],
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]                    
                    ],
                    "documento" => [
                        "tipoDte"=> $facturaOriginal["tipoDte"],
                        "codigoGeneracion"=> $facturaOriginal["codigoGeneracion"],
                        "selloRecibido"=> $facturaOriginal["sello"],
                        "numeroControl"=> $facturaOriginal["numeroControl"],
                        "fecEmi"=> $facturaOriginal["fecEmi"],
                        "montoIva"=> $montoIva,
                        "codigoGeneracionR"=> null,
                        "tipoDocumento"=> $tipoDocu,
                        "numDocumento"=> $docu,
                        "nombre"=> $cliente["nombre"],
                        "telefono"=> $cliente["telefono"],  
                        "correo"=> $cliente["correo"]
                    ],
                    "motivo" => [
                        "tipoAnulacion" => 2,
                        "motivoAnulacion" => $factura["motivoAnulacion"],
                        "nombreResponsable" => $empresa["nombre"],
                        "tipDocResponsable" => "36",
                        "numDocResponsable" => $empresa["nit"],
                        "nombreSolicita" => $cliente["nombre"],
                        "tipDocSolicita" => "36",
                        "numDocSolicita" => $cliente["NIT"]
                    ]
                ]
            ];
        

        
        
        // Convertir el array PHP a JSON
        $jsonData = json_encode($data);

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar cURL para enviar datos JSON en una solicitud POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ejecutar la solicitud y almacenar la respuesta
        $response = curl_exec($ch);
        
        // Verificar si hubo algún error
        if (curl_errno($ch)) {
            echo json_encode(['error' => curl_error($ch)]);
        } else {
            
            // Decodificar la respuesta del servidor
            $decodedResponse = json_decode($response, true);

            // Acceder al campo "body" de la respuesta
            $bodyContent = $decodedResponse['body'] ?? null;

            $tabla = "anuladas";
            $item1 = "firmaDigital";
            $valor1 = $bodyContent;
            $item2 = "id";
            $valor2 = $this->idFacturaA;

            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
            
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no"); // Ahora ambos resultados están en formato JSON
            }
            


        }

        // Cerrar la sesión cURL
        curl_close($ch);


	}
    

     /*=============================================
	FIRMAR FACTURA
	=============================================*/	
	public $idFactura;

	public function ajaxEnviarFactura() {
        

        $item = "id";
        $orden = "id";
		$valor = $this->idFactura;
        $optimizacion = "no";

		$factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $item = "id";
        $orden = "id";
        $valor = "1";

        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        
        
        $item = "id";
        $orden = "id";
        $valor = $factura["id_cliente"];

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

        $item = "id";
        $orden = "id";
        $valor = $factura["idMotorista"];

        $motorista = ControladorClientes::ctrMostrarMotoristas($item, $valor, $orden);

        // Decodificar los productos de la factura
        $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo

        // Inicializar el array cuerpoDocumento
        $cuerpoDocumento = [];

        // Número de ítem inicial
        $numItem = 1;
        $descuentoGobal = 0;

        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "00"){ // Factura, persona normal
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $descTotal = ($producto["descuentoConIva"] * $producto["cantidad"]);
                if($productoLei["exento_iva"] == "no"){
                    $ivaItem = ($producto["totalProducto"] - $descTotal) - (($producto["totalProducto"] - $descTotal) / 1.13);
                } else {
                    $ivaItem = 0.0;
                }
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                $totalPro = ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $item = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioConIva"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuentoConIva"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                    "ivaItem" => $ivaItemTotalFormateado
                ];
                
                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["ventaNoSuj"] = 0.0;
                    $item["ventaExenta"] = 0.0;
                    $item["ventaGravada"] = $totalProF;
                } else {
                    $item["ventaNoSuj"] = 0.0;
                    $item["ventaExenta"] = $totalProF;
                    $item["ventaGravada"] = 0.0;
                }
                
                // Agregar el item al array final
                $cuerpoDocumento[] = $item;                

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuentoConIva"] * $producto["cantidad"];
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

            $totalLetras = convertirMontoALetras(round((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))), 2);

            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }

            $numeroDUI = $cliente["DUI"]; // Tu número original
            $modificadoDUI = substr_replace($numeroDUI, '-', -1, 0);

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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "13",
                        "numDocumento" => $modificadoDUI,
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
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($factura["total"]),

                        "subTotalVentas" => floatval($factura["total"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($factura["total"]),
                        "ivaRete1" => $retencionGranContribuyente,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => round((($factura["totalSinIva"])+($factura["totalSinIva"]*0.13)), 2),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13)), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => $ivaTotalF,
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

        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "01"){ // Factura, persona que declara IVA - empresa
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $descTotal = ($producto["descuentoConIva"] * $producto["cantidad"]);
                    if($productoLei["exento_iva"] == "no"){
                        $ivaItem = ($producto["totalProducto"] - $descTotal) - (($producto["totalProducto"] - $descTotal) / 1.13);
                    } else {
                        $ivaItem = 0.0;
                    }
                    
                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));
    
                    $totalPro = ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));
    
                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "numeroDocumento" => null,
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "precioUni" => $producto["precioConIva"], // Precio con impuestos del producto
                        "montoDescu" => $producto["descuentoConIva"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "tributos" => null,
                        "psv" => 0,
                        "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                        "ivaItem" => $ivaItemTotalFormateado
                    ];
                    
                    // Agregar las claves según la condición
                    if ($productoLei["exento_iva"] == "no") {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = 0.0;
                        $item["ventaGravada"] = $totalProF;
                    } else {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = $totalProF;
                        $item["ventaGravada"] = 0.0;
                    }
                    
                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  
    
                    // Incrementar el número de ítem
                    $numItem++;
                    $descuentoGobal += $producto["descuentoConIva"] * $producto["cantidad"];

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

            $totalLetras = convertirMontoALetras(round((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))), 2);
            $ncrCliente = "";
            if($cliente["NRC"] == "" || $cliente["NRC"] == "00000") {
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
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
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($factura["total"]),

                        "subTotalVentas" => floatval($factura["total"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($factura["total"]),
                        "ivaRete1" => $retencionGranContribuyente,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => round((($factura["totalSinIva"])+($factura["totalSinIva"]*0.13)), 2),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13)), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => $ivaTotalF,
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

        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "02"){ // Factura, empresa con beneficios fiscales
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
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
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
                        "totalNoSuj" => floatval($factura["totalSinIva"]),
                        "totalExenta" => 0.0,
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
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

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // CCF, Declarante IVA - Empresa
            $sinIva = 0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $item = [
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
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["tributos"] = ["20"];
                } else {
                    $item["tributos"] = null;
                    $sinIva += $totalProF;
                }

                // Agregar el item al array final
                $cuerpoDocumento[] = $item;  


                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            
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

            $totalLetras = convertirMontoALetras(floatval($factura["total"] - $retencionGranContribuyente));
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
                        "version" => 3,
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
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
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($factura["totalSinIva"]),

                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => $retencionGranContribuyente,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => round(($factura["totalSinIva"]-$sinIva) * 0.13, 2)
                                ]
                        ],
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["total"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["total"] - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "ivaPerci1" => 0.0,
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

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "02"){ // CCF, Empresa con beneficios fiscales
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
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            
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

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
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
                        "version" => 3,
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
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
                        "totalNoSuj" => floatval($factura["totalSinIva"]),
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,

                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => 0.0,
                        "tributos" => null,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"]), 2),
                        "totalLetras" => $totalLetras,
                        "ivaPerci1" => 0.0,
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

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "03"){ // CCF, Diplomáticos
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
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            
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

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
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
                        "version" => 3,
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
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
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => 0.0,
                        "tributos" => null,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"])),
                        "totalLetras" => $totalLetras,
                        "ivaPerci1" => 0.0,
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

        if($factura["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Exportación, Declarante IVA - Empresa
            
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "tributos" => null,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
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

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $tipoDocumento = "";
            if($cliente["departamento"] == "00" || $cliente["municipio"] == "00"){
                $tipoDocumento = "37";
            } else {
                $tipoDocumento = "36";
            }
            
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            $totalOpera = $factura["flete"] + $factura["seguro"] + $factura["totalSinIva"];

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
                        "motivoContigencia" => null,
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
                        "correo" => $empresa["correo"],
                        "codEstableMH" => "M001",
                        "codEstable" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "tipoItemExpor" => 1, // Solo para vender bienes
                        "recintoFiscal" => $factura["recintoFiscal"],
                        "regimen" => $factura["regimen"],
                    ],
                    "receptor" => [
                        "nombre" => $cliente["nombre"],
                        "tipoDocumento" => $tipoDocumento,
                        "numDocumento" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "codPais" => $cliente["codPais"],
                        "nombrePais" => $cliente["nombrePais"],
                        "complemento" => $cliente["direccion"],
                        "tipoPersona" => intval($cliente["tipoPersona"]),
                        "descActividad" => $cliente["descActividad"],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => [
                        [
                            "codDocAsociado" => 4, // de transporte
                            "descDocumento" => null,
                            "detalleDocumento" => null,
                            "placaTrans" => $motorista["placaMotorista"],
                            "modoTransp" => intval($factura["modoTransporte"]),
                            "numConductor" => $motorista["duiMotorista"],
                            "nombreConductor" => $motorista["nombre"]
                        ]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalGravada" => floatval($factura["totalSinIva"]),
                        "descuento" => 0.0,

                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "seguro" => floatval($factura["seguro"]),
                        "flete" => floatval($factura["flete"]),
                        "montoTotalOperacion" => $totalOpera,
                        "totalNoGravado" => 0.0,
                        "totalPagar" => $totalOpera,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => round(($factura["condicionOperacion"]), 2),
                        "pagos" => null,
                        "codIncoterms" => null,
                        "descIncoterms" => null,
                        "numPagoElectronico" => null,
                        "observaciones" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){ // Factura sujeto excluido, persona normal
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
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto                    
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "compra" => $totalProF, // Valor de venta gravada
                    
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
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

            
            $totalProF = floatval(number_format($totalPro, 2, '.', ''));
            $renta = floatval(number_format(($factura["totalSinIva"] * 0.10), 2, '.', ''));
            $totalSinRenta = floatval(number_format(($factura["totalSinIva"] - $renta), 2, '.', ''));
            $totalLetras = convertirMontoALetras(floatval($totalSinRenta));
            
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }

            $numeroDUI = $cliente["DUI"]; // Tu número original
            $modificadoDUI = $numeroDUI;

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
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "sujetoExcluido" => [
                        "tipoDocumento" => "13",
                        "numDocumento" => $modificadoDUI,
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
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalCompra" => floatval($factura["totalSinIva"]),
                        "descu" => 0.0,
                        "totalDescu" => floatval($descuentoGobalF),
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => 0.0,
                        "reteRenta" => $renta,
                        "totalPagar" => $totalSinRenta,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "observaciones" => null,

                    ],
                    "apendice" => null
                ]
            ];


        }
        $item = "id";
        $orden = "id";
        $valor = $factura["idFacturaRelacionada"];
        $optimizacion = "no";

        $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] != "11"){ // Nota de crédito, CCF Declarante IVA - Empresa

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;

            $sinIva = 0;

            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $item = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                ];
                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["tributos"] = ["20"];
                } else {
                    $item["tributos"] = null;
                    $sinIva += $totalProF;
                }

                // Agregar el item al array final
                $cuerpoDocumento[] = $item;  

                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }
            
            $retencionGranContribuyente = 0.0;
            if($factura["gran_contribuyente"] == "Si"){
                $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
            }

            $opera = $totalGravado + ($totalGravado * 0.13) - $retencionGranContribuyente;
            $operaR = floatval(number_format($opera, 2, '.', ''));

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = isset($partes[1]) ? str_pad($partes[1], 2, '0', STR_PAD_RIGHT) : '00'; // Siempre dos decimales
                
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
            
                // Si el número es 0, devolvemos "cero"
                if ($numero == 0) {
                    return "cero";
                }
            
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
            

            $totalLetras = convertirMontoALetras(floatval($totalGravado - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
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
                        "version" => 3,
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
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
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
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => $totalGravado,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "totalDescu" => 0.0,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => round(($totalGravado - $sinIva) * 0.13, 2)
                                ]
                        ],
                        "subTotal" => $totalGravado,
                        "ivaPerci1" => 0.0,
                        "ivaRete1" => $retencionGranContribuyente,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                    ],
                    "extension" => null,
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "02"){ // Nota de crédito, CCF Beneficios fiscales

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }
            
            $opera = $totalGravado;
            $operaR = floatval(number_format($opera, 2, '.', ''));

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

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
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
                        "version" => 3,
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
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
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
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => $totalGravado,
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "totalDescu" => 0.0,
                        "tributos" => null,
                        "subTotal" => $totalGravado,
                        "ivaPerci1" => 0.0,
                        "ivaRete1" => 0.0,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                    ],
                    "extension" => null,
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "03"){ // Nota de crédito, CCF Diplomáticos

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }
            
            $opera = $totalGravado;
            $operaR = floatval(number_format($opera, 2, '.', ''));

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

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
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
                        "version" => 3,
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
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
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
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => $totalGravado,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "totalDescu" => 0.0,
                        "tributos" => null,
                        "subTotal" => $totalGravado,
                        "ivaPerci1" => 0.0,
                        "ivaRete1" => 0.0,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                    ],
                    "extension" => null,
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] != "11"){ // Nota de débito, CCF Declarante IVA - Empresa

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;

            $sinIva = 0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $item = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                ];

                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["tributos"] = ["20"];
                } else {
                    $item["tributos"] = null;
                    $sinIva += $totalProF;
                }

                // Agregar el item al array final
                $cuerpoDocumento[] = $item;  


                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }
            
            $opera = $totalGravado + ($totalGravado * 0.13);
            $operaR = floatval(number_format($opera, 2, '.', ''));

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

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
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
                        "version" => 3,
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
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
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
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => $totalGravado,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "totalDescu" => 0.0,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => round(($totalGravado - $sinIva) * 0.13, 2)
                                ]
                        ],
                        "subTotal" => $totalGravado,
                        "ivaPerci1" => 0.0,
                        "ivaRete1" => 0.0,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "numPagoElectronico" => null
                    ],
                    "extension" => null,
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "02"){ // Nota de débito, CCF Empresa con beneficios fiscales

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }
            
            $opera = $totalGravado;
            $operaR = floatval(number_format($opera, 2, '.', ''));

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

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
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
                        "version" => 3,
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
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
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
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => $totalGravado,
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "totalDescu" => 0.0,
                        "tributos" => null,
                        "subTotal" => $totalGravado,
                        "ivaPerci1" => 0.0,
                        "ivaRete1" => 0.0,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "numPagoElectronico" => null
                    ],
                    "extension" => null,
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "03"){ // Nota de débito, CCF Diplomáticos

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }
            
            $opera = $totalGravado;
            $operaR = floatval(number_format($opera, 2, '.', ''));

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

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
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
                        "version" => 3,
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
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
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
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => $totalGravado,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "totalDescu" => 0.0,
                        "tributos" => null,
                        "subTotal" => $totalGravado,
                        "ivaPerci1" => 0.0,
                        "ivaRete1" => 0.0,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "numPagoElectronico" => null
                    ],
                    "extension" => null,
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        
        if($factura["idFacturaRelacionada"] != ""){

            if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] == "03"){ // Nota de remisión, CCF Declarante IVA - Empresa
            
                $totalGravado = 0.0;
    
                $sinIva = 0;
    
                // Recorrer cada producto y mapear los datos
                foreach ($productos as $producto) {
                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    
                    $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));
    
                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "numeroDocumento" => null,
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                        "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => $totalProF, // Valor de venta gravada
                    ];
                    // Agregar las claves según la condición
                    if ($productoLei["exento_iva"] == "no") {
                        $item["tributos"] = ["20"];
                    } else {
                        $item["tributos"] = null;
                        $sinIva += $totalProF;
                    }
    
                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  
    
                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                    $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
                }
                
                $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
    
                $opera = $totalGravado + ($totalGravado * 0.13);
                $operaR = floatval(number_format($opera, 2, '.', ''));
    
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
    
                $totalLetras = convertirMontoALetras(floatval($totalGravado));
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
                            "version" => 3,
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
                        "documentoRelacionado" => null,
                        "emisor" => [
                            "nit" => $empresa["nit"],
                            "nrc" => $empresa["nrc"],
                            "nombre" => $empresa["nombre"],
                            "codActividad" => $empresa["codActividad"],
                            "descActividad" => $empresa["desActividad"],
                            "nombreComercial" => null,
                            "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                            "codEstableMH" => "M001",
                            "codEstable" => "M001",
                            "codPuntoVentaMH" => "P001",
                            "codPuntoVenta" => "P001",
                            "direccion" => [
                                "departamento" => $empresa["departamento"],
                                "municipio" => $empresa["municipio"],
                                "complemento" => $empresa["direccion"]
                            ],
                            "telefono" => $empresa["telefono"],
                            "correo" => $empresa["correo"]
                        ],
                        "receptor" => [
                            "tipoDocumento" => "36",
                            "nrc" => $ncrCliente,
                            "numDocumento" => $cliente["NIT"],
                            "nombre" => $cliente["nombre"],
                            "codActividad" => $cliente["codActividad"],
                            "descActividad" => $cliente["descActividad"],
                            "nombreComercial" => null,
                            "direccion" => [
                                "departamento" => $cliente["departamento"],
                                "municipio" => $cliente["municipio"],
                                "complemento" => $cliente["direccion"]
                            ],
                            "telefono" => $cliente["telefono"],
                            "correo" => $cliente["correo"],
                            "bienTitulo" => "04"
                        ],
                        "ventaTercero" => null,
                        "cuerpoDocumento" => $cuerpoDocumento,
                        "resumen" => [
                            "totalNoSuj" => 0.0,
                            "totalExenta" => 0.0,
                            "totalGravada" => $totalGravado,
                            "subTotalVentas" => $totalGravado,
                            "descuNoSuj" => 0.0,
                            "descuExenta" => 0.0,
                            "descuGravada" => 0.0,
                            "porcentajeDescuento" => 0.0,
                            "totalDescu" => $descuentoGobalF,
                            "tributos" => [
                                    [
                                        "codigo" => "20",
                                        "descripcion" => "Impuesto al Valor Agregado 13%",
                                        "valor" => round($totalGravado * 0.13, 2)
                                    ]
                            ],
                            "subTotal" => $totalGravado,
                            "montoTotalOperacion" => $operaR,
                            "totalLetras" => $totalLetras
                        ],
                        "extension" => null,
                        "apendice" => null
                    ]
                ];
                //echo json_encode($data);
                //return;
                
            }

            if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "02" && $facturaOriginal["tipoDte"] == "03"){ // Nota de remisión, CCF Empresa con beneficios fiscales
            
                $totalGravado = 0.0;
                
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
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                        "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => 0.0, // Valor de venta gravada
                        "tributos" => [
                                    "20"
                        ],
                    ];
    
                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                    $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
                }
                $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
                
                $opera = $totalGravado;
                $operaR = floatval(number_format($opera, 2, '.', ''));
    
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
    
                $totalLetras = convertirMontoALetras(floatval($totalGravado));
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
                            "version" => 3,
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
                        "documentoRelacionado" => null,
                        "emisor" => [
                            "nit" => $empresa["nit"],
                            "nrc" => $empresa["nrc"],
                            "nombre" => $empresa["nombre"],
                            "codActividad" => $empresa["codActividad"],
                            "descActividad" => $empresa["desActividad"],
                            "nombreComercial" => null,
                            "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                            "codEstableMH" => "M001",
                            "codEstable" => "M001",
                            "codPuntoVentaMH" => "P001",
                            "codPuntoVenta" => "P001",
                            "direccion" => [
                                "departamento" => $empresa["departamento"],
                                "municipio" => $empresa["municipio"],
                                "complemento" => $empresa["direccion"]
                            ],
                            "telefono" => $empresa["telefono"],
                            "correo" => $empresa["correo"]
                        ],
                        "receptor" => [
                            "tipoDocumento" => "36",
                            "nrc" => $ncrCliente,
                            "numDocumento" => $cliente["NIT"],
                            "nombre" => $cliente["nombre"],
                            "codActividad" => $cliente["codActividad"],
                            "descActividad" => $cliente["descActividad"],
                            "nombreComercial" => null,
                            "direccion" => [
                                "departamento" => $cliente["departamento"],
                                "municipio" => $cliente["municipio"],
                                "complemento" => $cliente["direccion"]
                            ],
                            "telefono" => $cliente["telefono"],
                            "correo" => $cliente["correo"],
                            "bienTitulo" => "04"
                        ],
                        "ventaTercero" => null,
                        "cuerpoDocumento" => $cuerpoDocumento,
                        "resumen" => [
                            "totalNoSuj" => $totalGravado,
                            "totalExenta" => 0.0,
                            "totalGravada" => 0.0,
                            "subTotalVentas" => $totalGravado,
                            "descuNoSuj" => 0.0,
                            "descuExenta" => 0.0,
                            "descuGravada" => 0.0,
                            "porcentajeDescuento" => 0.0,
                            "totalDescu" => $descuentoGobalF,
                            "tributos" => [
                                    [
                                        "codigo" => "20",
                                        "descripcion" => "Impuesto al Valor Agregado 13%",
                                        "valor" => 0.0
                                    ]
                            ],
                            "subTotal" => $totalGravado,
                            "montoTotalOperacion" => $operaR,
                            "totalLetras" => $totalLetras
                        ],
                        "extension" => null,
                        "apendice" => null
                    ]
                ];
                //echo json_encode($data);
                //return;
                
            }
    
            if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "03" && $facturaOriginal["tipoDte"] == "03"){ // Nota de remisión, CCF Diplomaticos
                
                $totalGravado = 0.0;
                
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
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                        "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => 0.0, // Valor de venta gravada
                        "tributos" => [
                                    "20"
                        ],
                    ];
    
                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                    $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
                }
                
                $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
                $opera = $totalGravado;
                $operaR = floatval(number_format($opera, 2, '.', ''));
    
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
    
                $totalLetras = convertirMontoALetras(floatval($totalGravado));
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
                            "version" => 3,
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
                        "documentoRelacionado" => null,
                        "emisor" => [
                            "nit" => $empresa["nit"],
                            "nrc" => $empresa["nrc"],
                            "nombre" => $empresa["nombre"],
                            "codActividad" => $empresa["codActividad"],
                            "descActividad" => $empresa["desActividad"],
                            "nombreComercial" => null,
                            "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                            "codEstableMH" => "M001",
                            "codEstable" => "M001",
                            "codPuntoVentaMH" => "P001",
                            "codPuntoVenta" => "P001",
                            "direccion" => [
                                "departamento" => $empresa["departamento"],
                                "municipio" => $empresa["municipio"],
                                "complemento" => $empresa["direccion"]
                            ],
                            "telefono" => $empresa["telefono"],
                            "correo" => $empresa["correo"]
                        ],
                        "receptor" => [
                            "tipoDocumento" => "36",
                            "nrc" => $ncrCliente,
                            "numDocumento" => $cliente["NIT"],
                            "nombre" => $cliente["nombre"],
                            "codActividad" => $cliente["codActividad"],
                            "descActividad" => $cliente["descActividad"],
                            "nombreComercial" => null,
                            "direccion" => [
                                "departamento" => $cliente["departamento"],
                                "municipio" => $cliente["municipio"],
                                "complemento" => $cliente["direccion"]
                            ],
                            "telefono" => $cliente["telefono"],
                            "correo" => $cliente["correo"],
                            "bienTitulo" => "04"
                        ],
                        "ventaTercero" => null,
                        "cuerpoDocumento" => $cuerpoDocumento,
                        "resumen" => [
                            "totalNoSuj" => 0.0,
                            "totalExenta" => $totalGravado,
                            "totalGravada" => 0.0,
                            "subTotalVentas" => $totalGravado,
                            "descuNoSuj" => 0.0,
                            "descuExenta" => 0.0,
                            "descuGravada" => 0.0,
                            "porcentajeDescuento" => 0.0,
                            "totalDescu" => $descuentoGobalF,
                            "tributos" => [
                                    [
                                        "codigo" => "20",
                                        "descripcion" => "Impuesto al Valor Agregado 13%",
                                        "valor" => 0.0
                                    ]
                            ],
                            "subTotal" => $totalGravado,
                            "montoTotalOperacion" => $operaR,
                            "totalLetras" => $totalLetras
                        ],
                        "extension" => null,
                        "apendice" => null
                    ]
                ];
                //echo json_encode($data);
                //return;
                
            }
    
            if($factura["tipoDte"] === "04" && $cliente["tipo_cliente"] === "01" && $facturaOriginal["tipoDte"] === "11"){ // Nota de remisión, XPORT Declarante IVA - Empresa
                
                $totalGravado = 0.0;
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
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                        "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => $totalProF, // Valor de venta gravada
                        "tributos" => null,
                    ];
    
                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                    $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
                }
                $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
                $opera = $totalGravado;
                $operaR = floatval(number_format($opera, 2, '.', ''));
    
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
    
                $totalLetras = convertirMontoALetras(floatval($totalGravado));
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
                            "version" => 3,
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
                        "documentoRelacionado" => null,
                        "emisor" => [
                            "nit" => $empresa["nit"],
                            "nrc" => $empresa["nrc"],
                            "nombre" => $empresa["nombre"],
                            "codActividad" => $empresa["codActividad"],
                            "descActividad" => $empresa["desActividad"],
                            "nombreComercial" => null,
                            "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                            "codEstableMH" => "M001",
                            "codEstable" => "M001",
                            "codPuntoVentaMH" => "P001",
                            "codPuntoVenta" => "P001",
                            "direccion" => [
                                "departamento" => $empresa["departamento"],
                                "municipio" => $empresa["municipio"],
                                "complemento" => $empresa["direccion"]
                            ],
                            "telefono" => $empresa["telefono"],
                            "correo" => $empresa["correo"]
                        ],
                        "receptor" => [
                            "tipoDocumento" => "36",
                            "nrc" => $ncrCliente,
                            "numDocumento" => $cliente["NIT"],
                            "nombre" => $cliente["nombre"],
                            "codActividad" => $cliente["codActividad"],
                            "descActividad" => $cliente["descActividad"],
                            "nombreComercial" => null,
                            "direccion" => [
                                "departamento" => $cliente["departamento"],
                                "municipio" => $cliente["municipio"],
                                "complemento" => $cliente["direccion"]
                            ],
                            "telefono" => $cliente["telefono"],
                            "correo" => $cliente["correo"],
                            "bienTitulo" => "04"
                        ],
                        "ventaTercero" => null,
                        "cuerpoDocumento" => $cuerpoDocumento,
                        "resumen" => [
                            "totalNoSuj" => 0.0,
                            "totalExenta" => 0.0,
                            "totalGravada" => $totalGravado,
                            "subTotalVentas" => $totalGravado,
                            "descuNoSuj" => 0.0,
                            "descuExenta" => 0.0,
                            "descuGravada" => 0.0,
                            "porcentajeDescuento" => 0.0,
                            "totalDescu" => $descuentoGobalF,
                            "tributos" => null,
                            "subTotal" => $totalGravado,
                            "montoTotalOperacion" => $operaR,
                            "totalLetras" => $totalLetras
                        ],
                        "extension" => null,
                        "apendice" => null
                    ]
                ];
                //echo json_encode($data);
                //return;
                
            }
    
            if($factura["tipoDte"] === "04" && $cliente["tipo_cliente"] === "02" && $facturaOriginal["tipoDte"] === "11"){ // Nota de remisión, XPORT Empresa beneficios fiscales
                
                $totalGravado = 0.0;
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
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                        "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => 0.0, // Valor de venta gravada
                        "tributos" => null,
                    ];
    
                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                    $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
                }
                $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
                $opera = $totalGravado;
                $operaR = floatval(number_format($opera, 2, '.', ''));
    
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
    
                $totalLetras = convertirMontoALetras(floatval($totalGravado));
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
                            "version" => 3,
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
                        "documentoRelacionado" => null,
                        "emisor" => [
                            "nit" => $empresa["nit"],
                            "nrc" => $empresa["nrc"],
                            "nombre" => $empresa["nombre"],
                            "codActividad" => $empresa["codActividad"],
                            "descActividad" => $empresa["desActividad"],
                            "nombreComercial" => null,
                            "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                            "codEstableMH" => "M001",
                            "codEstable" => "M001",
                            "codPuntoVentaMH" => "P001",
                            "codPuntoVenta" => "P001",
                            "direccion" => [
                                "departamento" => $empresa["departamento"],
                                "municipio" => $empresa["municipio"],
                                "complemento" => $empresa["direccion"]
                            ],
                            "telefono" => $empresa["telefono"],
                            "correo" => $empresa["correo"]
                        ],
                        "receptor" => [
                            "tipoDocumento" => "36",
                            "nrc" => $ncrCliente,
                            "numDocumento" => $cliente["NIT"],
                            "nombre" => $cliente["nombre"],
                            "codActividad" => $cliente["codActividad"],
                            "descActividad" => $cliente["descActividad"],
                            "nombreComercial" => null,
                            "direccion" => [
                                "departamento" => $cliente["departamento"],
                                "municipio" => $cliente["municipio"],
                                "complemento" => $cliente["direccion"]
                            ],
                            "telefono" => $cliente["telefono"],
                            "correo" => $cliente["correo"],
                            "bienTitulo" => "04"
                        ],
                        "ventaTercero" => null,
                        "cuerpoDocumento" => $cuerpoDocumento,
                        "resumen" => [
                            "totalNoSuj" => $totalGravado,
                            "totalExenta" => 0.0,
                            "totalGravada" => 0.0,
                            "subTotalVentas" => $totalGravado,
                            "descuNoSuj" => 0.0,
                            "descuExenta" => 0.0,
                            "descuGravada" => 0.0,
                            "porcentajeDescuento" => 0.0,
                            "totalDescu" => $descuentoGobalF,
                            "tributos" => null,
                            "subTotal" => $totalGravado,
                            "montoTotalOperacion" => $operaR,
                            "totalLetras" => $totalLetras
                        ],
                        "extension" => null,
                        "apendice" => null
                    ]
                ];
                //echo json_encode($data);
                //return;
                
            }
    
            if($factura["tipoDte"] === "04" && $cliente["tipo_cliente"] === "03" && $facturaOriginal["tipoDte"] === "11"){ // Nota de remisión, XPORT Diplomaticos
                
                $totalGravado = 0.0;
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
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                        "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => 0.0, // Valor de venta gravada
                        "tributos" => null,
                    ];
    
                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                    $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
                }
                $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
                $opera = $totalGravado;
                $operaR = floatval(number_format($opera, 2, '.', ''));
    
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
    
                $totalLetras = convertirMontoALetras(floatval($totalGravado));
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
                            "version" => 3,
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
                        "documentoRelacionado" => null,
                        "emisor" => [
                            "nit" => $empresa["nit"],
                            "nrc" => $empresa["nrc"],
                            "nombre" => $empresa["nombre"],
                            "codActividad" => $empresa["codActividad"],
                            "descActividad" => $empresa["desActividad"],
                            "nombreComercial" => null,
                            "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                            "codEstableMH" => "M001",
                            "codEstable" => "M001",
                            "codPuntoVentaMH" => "P001",
                            "codPuntoVenta" => "P001",
                            "direccion" => [
                                "departamento" => $empresa["departamento"],
                                "municipio" => $empresa["municipio"],
                                "complemento" => $empresa["direccion"]
                            ],
                            "telefono" => $empresa["telefono"],
                            "correo" => $empresa["correo"]
                        ],
                        "receptor" => [
                            "tipoDocumento" => "36",
                            "nrc" => $ncrCliente,
                            "numDocumento" => $cliente["NIT"],
                            "nombre" => $cliente["nombre"],
                            "codActividad" => $cliente["codActividad"],
                            "descActividad" => $cliente["descActividad"],
                            "nombreComercial" => null,
                            "direccion" => [
                                "departamento" => $cliente["departamento"],
                                "municipio" => $cliente["municipio"],
                                "complemento" => $cliente["direccion"]
                            ],
                            "telefono" => $cliente["telefono"],
                            "correo" => $cliente["correo"],
                            "bienTitulo" => "04"
                        ],
                        "ventaTercero" => null,
                        "cuerpoDocumento" => $cuerpoDocumento,
                        "resumen" => [
                            "totalNoSuj" => 0.0,
                            "totalExenta" => $totalGravado,
                            "totalGravada" => 0.0,
                            "subTotalVentas" => $totalGravado,
                            "descuNoSuj" => 0.0,
                            "descuExenta" => 0.0,
                            "descuGravada" => 0.0,
                            "porcentajeDescuento" => 0.0,
                            "totalDescu" => $descuentoGobalF,
                            "tributos" => null,
                            "subTotal" => $totalGravado,
                            "montoTotalOperacion" => $operaR,
                            "totalLetras" => $totalLetras
                        ],
                        "extension" => null,
                        "apendice" => null
                    ]
                ];
                //echo json_encode($data);
                //return;
                
            }
        }
        
        if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "02"){ // Nota de remisión, CCF Empresa con beneficios fiscales
            
            $totalGravado = 0.0;
            
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
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => [
                                "20"
                    ],
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalGravado += $totalProF;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            $opera = $totalGravado;
            $operaR = floatval(number_format($opera, 2, '.', ''));

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

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
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
                        "version" => 3,
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
                    "documentoRelacionado" => null,
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "codEstableMH" => "M001",
                        "codEstable" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "nrc" => $ncrCliente,
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"],
                        "bienTitulo" => "04"
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => $totalGravado,
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => 0.0
                                ]
                        ],
                        "subTotal" => $totalGravado,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras
                    ],
                    "extension" => null,
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "03"){ // Nota de remisión, CCF Diplomaticos
            
            $totalGravado = 0.0;
            
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
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => [
                                "20"
                    ],
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalGravado += $totalProF;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            $opera = $totalGravado;
            $operaR = floatval(number_format($opera, 2, '.', ''));

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

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
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
                        "version" => 3,
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
                    "documentoRelacionado" => null,
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "codEstableMH" => "M001",
                        "codEstable" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "nrc" => $ncrCliente,
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"],
                        "bienTitulo" => "04"
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => $totalGravado,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => 0.0
                                ]
                        ],
                        "subTotal" => $totalGravado,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras
                    ],
                    "extension" => null,
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] === "04" && $cliente["tipo_cliente"] === "01"){ // Nota de remisión, XPORT Declarante IVA - Empresa
            
            $totalGravado = 0.0;
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
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "tributos" => null,
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalGravado += $totalProF;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            $opera = $totalGravado;
            $operaR = floatval(number_format($opera, 2, '.', ''));

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

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
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
                        "version" => 3,
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
                    "documentoRelacionado" => null,
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "codEstableMH" => "M001",
                        "codEstable" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "nrc" => $ncrCliente,
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"],
                        "bienTitulo" => "04"
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => $totalGravado,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => $totalGravado,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras
                    ],
                    "extension" => null,
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }
        
        // Convertir el array PHP a JSON
        $jsonData = json_encode($data);

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar cURL para enviar datos JSON en una solicitud POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ejecutar la solicitud y almacenar la respuesta
        $response = curl_exec($ch);
        
        // Verificar si hubo algún error
        if (curl_errno($ch)) {
            echo json_encode(['error' => curl_error($ch)]);
        } else {
            
            // Decodificar la respuesta del servidor
            $decodedResponse = json_decode($response, true);

            // Acceder al campo "body" de la respuesta
            $bodyContent = $decodedResponse['body'] ?? null;

            $tabla = "facturas_locales";
            $item1 = "firmaDigital";
            $valor1 = $bodyContent;
            $item2 = "id";
            $valor2 = $this->idFactura;

            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);

            $tabla = "facturas_locales";
            $item1 = "json_guardado";
            $valor1 = json_encode($data);
            $item2 = "id";
            $valor2 = $this->idFactura;

            $respuesta2 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
            
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no"); // Ahora ambos resultados están en formato JSON
            }
            


        }

        // Cerrar la sesión cURL
        curl_close($ch);



	}

    /*=============================================
	FIRMAR FACTURA CONTINGENCIA
	=============================================*/	
	public $idFacturaContingencia;

	public function ajaxEnviarFacturaContingencia() {
        

        $item = "id";
        $orden = "id";
		$valor = $this->idFacturaContingencia;
        $optimizacion = "no";

		$factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $item = "id";
        $orden = "id";
        $valor = "1";

        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        
        
        $item = "id";
        $orden = "id";
        $valor = $factura["id_cliente"];

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

        $item = "id";
        $orden = "id";
        $valor = $factura["idMotorista"];

        $motorista = ControladorClientes::ctrMostrarMotoristas($item, $valor, $orden);

        // Decodificar los productos de la factura
        $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo

        // Inicializar el array cuerpoDocumento
        $cuerpoDocumento = [];

        // Número de ítem inicial
        $numItem = 1;
        $descuentoGobal = 0;
        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "00"){ // Factura, persona normal y persona que declara IVA - empresa
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $descTotal = ($producto["descuentoConIva"] * $producto["cantidad"]);
                if($productoLei["exento_iva"] == "no"){
                    $ivaItem = ($producto["totalProducto"] - $descTotal) - (($producto["totalProducto"] - $descTotal) / 1.13);
                } else {
                    $ivaItem = 0.0;
                }
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                $totalPro = ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                $item = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioConIva"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuentoConIva"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                    "ivaItem" => $ivaItemTotalFormateado
                ];
                
                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["ventaNoSuj"] = 0.0;
                    $item["ventaExenta"] = 0.0;
                    $item["ventaGravada"] = $totalProF;
                } else {
                    $item["ventaNoSuj"] = 0.0;
                    $item["ventaExenta"] = $totalProF;
                    $item["ventaGravada"] = 0.0;
                }
                
                // Agregar el item al array final
                $cuerpoDocumento[] = $item;    

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuentoConIva"] * $producto["cantidad"];
            }

            $descuentoGobalF = floatval(number_format($descuentoGobal, 2, '.', ''));

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

            $totalLetras = convertirMontoALetras(round((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))), 2);

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
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
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
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($factura["total"]),

                        "subTotalVentas" => floatval($factura["total"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($factura["total"]),
                        "ivaRete1" => $retencionGranContribuyente,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => round((($factura["totalSinIva"])+($factura["totalSinIva"]*0.13)), 2),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13)), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => $ivaTotalF,
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

        if($factura["tipoDte"] == "01" &&  $cliente["tipo_cliente"] == "01"){ // Factura, persona normal y persona que declara IVA - empresa
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                if($factura["gran_contribuyente"] == "Si"){
                    $nuevoPrecioSinImpuestos = round(($producto["precioSinImpuestos"] - ($producto["precioSinImpuestos"] * 0.01)), 2);
                    $nuevoPrecioConIva = round(($nuevoPrecioSinImpuestos + ($nuevoPrecioSinImpuestos * 0.13)), 2);
                    $nuevoTotalProducto = round(($nuevoPrecioConIva * $producto["cantidad"]), 2);
                    $nuevoDescuento = round(($producto["descuento"] - ($producto["descuento"] * 0.01)), 2);
                    $nuevoDescuentoConIva = round(($nuevoDescuento +  ($nuevoDescuento * 0.13)), 2);

                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $descTotal = ($nuevoDescuentoConIva * $producto["cantidad"]);
                    if($productoLei["exento_iva"] == "no"){
                        $ivaItem = ($nuevoTotalProducto - $descTotal) - (($nuevoTotalProducto - $descTotal) / 1.13);
                    } else {
                        $ivaItem = 0.0;
                    }
                    
                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                    $totalPro = ($nuevoPrecioConIva - $nuevoDescuentoConIva) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "numeroDocumento" => null,
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "precioUni" => $nuevoPrecioConIva, // Precio con impuestos del producto
                        "montoDescu" => $nuevoDescuentoConIva * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "tributos" => null,
                        "psv" => 0,
                        "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                        "ivaItem" => $ivaItemTotalFormateado
                    ];
                    
                    // Agregar las claves según la condición
                    if ($productoLei["exento_iva"] == "no") {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = 0.0;
                        $item["ventaGravada"] = $totalProF;
                    } else {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = $totalProF;
                        $item["ventaGravada"] = 0.0;
                    }
                    
                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  

                    // Incrementar el número de ítem
                    $numItem++;
                    $descuentoGobal += $nuevoDescuentoConIva * $producto["cantidad"];
                } else {
                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $descTotal = ($producto["descuentoConIva"] * $producto["cantidad"]);
                    if($productoLei["exento_iva"] == "no"){
                        $ivaItem = ($producto["totalProducto"] - $descTotal) - (($producto["totalProducto"] - $descTotal) / 1.13);
                    } else {
                        $ivaItem = 0.0;
                    }
                    
                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));
    
                    $totalPro = ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));
    
                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "numeroDocumento" => null,
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "precioUni" => $producto["precioConIva"], // Precio con impuestos del producto
                        "montoDescu" => $producto["descuentoConIva"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "tributos" => null,
                        "psv" => 0,
                        "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                        "ivaItem" => $ivaItemTotalFormateado
                    ];
                    
                    // Agregar las claves según la condición
                    if ($productoLei["exento_iva"] == "no") {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = 0.0;
                        $item["ventaGravada"] = $totalProF;
                    } else {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = $totalProF;
                        $item["ventaGravada"] = 0.0;
                    }
                    
                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  
    
                    // Incrementar el número de ítem
                    $numItem++;
                    $descuentoGobal += $producto["descuentoConIva"] * $producto["cantidad"];
                }
                
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

            $totalLetras = convertirMontoALetras(round((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13))), 2);
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
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
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
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($factura["total"]),

                        "subTotalVentas" => floatval($factura["total"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($factura["total"]),
                        "ivaRete1" => $retencionGranContribuyente,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => round((($factura["totalSinIva"])+($factura["totalSinIva"]*0.13)), 2),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round((($factura["totalSinIva"] - $retencionGranContribuyente)+($factura["totalSinIva"]*0.13)), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => $ivaTotalF,
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

        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "02"){ // Factura, empresa con beneficios fiscales
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
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
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
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
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
                        "totalNoSuj" => floatval($factura["totalSinIva"]),
                        "totalExenta" => 0.0,
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
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingenca"],
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
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

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // CCF, Declarante IVA - Empresa
            $sinIva = 0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $item = [
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
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["tributos"] = ["20"];
                } else {
                    $item["tributos"] = null;
                    $sinIva += $totalProF;
                }

                // Agregar el item al array final
                $cuerpoDocumento[] = $item;  
                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            
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

            $totalLetras = convertirMontoALetras(floatval($factura["total"] - $retencionGranContribuyente));
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
                        "version" => 3,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
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
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($factura["totalSinIva"]),

                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => $retencionGranContribuyente,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => round(($factura["totalSinIva"] - $sinIva) * 0.13, 2)
                                ]
                        ],
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["total"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["total"] - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "ivaPerci1" => 0.0,
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

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "02"){ // CCF, Empresa con beneficios fiscales
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
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            
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

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
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
                        "version" => 3,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
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
                        "totalNoSuj" => floatval($factura["totalSinIva"]),
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,

                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => 0.0,
                        "tributos" => null,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"]), 2),
                        "totalLetras" => $totalLetras,
                        "ivaPerci1" => 0.0,
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

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "03"){ // CCF, Diplomáticos
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
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
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

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
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
                        "version" => 3,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
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
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
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
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => 0.0,
                        "tributos" => null,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"]), 2),
                        "totalLetras" => $totalLetras,
                        "ivaPerci1" => 0.0,
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

        if($factura["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Exportación, Declarante IVA - Empresa
            
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "tributos" => null,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
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

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
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

            $totalOpera = $factura["flete"] + $factura["seguro"] + $factura["totalSinIva"];

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
                        "tipoContingencia" => intval($factura["tipo_contingencia"]),
                        "motivoContigencia" => $factura["motivo_contingencia"],
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
                        "correo" => $empresa["correo"],
                        "codEstableMH" => "M001",
                        "codEstable" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "tipoItemExpor" => 1, // Solo para vender bienes
                        "recintoFiscal" => $factura["recintoFiscal"],
                        "regimen" => $factura["regimen"],
                    ],
                    "receptor" => [
                        "nombre" => $cliente["nombre"],
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "codPais" => $cliente["codPais"],
                        "nombrePais" => $cliente["nombrePais"],
                        "complemento" => $cliente["direccion"],
                        "tipoPersona" => intval($cliente["tipoPersona"]),
                        "descActividad" => $cliente["descActividad"],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => [
                        [
                            "codDocAsociado" => 4, // de transporte
                            "descDocumento" => null,
                            "detalleDocumento" => null,
                            "placaTrans" => $motorista["placaMotorista"],
                            "modoTransp" => intval($factura["modoTransporte"]),
                            "numConductor" => $motorista["duiMotorista"],
                            "nombreConductor" => $motorista["nombre"]
                        ]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalGravada" => floatval($factura["totalSinIva"]),
                        "descuento" => 0.0,

                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "seguro" => floatval($factura["seguro"]),
                        "flete" => floatval($factura["flete"]),
                        "montoTotalOperacion" => $totalOpera,
                        "totalNoGravado" => 0.0,
                        "totalPagar" => $totalOpera,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "codIncoterms" => null,
                        "descIncoterms" => null,
                        "numPagoElectronico" => null,
                        "observaciones" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){ // Factura sujeto excluido, persona normal
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
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto                    
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "compra" => $totalProF, // Valor de venta gravada
                    
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
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

            
            $totalProF = floatval(number_format($totalPro, 2, '.', ''));
            $renta = floatval(number_format(($factura["totalSinIva"] * 0.10), 2, '.', ''));
            $totalSinRenta = floatval(number_format(($factura["totalSinIva"] - $renta), 2, '.', ''));
            $totalLetras = convertirMontoALetras(floatval($totalSinRenta));
            
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
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
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
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codEstableMH" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "sujetoExcluido" => [
                        "tipoDocumento" => "13",
                        "numDocumento" => $cliente["DUI"],
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
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalCompra" => floatval($factura["totalSinIva"]),
                        "descu" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => 0.0,
                        "reteRenta" => $renta,
                        "totalPagar" => $totalSinRenta,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "observaciones" => null,

                    ],
                    "apendice" => null
                ]
            ];


        }
        
        
        // Convertir el array PHP a JSON
        $jsonData = json_encode($data);

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar cURL para enviar datos JSON en una solicitud POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ejecutar la solicitud y almacenar la respuesta
        $response = curl_exec($ch);
        
        // Verificar si hubo algún error
        if (curl_errno($ch)) {
            echo json_encode(['error' => curl_error($ch)]);
        } else {
            
            // Decodificar la respuesta del servidor
            $decodedResponse = json_decode($response, true);

            // Acceder al campo "body" de la respuesta
            $bodyContent = $decodedResponse['body'] ?? null;

            $tabla = "facturas_locales";
            $item1 = "firmaDigital";
            $valor1 = $bodyContent;
            $item2 = "id";
            $valor2 = $this->idFacturaContingencia;

            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);

            $tabla = "facturas_locales";
            $item1 = "json_guardado";
            $valor1 = json_encode($data);
            $item2 = "id";
            $valor2 = $this->idFacturaContingencia;

            $respuesta2 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
            
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no"); // Ahora ambos resultados están en formato JSON
            }
            


        }

        // Cerrar la sesión cURL
        curl_close($ch);


	}

    /*=============================================
	FIRMAR EVENTO CONTINGENCIA
	=============================================*/	
	public $idEventoContingencia;

	public function ajaxFirmarEventoContingencia() {
        

        $item = "id";
        $orden = "id";
		$valor = $this->idEventoContingencia;

		$evento = ControladorFacturas::ctrMostrarEventosContingencias($item, $valor, $orden);

        $item = "id";
        $orden = "id";
        $valor = "1";

        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        // Decodificar los ids de la factura
        $facturasIds = json_decode($evento["ids_facturas"], true); // true para obtener un array asociativo

        // Inicializar el array cuerpoDocumento
        $detalleDTE = [];

        // Número de ítem inicial
        $numItem = 1;
        
        
        // Recorrer cada producto y mapear los datos
        foreach ($facturasIds as $facturaId) {

            $item = "id";
            $valor = $facturaId;
            $orden = "id";
            $optimizacion = "no";
        
            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            $detalleDTE[] = [
                "noItem" => $numItem,
                "codigoGeneracion" => $facturaOriginal["codigoGeneracion"], // Puedes ajustarlo según sea necesario
                "tipoDoc" => $facturaOriginal["tipoDte"]
            ];

            // Incrementar el número de ítem
            $numItem++;
        }
        
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Establecer la zona horaria de El Salvador
	        date_default_timezone_set('America/El_Salvador');

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 3,
                        "ambiente" => "01",
                        "codigoGeneracion" => $evento["codigoGeneracion"],
                        "fTransmision" => date("Y-m-d"), // Fecha actual en formato YYYY-MM-DD
                        "hTransmision" => date("H:i:s")
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nombre" => $empresa["nombre"],
                        "nombreResponsable" => $empresa["nombre"],
                        "tipoDocResponsable" => "36",
                        "numeroDocResponsable" => $empresa["nit"],
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "codEstableMH" => "M001",
                        "codPuntoVenta" => "P001",
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "detalleDTE" => $detalleDTE,
                    "motivo" => [
                        "fInicio" => $evento["fecha_inicio"], // Fecha de inicio de la contingencia
                        "fFin" => $evento["fecha_fin"],   // Fecha de fin de la contingencia
                        "hInicio" => $evento["hora_inicio"],  // Hora de inicio de la contingencia
                        "hFin" => $evento["hora_fin"],     // Hora de fin de la contingencia
                        "tipoContingencia" => intval($evento["tipo_contingencia"]),  // Tipo de contingencia
                        "motivoContingencia" => $evento["motivo_contingencia"] // Convertir a entero

                    ]
                ]
            ];

            
        
        
        // Convertir el array PHP a JSON
        $jsonData = json_encode($data);

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar cURL para enviar datos JSON en una solicitud POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ejecutar la solicitud y almacenar la respuesta
        $response = curl_exec($ch);
        
        // Verificar si hubo algún error
        if (curl_errno($ch)) {
            echo json_encode(['error' => curl_error($ch)]);
        } else {
            
            // Decodificar la respuesta del servidor
            $decodedResponse = json_decode($response, true);

            // Acceder al campo "body" de la respuesta
            $bodyContent = $decodedResponse['body'] ?? null;

            $tabla = "contingencias";
            $item1 = "firmaDigital";
            $valor1 = $bodyContent;
            $item2 = "id";
            $valor2 = $this->idEventoContingencia;

            

            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
            
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no"); // Ahora ambos resultados están en formato JSON
            }
            


        }

        // Cerrar la sesión cURL
        curl_close($ch);


	}

    /*=============================================
	INICIAR SESIÓN EN MH
	=============================================*/	

    public function iniciarSesionMh() {
        // URL de la API a la que quieres enviar el POST
        $url = 'https://api.dtes.mh.gob.sv/seguridad/auth'; // Cambia esto a la URL de tu API
    
        // Datos en formato x-www-form-urlencoded
        $data = [
            'user' => '05012301211011',
            'pwd' => 'AromazoneES25_'
        ];
        
        // Convertimos los datos a formato x-www-form-urlencoded
        $dataString = http_build_query($data);
    
        // Inicializamos cURL
        $ch = curl_init($url);
    
        // Configuramos la solicitud POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: facturacion'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        

        // Ejecutamos la solicitud
        $response = curl_exec($ch);
    
        // Verificamos si hubo algún error
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            // Decodificamos la respuesta JSON
            $responseData = json_decode($response, true); // Decodificamos la respuesta como un array asociativo
    
            // Verificamos si el token está en la respuesta
            if (isset($responseData['body']['token'])) {
                // Almacenamos el token en la sesión
                $_SESSION["tokenInicioSesionMh"] = $responseData['body']['token'];
                echo json_encode("si");
            } else {
                // Manejo de error si no se encuentra el token
                echo json_encode("no");
            }
        }
    
        // Cerramos cURL
        curl_close($ch);
    }
    


}

/*=============================================
SELLAR FACTURA
=============================================*/
if (isset($_POST["idFacturaS"])) {
    $sellar = new AjaxFacturas();
    $sellar->idFacturaS = $_POST["idFacturaS"];
    $sellar->ajaxSellarFactura();
}

/*=============================================
SELLAR EVENTO CONTINGENCIA
=============================================*/
if (isset($_POST["idEvento"])) {
    
    $sellar = new AjaxFacturas();
    $sellar->idEventoContingencia = $_POST["idEvento"];
    $sellar->ajaxFirmarEventoContingencia();
}

/*=============================================
SELLAR EVENTO CONTINGENCIA
=============================================*/
if (isset($_POST["idEventoH"])) {
    
    $sellarA = new AjaxFacturas();
    $sellarA->idEventoH = $_POST["idEventoH"];
    $sellarA->ajaxSellarEvento();
}

/*=============================================
SELLAR ANULACION
=============================================*/
if (isset($_POST["idFacturaSA"])) {
    
    $sellarA = new AjaxFacturas();
    $sellarA->idFacturaSA = $_POST["idFacturaSA"];
    $sellarA->ajaxSellarAnulacion();
}


/*=============================================
FIRMAR FACTURA
=============================================*/
if (isset($_POST["idFacturaF"])) {
    $firmar = new AjaxFacturas();
    $firmar->idFactura = $_POST["idFacturaF"];
    $firmar->ajaxEnviarFactura();
}

/*=============================================
FIRMAR FACTURA CONTINGENCIA
=============================================*/
if (isset($_POST["idFacturaFContingencia"])) {
    
    $firmar = new AjaxFacturas();
    $firmar->idFacturaContingencia = $_POST["idFacturaFContingencia"];
    $firmar->ajaxEnviarFacturaContingencia();
}

/*=============================================
FIRMAR DTE A ANULAR
=============================================*/
if (isset($_POST["idFacturaFA"])) {
    
    $firmarA = new AjaxFacturas();
    $firmarA->idFacturaA = $_POST["idFacturaFA"];
    $firmarA->ajaxAnularDTE();
}

/*=============================================
INICIAR SESIÓN EN MH
=============================================*/
if (isset($_POST["iniciarSesionMh"])) {
    $iniciar = new AjaxFacturas();
    $iniciar->iniciarSesionMh();
}