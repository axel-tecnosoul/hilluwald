<?php
ini_set("display_errors",1);
ini_set("display_startup_errors",1);
error_reporting(E_ALL);
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';

if ( !empty($_POST)) {
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $modoDebug=0;
  $aDebug=[];

  $pdo->beginTransaction();
  
  if ($modoDebug==1) {
    var_dump($_POST);
    var_dump($_GET);
  }

  if($_POST['id_localidad']=="") $_POST['id_localidad']=NULL;
  if($_POST['id_lote']=="") $_POST['id_lote']=NULL;
  if($_POST['id_plantador']=="") $_POST['id_plantador']=NULL;

  //$id_cliente=($_POST['id_cliente']) ?: NULL;

  $sql = "INSERT INTO despachos (fecha, id_cliente, id_pedido, id_cliente_retira, campana, id_transporte, id_chofer, id_vehiculo, patente2, id_lote, id_plantador, id_localidad, lugar_entrega, observaciones, id_usuario) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
  $q = $pdo->prepare($sql);
  $params=array($_POST['fecha_despacho'],$_GET['id_cliente'],$_POST['id_pedido_despacho'],$_POST['id_cliente_retira'],$_POST['campana_despacho'],$_POST['id_transporte'],$_POST['id_chofer'],$_POST['id_vehiculo'],$_POST['patente2'],$_POST['id_lote'],$_POST['id_plantador'],$_POST['id_localidad'],$_POST['lugar_entrega'],$_POST['observaciones_despacho'],$_SESSION['user']['id']);
  $q->execute($params);
  $id_despacho = $pdo->lastInsertId();

  $aDebug[]=[
    "consulta"=>$sql,
    "params"=>$params,
    "afe"=>$q->rowCount(),
  ];

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
  }
  
  //$cantPrendas = count($_POST["id_cultivo"]);

  $aProductos=[];
  $cantProdOK=0;
  foreach ($_POST['id_servicio'] as $key => $id_servicio) {
    $cantidad_despachar = $_POST['cantidad_despachar'][$key];

    if($cantidad_despachar>0){

      $id_servicio = $_POST['id_servicio'][$key];
      $id_especie = $_POST['id_especie'][$key];
      $id_procedencia = $_POST['id_procedencia'][$key];
      $id_material = $_POST['id_material'][$key];

      $aProductos[]=[
        "id_servicio"=>$id_servicio,
        "id_especie"=>$id_especie,
        "id_procedencia"=>$id_procedencia,
        "id_material"=>$id_material,
        "cantidad_despachar"=>$cantidad_despachar,
      ];

      $sql = "INSERT INTO despachos_detalle (id_despacho, id_servicio, id_especie, id_procedencia, id_material, cantidad_plantines) VALUES (?,?,?,?,?,?)";
      $q = $pdo->prepare($sql);
      //$q->execute(array($idVenta,$id_servicio,$cantidad_despachar,$precio,$subtotal,$modalidad,$pagado));
      $params=array($id_despacho,$id_servicio,$id_especie,$id_procedencia,$id_material,$cantidad_despachar);
      $q->execute($params);
      $afe=$q->rowCount();

      if($afe==1){
        $cantProdOK++;
      }

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }

      $aDebug[]=[
        "consulta"=>$sql,
        "params"=>$params,
        "afe"=>$q->rowCount(),
      ];

    }
    
  }

  $aContenedores=[];
  $cantContenedoresOK=0;
  foreach ($_POST['id_contenedor'] as $key => $id_contenedor) {
    $cantidad_contenedores = $_POST['cantidad_contenedores'][$key];

    if($cantidad_contenedores>0){

      $aContenedores[]=[
        "id_contenedor"=>$id_contenedor,
        "cantidad_contenedores"=>$cantidad_contenedores,
      ];

      $sql = "INSERT INTO despachos_contenedores (id_despacho, id_contenedor, cantidad_despachada) VALUES (?,?,?)";
      $q = $pdo->prepare($sql);
      $params=array($id_despacho,$id_contenedor,$cantidad_contenedores);
      $q->execute($params);
      $afe=$q->rowCount();

      if($afe==1){
        $cantContenedoresOK++;
      }

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }

      $aDebug[]=[
        "consulta"=>$sql,
        "params"=>$params,
        "afe"=>$q->rowCount(),
      ];

    }
    
  }


  if ($modoDebug==1) {
    var_dump($aProductos);
    echo "cantProdOK==count(aProductos)<br>";
    echo $cantProdOK."==".count($aProductos)."<br>";
    var_dump($cantProdOK==count($aProductos));

    var_dump($aContenedores);
    echo "cantContenedoresOK==count(aContenedores)<br>";
    echo $cantContenedoresOK."==".count($aContenedores)."<br>";
    var_dump($cantContenedoresOK==count($aContenedores));

    echo "<br><br>";
  }

  $todoOk=0;
  if($cantProdOK==count($aProductos) and $cantContenedoresOK==count($aContenedores)){
    $todoOk=1;
  }

  if($todoOk==0){
    $pdo->rollback();
    var_dump($aDebug);
    die("Ha ocurrido un error al cargar los productos de la venta");
  }

  if ($modoDebug==1) {
    $pdo->rollBack();
    die();
  }

  $pdo->commit();
  Database::disconnect();
  
  header("Location: verCliente.php?id=".$_GET["id_cliente"]);
}
$id_perfil=$_SESSION["user"]["id_perfil"];?>
<!DOCTYPE html>
<html lang="en">
  <head><?php
    include('head_forms.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/datatables.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
    <style>
      .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0;}
    </style>
  </head>
  <body class="light-only">
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper"><?php
      include('header.php');?>
      <!-- Page Header Start-->
      <div class="page-body-wrapper"><?php
        include('menu.php');?>
        <!-- Page Sidebar Start-->
        <!-- Right sidebar Ends-->
        <div class="page-body">
          <div class="container-fluid">
            <div class="page-header">
              <div class="row">
                <div class="col-10">
                  <div class="page-header-left">
                    <h3><?php include("title.php"); ?></h3>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="#"><i data-feather="home"></i></a></li>
                      <li class="breadcrumb-item">Nueva Venta</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?php echo date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
                    </ul>
                  </div>
                </div>
                <!-- Bookmark Ends-->
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Nueva Venta</h5>
                    <h6><?php
                      $disabledFacturaElectronica="";
                      if (extension_loaded('soap')) {
                        try{
                          $afip = new Afip($aInitializeAFIP);
                          $server_status = (array) $afip->ElectronicBilling->GetServerStatus();
                          //var_dump($server_status);
                          //$server_status["AppServer"]="Error";
                          //$server_status["DbServer"]="Error2";
                          //$server_status["AuthServer"]="Error3";
                          $ok=0;
                          foreach($server_status as $key => $value){
                            if($key!="DbServer"){//este parametro no lo contemplamos porque casi siempre está en NO y funciona igual
                              if($value!="OK"){
                                $disabledFacturaElectronica="disabled";
                                if($ok==0){
                                  echo "<br>AFIP informa los siguientes errores:<br>";
                                }
                                $ok++;
                                echo "<b>".$key.":</b> ".$value."<br>";
                              }
                            }
                          }
                        } catch (SoapFault $e) {
                          // Captura específicamente una excepción SOAP Fault
                          // Muestra un mensaje personalizado en lugar de mostrar el error real
                          echo "<br>Se produjo un error al comunicarse con el servidor de AFIP. Por favor, inténtelo de nuevo más tarde.";
                          $disabledFacturaElectronica="disabled";
                        } catch (Exception $e) {
                          // Captura otras excepciones que puedan ocurrir durante la ejecución del código
                          echo "<br>Se produjo un error inesperado. Por favor, póngase en contacto con el soporte técnico.";
                          $disabledFacturaElectronica="disabled";
                        }
                      } else {
                        echo "<br>SOAP no está habilitado en tu servidor. Aun no podemos generar Facturas Electronicas y te pedimos disculas por ello. Este mensaje desaparecerá automaticamente cuando SOAP haya sido habilitado";
                        $disabledFacturaElectronica="disabled";
                      }
                      $checkdFacturaConsumidorFinal="checked";
                      $checkdSinFactura="";
                      if($disabledFacturaElectronica=="disabled"){
                        $checkdFacturaConsumidorFinal="";
                        $checkdSinFactura="checked";
                      }?>
                    </h6>
                  </div>
                  <form class="form theme-form" role="form" method="post" action="nuevaVenta.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row"><?php
                            $pdo = Database::connect();
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $sqlZon = "SELECT MAX(fecha_cbte) AS fecha_ult_cbte FROM facturas";
                            $q = $pdo->prepare($sqlZon);
                            $q->execute();
                            $fila = $q->fetch(PDO::FETCH_ASSOC);

                            $hace_cinco_dias=date("Y-m-d",strtotime(date("Y-m-d")." -5 days"));
                            
                            $ultima_fecha_disponible_para_facturas = max($fila["fecha_ult_cbte"], $hace_cinco_dias);

                            Database::disconnect();
                          ?>
                            <label class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-9"><input type="date" name="fecha" id="fecha" class="form-control form-control-sm" value="<?=date("Y-m-d")?>" min="<?=$ultima_fecha_disponible_para_facturas?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-9">
                              <select name="id_cliente" id="id_cliente" class="js-example-basic-single col-sm-12" required>
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, razon_social, cuit FROM clientes WHERE activo = 1";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  $cuit="";
                                  if($fila["cuit"]){
                                    $cuit=" (".$fila["cuit"].")";
                                  }
                                  echo "<option value='".$fila['id']."' data-cuit='".$fila['cuit']."'>".$fila['razon_social']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <!-- <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tipo de comprobante</label>
                            <div class="col-sm-9">
                            <select name="tipo_comprobante" id="tipo_comprobante" class="js-example-basic-single col-sm-12" required="required">
                                <option value="">Seleccione...</option>
                                <option value="B" selected>Factura B</option>
                              </select>
                            </div>
                          </div> -->
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Facturar como:</label>
                            <div class="col-sm-9">
                              <label class="d-block" for="edo-ani">
                                <input class="radio_animated" value="Consumidor Final" <?=$checkdFacturaConsumidorFinal?> required id="edo-ani" type="radio" name="facturacion" <?=$disabledFacturaElectronica?>>
                                <label for="edo-ani">Consumidor Final</label>
                              </label>
                              <label class="d-block" for="edo-ani1">
                                <input class="radio_animated" value="Cliente" required id="edo-ani1" type="radio" name="facturacion" <?=$disabledFacturaElectronica?>>
                                <label for="edo-ani1">Cliente <span id="lbl_cuit_cliente"></span></label>
                              </label>
                              <input type="hidden" name="cuit" id="hidden_cuit_cliente">
                              <label class="d-block" for="edo-ani2">
                                <input class="radio_animated" value="Sin factura" <?=$checkdSinFactura?> required id="edo-ani2" type="radio" name="facturacion">
                                <label for="edo-ani2">Sin factura <span id="lbl_cuit_cliente"></span></label>
                              </label>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Productos</label>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12" id="tabla_productos">
                              <table class="table table-sm display">
                                <thead>
                                  <tr>
                                    <th>Descripcion</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                  </tr>
                                </thead>
                                <tbody><?php
                                  $pdo = Database::connect();
                                  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                  $sqlZon = "SELECT id, descripcion,precio,iva FROM productos p WHERE activo = 1 ORDER BY (SELECT COUNT(*) FROM ventas_detalle vd WHERE vd.id_producto=p.id) DESC";
                                  $q = $pdo->prepare($sqlZon);
                                  $q->execute();
                                  while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {?>
                                    <tr>
                                      <td><?=$fila["descripcion"]?></td>
                                      <td>
                                        <input type="number" name="precio[]" class="form-control form-control-sm precio" value="<?=$fila["precio"]?>">
                                        <input type="hidden" name="id_producto[]" value="<?=$fila["id"]?>">
                                        <input type="hidden" name="iva[]" value="<?=$fila["iva"]?>">
                                      </td>
                                      <td><input type="number" name="cantidad[]" class="form-control form-control-sm cantidad"></td>
                                      <td><input type="number" name="subtotal[]" class="form-control form-control-sm subtotal" readonly tabindex="-1"></td>
                                    </tr><?php
                                  }
                                  Database::disconnect();?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Total</label>
                            <div class="col-sm-9"><label id="total_compra">$ 0</label></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Modalidad de pago</label>
                            <div class="col-sm-9">
                              <label class="d-block" for="modalidad_cta_cte">
                                <input class="radio_animated" value="Cuenta Corriente" checked required id="modalidad_cta_cte" type="radio" name="modalidad_pago"><label for="modalidad_cta_cte">Cuenta Corriente</label>
                              </label>
                              <label class="d-block" for="modalidad_contado">
                                <input class="radio_animated" value="Contado" required id="modalidad_contado" type="radio" name="modalidad_pago"><label for="modalidad_contado">Contado</label>
                              </label>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Observaciones</label>
                            <div class="col-sm-9"><textarea name="observaciones" id="observaciones" class="form-control"></textarea></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Crear</button>
                        <a href="listarVentas.php" class="btn btn-light">Volver</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start--><?php 
        include("footer.php"); ?>
      </div>
    </div>
    <!-- latest jquery-->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap js-->
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <script src="assets/js/bootstrap/bootstrap.js"></script>
    <!-- feather icon js-->
    <script src="assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="assets/js/icons/feather-icon/feather-icon.js"></script>
    <!-- Sidebar jquery-->
    <script src="assets/js/sidebar-menu.js"></script>
    <script src="assets/js/config.js"></script>
    <!-- Plugins JS start-->
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	  <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
	
	  <script src="assets/js/datatable/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.buttons.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/jszip.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.colVis.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/pdfmake.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/vfs_fonts.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.autoFill.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.select.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.html5.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.print.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.responsive.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/responsive.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.keyTable.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.colReorder.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.fixedHeader.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.rowReorder.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.scroller.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
	  <script>
      $(document).ready(function() {

        $('#dataTables-example666').DataTable({
          stateSave: true,
          responsive: true,
          processing: true,
          scrollY: false,
          language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
            "infoEmpty": "Mostrando 0 to 0 of 0 Registros",
            "infoFiltered": "(Filtrado de _MAX_ total registros)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No hay resultados",
            "paginate": {
              "first": "Primero",
              "last": "Ultimo",
              "next": "Siguiente",
              "previous": "Anterior"
            }
          },
        })

        $("#id_cliente").on("change",function(){
          let lbl_cuit=""
          let cuit=0;
          console.log(this.value);
          if(this.value!=""){
            cuit=$("#id_cliente option[value='"+this.value+"']").data("cuit")
            lbl_cuit="("+cuit+")";
          }
          console.log(cuit);
          $("#lbl_cuit_cliente").html(lbl_cuit)
          $("#hidden_cuit_cliente").val(cuit)
        })

        $("form").on("submit",function(e){
          e.preventDefault();
          if($('#tabla_productos tbody tr').length){
            //console.log("submit");
            let sin_cantidad=1;
            //console.log($("input[type='number'][name='cantidad[]'"));
            $("input[type='number'][name='cantidad[]'").each(function(){
              if(this.value>0){
                sin_cantidad=0;
              }
            });
            if(sin_cantidad==1){
              alert("Ingrese la cantidad de al menos 1 producto")
            }else{
              $("#btnSubmit").addClass("disabled")
              this.submit();
              //console.log("submit")
            }
          }else{
            alert("Añada algún producto")
          }
        });

        $(document).on("keyup change",".cantidad, .precio",function(){
          let fila=$(this).closest("tr");
          let cantidad=fila.find(".cantidad").val()
          let precio=fila.find(".precio").val()
          fila.find(".subtotal").val(parseInt(precio)*parseInt(cantidad))
          actualizarMontoTotal()
        })

      });

      function actualizarMontoTotal(){
        var total=0;
        $(".subtotal").each(function(){
          let subtotal=$(this).val()
          if(isNaN(subtotal) || subtotal==""){subtotal=0;}
          total+=parseInt(subtotal);
        })
        $("#total_compra").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total))
      }

		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
		
  </body>
</html>