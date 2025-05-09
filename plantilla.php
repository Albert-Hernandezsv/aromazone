<?php

session_start();

date_default_timezone_set('America/El_Salvador');
$horaActual = date("H"); // Obtiene solo la hora actual (sin minutos)
$horaProgramada1 = "07"; // Cambia a la hora deseada
$horaProgramada2 = "11"; // Cambia a la hora deseada
$horaProgramada3 = "14"; // Cambia a la hora deseada
$horaProgramada4 = "17"; // Cambia a la hora deseada

if ($horaActual === $horaProgramada1 || $horaActual === $horaProgramada2 || $horaActual === $horaProgramada3 || $horaActual === $horaProgramada4) {
      // Configuración de la base de datos y directorio de respaldo
      $host = 'localhost';
      $usuario = 'root';
      $password = '';
      $nombreBaseDatos = 'fox_control';
      $directorioRespaldo = 'vistas/respaldo-fox/';
      
      // Asegúrate de que el directorio de respaldo exista
      if (!is_dir($directorioRespaldo)) {
          mkdir($directorioRespaldo, 0777, true);
      }
  
      // Limitar el número de archivos a 5
      $archivos = glob($directorioRespaldo . '*.sql'); // Obtiene todos los archivos .sql en el directorio
      if (count($archivos) >= 5) {
          // Ordena los archivos por fecha de modificación, del más antiguo al más reciente
          array_multisort(array_map('filemtime', $archivos), SORT_ASC, $archivos);
  
          // Elimina el archivo más antiguo
          unlink($archivos[0]);
      }
  
      // Genera el nombre del nuevo archivo de respaldo
      $nombreArchivo = $directorioRespaldo . $nombreBaseDatos . '_backup_' . date("Y-m-d_H-i-s") . '.sql';
  
      // Crear la copia de seguridad
      try {
          $pdo = new PDO("mysql:host=$host;dbname=$nombreBaseDatos", $usuario, $password);
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $pdo->exec("SET NAMES utf8");
  
          // Genera el contenido del archivo de respaldo
          $salidaSQL = "";
          $tablas = $pdo->query("SHOW TABLES");
  
          foreach ($tablas as $fila) {
              $tabla = $fila[0];
              $estructura = $pdo->query("SHOW CREATE TABLE $tabla")->fetch(PDO::FETCH_ASSOC);
              $salidaSQL .= "\n\n" . $estructura['Create Table'] . ";\n\n";
              $datos = $pdo->query("SELECT * FROM $tabla");
  
              foreach ($datos as $filaDatos) {
                  $salidaSQL .= "INSERT INTO $tabla VALUES(";
                  foreach ($filaDatos as $dato) {
                      $salidaSQL .= $pdo->quote($dato) . ', ';
                  }
                  $salidaSQL = rtrim($salidaSQL, ', ') . ");\n";
              }
          }
  
          // Guarda el archivo de respaldo
          file_put_contents($nombreArchivo, $salidaSQL);
  
          
  
      } catch (PDOException $e) {
          
      }
}
  
?>

