<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';

$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarClientes.php");
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT id, razon_social, cuit, cond_fiscal, direccion, email, telefono, notas, activo FROM clientes WHERE id = ? ";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

$hoy=date("Y-m-d");
// Obtener el año actual
$anio_actual = date("Y");
// Calcular los años límite
$anio_inicial = $anio_actual - 2;
$anio_final = $anio_actual + 2;

$sql = " SELECT c.id AS id_cultivo, e.id AS id_especie, pe.id AS id_procedencia, e.especie, pe.procedencia, c.material, c.nombre_corto, e.icono, e.color FROM pedidos_detalle pd INNER JOIN pedidos p ON pd.id_pedido=p.id INNER JOIN especies e ON pd.id_especie=e.id LEFT JOIN procedencias_especies pe ON pd.id_procedencia=pe.id LEFT JOIN cultivos c ON pd.id_material=c.id WHERE p.anulado=0 AND p.id_cliente=".$id." GROUP BY c.id";
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
  </style>
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
                <div class="card">
                  
                  <div class="card-header">
                    <!-- <h5><?=$data['razon_social']; ?></h5>
                    <div class="row mt-3">
                      <div class="col-12">
                        
                        <button class="btn pedidos" style="text-transform: none;" title="Nuevo Pedido" data-toggle="modal" data-target="#nuevoPedido"><i class="fa fa-plus"></i> Pedido</button>

                      </div>
                    </div> -->

                    <div class="row">
                      <div class="col-md-4">
                        <h5><?=$data['razon_social']; ?></h5>
                        <button class="btn pedidos mt-3" style="text-transform: none;" title="Nuevo Pedido" data-toggle="modal" data-target="#nuevoPedido">
                          <i class="fa fa-plus"></i> Pedido
                        </button>

                        <!-- <button class="btn despachos" style="text-transform: none;" title="Nuevo Despacho" data-toggle="modal" data-target="#nuevoDespacho"><i class="fa fa-plus"></i> Despacho</button>
                        
                        <button class="btn pagos" style="text-transform: none;" title="Nuevo Pago" data-toggle="modal" data-target="#nuevoPago"><i class="fa fa-plus"></i> Pago</button>

                        <button class="btn contenedores" style="text-transform: none;" title="Nueva devolucion de contenedores" data-toggle="modal" data-target="#nuevaDevolucionContenedores"><i class="fa fa-plus"></i> Contenedores</button> -->
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
                            echo $cultivo_pedido["nombre"]?>
                          </a>
                        </li><?php
                      }?>

                      <li class="nav-item"><a class="nav-link" id="pills-contacto-tab" data-toggle="pill" href="#pills-contacto" role="tab" aria-controls="pills-contacto" aria-selected="true"><i class="icofont icofont-ui-user"></i>Datos</a></li>

                    </ul>

                    <div class="tab-content" id="pills-tabContent">

                      <div class="tab-pane fade show active" id="pills-resumen" role="tabpanel" aria-labelledby="pills-resumen-tab">

                      </div>
                      
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

                      <div class="tab-pane fade" id="pills-cta_cte" role="tabpanel" aria-labelledby="pills-cta_cte-tab">
                        <table class="table mb-3">
                          <tr>
                            <td class="text-right border-0 p-1">Desde: </td>
                            <td class="border-0 p-1"><input type="date" id="desde" value="<?=date("Y-m-d",strtotime(date("Y-m-d")." -1 year"))?>" class="form-control form-control-sm filtraTabla"></td>
                            <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Comprobante:</td>
                            <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                              <select id="tipo_comprobante" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-selected-text-format="count > 1" multiple>
                                <option value="Pedido">Pedido</option>
                                <option value="Despacho">Despacho</option>
                                <option value="Pago">Pago</option>
                              </select>
                            </td>
                          </tr>
                          <tr>
                            <td class="text-right border-0 p-1">Hasta: </td>
                            <td class="border-0 p-1"><input type="date" id="hasta" value="<?=date("Y-m-d")?>" class="form-control form-control-sm filtraTabla"></td>
                          </tr>
                        </table>

                        <table class="table table-striped table-bordered tablas_cta_cte" style="width:100%">
                          <thead>
                            <tr>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Fecha</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Comprobante</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Campaña</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Cantidad pedida</th>
                              <th style="text-align: center;vertical-align: middle;"class="borderDespachoLeft borderDespachoRight" colspan="2">Despachos</th>
                              <th style="text-align: center;vertical-align: middle;" class="borderPagoLeft borderPagoRight" colspan="2">Pagos</th>
                              <th style="text-align: center;vertical-align: middle;" class="borderPagoLeft borderPagoRight" colspan="2">Contenedores</th>
                            </tr>
                            <tr>
                              <th style="text-align: center;vertical-align: middle;">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;">Saldo</th>
                              <th style="text-align: center;vertical-align: middle;">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;">Saldo</th>
                              <th style="text-align: center;vertical-align: middle;">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;">Saldo</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr>
                              <th class="text-right" colspan="3">Totales</th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                              <th class="text-right"></th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>

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
          <div class="modal fade" id="nuevoPedido" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1000000000000" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="max-width: 1200px;">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Nuevo Pedido de <?=$data['razon_social'];?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form class="form theme-form" id="formPedido" role="form" method="post" action="nuevoPedido.php?id_cliente=<?=$id?>">
                  <div class="modal-body">
                    <div class="row">
                      <div class="form-group col-4">
                        <label for="fecha_pedido">Fecha</label>
                        <input name="fecha_pedido" id="fecha_pedido" type="date" class="form-control multiselect" value="<?=$hoy?>" required>
                      </div>
                      <div class="form-group col-4">
                        <label for="campana_pedido">Campaña</label>
                        <select name="campana_pedido" id="campana_pedido" style="width: 100%;" required class="js-example-basic-single"><?php
                          // Generar las opciones del select
                          for ($i = $anio_inicial; $i <= $anio_final; $i++) {
                            // Si el año es el actual, marcarlo como seleccionado por defecto
                            $selected = ($i == $anio_actual) ? "selected" : "";
                            echo "<option value='$i' $selected>$i</option>";
                          }?>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <label for="sucursal_pedido">Sucursal</label>
                        <select name="sucursal_pedido" id="sucursal_pedido" style="width: 100%;" required class="js-example-basic-single"><?php
                          $pdo = Database::connect();
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                          $sqlZon = "SELECT id_sucursal,s.nombre FROM cliente_sucursal cs INNER JOIN sucursales s ON cs.id_sucursal=s.id WHERE id_cliente = ".$id;
                          $q = $pdo->prepare($sqlZon);
                          $q->execute();
                          $afe=$q->rowCount();
                          if($afe>1){
                            echo "<option value=''>Seleccione...</option>";
                          }
                          while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='".$fila['id_sucursal']."'";
                            echo ">".$fila['nombre']."</option>";
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-2">
                        <label for="observaciones_pedido" class="col-form-label">Observaciones:</label>
                      </div>
                      <div class="form-group col-10">
                        <textarea name="observaciones_pedido" id="observaciones_pedido" class="form-control"></textarea>
                      </div>
                    </div><?php
                      $pdo = Database::connect();
                      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                      $sqlZon = "SELECT id, servicio FROM servicios WHERE activo = 1";
                      $q = $pdo->prepare($sqlZon);
                      $q->execute();
                      $aServicio=[];
                      while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                        $aServicio[]=[
                          "id"=>$fila['id'],
                          "servicio"=>$fila['servicio'],
                        ];
                      }
                      
                      $sqlZon = "SELECT id, especie FROM especies WHERE activo = 1";
                      $q = $pdo->prepare($sqlZon);
                      $q->execute();
                      $aEspecies=[];
                      while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                        $aEspecies[]=[
                          "id"=>$fila['id'],
                          "especie"=>$fila['especie'],
                        ];
                      }

                      $sqlZon = "SELECT id, procedencia FROM procedencias_especies WHERE activo = 1";
                      $q = $pdo->prepare($sqlZon);
                      $q->execute();
                      $aProcedencias=[];
                      while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                        $aProcedencias[]=[
                          "id"=>$fila['id'],
                          "procedencia"=>$fila['procedencia'],
                        ];
                      }

                      $sqlZon = "SELECT id, material, id_procedencia, id_especie FROM cultivos WHERE activo = 1";
                      $q = $pdo->prepare($sqlZon);
                      $q->execute();
                      $aMateriales=[];
                      while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                        $aMateriales[]=[
                          "id"=>$fila['id'],
                          "material"=>$fila['material'],
                          "id_procedencia"=>$fila['id_procedencia'],
                          "id_especie"=>$fila['id_especie'],
                        ];
                      }

                      Database::disconnect();?>
                    <div class="row">
                      <div class="col-sm-12">
                        <table class="table-detalle table table-bordered table-hover text-center" id="tableCultivos" style="table-layout: fixed;">
                          <thead>
                            <tr>
                              <th style="width: 17%;">Servicio</th>
                              <th style="width: 20%;">Especie</th>
                              <th style="width: 20%;">Procedencia</th>
                              <th style="width: 20%;">Material</th>
                              <th style="width: 13%;">Cantidad</th>
                              <th style="width: 10%;">Eliminar</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr id='addr0' data-id="0" style="display: none;">
                              <td data-name="id_servicio" class="text-left">
                                <select name="id_servicio[]" id="id_servicio-0" class="js-example-basic-single id_servicio" style="width: 100%;" data-required="1">
                                  <option value="">Seleccione...</option><?php
                                  foreach ($aServicio as $servicio) {
                                    echo "<option value='".$servicio['id']."'";
                                    echo ">".$servicio['servicio']."</option>";
                                  }
                                  Database::disconnect();?>
                                </select>
                              </td>
                              <td data-name="id_especie" class="text-left">
                                <select name="id_especie[]" id="id_especie-0" class="js-example-basic-single id_especie" style="width: 100%;" data-required="1">
                                  <option value="">Seleccione...</option><?php
                                  foreach ($aEspecies as $especie) {
                                    echo "<option value='".$especie['id']."'";
                                    echo ">".$especie['especie']."</option>";
                                  }
                                  Database::disconnect();?>
                                </select>
                              </td>
                              <td data-name="id_procedencia" class="text-left">
                                <select name="id_procedencia[]" id="id_procedencia-0" class="js-example-basic-single id_procedencia" style="width: 100%;" data-required="0" disabled>
                                  <option value="">Seleccione una especie...</option>
                                </select>
                              </td>
                              <td data-name="id_material" class="text-left">
                                <select name="id_material[]" id="id_material-0" class="js-example-basic-single id_material" style="width: 100%;" data-required="0" disabled>
                                  <option value="">Seleccione una procedencia...</option>
                                </select>
                              </td>
                              <td data-name="cantidad">
                                <input type="number" name="cantidad[]" id="cantidad-0" class="form-control cantidad" placeholder="Cantidad" min="1" data-required="1" disabled>
                              </td>
                              <td data-name="eliminar">
                                <span name="eliminar[]" title="Eliminar" class="btn btn-sm row-remove text-center" onClick="eliminarFila(this);">
                                  <img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar">
                                </span>
                              </td>
                            </tr>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="5" align='right'>
                                <input type="button" class="btn btn-dark" id="addRowCultivos" value="Agregar Cultivo">
                              </td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <div class="col-12 text-center">
                      <button class="btn btn-primary" type="submit">Crear</button>
                      <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- FIN MODAL PARA NUEVO PEDIDO -->

          <!-- MODAL PARA NUEVO DESPACHO -->
          <div class="modal fade" id="nuevoDespacho" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1000000000000" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="max-width: 1200px;">
              <div class="modal-content">
                <div class="modal-header">
                  <h5>Nuevo Despacho de <?=$data['razon_social'];?> Pedido N° <span class="idPedido"></span></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form class="form theme-form" role="form" method="post" action="nuevoDespacho.php?id_cliente=<?=$id?>">
                  <input name="id_pedido_despacho" id="id_pedido_despacho" type="hidden">
                  <div class="modal-body">
                    <!-- <div class="row">
                      <div class="form-group col-4">
                        <label for="fecha_despacho">Fecha</label>
                        <input name="fecha_despacho" id="fecha_despacho" type="date" class="form-control multiselect" value="<?=$hoy?>" required>
                      </div>
                      <div class="form-group col-4">
                        <label for="campana_despacho">Campaña</label><br>
                        <select name="campana_despacho" id="campana_despacho" style="width: 100%;" required class="js-example-basic-single"><?php
                        // data-style="multiselect" data-live-search="true"
                          // Generar las opciones del select
                          for ($i = $anio_inicial; $i <= $anio_final; $i++) {
                            // Si el año es el actual, marcarlo como seleccionado por defecto
                            $selected = ($i == $anio_actual) ? "selected" : "";
                            echo "<option value='$i' $selected>$i</option>";
                          }?>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <label for="id_cliente_retira">Razon social</label>
                        <select name="id_cliente_retira" id="id_cliente_retira" style="width: 100%;" required class="js-example-basic-single"><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, razon_social FROM clientes";
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>"><?=$row["razon_social"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-4">
                        <label>Transporte</label>
                        <select name="id_transporte" id="id_transporte" class="js-example-basic-single" required style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, razon_social FROM transportes";
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>"><?=$row["razon_social"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <label for="id_chofer">Chofer</label>
                        <select name="id_chofer" id="id_chofer" class="js-example-basic-single" required disabled style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, nombre_apellido, id_transporte FROM choferes";
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>" data-id-transporte="<?=$row["id_transporte"]?>"><?=$row["nombre_apellido"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <label for="id_vehiculo">Vehiculo</label>
                        <select name="id_vehiculo" id="id_vehiculo" class="js-example-basic-single" required disabled style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, descripcion, patente, patente2, id_transporte FROM vehiculos";
                          foreach ($pdo->query($sql) as $row) {
                            $patente=$row["patente"];
                            if(!is_null($row["patente2"])){
                              $patente.=" - ".$row["patente2"];
                            }
                            $mostrar=$row["descripcion"]." (".$patente.")"?>
                            <option value="<?=$row["id"]?>" data-id-transporte="<?=$row["id_transporte"]?>"><?=$mostrar?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-4">
                        <label>Lote</label>
                        <select name="id_lote" id="id_lote" class="js-example-basic-single" style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT l.id, nombre, direccion, localidad, provincia FROM lotes l INNER JOIN localidades l2 ON l.id_localidad=l2.id INNER JOIN provincias p ON l2.id_provincia=p.id WHERE l.id_cliente=".$id;
                          foreach ($pdo->query($sql) as $row) {
                            $mostrar=$row["nombre"]." (".$row["direccion"]." ".$row["localidad"]." ".$row["provincia"].")"?>
                            <option value="<?=$row["id"]?>"><?=$mostrar?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <label for="id_plantador">Plantador</label>
                        <select name="id_plantador" id="id_plantador" class="js-example-basic-single" style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, nombre FROM plantadores WHERE id_cliente=".$id;
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>"><?=$row["nombre"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <label for="id_vehiculo">Patente 2</label>
                        <input type="text" name="patente2" id="patente2" class="form-control" style="width: 100%;">
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-4">
                        <label for="id_provincia">Provincia</label>
                        <select name="id_provincia" id="id_provincia" class="js-example-basic-single" style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, provincia FROM provincias";
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>"><?=$row["provincia"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <label for="id_localidad">Localidad</label>
                        <select name="id_localidad" id="id_localidad" class="js-example-basic-single" style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, localidad, id_provincia FROM localidades";
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>" data-id-provincia="<?=$row["id_provincia"]?>"><?=$row["localidad"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <label for="id_vehiculo">Lugar de entrega</label>
                        <input type="text" name="lugar_entrega" id="lugar_entrega" class="form-control" style="width: 100%;">
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-2">
                        <label for="observaciones_pedido" class="col-form-label">Observaciones:</label>
                      </div>
                      <div class="form-group col-10">
                        <textarea name="observaciones_pedido" id="observaciones_pedido" class="form-control"></textarea>
                      </div>
                    </div> -->
                    <div class="row">
                      <div class="form-group col-4">
                        <label for="fecha_despacho">Fecha</label>
                        <input name="fecha_despacho" id="fecha_despacho" type="date" class="form-control multiselect" value="<?=$hoy?>" required>
                      </div>
                      <div class="form-group col-4">
                        <label for="campana_despacho">Campaña</label><br>
                        <select name="campana_despacho" id="campana_despacho" style="width: 100%;" required class="js-example-basic-single"><?php
                        // data-style="multiselect" data-live-search="true"
                          // Generar las opciones del select
                          for ($i = $anio_inicial; $i <= $anio_final; $i++) {
                            // Si el año es el actual, marcarlo como seleccionado por defecto
                            $selected = ($i == $anio_actual) ? "selected" : "";
                            echo "<option value='$i' $selected>$i</option>";
                          }?>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <label for="id_cliente_retira">Razon social</label>
                        <select name="id_cliente_retira" id="id_cliente_retira" style="width: 100%;" required class="js-example-basic-single">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, razon_social FROM clientes";
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>"><?=$row["razon_social"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-3">
                        <label>Transporte</label>
                        <select name="id_transporte" id="id_transporte" class="js-example-basic-single" required style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, razon_social FROM transportes";
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>"><?=$row["razon_social"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-3">
                        <label for="id_chofer">Chofer</label>
                        <select name="id_chofer" id="id_chofer" class="js-example-basic-single" required disabled style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, nombre_apellido, id_transporte FROM choferes";
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>" data-id-transporte="<?=$row["id_transporte"]?>"><?=$row["nombre_apellido"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-3">
                        <label for="id_vehiculo">Vehiculo</label>
                        <select name="id_vehiculo" id="id_vehiculo" class="js-example-basic-single" required disabled style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, descripcion, patente, patente2, id_transporte FROM vehiculos";
                          foreach ($pdo->query($sql) as $row) {
                            $patente=$row["patente"];
                            if(!is_null($row["patente2"])){
                              $patente.=" - ".$row["patente2"];
                            }
                            $mostrar=$row["descripcion"]." (".$patente.")"?>
                            <option value="<?=$row["id"]?>" data-id-transporte="<?=$row["id_transporte"]?>" data-patente2="<?=$row["patente2"]?>"><?=$mostrar?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-3">
                        <label for="id_vehiculo">Patente 2</label>
                        <input type="text" name="patente2" id="patente2" class="form-control" style="width: 100%;">
                      </div>
                    </div>
                    <div class="row">
                      <!-- <div class="form-group col-3">
                        <label for="id_provincia">Provincia</label>
                        <select name="id_provincia" id="id_provincia" class="js-example-basic-single" style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          /*$pdo = Database::connect();
                          $sql = " SELECT id, provincia FROM provincias";
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>"><?=$row["provincia"]?></option><?php
                          }
                          Database::disconnect();*/?>
                        </select>
                      </div> -->
                      <div class="form-group col-3">
                        <label for="id_localidad">Localidad</label>
                        <select name="id_localidad" id="id_localidad" class="js-example-basic-single" style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT l.id, l.localidad, p.provincia FROM localidades l INNER JOIN provincias p ON l.id_provincia=p.id";
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>"><?=$row["localidad"]." - ".$row["provincia"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-3">
                        <label>Lote</label>
                        <select name="id_lote" id="id_lote" class="js-example-basic-single" style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT l.id, nombre, direccion, localidad, provincia FROM lotes l INNER JOIN localidades l2 ON l.id_localidad=l2.id INNER JOIN provincias p ON l2.id_provincia=p.id WHERE l.id_cliente=".$id;
                          foreach ($pdo->query($sql) as $row) {
                            $mostrar=$row["nombre"]." (".$row["direccion"]." ".$row["localidad"]." ".$row["provincia"].")"?>
                            <option value="<?=$row["id"]?>"><?=$mostrar?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                      <div class="form-group col-3">
                        <label for="id_vehiculo">Lugar de entrega</label>
                        <input type="text" name="lugar_entrega" id="lugar_entrega" class="form-control" style="width: 100%;">
                      </div>
                      <div class="form-group col-3">
                        <label for="id_plantador">Plantador</label>
                        <select name="id_plantador" id="id_plantador" class="js-example-basic-single" style="width: 100%;">
                          <option value="">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql = " SELECT id, nombre FROM plantadores WHERE id_cliente=".$id;
                          foreach ($pdo->query($sql) as $row) {?>
                            <option value="<?=$row["id"]?>"><?=$row["nombre"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-2">
                        <label for="observaciones_despacho" class="col-form-label">Observaciones:</label>
                      </div>
                      <div class="form-group col-10">
                        <textarea name="observaciones_despacho" id="observaciones_despacho" class="form-control"></textarea>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group row">
                          <div class="col-sm-12">
                            <table class="table table-striped table-bordered" id="detallePedido" style="table-layout: fixed;">
                              <thead>
                                <tr>
                                  <th style="width: 12%;">Servicio</th>
                                  <th style="width: 20%;">Especie</th>
                                  <th style="width: 20%;">Procedencia</th>
                                  <th style="width: 20%;">Material</th>
                                  <th style="width: 14%;">Cantidad total pedido</th>
                                  <th style="width: 14%;">Despachar/Pendiente</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                            </table>
                            <div class="mensajeError" style="color: red; display: none;">Por favor, ingrese al menos una cantidad.</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <div class="col-12 text-center">
                      <button class="btn btn-primary" type="submit">Crear</button>
                      <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- FIN MODAL PARA NUEVO DESPACHO -->

          <!-- MODAL PARA NUEVO PAGO -->
          <div class="modal fade" id="nuevoPago" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1000000000000" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5>Nuevo Pedido de <?=$data['razon_social'];?> Pedido N° <span class="idPedido"></span></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form class="form theme-form" role="form" method="post" action="nuevoPedido.php?id_cliente=<?=$id?>">
                  <input name="id_pedido_pago" id="id_pedido_pago" type="hidden">
                  <div class="modal-body">
                    <div class="row">
                      <div class="col">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Fecha</label>
                          <div class="col-sm-3"><input name="fecha_pago" id="fecha_pago" type="date" class="form-control" value="<?=$hoy?>" required></div>
                          <label class="col-sm-3 col-form-label">Campaña</label>
                          <div class="col-sm-3">
                            <select name="campana_pago" id="campana_pago" class="form-control"><?php
                              // Generar las opciones del select
                              for ($i = $anio_inicial; $i <= $anio_final; $i++) {
                                // Si el año es el actual, marcarlo como seleccionado por defecto
                                $selected = ($i == $anio_actual) ? "selected" : "";
                                echo "<option value='$i' $selected>$i</option>";
                              }?>
                            </select>
                          </div>
                        </div>
                        <div class="form-group row">
                          <!-- <label class="col-sm-12 col-form-label">Cultivos</label> -->
                          <div class="col-sm-12">
                            <table class="table table-striped">
                              <tr>
                                <th>Cultivo</th>
                                <th>Cantidad</th>
                              </tr><?php
                              //include 'database.php';
                              /*$pdo = Database::connect();
                              $sql = " SELECT id, nombre FROM cultivos";
                              foreach ($pdo->query($sql) as $row) {?>
                                <tr>
                                  <td><?=$row["nombre"]?></td>
                                  <td>
                                    <input type="hidden" name="id_cultivo[]" value="<?=$row["id"]?>">
                                    <input type="number" name="cantidad[]" class="form-control" placeholder="Cantidad">
                                  </td>
                                </tr><?php
                              }
                              Database::disconnect();*/?>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <div class="col-sm-9 offset-sm-3">
                      <button class="btn btn-primary" type="submit">Crear</button>
                      <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- FIN MODAL PARA NUEVO PAGO -->

          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
		    <?php include("footer.php"); ?>
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

    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>
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

        $('#formPedido').submit(function(event) {
          //event.preventDefault(); // Evitar el envío del formulario
          $(".id_especie").attr("disabled",false)
          //console.log($(".id_especie"));
          $(".id_procedencia").attr("disabled",false)
          //console.log($(".id_procedencia"));
          $(".id_material").attr("disabled",false)
          //console.log($(".id_material"));
          $(".cantidad").attr("disabled",false)
          //console.log($(".id_material"));
        })

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

        $("#addRowCultivos").on('click', function(event) {
          event.preventDefault();
          addRowCultivos();
        }).click();

        function addRowCultivos(){
          //alert("hola");
          var newid = 0;
          var primero="";
          var ultimoRegistro=0;
          $.each($("#tableCultivos tr"), function() {
            if (parseInt($(this).data("id")) > newid) {
              newid = parseInt($(this).data("id"));
            }
          });
          //debugger;
          newid++;
          //console.log(newid);
          var tr = $("<tr></tr>", {
            "id": "addr"+newid,
            "data-id": newid
          });
          //console.log(newid);
          var p=0;
          $.each($("#tableCultivos tbody tr:nth(0) td"),function(){//loop through each td and create new elements with name of newid
            var cur_td = $(this); 
            var children = cur_td.children();
            if($(this).data("name")!=undefined){// add new td and element if it has a name
              var td = $("<td></td>", {
                "data-name": $(cur_td).data("name"),
                "class": this.className
              });
              var c = $(cur_td).find($(children[0]).prop('tagName')).clone();//.val("")
              
              var id=$(c).attr("id");
              if($(c).data("required")==1){
                $(c).attr("required",true);
              }
              ultimoRegistro=id;
              if(id!=undefined){
                //console.log("id1: ");
                //console.log(id);
                id=id.split("-");
                c.attr("id", id[0]+"-"+newid);//modificamos el id de cada input
                if(p==0){
                  primero=c;
                  p++;
                }
              }
              c.appendTo($(td));
              td.appendTo($(tr));
              
            }else {
              //console.log("<td></td>",{'text':$('#tab_logic tr').length})
              var td = $("<td></td>", {
                'text': $('#tableCultivos tr').length
              }).appendTo($(tr));
            }
          });
          //console.log($(tr).find($("input[name='detalledireccion[]']")));
          //console.log(tr);//.find($("input"))
          $(tr).appendTo($('#tableCultivos'));// add the new row
          if(newid>0){
            //primero.focus();
            let sel5=$("#id_servicio-"+newid)
            sel5.select2();//llamamos para inicializar select2
            //lo destruimos para que elimine las clases que arrastra de la clonacion y volvemos a inicializar
            sel5.select2('destroy');
            sel5.select2();
            sel5.css('width', '100%');

            let sel2=$("#id_especie-"+newid)
            sel2.select2();//llamamos para inicializar select2
            //lo destruimos para que elimine las clases que arrastra de la clonacion y volvemos a inicializar
            sel2.select2('destroy');
            sel2.select2();
            sel2.css('width', '100%');

            let sel3=$("#id_procedencia-"+newid)
            sel3.select2();//llamamos para inicializar select2
            //lo destruimos para que elimine las clases que arrastra de la clonacion y volvemos a inicializar
            sel3.select2('destroy');
            sel3.select2();
            sel3.css('width', '100%');

            let sel4=$("#id_material-"+newid)
            sel4.select2();//llamamos para inicializar select2
            //lo destruimos para que elimine las clases que arrastra de la clonacion y volvemos a inicializar
            sel4.select2('destroy');
            sel4.select2();
            sel4.css('width', '100%');
            
          }
          return tr.attr("id");
        }

        $("#id_transporte").on("change",function(){
          let id_transporte=$(this).val();
          getChoferes(id_transporte);
          getVehiculos(id_transporte);
        })

        function getChoferes(id_transporte){
          let selectChofer=$("#id_chofer")
          selectChofer.val("").change()
          if(id_transporte>0){
            selectChofer.attr("disabled",false)
            selectChofer.find("option").each(function(){
              $(this).attr("disabled",true);
              if(this.value=="" || this.dataset.idTransporte==id_transporte){
                $(this).attr("disabled",false);
              }
              
            })
            
            // Destruir y volver a aplicar Select2
            selectChofer.select2('destroy');
            selectChofer.select2();
          }else{
            selectChofer.attr("disabled",true)
            selectChofer.val("").change()
          }
        }

        function getVehiculos(id_transporte){
          let selectVehiculo=$("#id_vehiculo")
          selectVehiculo.val("").change()
          if(id_transporte>0){
            selectVehiculo.attr("disabled",false)
            selectVehiculo.find("option").each(function(){
              $(this).attr("disabled",true);
              if(this.value=="" || this.dataset.idTransporte==id_transporte){
                $(this).attr("disabled",false);
              }
              
            })
            
            // Destruir y volver a aplicar Select2
            selectVehiculo.select2('destroy');
            selectVehiculo.select2();
          }else{
            selectVehiculo.attr("disabled",true)
          }
          resetPatente2()
        }

        function resetPatente2(){
          var patente2 = $("#id_vehiculo").find('option:selected').data('patente2');
          $("#patente2").val(patente2)
          console.log(patente2)
        }
        $("#id_vehiculo").on("change",function(){
          resetPatente2()
        })

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

        function getDetallePedido(id_pedido){
          let table=$('#detallePedido')
          table.DataTable().destroy();
          table.DataTable({
            //dom: 'rtip',
            //serverSide: true,
            processing: true,
            ajax:{url:'ajaxGetDetallePedidoNuevoDespacho.php?id_pedido='+id_pedido,dataSrc:""},
            stateSave: true,
            //responsive: true,

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
              {render: function(data, type, row, meta) {
                return `<input type='hidden' name="id_servicio[]" value='${row.id_servicio}'>`+row.servicio;
              }},
              {render: function(data, type, row, meta) {
                return `<input type='hidden' name="id_especie[]" class='id_especie' value='${row.id_especie}'>`+row.especie;
              }},
              {render: function(data, type, row, meta) {
                let id_procedencia=row.id_procedencia;
                let clase;
                if(id_procedencia>0){
                  return `<input type='hidden' name="id_procedencia[]" value='${row.id_procedencia}'>`+row.procedencia;
                }else{
                  let selectProcedencia=`<select name="id_procedencia[]" class="js-example-basic-single id_procedencia" required style="width:100%">
                    <option value="">Seleccione...</option>`;
                    row.aProcedencias.forEach((procedencia)=>{
                      selectProcedencia+=`<option value="${procedencia.id}">${procedencia.procedencia}</option>`;
                    });
                    selectProcedencia+=`</select>`;

                  return selectProcedencia;
                }
              }},
              {render: function(data, type, row, meta) {
                let id_material=row.id_material;
                let clase;
                if(id_material>0){
                  return `<input type='hidden' name="id_material[]" value='${row.id_material}'>`+row.material;
                }else{
                  let disabled="disabled";
                  console.log(row.id_procedencia);
                  if(row.id_procedencia>0){
                    disabled=""
                  }
                  let selectMaterial=`<select name="id_material[]" class="js-example-basic-single id_material" required ${disabled} style="width:100%">
                    <option value="">Seleccione una procedencia...</option>`;
                    row.aMateriales.forEach((material)=>{
                      selectMaterial+=`<option value="${material.id}">${material.material}</option>`;
                    });
                    selectMaterial+=`</select>`;
                  return selectMaterial;
                }
              }},
              {render: function(data, type, row, meta) {
                return Intl.NumberFormat("de-DE").format(row.cantidad_plantines);
              }},
              {render: function(data, type, row, meta) {
                let cantidad_plantines=parseInt(row.cantidad_plantines);
                let plantines_retirados=parseInt(row.plantines_retirados);
                let pendiente=cantidad_plantines-plantines_retirados
                return `<div class="input-group">
                  <input type="number" name="cantidad_despachar[]" class="form-control" required>
                  <div class="input-group-append">
                    <span class="input-group-text">/ ${Intl.NumberFormat("de-DE").format(pendiente)}</span>
                  </div>
                </div>`;
              }},
            ],
            initComplete: function(settings, json){
              $("#detallePedido").find(".id_procedencia").select2()
              $("#detallePedido").find(".id_material").select2()
            },
            "columnDefs": [
              { "className": "dt-body-right align-middle", "targets": [4] },
              { "className": "align-middle", "targets": "_all" },
            ],
          })
        }

        $(".nav_id_cultivo").on("click",function(){
          let id_cultivo=this.dataset.id_cultivo
          console.log(id_cultivo);

          getCtaCte(id_cultivo);
        })

        function getCtaCte(id_cultivo){
          let desde=$("#desde").val();
          let hasta=$("#hasta").val();
          let tipo_comprobante=$("#tipo_comprobante").val();
          let id_cliente=<?=$id?>;

          let table=$('.tablas_cta_cte')
          table.DataTable().destroy();
          table.DataTable({
            //dom: 'rtip',
            //serverSide: true,
            processing: true,
            ajax:{url:'ajaxGetCtaCte.php?desde='+desde+'&hasta='+hasta+'&tipo_comprobante='+tipo_comprobante+'&id_cliente='+id_cliente+'&id_cultivo='+id_cultivo,dataSrc:""},
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
                //<span class="badge '+clase+'">'++'</span>
                //<a class="dropdown-item" href="#">Action</a>
                //<a class="dropdown-item" href="#">Another action</a>
                //<a class="dropdown-item" href="#">Something else here</a>
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
                        <a href="#" class="dropdown-item contenedores btnNuevaDevolucionContenedores" data-id-pedido="${row.id_pedido}">
                          <i class="fa fa-plus"></i> Devolucion de contenedores
                        </a>
                        <a href="#" class="dropdown-item plantines btnNuevaDevolucionPlantines" data-id-pedido="${row.id_pedido}">
                          <i class="fa fa-plus"></i> Devolucion de plantines
                        </a>
                      </div>
                    </div>
                  `;
                }else{
                  return '<button class="btn btn-sm '+clase+'">'+tipo_comprobante+' N° '+row.id_pedido+'</button>'
                }
              }},
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
              {
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
              },
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
              $(api.column(3).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalPedido));
              $(api.column(4).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalDespacho));
              $(api.column(5).footer()).html(Intl.NumberFormat("de-DE").format(ultimosSaldos.saldoDespacho));
              $(api.column(6).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalPago));
              $(api.column(7).footer()).html(Intl.NumberFormat("de-DE").format(ultimosSaldos.saldoPago));
              $(api.column(8).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalPago));
              $(api.column(9).footer()).html(Intl.NumberFormat("de-DE").format(ultimosSaldos.saldoPago));

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