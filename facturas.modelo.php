<?php

require_once "conexion.php";

class ModeloFacturas{

	/*=============================================
	MOSTRAR FACTURAS
	=============================================*/

	static public function mdlMostrarFacturas($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasVarias($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasCortes($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasDashPorMes($anio, $mes, $orden){
		try {
			$conexion = Conexion::conectar();
			$ordenCampo = !empty($orden) ? $orden : "fecEmi";
	
			$stmt = $conexion->prepare("SELECT * FROM facturas_locales WHERE YEAR(fecEmi) = :anio AND MONTH(fecEmi) = :mes ORDER BY $ordenCampo DESC");
			$stmt->bindParam(":anio", $anio, PDO::PARAM_INT);
			$stmt->bindParam(":mes", $mes, PDO::PARAM_INT);
	
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		} catch (PDOException $e) {
			error_log("Error en mdlMostrarFacturasDashPorMes: " . $e->getMessage());
			return [];
		} finally {
			$stmt = null;
			$conexion = null;
		}
	}

	static public function mdlMostrarFacturasOptimizadas($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC LIMIT 10");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC LIMIT 10");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasAsc($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id ASC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id ASC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasEliminadas($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasEliminadasOptimizadas($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC LIMIT 10");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC LIMIT 10");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasVentas($tabla, $fechaInicio, $fechaFin){

		$stmt = Conexion::conectar()->prepare(
			"SELECT * FROM $tabla 
			 WHERE fecEmi BETWEEN :fechaInicio AND :fechaFin 
			 ORDER BY fecEmi ASC"
		);
	
		$stmt->bindParam(":fechaInicio", $fechaInicio, PDO::PARAM_STR);
		$stmt->bindParam(":fechaFin", $fechaFin, PDO::PARAM_STR);
	
		$stmt->execute();
	
		$resultado = $stmt->fetchAll();
	
		
		$stmt = null;
	
		return $resultado;
	}

	static public function mdlMostrarFacturasFechaOptimizada($tabla, $fechaOptimizada) {

		// Obtener primer día del mes
		$fechaInicio = $fechaOptimizada . "-01";
	
		// Obtener último día del mes (con date("Y-m-t"))
		$fechaFin = date("Y-m-t", strtotime($fechaInicio));
	
		$stmt = Conexion::conectar()->prepare(
			"SELECT * FROM $tabla 
			 WHERE fecEmi BETWEEN :fechaInicio AND :fechaFin 
			 ORDER BY id DESC"
		);
	
		$stmt->bindParam(":fechaInicio", $fechaInicio, PDO::PARAM_STR);
		$stmt->bindParam(":fechaFin", $fechaFin, PDO::PARAM_STR);
	
		$stmt->execute();
	
		$resultado = $stmt->fetchAll();
	
		$stmt = null;
	
		return $resultado;
	}

	static public function mdlMostrarFacturasEliminadasFechaOptimizada($tabla, $fechaOptimizada) {

		// Obtener primer día del mes
		$fechaInicio = $fechaOptimizada . "-01";
	
		// Obtener último día del mes (con date("Y-m-t"))
		$fechaFin = date("Y-m-t", strtotime($fechaInicio));
	
		$stmt = Conexion::conectar()->prepare(
			"SELECT * FROM $tabla 
			 WHERE fecha BETWEEN :fechaInicio AND :fechaFin 
			 ORDER BY id DESC"
		);
	
		$stmt->bindParam(":fechaInicio", $fechaInicio, PDO::PARAM_STR);
		$stmt->bindParam(":fechaFin", $fechaFin, PDO::PARAM_STR);
	
		$stmt->execute();
	
		$resultado = $stmt->fetchAll();
	
		$stmt = null;
	
		return $resultado;
	}

	/*=============================================
	MOSTRAR FACTURAS CONTINGENCIA
	=============================================*/

	static public function mdlMostrarEventosContingencias($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	MOSTRAR ABONOS
	=============================================*/

	static public function mdlMostrarAbonos($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	MOSTRAR CORTES
	=============================================*/

	static public function mdlMostrarCortes($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	MOSTRAR COMPRAS
	=============================================*/

	static public function mdlMostrarCompras($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarComprasOptimizadas($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC LIMIT 10");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC LIMIT 10");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	REGISTRO DE FACTURA LOCAL
	=============================================*/

	static public function mdlIngresarFactura($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_cliente, id_vendedor, id_usuario, modo, notaRemision, estado, idFacturaRelacionada, productos, total, totalSinIva, tipoDte, recintoFiscal, regimen, modoTransporte, seguro, flete, idMotorista, condicionOperacion, numeroControl, codigoGeneracion, horEmi, fecEmi, orden_compra, incoterm, origen, gran_contribuyente) VALUES (:id_cliente, :id_vendedor, :id_usuario, :modo, :notaRemision, :estado, :idFacturaRelacionada, :productos, :total, :totalSinIva, :tipoDte, :recintoFiscal, :regimen, :modoTransporte, :seguro, :flete, :idMotorista, :condicionOperacion, :numeroControl, :codigoGeneracion, :horEmi, :fecEmi, :orden_compra, :incoterm, :origen, :gran_contribuyente)");

		$stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_STR);
		$stmt->bindParam(":id_vendedor", $datos["id_vendedor"], PDO::PARAM_STR);
        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":modo", $datos["modo"], PDO::PARAM_STR);
		$stmt->bindParam(":notaRemision", $datos["notaRemision"], PDO::PARAM_STR);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
		$stmt->bindParam(":idFacturaRelacionada", $datos["idFacturaRelacionada"], PDO::PARAM_STR);
        $stmt->bindParam(":productos", $datos["productos"], PDO::PARAM_STR); // Cambié esto
        $stmt->bindParam(":total", $datos["total"], PDO::PARAM_STR);
		$stmt->bindParam(":totalSinIva", $datos["totalSinIva"], PDO::PARAM_STR);
        $stmt->bindParam(":tipoDte", $datos["tipoDte"], PDO::PARAM_STR);
		$stmt->bindParam(":gran_contribuyente", $datos["gran_contribuyente"], PDO::PARAM_STR);
		
		$stmt->bindParam(":recintoFiscal", $datos["recintoFiscal"], PDO::PARAM_STR);
		$stmt->bindParam(":regimen", $datos["regimen"], PDO::PARAM_STR);
		$stmt->bindParam(":modoTransporte", $datos["modoTransporte"], PDO::PARAM_STR);
		$stmt->bindParam(":seguro", $datos["seguro"], PDO::PARAM_STR);
		$stmt->bindParam(":flete", $datos["flete"], PDO::PARAM_STR);
		$stmt->bindParam(":idMotorista", $datos["idMotorista"], PDO::PARAM_INT);

		$stmt->bindParam(":condicionOperacion", $datos["condicionOperacion"], PDO::PARAM_STR);
		$stmt->bindParam(":numeroControl", $datos["numeroControl"], PDO::PARAM_STR);
		$stmt->bindParam(":codigoGeneracion", $datos["codigoGeneracion"], PDO::PARAM_STR);
		$stmt->bindParam(":horEmi", $datos["horEmi"], PDO::PARAM_STR);
		$stmt->bindParam(":fecEmi", $datos["fecEmi"], PDO::PARAM_STR);
		$stmt->bindParam(":orden_compra", $datos["orden_compra"], PDO::PARAM_STR);
		$stmt->bindParam(":incoterm", $datos["incoterm"], PDO::PARAM_STR);
		$stmt->bindParam(":origen", $datos["origen"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	REGISTRO DE FACTURA LOCAL CONTINGENCIA
	=============================================*/

	static public function mdlIngresarFacturaContingencia($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_cliente, id_vendedor, id_usuario, modo, tipo_contingencia, motivo_contingencia, notaRemision, estado, idFacturaRelacionada, productos, total, totalSinIva, tipoDte, recintoFiscal, regimen, modoTransporte, seguro, flete, idMotorista, condicionOperacion, numeroControl, codigoGeneracion, horEmi, fecEmi, orden_compra, incoterm, origen, gran_contribuyente) VALUES (:id_cliente, :id_vendedor, :id_usuario, :modo, :tipo_contingencia, :motivo_contingencia, :notaRemision, :estado, :idFacturaRelacionada, :productos, :total, :totalSinIva, :tipoDte, :recintoFiscal, :regimen, :modoTransporte, :seguro, :flete, :idMotorista, :condicionOperacion, :numeroControl, :codigoGeneracion, :horEmi, :fecEmi, :orden_compra, :incoterm, :origen, :gran_contribuyente)");

        $stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_STR);
		$stmt->bindParam(":id_vendedor", $datos["id_vendedor"], PDO::PARAM_STR);
        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":modo", $datos["modo"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_contingencia", $datos["tipo_contingencia"], PDO::PARAM_STR);
		$stmt->bindParam(":motivo_contingencia", $datos["motivo_contingencia"], PDO::PARAM_STR);
		$stmt->bindParam(":notaRemision", $datos["notaRemision"], PDO::PARAM_STR);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
		$stmt->bindParam(":idFacturaRelacionada", $datos["idFacturaRelacionada"], PDO::PARAM_STR);
        $stmt->bindParam(":productos", $datos["productos"], PDO::PARAM_STR); // Cambié esto
        $stmt->bindParam(":total", $datos["total"], PDO::PARAM_STR);
		$stmt->bindParam(":totalSinIva", $datos["totalSinIva"], PDO::PARAM_STR);
        $stmt->bindParam(":tipoDte", $datos["tipoDte"], PDO::PARAM_STR);
		$stmt->bindParam(":gran_contribuyente", $datos["gran_contribuyente"], PDO::PARAM_STR);
		
		$stmt->bindParam(":recintoFiscal", $datos["recintoFiscal"], PDO::PARAM_STR);
		$stmt->bindParam(":regimen", $datos["regimen"], PDO::PARAM_STR);
		$stmt->bindParam(":modoTransporte", $datos["modoTransporte"], PDO::PARAM_STR);
		$stmt->bindParam(":seguro", $datos["seguro"], PDO::PARAM_STR);
		$stmt->bindParam(":flete", $datos["flete"], PDO::PARAM_STR);
		$stmt->bindParam(":idMotorista", $datos["idMotorista"], PDO::PARAM_INT);

		$stmt->bindParam(":condicionOperacion", $datos["condicionOperacion"], PDO::PARAM_STR);
		$stmt->bindParam(":numeroControl", $datos["numeroControl"], PDO::PARAM_STR);
		$stmt->bindParam(":codigoGeneracion", $datos["codigoGeneracion"], PDO::PARAM_STR);
		$stmt->bindParam(":horEmi", $datos["horEmi"], PDO::PARAM_STR);
		$stmt->bindParam(":fecEmi", $datos["fecEmi"], PDO::PARAM_STR);
		$stmt->bindParam(":orden_compra", $datos["orden_compra"], PDO::PARAM_STR);
		$stmt->bindParam(":incoterm", $datos["incoterm"], PDO::PARAM_STR);
		$stmt->bindParam(":origen", $datos["origen"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	REGISTRO DE EVENTO CONTINGENCIA
	=============================================*/

	static public function mdlIngresarEventoContingencia($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(fecha_inicio, fecha_fin, hora_inicio, hora_fin, tipo_contingencia, motivo_contingencia, ids_facturas, codigoGeneracion) 
		VALUES (:fecha_inicio, :fecha_fin, :hora_inicio, :hora_fin, :tipo_contingencia, :motivo_contingencia, :ids_facturas, :codigoGeneracion)");

        $stmt->bindParam(":fecha_inicio", $datos["fecha_inicio"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_fin", $datos["fecha_fin"], PDO::PARAM_STR);
		$stmt->bindParam(":hora_inicio", $datos["hora_inicio"], PDO::PARAM_STR);
		$stmt->bindParam(":hora_fin", $datos["hora_fin"], PDO::PARAM_STR);;
		$stmt->bindParam(":tipo_contingencia", $datos["tipo_contingencia"], PDO::PARAM_STR);
		$stmt->bindParam(":motivo_contingencia", $datos["motivo_contingencia"], PDO::PARAM_STR);
		$stmt->bindParam(":ids_facturas", $datos["ids_facturas"], PDO::PARAM_STR);
		$stmt->bindParam(":codigoGeneracion", $datos["codigoGeneracion"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	REGISTRO DE ANULACION LOCAL
	=============================================*/

	static public function mdlIngresarAnulacion($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(codigoGeneracion, fecEmi, horEmi, facturaRelacionada, motivoAnulacion) VALUES (:codigoGeneracion, :fecEmi, :horEmi, :facturaRelacionada, :motivoAnulacion)");

		$stmt->bindParam(":codigoGeneracion", $datos["codigoGeneracion"], PDO::PARAM_STR);
		$stmt->bindParam(":fecEmi", $datos["fecEmi"], PDO::PARAM_STR);
		$stmt->bindParam(":horEmi", $datos["horEmi"], PDO::PARAM_STR);
		$stmt->bindParam(":facturaRelacionada", $datos["facturaRelacionada"], PDO::PARAM_STR);
		$stmt->bindParam(":motivoAnulacion", $datos["motivoAnulacion"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	REGISTRO DE ELIMINADA NUMERO CONTROL
	=============================================*/

	static public function mdlIngresarEliminada($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(numero_control, codigo_generacion) VALUES (:numero_control, :codigo_generacion)");

		$stmt->bindParam(":numero_control", $datos["numero_control"], PDO::PARAM_STR);
		$stmt->bindParam(":codigo_generacion", $datos["codigo_generacion"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	REGISTRO DE COMPRA
	=============================================*/

	static public function mdlIngresarCompra($tabla, $datos) { 	

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(fecha, clase_documento, tipo_documento, numero_documento, nit_nrc, nombre_proveedor, compras_internas_exentas, internaciones_exentas_y_no_sujetas, importaciones_exentas_y_no_sujetas, compras_internas_gravadas, internaciones_gravadas_de_bienes, importaciones_gravadas_de_bienes, importaciones_gravadas_de_servicios, credito_fiscal, total_de_compras, dui_del_proveedor, tipo_de_operacion, clasificacion, sector, tipo, anexo) VALUES (:fecha, :clase_documento, :tipo_documento, :numero_documento, :nit_nrc, :nombre_proveedor, :compras_internas_exentas, :internaciones_exentas_y_no_sujetas, :importaciones_exentas_y_no_sujetas, :compras_internas_gravadas, :internaciones_gravadas_de_bienes, :importaciones_gravadas_de_bienes, :importaciones_gravadas_de_servicios, :credito_fiscal, :total_de_compras, :dui_del_proveedor, :tipo_de_operacion, :clasificacion, :sector, :tipo, :anexo)");

		$stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
		$stmt->bindParam(":clase_documento", $datos["clase_documento"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_documento", $datos["tipo_documento"], PDO::PARAM_STR);
		$stmt->bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR);
		$stmt->bindParam(":nit_nrc", $datos["nit_nrc"], PDO::PARAM_STR);
		$stmt->bindParam(":nombre_proveedor", $datos["nombre_proveedor"], PDO::PARAM_STR);
		$stmt->bindParam(":compras_internas_exentas", $datos["compras_internas_exentas"], PDO::PARAM_STR);
		$stmt->bindParam(":internaciones_exentas_y_no_sujetas", $datos["internaciones_exentas_y_no_sujetas"], PDO::PARAM_STR);
		$stmt->bindParam(":importaciones_exentas_y_no_sujetas", $datos["importaciones_exentas_y_no_sujetas"], PDO::PARAM_STR);
		$stmt->bindParam(":compras_internas_gravadas", $datos["compras_internas_gravadas"], PDO::PARAM_STR);
		$stmt->bindParam(":internaciones_gravadas_de_bienes", $datos["internaciones_gravadas_de_bienes"], PDO::PARAM_STR);
		$stmt->bindParam(":importaciones_gravadas_de_bienes", $datos["importaciones_gravadas_de_bienes"], PDO::PARAM_STR);
		$stmt->bindParam(":importaciones_gravadas_de_servicios", $datos["importaciones_gravadas_de_servicios"], PDO::PARAM_STR);
		$stmt->bindParam(":credito_fiscal", $datos["credito_fiscal"], PDO::PARAM_STR);
		$stmt->bindParam(":total_de_compras", $datos["total_de_compras"], PDO::PARAM_STR);
		$stmt->bindParam(":dui_del_proveedor", $datos["dui_del_proveedor"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_de_operacion", $datos["tipo_de_operacion"], PDO::PARAM_STR);
		$stmt->bindParam(":clasificacion", $datos["clasificacion"], PDO::PARAM_STR);
		$stmt->bindParam(":sector", $datos["sector"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":anexo", $datos["anexo"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }
	
	/*=============================================
	BORRAR FACTURA
	=============================================*/

	static public function mdlBorrarFactura($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");

		$stmt -> bindParam(":id", $datos, PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;


	}

	/*=============================================
	ACTUALIZAR FACTURA
	=============================================*/

	static public function mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");

		$stmt -> bindParam(":".$item1, $valor1, PDO::PARAM_STR);
		$stmt -> bindParam(":".$item2, $valor2, PDO::PARAM_STR);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	REGISTRO DE CORTE DE CAJA
	=============================================*/

	static public function mdlIngresarCorte($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(ids_facturas, id_facturador) VALUES (:ids_facturas, :id_facturador)");

        $stmt->bindParam(":ids_facturas", $datos["ids_facturas"], PDO::PARAM_STR);
		$stmt->bindParam(":id_facturador", $datos["id_facturador"], PDO::PARAM_INT);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	REGISTRO DE ABONO A FACTURA
	=============================================*/

	static public function mdlIngresarAbono($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_factura, forma_abono, fecha_abono, gestion_abono, banco, monto) VALUES (:id_factura, :forma_abono, :fecha_abono, :gestion_abono, :banco, :monto)");

        $stmt->bindParam(":id_factura", $datos["id_factura"], PDO::PARAM_INT);
		$stmt->bindParam(":forma_abono", $datos["forma_abono"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_abono", $datos["fecha_abono"], PDO::PARAM_STR);
		$stmt->bindParam(":gestion_abono", $datos["gestion_abono"], PDO::PARAM_STR);
        $stmt->bindParam(":banco", $datos["banco"], PDO::PARAM_STR); // Cambié esto
        $stmt->bindParam(":monto", $datos["monto"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	EDITAR COMPRA
	=============================================*/

	static public function mdlEditarCompra($tabla, $datos){
		
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET fecha = :fecha, clase_documento = :clase_documento, tipo_documento = :tipo_documento, numero_documento = :numero_documento, nit_nrc = :nit_nrc, nombre_proveedor = :nombre_proveedor, compras_internas_exentas = :compras_internas_exentas, internaciones_exentas_y_no_sujetas = :internaciones_exentas_y_no_sujetas, importaciones_exentas_y_no_sujetas = :importaciones_exentas_y_no_sujetas, compras_internas_gravadas = :compras_internas_gravadas, internaciones_gravadas_de_bienes = :internaciones_gravadas_de_bienes, importaciones_gravadas_de_bienes = :importaciones_gravadas_de_bienes, importaciones_gravadas_de_servicios = :importaciones_gravadas_de_servicios, credito_fiscal = :credito_fiscal, total_de_compras = :total_de_compras, dui_del_proveedor = :dui_del_proveedor, tipo_de_operacion = :tipo_de_operacion, clasificacion = :clasificacion, sector = :sector, tipo = :tipo, anexo = :anexo WHERE id = :id");

		$stmt -> bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);  
		$stmt -> bindParam(":clase_documento", $datos["clase_documento"], PDO::PARAM_STR);  
		$stmt -> bindParam(":tipo_documento", $datos["tipo_documento"], PDO::PARAM_STR);  
		$stmt -> bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR);  
		$stmt -> bindParam(":nit_nrc", $datos["nit_nrc"], PDO::PARAM_STR);  
		$stmt -> bindParam(":nombre_proveedor", $datos["nombre_proveedor"], PDO::PARAM_STR);  
		$stmt -> bindParam(":compras_internas_exentas", $datos["compras_internas_exentas"], PDO::PARAM_STR);  
		$stmt -> bindParam(":internaciones_exentas_y_no_sujetas", $datos["internaciones_exentas_y_no_sujetas"], PDO::PARAM_STR);  
		$stmt -> bindParam(":importaciones_exentas_y_no_sujetas", $datos["importaciones_exentas_y_no_sujetas"], PDO::PARAM_STR);  
		$stmt -> bindParam(":compras_internas_gravadas", $datos["compras_internas_gravadas"], PDO::PARAM_STR);  
		$stmt -> bindParam(":internaciones_gravadas_de_bienes", $datos["internaciones_gravadas_de_bienes"], PDO::PARAM_STR);  
		$stmt -> bindParam(":importaciones_gravadas_de_bienes", $datos["importaciones_gravadas_de_bienes"], PDO::PARAM_STR);  
		$stmt -> bindParam(":importaciones_gravadas_de_servicios", $datos["importaciones_gravadas_de_servicios"], PDO::PARAM_STR);  
		$stmt -> bindParam(":credito_fiscal", $datos["credito_fiscal"], PDO::PARAM_STR);  
		$stmt -> bindParam(":total_de_compras", $datos["total_de_compras"], PDO::PARAM_STR);  
		$stmt -> bindParam(":dui_del_proveedor", $datos["dui_del_proveedor"], PDO::PARAM_STR);  
		$stmt -> bindParam(":tipo_de_operacion", $datos["tipo_de_operacion"], PDO::PARAM_STR);  
		$stmt -> bindParam(":clasificacion", $datos["clasificacion"], PDO::PARAM_STR);  
		$stmt -> bindParam(":sector", $datos["sector"], PDO::PARAM_STR);  
		$stmt -> bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);  
		$stmt -> bindParam(":anexo", $datos["anexo"], PDO::PARAM_STR); 

		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);
	

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	EDITAR CORTE
	=============================================*/

	static public function mdlEditarCorte($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET autorizacion = :autorizacion, cuadrada = :cuadrada, comentarios = :comentarios, total = :total WHERE id = :id");

		$stmt -> bindParam(":autorizacion", $datos["autorizacion"], PDO::PARAM_STR);  
		$stmt -> bindParam(":cuadrada", $datos["cuadrada"], PDO::PARAM_STR);  
		$stmt -> bindParam(":comentarios", $datos["comentarios"], PDO::PARAM_STR);  
		$stmt -> bindParam(":total", $datos["total"], PDO::PARAM_STR);  

		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);
	

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}

}