<!DOCTYPE html>
<html>
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>FOX CONTROL</title>

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="icon" href="vistas/img/logo.png">

   <!--=====================================
  PLUGINS DE CSS
  ======================================-->

  <!-- MÍO -->
  <link rel="stylesheet" href="vistas/dist/css/estilos.css">

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="vistas/bower_components/bootstrap/dist/css/bootstrap.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="vistas/bower_components/font-awesome/css/font-awesome.min.css">

  <!-- Ionicons -->
  <link rel="stylesheet" href="vistas/bower_components/Ionicons/css/ionicons.min.css">
  

    <!-- jQuery 3 -->
    <script src="vistas/bower_components/jquery/dist/jquery.min.js"></script>

  <!-- Charts para gráficas -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



  <!-- Theme style -->
  <!--<link rel="stylesheet" href="vistas/dist/css/AdminLTE.css">-->
  
  <!-- AdminLTE Skins -->
  <!--<link rel="stylesheet" href="vistas/dist/css/skins/_all-skins.min.css">-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

   <!-- DataTables CSS-->
  <link rel="stylesheet" href="vistas/bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

  <!-- iCheck for checkboxes and radio inputs -->
  <!--<link rel="stylesheet" href="vistas/plugins/iCheck/all.css">-->

   <!-- Daterange picker -->
  <link rel="stylesheet" href="vistas/bower_components/bootstrap-daterangepicker/daterangepicker.css">

  <!-- Morris chart -->
  <link rel="stylesheet" href="vistas/bower_components/morris.js/morris.css">


  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


  <!--=====================================
  PLUGINS DE JAVASCRIPT
  ======================================-->

  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>

  
  <!-- Bootstrap 3.3.7 -->
  <script src="vistas/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

  <!-- FastClick -->
  <script src="vistas/bower_components/fastclick/lib/fastclick.js"></script>
  
  <!-- AdminLTE App -->
  <!--<script src="vistas/dist/js/adminlte.min.js"></script>-->

  <!-- DataTables JS-->
  <script src="vistas/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="vistas/bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
  <script src="vistas/bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <!-- SweetAlert 2 -->
  <script src="vistas/plugins/sweetalert2/sweetalert2.all.js"></script>
   <!-- By default SweetAlert2 doesn't support IE. To enable IE 11 support, include Promise polyfill:-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>

  <!-- iCheck 1.0.1 -->
  <!--<script src="vistas/plugins/iCheck/icheck.min.js"></script>-->

  <!-- InputMask -->
  
  <script src="https://cdn.jsdelivr.net/npm/cleave.js/dist/cleave.min.js"></script>




  <!-- daterangepicker http://www.daterangepicker.com/-->
  <script src="vistas/bower_components/moment/min/moment.min.js"></script>
  <script src="vistas/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>

  <!-- Morris.js charts http://morrisjs.github.io/morris.js/-->
  <script src="vistas/bower_components/raphael/raphael.min.js"></script>
  <script src="vistas/bower_components/morris.js/morris.min.js"></script>

  <!-- ChartJS http://www.chartjs.org/-->
  <script src="vistas/bower_components/chart.js/Chart.js"></script>

  <!-- JsPdf para reportes de tablas a PDF-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

  <!-- xlsx para reportes de tablas a Excel-->
  <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/canvg/3.0.7/umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.3/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>






  <!-- Enlaza el JS de Bootstrap y el script personalizado -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>


  <!-- EDITAR TITULOS --> 
<!--<script src="https://cdn.tiny.cloud/1/tss7am5aieql2w8om6zwifui2wvg2wmjj7hxg3eb10e1oir6/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>-->

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<!--<script>
  tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
  });
</script>-->

</head>

