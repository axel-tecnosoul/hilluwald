<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
require 'funciones.php';

$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarClientes.php");
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT c.id, c.razon_social, c.cuit, c.cond_fiscal, c.direccion, c.email, c.telefono, c.notas, lo.localidad, pr.provincia, c.activo FROM clientes c INNER JOIN localidades lo ON c.id_localidad = lo.id INNER JOIN provincias pr ON lo.id_provincia = pr.id WHERE c.id = ? ";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

$hoy=date("Y-m-d");
// Obtener el año actual
$anio_actual = date("Y");
// Calcular los años límite
$anio_inicial = $anio_actual - 2;
$anio_final = $anio_actual + 2;

$sql = " SELECT c.id AS id_cultivo, e.id AS id_especie, pe.id AS id_procedencia, e.especie, pe.procedencia, c.material, c.nombre_corto, e.icono, e.color FROM pedidos_detalle pd INNER JOIN pedidos p ON pd.id_pedido=p.id INNER JOIN especies e ON pd.id_especie=e.id LEFT JOIN procedencias_especies pe ON pd.id_procedencia=pe.id LEFT JOIN cultivos c ON pd.id_material=c.id WHERE p.anulado=0 AND p.id_cliente=".$id." GROUP BY e.id";
//$sql = " SELECT c.id, c.nombre FROM cultivos c";
$aCultivosPedidos=[];
foreach ($pdo->query($sql) as $row) {
  $nombre=$row["nombre_corto"];
  if(empty($nombre)){
    $nombre=$row["especie"];
  }
  $aCultivosPedidos[]=[
    "id_cultivo"=>$row["id_especie"],
    "nombre"=>$nombre,
    "icono"=>$row["icono"],
    "color"=>$row["color"],
  ];
}

Database::disconnect();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
    <link rel="stylesheet" type="text/css" href="assets/css/datatables.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css">
  </head>
  <style>
    .pedidos{
      background-color: #28a745!important;
      color: #fff!important;
    }
    .despachos{
      background-color: #6c757d!important;
      color: #fff!important;
    }
    .pagos{
      background-color: #007bff!important;
      color: #fff!important;
    }
    .contenedores {
      background-color: #ffc107!important;
      color: #000!important;
    }
    .plantines {
      background-color: #dc3545!important;
      color: #fff!important;
    }
    .tablas_cta_cte th, .tablas_cta_cte td {
      border: 1px solid #dee2e6;
    }
    .borderDespachoLeft{
      /*border-left: solid 1px #6c757d !important;*/
      /*background-color: #6c757d !important;*/
      /*background-color: rgb(108 117 125)!important;*/
    }
    .borderDespachoRight{
      /*border-right: solid 1px #6c757d !important;*/
      /*background-color: #6c757d !important;*/
      /*background-color: rgb(108 117 125)!important;*/
    }
    .borderPagoLeft{
      /*border-left: solid 1px #007bff !important;*/
      /*background-color: #007bff !important;*/
      /*background-color: rgb(0,123,255,0.5) !important;*/
    }
    .borderPagoRight{
      /*border-right: solid 1px #007bff !important;*/
      /*background-color: #007bff !important;*/
      /*background-color: rgb(0,123,255,0.5) !important;*/
    }
    td.child {
      background-color: beige;
    }
    .multiselect{
      color:#212529 !important;
      background-color:#fff !important;
      border-color:#ccc !important;
      /*height: auto !important;*/
      height: calc(2.25rem + 2px);
    }
    .select2-selection .select2-selection--single{
      border-color: #ccc !important;
      height: calc(2.25rem + 2px) !important;
    }
    .input-error {
      border: 1px solid red !important;
    }
    .select2-container--default .select2-results__option[aria-disabled=true] {
      display: none;
    }

    .dataTables_wrapper button{
      font-size: 12px;
    }
    .dropdown-menu{
      padding: 0;
    }
    .ctaCteCellHeader{
      text-align: center !important;
      vertical-align: middle !important;
    }
  </style>

  <!-- latest jquery-->
  <script src="assets/js/jquery-3.2.1.min.js"></script>
  <script src="assets/js/select2/select2.full.min.js"></script>
  <script src="assets/js/select2/select2-custom.js"></script>
  
  <body class="light-only">
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
	    <?php include('header.php');?>
	  
      <!-- Page Header Start-->
      <div class="page-body-wrapper">
		    <?php include('menu.php');?>
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
                      <li class="breadcrumb-item">Ver Proveedor</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a  target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?=date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
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
                <div class="card"><?php
                //echo $sql;?>
                  
                  <div class="card-header">
                    <div class="row">
                      <div class="col-md-4">
                        <h5><?=$data['razon_social']; ?></h5>
                        <button class="btn pedidos mt-3" style="text-transform: none;" title="Nuevo Pedido" data-toggle="modal" data-target="#nuevoPedido">
                          <i class="fa fa-plus"></i> Pedido
                        </button>
                      </div>
                      <div class="col-md-8">
                        <div class="row">
                          <div class="col-md-1">
                            <label for="notas" class="col-form-label">Notas:</label>
                          </div>
                          <div class="col-md-11">
                            <textarea name="notas" id="notas" class="form-control"><?=$data["notas"]?></textarea>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="card-body">
                    <ul class="nav nav-tabs border-tab" id="pills-tab" role="tablist" style="margin-top: -20px;">
                    
                      <li class="nav-item"><a class="nav-link active" id="pills-resumen-tab" data-toggle="pill" href="#pills-resumen" role="tab" aria-controls="pills-resumen" aria-selected="true"><i class="fa fa-server"></i>Resumen</a></li><?php
                      
                      foreach ($aCultivosPedidos as $cultivo_pedido) {?>
                        <li class="nav-item">
                          <a class="nav-link nav_id_cultivo" id="pills-cta_cte-tab" data-toggle="pill" href="#pills-cta_cte" role="tab" aria-controls="pills-cta_cte" aria-selected="false" data-id_cultivo="<?=$cultivo_pedido["id_cultivo"]?>"><?php
                            if($cultivo_pedido["icono"]){
                              $style="";
                              if($cultivo_pedido["color"]){
                                $style="color:".$cultivo_pedido["color"];
                              }?>
                              <i class="<?=$cultivo_pedido["icono"]?>" style=<?=$style?>></i><?php
                            }
                            echo acortarPalabras($cultivo_pedido["nombre"])?>
                          </a>
                        </li><?php
                      }?>

                      <li class="nav-item"><a class="nav-link" id="pills-contenedores-tab" data-toggle="pill" href="#pills-contenedores" role="tab" aria-controls="pills-contenedores" aria-selected="false"><i class="fa fa-th"></i>Contenedores</a></li>

                      <li class="nav-item"><a class="nav-link" id="pills-contacto-tab" data-toggle="pill" href="#pills-contacto" role="tab" aria-controls="pills-contacto" aria-selected="true"><i class="icofont icofont-ui-user"></i>Datos</a></li>

                    </ul>

                    <div class="tab-content" id="pills-tabContent">

                      <div class="tab-pane fade show active" id="pills-resumen" role="tabpanel" aria-labelledby="pills-resumen-tab"></div>
                      
                      <div class="tab-pane fade show" id="pills-contacto" role="tabpanel" aria-labelledby="pills-contacto-tab">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Razon Social</label>
                          <div class="col-sm-9"><input name="razon_social" type="text" maxlength="99" class="form-control" value="<?=$data['razon_social']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">CUIT</label>
                          <div class="col-sm-9"><input name="cuit" type="text" maxlength="99" class="form-control" value="<?=$data['cuit']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Condicion fiscal</label>
                          <div class="col-sm-9"><input name="cond_fiscal" type="text" maxlength="99" class="form-control" value="<?=$data['cond_fiscal']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Localidad</label>
                          <div class="col-sm-9"><input name="id_localidad" type="text" maxlength="99" class="form-control" value="<?=$data['localidad'] . " - " . $data['provincia']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Direccion</label>
                          <div class="col-sm-9"><input name="direccion" type="text" maxlength="99" class="form-control" value="<?=$data['direccion']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">E-Mail</label>
                          <div class="col-sm-9"><input name="email" type="email" maxlength="99" class="form-control" value="<?=$data['email']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Teléfono</label>
                          <div class="col-sm-9"><input name="telefono" type="text" maxlength="99" class="form-control" value="<?=$data['telefono']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Activo</label>
                          <div class="col-sm-9">
                            <select name="activo" id="activo" class="js-example-basic-single col-sm-12" disabled="disabled">
                              <option value="">Seleccione...</option>
                              <option value="1" <?php if ($data['activo']==1) echo " selected ";?>>Si</option>
                              <option value="0" <?php if ($data['activo']==0) echo " selected ";?>>No</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <!-- INICIO CTA CTE -->
                      <div class="tab-pane fade" id="pills-cta_cte" role="tabpanel" aria-labelledby="pills-cta_cte-tab">
                        <table class="table mb-3">
                          <tr>
                            <td class="text-right border-0 p-1">Desde: </td>
                            <td class="border-0 p-1">
                              <input type="date" id="desde_cta_cte" value="<?=date("Y-m-d",strtotime(date("Y-m-d")." -1 year"))?>" class="form-control form-control-sm filtraTablaCtaCte">
                              <input type="hidden" id="id_cultivo_cta_cte">
                            </td>
                            <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Servicio:</td>
                            <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                              <select id="id_servicio" class="form-control form-control-sm filtraTablaCtaCte selectpicker" data-style="multiselect"><?php
                                $pdo = Database::connect();
                                $sql = "SELECT id,servicio FROM servicios WHERE activo=1";
                                foreach ($pdo->query($sql) as $row) {
                                  echo "<option value='".$row['id']."'";
                                  if($row["id"]==2){
                                    echo " selected";
                                  }
                                  echo ">".$row['servicio']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </td>
                            <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Comprobante:</td>
                            <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                              <select id="tipo_comprobante" class="form-control form-control-sm filtraTablaCtaCte selectpicker" data-style="multiselect" data-selected-text-format="count > 1" multiple>
                                <option value="Pedido">Pedido</option>
                                <option value="Despacho">Despacho</option>
                                <option value="Pago">Pago</option>
                              </select>
                            </td>
                          </tr>
                          <tr>
                            <td class="text-right border-0 p-1">Hasta: </td>
                            <td class="border-0 p-1"><input type="date" id="hasta_cta_cte" value="<?=date("Y-m-d")?>" class="form-control form-control-sm filtraTabla"></td>
                          </tr>
                        </table>
                        <table class="table table-striped table-bordered tablas_cta_cte" style="width:100%">
                          <thead>
                            <tr>
                              <th class="ctaCteCellHeader" rowspan="2">Fecha</th>
                              <th class="ctaCteCellHeader" rowspan="2">Comprobante</th>
                              <th class="ctaCteCellHeader" rowspan="2">Detalle</th>
                              <th class="ctaCteCellHeader" rowspan="2">Campaña</th>
                              <th class="ctaCteCellHeader" rowspan="2">Cantidad pedida</th>
                              <th class="ctaCteCellHeader" colspan="2">Despachos</th>
                              <th class="ctaCteCellHeader" colspan="2">Pagos</th>
                            </tr>
                            <tr>
                              <th>Cantidad</th>
                              <th>Saldo</th>
                              <th>Cantidad</th>
                              <th>Saldo</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr>
                              <th class="text-right" colspan="4">Totales</th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                      <!-- FIN CTA CTE -->

                      <!-- INICIO CONTENEDORES -->
                      <div class="tab-pane fade" id="pills-contenedores" role="tabpanel" aria-labelledby="pills-contenedores-tab">
                        <table class="table mb-3">
                          <tr>
                            <td class="text-right border-0 p-1">Desde: </td>
                            <td class="border-0 p-1"><input type="date" id="desde_contenedores" value="<?=date("Y-m-d",strtotime(date("Y-m-d")." -1 year"))?>" class="form-control form-control-sm filtraTablaContenedores"></td>
                            <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Contenedores:</td>
                            <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                              <select id="id_contenedor" class="form-control form-control-sm filtraTablaContenedores selectpicker" data-style="multiselect"><?php
                                $pdo = Database::connect();
                                $sql = "SELECT c.id AS id_contenedor,tp.tipo,c.cantidad_orificios,c.ancho,c.alto FROM contenedores c INNER JOIN tipos_contenedores tp ON c.id_tipo_contenedor=tp.id WHERE c.activo=1";
                                foreach ($pdo->query($sql) as $row) {
                                  $mostrar=$row["tipo"]." ".$row["cantidad_orificios"]."u. ".$row["ancho"]."x".$row["alto"]."x[LARGO]"?>
                                  <option value="<?=$row["id_contenedor"]?>"><?=$mostrar?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </td>
                            <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1"></td>
                            <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1"></td>
                          </tr>
                          <tr>
                            <td class="text-right border-0 p-1">Hasta: </td>
                            <td class="border-0 p-1"><input type="date" id="hasta_contenedores" value="<?=date("Y-m-d")?>" class="form-control form-control-sm filtraTablaContenedores"></td>
                          </tr>
                        </table>
                        <table class="table table-striped table-bordered" id="tabla_contenedores" style="width:100%">
                          <thead>
                            <tr>
                              <th class="ctaCteCellHeader">Fecha</th>
                              <th class="ctaCteCellHeader">Comprobante</th>
                              <!-- <th class="ctaCteCellHeader">Detalle</th> -->
                              <!-- <th class="ctaCteCellHeader">Campaña</th> -->
                              <th class="ctaCteCellHeader">Cantidad despachada</th>
                              <th class="ctaCteCellHeader">Cantidad devuelta</th>
                              <th class="ctaCteCellHeader">Saldo</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr>
                              <th class="text-right" colspan="2">Totales</th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                      <!-- FIN CONTENEDORES -->

                    </div>
                  </div>

                  <div class="card-footer">
                    <div class="col-sm-9 offset-sm-3">
                    <a href="listarClientes.php" class="btn btn-light">Volver</a>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!-- MODAL PARA NUEVO PEDIDO -->
          <?php include_once("modalNuevoPedido.php")?>
          <!-- FIN MODAL PARA NUEVO PEDIDO -->

          <!-- MODAL PARA NUEVO DESPACHO -->
          <?php include_once("modalNuevoDespacho.php")?>
          <!-- FIN MODAL PARA NUEVO DESPACHO -->

          <!-- MODAL PARA NUEVO PAGO -->
          <?php include_once("modalNuevoPago.php")?>
          <!-- FIN MODAL PARA NUEVO PAGO -->

          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
		    <?php include("footer.php"); ?>
      </div>
    </div>

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

    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>

    <!-- Plugin used-->    
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
        //$("#nuevoPedido").modal("show")

        $('.formulario').submit(function(event) {
          let inputs_cantidad=$(this).find('input.cantidad')
          let bandera=0;
          var cantidades = inputs_cantidad.map(function() {
            if($(this).val()!=""){
              bandera=1;
            }
          }).get();

          if (bandera==0) {
            // Mostrar mensaje de error
            $(this).find('.mensajeError').fadeIn();

            // Resaltar los inputs vacíos temporalmente
            inputs_cantidad.addClass('input-error');
            setTimeout(function() {
                inputs_cantidad.removeClass('input-error');
            }, 2000);

            event.preventDefault(); // Evitar el envío del formulario
          }
        });

        $(document).on("change",".id_especie",function(){
          let id_especie=this.value;
          let fila=$(this).parents("tr");
          
          let disabled=true;
          if(id_especie>0){
            disabled=false;
          }

          fila.find(".cantidad").attr("disabled",disabled)

          let select_procedencia=fila.find(".id_procedencia")
          select_procedencia=select_procedencia[0]

          getProcedenciasDeEspecie(select_procedencia,id_especie);

          let id_procedencia=fila.find(".id_procedencia").val()
          let select_material=fila.find(".id_material")
          select_material=select_material[0]

          getMaterialesDeProcedencia(select_material,id_procedencia,id_especie);
        })

        $(document).on("change",".id_procedencia",function(){
          console.log(this);
          let id_procedencia=this.value;
          let fila=$(this).parents("tr");
          
          let disabled=true;
          if(id_procedencia>0){
            disabled=false;
          }
          
          fila.find(".id_material").attr("disabled",disabled)

          let id_especie=fila.find(".id_especie").val()
          let select_material=fila.find(".id_material")
          select_material=select_material[0]

          getMaterialesDeProcedencia(select_material,id_procedencia,id_especie);
        })

        function getProcedenciasDeEspecie(select_procedencia,id_especie){
          select_procedencia.innerHTML="";

          if(id_especie>0){
            select_procedencia.disabled=true;
            $option = document.createElement("option");
            let optionText = document.createTextNode("Buscando...");
            $option.appendChild(optionText);
            $option.setAttribute("value","");
            //$option.setAttribute("data-horas", procedencias.horas_caducidad);
            select_procedencia.appendChild($option);

            let datosIniciales = new FormData();
            datosIniciales.append('id_especie', id_especie);
            $.ajax({
              data: datosIniciales,
              url: "getProcedenciasDeEspecie.php",
              method: "post",
              cache: false,
              contentType: false,
              processData: false,
              success: function(respuesta){
                //console.log(respuesta);
                /*Convierto en json la respuesta del servidor*/
                respuestaJson = JSON.parse(respuesta);

                select_procedencia.disabled=false;

                select_procedencia.innerHTML="";

                $option = document.createElement("option");
                let optionText = document.createTextNode("Seleccione...");
                $option.appendChild(optionText);
                $option.setAttribute("value","");
                //$option.setAttribute("data-horas", procedencias.horas_caducidad);
                select_procedencia.appendChild($option);

                //Genero los options del select de prioridades
                respuestaJson.forEach((procedencias)=>{
                  $option = document.createElement("option");
                  let optionText = document.createTextNode(procedencias.procedencia);
                  $option.appendChild(optionText);
                  $option.setAttribute("value", procedencias.id);
                  //$option.setAttribute("data-horas", procedencias.horas_caducidad);
                  select_procedencia.appendChild($option);
                });

              }
            });
          }else{
            select_procedencia.disabled=true;
            $option = document.createElement("option");
            let optionText = document.createTextNode("Seleccione una procedencia...");
            $option.appendChild(optionText);
            $option.setAttribute("value","");
            //$option.setAttribute("data-horas", procedencias.horas_caducidad);
            select_procedencia.appendChild($option);
          }
        }

        function getMaterialesDeProcedencia(select_material,id_procedencia,id_especie){
          console.log(select_material);
          select_material.innerHTML="";

          if(id_procedencia>0){

            select_material.disabled=false;
            $option = document.createElement("option");
            let optionText = document.createTextNode("Buscando...");
            $option.appendChild(optionText);
            $option.setAttribute("value","");
            //$option.setAttribute("data-horas", procedencias.horas_caducidad);
            select_material.appendChild($option);

            let datosIniciales = new FormData();
            datosIniciales.append('id_especie', id_especie);
            datosIniciales.append('id_procedencia', id_procedencia);
            $.ajax({
              data: datosIniciales,
              url: "getMaterialesDeProcedencia.php",
              method: "post",
              cache: false,
              contentType: false,
              processData: false,
              success: function(respuesta){
                //console.log(respuesta);
                /*Convierto en json la respuesta del servidor*/
                respuestaJson = JSON.parse(respuesta);
                console.log(respuestaJson);

                select_material.innerHTML="";

                $option = document.createElement("option");
                let optionText = document.createTextNode("Seleccione...");
                $option.appendChild(optionText);
                $option.setAttribute("value","");
                //$option.setAttribute("data-horas", procedencias.horas_caducidad);
                select_material.appendChild($option);

                //Genero los options del select de prioridades
                respuestaJson.forEach((materiales)=>{
                  $option = document.createElement("option");
                  let optionText = document.createTextNode(materiales.material);
                  $option.appendChild(optionText);
                  $option.setAttribute("value", materiales.id);
                  //$option.setAttribute("data-horas", materiales.horas_caducidad);
                  select_material.appendChild($option);
                });

              }
            });
          }else{
            select_material.disabled=true;
            $option = document.createElement("option");
            let optionText = document.createTextNode("Seleccione una procedencia...");
            $option.appendChild(optionText);
            $option.setAttribute("value","");
            //$option.setAttribute("data-horas", procedencias.horas_caducidad);
            select_material.appendChild($option);
          }
        }

        $(document).on("click",".btnNuevoDespacho",function(){
          let id_pedido=this.dataset.idPedido;
          let modal=$("#nuevoDespacho")
          modal.modal("show")
          modal.find("span.idPedido").html(id_pedido)
          $("#id_pedido_despacho").val(id_pedido)

          getDetallePedido(id_pedido)
        })

        $(document).on("click",".btnNuevoPago",function(){
          let modal=$("#nuevoPago")
          modal.modal("show")
          modal.find("span.idPedido").html(this.dataset.idPedido)
        })

        $(document).on("click",".btnNuevaDevolucionContenedores",function(){
          let modal=$("#nuevaDevolucionContenedores")
          modal.modal("show")
          modal.find("span.idPedido").html(this.dataset.idPedido)
        })

        $(document).on("click",".btnNuevaDevolucionPlantines",function(){
          let modal=$("#nuevaDevolucionPlantines")
          modal.modal("show")
          modal.find("span.idPedido").html(this.dataset.idPedido)
        })

        $("#pills-contenedores-tab").on("click",getCtaCteContenedores)

        $(".filtraTablaContenedores").on("change",getCtaCteContenedores)

        function getCtaCteContenedores(){
          let desde=$("#desde_contenedores").val();
          let hasta=$("#hasta_contenedores").val();
          //let tipo_comprobante=$("#tipo_comprobante").val();
          let id_contenedor=$("#id_contenedor").val();
          let id_cliente=<?=$id?>;

          let table=$('#tabla_contenedores')
          table.DataTable().destroy();
          table.DataTable({
            //dom: 'rtip',
            //serverSide: true,
            processing: true,
            ajax:{url:'ajaxGetCtaCteContenedores.php?desde='+desde+'&hasta='+hasta+'&id_cliente='+id_cliente+'&id_contenedor='+id_contenedor,dataSrc:""},
            stateSave: true,
            responsive: true,

            dom: 'rtip',
            ordering: false,
            paginate: false,
            //scrollY: '100vh',
            scrollCollapse: true,
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
            "columns":[
              {"data": "fecha"},
              {render: function(data, type, row, meta) {
                let tipo_comprobante=row.tipo_comprobante;
                let clase;
                if(tipo_comprobante=="Pedido"){
                  clase="pedidos";
                }
                if(tipo_comprobante=="Despacho"){
                  clase="despachos";
                }
                if(tipo_comprobante=="Pago"){
                  clase="pagos";
                }
                if(tipo_comprobante=="Despacho"){
                  return `
                    <div class="dropdown">
                      <button class="btn btn-sm ${clase} dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" style="white-space: nowrap;">
                        ${tipo_comprobante+' N° '+row.id_despacho}
                      </button>
                      <div class="dropdown-menu">
                        <a href="#" class="dropdown-item contenedores btnNuevaDevolucionContenedores" data-id-pedido="${row.id_despacho}">
                          <i class="fa fa-plus"></i> Devolucion
                        </a>
                      </div>
                    </div>
                  `;
                }else{
                  return '<button class="btn btn-sm '+clase+'">'+tipo_comprobante+' N° '+row.id_despacho+'</button>'
                }
              }},
              /*{
                render: function(data, type, row, meta) {
                  let procedencia=row.procedencia
                  if(procedencia!=""){
                    procedencia="P: "+procedencia
                  }

                  let material=row.material
                  if(material!=""){
                    material=" - M: "+material
                  }
                  return procedencia+material;
                },
                //className: "dt-body-right",
              },*/
              //{"data": "campana"},
              {
                render: function(data, type, row, meta) {
                  return Intl.NumberFormat("de-DE").format(row.cantidad_despachada)
                },
                className: "dt-body-right",
              },
              {
                render: function(data, type, row, meta) {
                  return Intl.NumberFormat("de-DE").format(row.cantidad_devuelta)
                },
                className: "dt-body-right borderDespachoLeft",
              },
              {
                render: function(data, type, row, meta) {
                  return Intl.NumberFormat("de-DE").format(row.saldo_contenedores)
                },
                className: "dt-body-right borderDespachoRight",
              },
            ],
            "columnDefs": [
              { "className": "dt-body-right", "targets": [0,3] },
              { "className": "dt-body-center", "targets": 2 }
            ],
            initComplete: function(settings, json){
              /*let total_facturas_recibos=json.queryInfo.total_facturas_recibos*/

              let ultimoSaldo=0
              json.forEach(item => {
                ultimoSaldo=item.saldo_contenedores
              });
              var api = this.api();
              // Update footer
              $(api.column(4).footer()).html(Intl.NumberFormat("de-DE").format(ultimoSaldo));

              $('[title]').tooltip();
            }
          })
        }

        $(".nav_id_cultivo").on("click",function(){
          let id_cultivo=this.dataset.id_cultivo
          console.log(id_cultivo);
          $("#id_cultivo_cta_cte").val(id_cultivo)

          getCtaCteEspecies();
        })

        $(".filtraTablaCtaCte").on("change",getCtaCteEspecies)

        function getCtaCteEspecies(){
          let id_cultivo=$("#id_cultivo_cta_cte").val()
          let desde=$("#desde_cta_cte").val();
          let hasta=$("#hasta_cta_cte").val();
          let tipo_comprobante=$("#tipo_comprobante").val();
          let id_servicio=$("#id_servicio").val();
          let id_cliente=<?=$id?>;

          let table=$('.tablas_cta_cte')
          table.DataTable().destroy();
          table.DataTable({
            //dom: 'rtip',
            //serverSide: true,
            processing: true,
            ajax:{url:'ajaxGetCtaCteEspecies.php?desde='+desde+'&hasta='+hasta+'&tipo_comprobante='+tipo_comprobante+'&id_cliente='+id_cliente+'&id_servicio='+id_servicio+'&id_cultivo='+id_cultivo,dataSrc:""},
            stateSave: true,
            responsive: true,

            dom: 'rtip',
            ordering: false,
            paginate: false,
            //scrollY: '100vh',
            scrollCollapse: true,
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
            "columns":[
              {"data": "fecha"},
              {render: function(data, type, row, meta) {
                let tipo_comprobante=row.tipo_comprobante;
                let clase;
                if(tipo_comprobante=="Pedido"){
                  clase="pedidos";
                }
                if(tipo_comprobante=="Despacho"){
                  clase="despachos";
                }
                if(tipo_comprobante=="Pago"){
                  clase="pagos";
                }
                if(tipo_comprobante=="Pedido"){
                  return `
                    <div class="dropdown">
                      <button class="btn btn-sm ${clase} dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" style="white-space: nowrap;">
                        ${tipo_comprobante+' N° '+row.id_pedido}
                      </button>
                      <div class="dropdown-menu">
                        <a href="#" class="dropdown-item despachos btnNuevoDespacho" data-id-pedido="${row.id_pedido}">
                          <i class="fa fa-plus"></i> Despacho
                        </a>
                        <a href="#" class="dropdown-item pagos btnNuevoPago" data-id-pedido="${row.id_pedido}">
                          <i class="fa fa-plus"></i> Pago
                        </a>
                        <a href="#" class="dropdown-item plantines btnNuevaDevolucionPlantines" data-id-pedido="${row.id_pedido}">
                          <i class="fa fa-plus"></i> Devolucion de plantines
                        </a>
                      </div>
                    </div>
                  `;
                  /*
                    <a href="#" class="dropdown-item contenedores btnNuevaDevolucionContenedores" data-id-pedido="${row.id_pedido}">
                      <i class="fa fa-plus"></i> Devolucion de contenedores
                    </a>
                  */
                }else{
                  return '<button class="btn btn-sm '+clase+'">'+tipo_comprobante+' N° '+row.id_pedido+'</button>'
                }
              }},
              {
                render: function(data, type, row, meta) {
                  let procedencia=row.procedencia
                  if(procedencia!=""){
                    procedencia="P: "+procedencia
                  }

                  let material=row.material
                  if(material!=""){
                    material=" - M: "+material
                  }
                  return procedencia+material;
                },
                //className: "dt-body-right",
              },
              {"data": "campana"},
              {
                render: function(data, type, row, meta) {
                  return Intl.NumberFormat("de-DE").format(row.cantidad_pedido)
                },
                className: "dt-body-right",
              },
              {
                render: function(data, type, row, meta) {
                  return Intl.NumberFormat("de-DE").format(row.cantidad_despacho)
                },
                className: "dt-body-right borderDespachoLeft",
              },
              {
                render: function(data, type, row, meta) {
                  return Intl.NumberFormat("de-DE").format(row.saldo_despacho)
                },
                className: "dt-body-right borderDespachoRight",
              },
              {
                render: function(data, type, row, meta) {
                  return Intl.NumberFormat("de-DE").format(row.cantidad_pago)
                },
                className: "dt-body-right borderPagoLeft",
              },
              {
                render: function(data, type, row, meta) {
                  return Intl.NumberFormat("de-DE").format(row.saldo_pago)
                },
                className: "dt-body-right borderPagoRight",
              },
              /*{
                render: function(data, type, row, meta) {
                  //return Intl.NumberFormat("de-DE").format(row.cantidad_pago)
                  return 0;
                },
                className: "dt-body-right borderPagoLeft",
              },
              {
                render: function(data, type, row, meta) {
                  //return Intl.NumberFormat("de-DE").format(row.saldo_pago)
                  return 0;
                },
                className: "dt-body-right borderPagoRight",
              },*/
            ],
            "columnDefs": [
              { "className": "dt-body-right", "targets": [0,3] },
              { "className": "dt-body-center", "targets": 2 }
            ],
            initComplete: function(settings, json){
              /*let total_facturas_recibos=json.queryInfo.total_facturas_recibos*/

              var api = this.api();
              // Llamada a las funciones
              const totales = sumarCantidades(json);
              //console.log(totales);
              const ultimosSaldos = obtenerUltimosSaldos(json);
              //console.log(ultimosSaldos);
              // Update footer
              //$(api.column(3).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_cantidad));
              $(api.column(4).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalPedido));
              $(api.column(5).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalDespacho));
              $(api.column(6).footer()).html(Intl.NumberFormat("de-DE").format(ultimosSaldos.saldoDespacho));
              $(api.column(7).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalPago));
              $(api.column(8).footer()).html(Intl.NumberFormat("de-DE").format(ultimosSaldos.saldoPago));
              /*$(api.column(9).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalPago));
              $(api.column(10).footer()).html(Intl.NumberFormat("de-DE").format(ultimosSaldos.saldoPago));*/

              $('[title]').tooltip();
            }
          })
        }

        // Función para sumar las cantidades de pedidos, despachos y pagos
        function sumarCantidades(datos) {
          let totalPedido = 0;
          let totalDespacho = 0;
          let totalPago = 0;

          datos.forEach(item => {
            if (item.cantidad_pedido !== "") {
              totalPedido += parseInt(item.cantidad_pedido);
            }
            if (item.cantidad_despacho !== "") {
              totalDespacho += parseInt(item.cantidad_despacho);
            }
            if (item.cantidad_pago !== "") {
              totalPago += parseInt(item.cantidad_pago);
            }
          });

          return {
            totalPedido,
            totalDespacho,
            totalPago
          };
        }

        // Función para obtener el último saldo de despacho y pago
        function obtenerUltimosSaldos(datos) {
          const ultimoDespacho = datos.filter(item => item.saldo_despacho !== "").pop();
          const ultimoPago = datos.filter(item => item.saldo_pago !== "").pop();

          return {
            saldoDespacho: ultimoDespacho ? ultimoDespacho.saldo_despacho : 0,
            saldoPago: ultimoPago ? ultimoPago.saldo_pago : 0
          };
        }

        // Escuchar el evento input del textarea para guardar automáticamente el contenido
        document.getElementById('notas').addEventListener('input', guardarNotas);

        function guardarNotas() {
          let idCliente="<?=$id?>";
          var nota = document.getElementById('notas').value;
          var xhr = new XMLHttpRequest();
          xhr.open('POST', 'guardar_nota_cliente.php');
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          xhr.onload = function() {
              if (xhr.status === 200) {
                  console.log('nota guardado exitosamente');
              } else {
                  console.log('Error al guardar el nota');
              }
          };
          xhr.send('nota=' + encodeURIComponent(nota)+'&idCliente='+idCliente);
          /*xhr.send('nota=' + encodeURIComponent(nota));
          xhr.send('idCliente'+idCliente);*/
        }

      });
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
  </body>
</html>