<!--=====================================
CUERPO DOCUMENTO
======================================-->

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

 
  <?php

      $conectado = @fsockopen("www.google.com", 80); 
      if ($conectado) {
        if(isset($_SESSION["activo"])){
          if($_SESSION["activo"] != "Activo"){
            $item = "localizacion";
            $valor = "3";
            $orden = "id";
          
            $cliente = ControladorClientes::ctrMostrarClientesMios($item, $valor, $orden);
            if($cliente["estado"] == "Activo"){
              $_SESSION["activo"] = "Activo";
            } else {
              $_SESSION["activo"] = "Desactivado";
            }
          }
            
        } else {
            $item = "localizacion";
            $valor = "3";
            $orden = "id";
          
            $cliente = ControladorClientes::ctrMostrarClientesMios($item, $valor, $orden);
            if($cliente["estado"] == "Activo"){
              $_SESSION["activo"] = "Activo";
            } else {
              $_SESSION["activo"] = "Desactivado";
            }
        }
  
            if($_SESSION["activo"] == "Activo"){
              if(isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok"){
    
                echo '<div class="wrapper">';
            
                  /*=============================================
                  CABEZOTE
                  =============================================*/
            
                  include "modulos/cabezote.php";
            
                  /*=============================================
                  MENU
                  =============================================*/
            
                  include "modulos/menu.php";
                  
            
                  /*=============================================
                  CONTENIDO
                  =============================================*/
            
                  if(isset($_GET["ruta"])){
            
                    if(
                      $_GET["ruta"] == "dashboard" ||
                      $_GET["ruta"] == "usuarios" ||
                      $_GET["ruta"] == "inventario" ||
                      $_GET["ruta"] == "kardex" ||
                      $_GET["ruta"] == "facturacion" ||
                      $_GET["ruta"] == "facturacion-contingencia" ||
                      $_GET["ruta"] == "contabilidad" ||
                      $_GET["ruta"] == "cxc" ||
                      $_GET["ruta"] == "ventas" ||
                      $_GET["ruta"] == "compras" ||
                      $_GET["ruta"] == "ver-compras" ||
                      $_GET["ruta"] == "escoger-factura" ||
                      $_GET["ruta"] == "escoger-factura-contingencia" ||
                      $_GET["ruta"] == "crear-factura" ||
                      $_GET["ruta"] == "crear-factura-contingencia" ||
                      $_GET["ruta"] == "crear-ccf" ||
                      $_GET["ruta"] == "crear-ccf-contingencia" ||
                      $_GET["ruta"] == "crear-factura-exportacion" ||
                      $_GET["ruta"] == "crear-factura-exportacion-contingencia" ||
                      $_GET["ruta"] == "crear-factura-sujeto" ||
                      $_GET["ruta"] == "crear-factura-sujeto-contingencia" ||
                      $_GET["ruta"] == "ver-factura" ||
                      $_GET["ruta"] == "crear-nota-remision" ||
                      $_GET["ruta"] == "crear-nota-credito" ||
                      $_GET["ruta"] == "crear-nota-debito" ||
                      $_GET["ruta"] == "anular-dte" ||
                      $_GET["ruta"] == "ordenes-compra" ||
                      $_GET["ruta"] == "crear-orden-compra" ||
                      $_GET["ruta"] == "ver-corte" ||

                      $_GET["ruta"] == "dashboard" ||
                      $_GET["ruta"] == "salir"){
            
                      include "modulos/".$_GET["ruta"].".php";
                      include "modulos/footer.php";
            
                    }else{
            
                      include "modulos/404.php";
            
                    }
            
                  } else {
                      include "modulos/dashboard.php";
                  }
            
                  /*=============================================
                  FOOTER
                  =============================================*/
            
            
                  echo '</div>';
            
              } else {
                  echo '<div class="wrapper">';
                  
            
                  /*=============================================
                  CONTENIDO
                  =============================================*/
            
                  if(isset($_GET["ruta"])){
            
                    if($_GET["ruta"] == "login"){
            
                      include "modulos/".$_GET["ruta"].".php";
            
                    }else{
            
                      include "modulos/login.php";
            
                    }
            
                  } else {
                      include "modulos/login.php";
                  }
            
                  /*=============================================
                  FOOTER
                  =============================================*/
            
            
                  echo '</div>';
            
              }        
            } else {
              if($_SESSION["activo"] == "Desactivado"){
                echo '
                    <style>
                      body {
                          margin: 0;
                          padding: 0;
                          font-family: "Arial", sans-serif;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          height: 100vh;
                          background: linear-gradient(145deg, #141e30, #243b55);
                          color: #fff;
                      }
        
                      .container {
                          text-align: center;
                          padding: 2rem;
                          border-radius: 15px;
                          background: rgba(255, 255, 255, 0.1);
                          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
                      }
        
                      h1 {
                          font-size: 2.5rem;
                          margin-bottom: 1rem;
                          text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
                      }
        
                      p {
                          font-size: 1.2rem;
                          margin-bottom: 1.5rem;
                          line-height: 1.6;
                      }
        
                      .contact {
                          display: inline-block;
                          padding: 0.8rem 1.5rem;
                          font-size: 1rem;
                          text-decoration: none;
                          color: #fff;
                          background-color:rgb(20, 171, 0);
                          border-radius: 5px;
                          transition: background 0.3s ease;
                      }
        
                      .contact:hover {
                          background-color:rgb(20, 171, 0);
                          text-decoration: none;
                          color: #fff;
                      }
                    </style>
                    <div class="container">
                      <img src="vistas/img/logo.png" width="50%">
                      <h1>Fox Control ha sido desactivado</h1>
                      <p>Lo sentimos, tu sistema ha sido desactivado. Por favor, comunícate con los administradores para más información.</p>
                      <a href="https://wa.link/nttzpw"" class="contact">+503 7250-2171</a>
                  </div>';
                }
            }
      } else {
              if(isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok"){

                echo '<div class="wrapper">';
            
                  /*=============================================
                  CABEZOTE
                  =============================================*/
            
                  include "modulos/cabezote.php";
            
                  /*=============================================
                  MENU
                  =============================================*/
            
                  include "modulos/menu.php";
                  
            
                  /*=============================================
                  CONTENIDO
                  =============================================*/
            
                  if(isset($_GET["ruta"])){
            
                    if(
                      $_GET["ruta"] == "dashboard" ||
                      $_GET["ruta"] == "usuarios" ||
                      $_GET["ruta"] == "inventario" ||
                      $_GET["ruta"] == "kardex" ||
                      $_GET["ruta"] == "facturacion" ||
                      $_GET["ruta"] == "facturacion-contingencia" ||
                      $_GET["ruta"] == "contabilidad" ||
                      $_GET["ruta"] == "cxc" ||
                      $_GET["ruta"] == "ventas" ||
                      $_GET["ruta"] == "compras" ||
                      $_GET["ruta"] == "ver-compras" ||
                      $_GET["ruta"] == "escoger-factura" ||
                      $_GET["ruta"] == "escoger-factura-contingencia" ||
                      $_GET["ruta"] == "crear-factura" ||
                      $_GET["ruta"] == "crear-factura-contingencia" ||
                      $_GET["ruta"] == "crear-ccf" ||
                      $_GET["ruta"] == "crear-ccf-contingencia" ||
                      $_GET["ruta"] == "crear-factura-exportacion" ||
                      $_GET["ruta"] == "crear-factura-exportacion-contingencia" ||
                      $_GET["ruta"] == "crear-factura-sujeto" ||
                      $_GET["ruta"] == "crear-factura-sujeto-contingencia" ||
                      $_GET["ruta"] == "ver-factura" ||
                      $_GET["ruta"] == "crear-nota-remision" ||
                      $_GET["ruta"] == "crear-nota-credito" ||
                      $_GET["ruta"] == "crear-nota-debito" ||
                      $_GET["ruta"] == "anular-dte" ||
                      $_GET["ruta"] == "ordenes-compra" ||
                      $_GET["ruta"] == "crear-orden-compra" ||
                      $_GET["ruta"] == "ver-corte" ||
            
                      $_GET["ruta"] == "dashboard" ||
                      $_GET["ruta"] == "salir"){
            
                      include "modulos/".$_GET["ruta"].".php";
                      include "modulos/footer.php";
            
                    }else{
            
                      include "modulos/404.php";
            
                    }
            
                  } else {
                      include "modulos/dashboard.php";
                  }
            
                  /*=============================================
                  FOOTER
                  =============================================*/
            
            
                  echo '</div>';
            
              } else {
                  echo '<div class="wrapper">';
                  
            
                  /*=============================================
                  CONTENIDO
                  =============================================*/
            
                  if(isset($_GET["ruta"])){
            
                    if($_GET["ruta"] == "login"){
            
                      include "modulos/".$_GET["ruta"].".php";
            
                    }else{
            
                      include "modulos/login.php";
            
                    }
            
                  } else {
                      include "modulos/login.php";
                  }
            
                  /*=============================================
                  FOOTER
                  =============================================*/
            
            
                  echo '</div>';
            
              }
      }

    

    
  ?>

<script src="vistas/js/login.js"></script>
<script src="vistas/js/cabezote.js"></script>
<script src="vistas/js/plantilla.js"></script>

<script src="vistas/js/usuarios.js"></script>
<script src="vistas/js/inventario.js"></script>
<script src="vistas/js/productos.js"></script>
<script src="vistas/js/clientes.js"></script>
<script src="vistas/js/facturacion.js"></script>
<script src="vistas/js/archivos.js"></script>
</body>
</html>
