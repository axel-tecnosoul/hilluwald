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
$sql = "SELECT id, razon_social, cuit, cond_fiscal, direccion, email, telefono, fecha_alta, activo FROM clientes WHERE id = ? ";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

$hoy=date("Y-m-d");
// Obtener el año actual
$anio_actual = date("Y");
// Calcular los años límite
$anio_inicial = $anio_actual - 2;
$anio_final = $anio_actual + 2;

$sql = " SELECT c.id, c.nombre, c.nombre_corto, c.icono, c.color FROM cultivos c INNER JOIN pedidos_detalle pd ON pd.id_cultivo=c.id INNER JOIN pedidos p ON pd.id_pedido=p.id WHERE p.anulado=0 AND p.id_cliente=".$id." GROUP BY c.id";
//$sql = " SELECT c.id, c.nombre FROM cultivos c";
$aCultivosPedidos=[];
foreach ($pdo->query($sql) as $row) {
  $nombre=$row["nombre_corto"];
  if(empty($nombre)){
    $nombre=$row["nombre"];
  }
  $aCultivosPedidos[]=[
    "id_cultivo"=>$row["id"],
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
    .retiros{
      background-color: #6c757d!important;
      color: #fff!important;
    }
    .pagos{
      background-color: #007bff!important;
      color: #fff!important;
    }
    .bandejas {
      background-color: #ffc107!important;
      color: #000!important;
    }
    .tablas_cta_cte th, .tablas_cta_cte td {
      border: 1px solid #dee2e6;
    }
    .borderRetiroLeft{
      /*border-left: solid 1px #6c757d !important;*/
      /*background-color: #6c757d !important;*/
      /*background-color: rgb(108 117 125)!important;*/
    }
    .borderRetiroRight{
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
                      <li><a  target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?php echo date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
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
                    <h5><?php echo $data['razon_social']; ?></h5>
                    <div class="row mt-3">
                      <div class="col-12">
                        
                        <button class="btn pedidos" style="text-transform: none;" title="Nuevo Pedido" data-toggle="modal" data-target="#nuevoPedido"><i class="fa fa-plus"></i> Pedido</button><!-- nuevoPedido.php -->
                        
                        <button class="btn retiros" style="text-transform: none;" title="Nuevo Retiro" data-toggle="modal" data-target="#nuevoRetiro"><i class="fa fa-plus"></i> Retiro</button><!-- nuevoRetiro.php -->
                        
                        <button class="btn pagos" style="text-transform: none;" title="Nuevo Pago" data-toggle="modal" data-target="#nuevoPago"><i class="fa fa-plus"></i> Pago</button><!-- nuevoPago.php -->

                        <button class="btn bandejas" style="text-transform: none;" title="Nueva devolucion de bandejas" data-toggle="modal" data-target="#nuevaDevolucionBandejas"><i class="fa fa-plus"></i> Bandejas</button><!-- nuevoPago.php -->

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
                          <div class="col-sm-9"><input name="razon_social" type="text" maxlength="99" class="form-control" value="<?php echo $data['razon_social']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">CUIT</label>
                          <div class="col-sm-9"><input name="cuit" type="text" maxlength="99" class="form-control" value="<?php echo $data['cuit']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Condicion fiscal</label>
                          <div class="col-sm-9"><input name="cond_fiscal" type="text" maxlength="99" class="form-control" value="<?php echo $data['cond_fiscal']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Direccion</label>
                          <div class="col-sm-9"><input name="direccion" type="text" maxlength="99" class="form-control" value="<?php echo $data['direccion']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">E-Mail</label>
                          <div class="col-sm-9"><input name="email" type="email" maxlength="99" class="form-control" value="<?php echo $data['email']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Teléfono</label>
                          <div class="col-sm-9"><input name="telefono" type="text" maxlength="99" class="form-control" value="<?php echo $data['telefono']; ?>" readonly="readonly"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Fecha de alta</label>
                          <div class="col-sm-9"><input name="fecha_alta" type="text" maxlength="99" class="form-control" value="<?php echo $data['fecha_alta']; ?>" readonly="readonly"></div>
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
                                <option value="Retiro">Retiro</option>
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
                              <th style="text-align: center;vertical-align: middle;"class="borderRetiroLeft borderRetiroRight" colspan="2">Retiros</th>
                              <th style="text-align: center;vertical-align: middle;" class="borderPagoLeft borderPagoRight" colspan="2">Pagos</th>
                              <th style="text-align: center;vertical-align: middle;" class="borderPagoLeft borderPagoRight" colspan="2">Bandejas</th>
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

                          <!-- <tbody>
                            <tr>
                              <td>1 02 2024</td>
                              <td>Pedido N° 1</td>
                              <td>2024</td>
                              <td align="right">1.000</td>
                              <td align="right"></td>
                              <td align="right">1.000</td>
                              <td align="right"></td>
                              <td align="right">1.000</td>
                            </tr>
                            <tr>
                              <td>1 02 2024</td>
                              <td>Retiro N° 1</td>
                              <td>2024</td>
                              <td align="right"></td>
                              <td align="right">200</td>
                              <td align="right">800</td>
                              <td align="right"></td>
                              <td align="right">1000</td>
                            </tr>
                            <tr>
                              <td>5 02 2024</td>
                              <td>Pago N° 1</td>
                              <td>2024</td>
                              <td align="right"></td>
                              <td align="right"></td>
                              <td align="right">800</td>
                              <td align="right">500</td>
                              <td align="right">500</td>
                            </tr>
                            <tr>
                              <td>6 02 2024</td>
                              <td>Pedido N° 2</td>
                              <td>2024</td>
                              <td align="right">500</td>
                              <td align="right"></td>
                              <td align="right">1.300</td>
                              <td align="right"></td>
                              <td align="right">1.000</td>
                            </tr>
                            <tr>
                              <td>6 02 2024</td>
                              <td>Retiro N° 2</td>
                              <td>2024</td>
                              <td align="right"></td>
                              <td align="right">200</td>
                              <td align="right">1.100</td>
                              <td align="right"></td>
                              <td align="right">1.000</td>
                            </tr>
                            <tr>
                              <td>7 02 2024</td>
                              <td>Retiro N° 3</td>
                              <td>2024</td>
                              <td align="right"></td>
                              <td align="right">200</td>
                              <td align="right">900</td>
                              <td align="right"></td>
                              <td align="right">1.000</td>
                            </tr>
                            <tr>
                              <td><strong>Totales</strong></td>
                              <td></td>
                              <td></td>
                              <td align="right"><strong>1.500</strong></td>
                              <td align="right"><strong>600</strong></td>
                              <td><strong></strong></td>
                              <td align="right"><strong>500</strong></td>
                              <td><strong></strong></td>
                            </tr>
                          </tbody> -->
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
          <div class="modal fade" id="nuevoPedido" role="dialog" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="card">
                  <div class="card-header">
                    <h5>Nuevo Pedido de <?php echo $data['razon_social']; ?></h5>
                  </div>
				          <form class="form theme-form formulario" role="form" method="post" action="nuevoPedido.php?id_cliente=<?=$id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="form-group col-6">
                          <label for="fecha_pedido">Fecha</label>
                          <input name="fecha_pedido" id="fecha_pedido" type="date" class="form-control multiselect" value="<?=$hoy?>" required>
                        </div>
                        <div class="form-group col-6">
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
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <table class="table table-striped table-bordered">
                            <tr>
                              <th>Cultivo</th>
                              <th>Cantidad</th>
                            </tr><?php
                            $pdo = Database::connect();
                            $sql = " SELECT id, nombre FROM cultivos";
                            foreach ($pdo->query($sql) as $row) {?>
                              <tr>
                                <td><?=$row["nombre"]?></td>
                                <td>
                                  <input type="hidden" name="id_cultivo[]" value="<?=$row["id"]?>">
                                  <input type="number" name="cantidad[]" class="form-control cantidad" placeholder="Cantidad">
                                </td>
                              </tr><?php
                            }
                            Database::disconnect();?>
                          </table>
                          <div class="mensajeError" style="color: red; display: none;">Por favor, ingrese al menos una cantidad.</div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
						            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- FIN MODAL PARA NUEVO PEDIDO -->

          <!-- MODAL PARA NUEVO RETIRO -->
          <div class="modal fade" id="nuevoRetiro" role="dialog" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="card">
                  <div class="card-header">
                    <h5>Nuevo Retiro de <?php echo $data['razon_social']; ?></h5>
                  </div>
				          <form class="form theme-form formulario" role="form" method="post" action="nuevoRetiro.php?id_cliente=<?=$id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="form-group col-4">
                          <label for="fecha_retiro">Fecha</label>
                          <input name="fecha_retiro" id="fecha_retiro" type="date" class="form-control multiselect" value="<?=$hoy?>" required>
                        </div>
                        <div class="form-group col-4">
                          <label for="campana_retiro">Campaña</label><br>
                          <select name="campana_retiro" id="campana_retiro" style="width: 100%;" required class="js-example-basic-single"><?php
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
                          <select name="id_lote" id="id_lote" class="js-example-basic-single" required style="width: 100%;">
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
                          <select name="id_plantador" id="id_plantador" class="js-example-basic-single" required style="width: 100%;">
                            <option value="">- Seleccione -</option><?php
                            /*$pdo = Database::connect();
                            $sql = " SELECT id, nombre_apellido, id_transporte FROM plantadores";
                            foreach ($pdo->query($sql) as $row) {?>
                              <option value="<?=$row["id"]?>" data-id-transporte="<?=$row["id_transporte"]?>"><?=$row["nombre_apellido"]?></option><?php
                            }
                            Database::disconnect();*/?>
                          </select>
                        </div>
                        <div class="form-group col-4">
                          <label for="id_vehiculo">Patente 2</label>
                          <input type="text" name="patente2" id="patente2" class="form-control" style="width: 100%;">
                            
                          </input>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <!-- <label class="col-sm-12 col-form-label">Cultivos</label> -->
                            <div class="col-sm-12">
                              <table class="table table-striped table-bordered">
                                <tr>
                                  <th align="center">Cultivo</th>
                                  <th align="center">Cantidad</th>
                                </tr><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, nombre FROM cultivos";
                                foreach ($pdo->query($sql) as $row) {?>
                                  <tr>
                                    <td><?=$row["nombre"]?></td>
                                    <td>
                                      <input type="hidden" name="id_cultivo[]" value="<?=$row["id"]?>">
                                      <input type="number" name="cantidad[]" class="form-control cantidad" placeholder="Cantidad">
                                    </td>
                                  </tr><?php
                                }
                                Database::disconnect();?>
                              </table>
                              <div class="mensajeError" style="color: red; display: none;">Por favor, ingrese al menos una cantidad.</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
						            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- FIN MODAL PARA NUEVO RETIRO -->

          <!-- MODAL PARA NUEVO PAGO -->
          <div class="modal fade" id="nuevoPago" role="dialog" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="card">
                  <div class="card-header">
                    <h5>Nuevo Pedido de <?php echo $data['razon_social']; ?></h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="nuevoPedido.php?id_cliente=<?=$id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-3"><input name="fecha" type="date" class="form-control" value="<?=$hoy?>" required></div>
                            <label class="col-sm-3 col-form-label">Campaña</label>
                            <div class="col-sm-3">
                              <select name="campana" class="form-control"><?php
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
                                $pdo = Database::connect();
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
                                Database::disconnect();?>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
						            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                      </div>
                    </div>
                  </form>
                </div>
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

        $("#id_transporte").on("change",function(){
          let id_transporte=$(this).val();
          getChoferes(id_transporte);
          getVehiculos(id_transporte);
        })

        function getChoferes(id_transporte){
          let selectChofer=$("#id_chofer")
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
          }
        }

        function getVehiculos(id_transporte){
          let selectVehiculo=$("#id_vehiculo")
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
        }

        /*$('.tablas_cta_cte').DataTable({
          stateSave: true,
          responsive: true,
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
          }}
        });*/

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
                if(tipo_comprobante=="Retiro"){
                  clase="retiros";
                }
                if(tipo_comprobante=="Pago"){
                  clase="pagos";
                }
                return '<span class="badge '+clase+'">'+tipo_comprobante+' N° '+row.id_pedido+'</span>';
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
                  return Intl.NumberFormat("de-DE").format(row.cantidad_retiro)
                },
                className: "dt-body-right borderRetiroLeft",
              },
              {
                render: function(data, type, row, meta) {
                  return Intl.NumberFormat("de-DE").format(row.saldo_retiro)
                },
                className: "dt-body-right borderRetiroRight",
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
              console.log(totales);
              const ultimosSaldos = obtenerUltimosSaldos(json);
              console.log(ultimosSaldos);
              // Update footer
              //$(api.column(3).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_cantidad));
              $(api.column(3).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalPedido));
              $(api.column(4).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalRetiro));
              $(api.column(5).footer()).html(Intl.NumberFormat("de-DE").format(ultimosSaldos.saldoRetiro));
              $(api.column(6).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalPago));
              $(api.column(7).footer()).html(Intl.NumberFormat("de-DE").format(ultimosSaldos.saldoPago));
              $(api.column(8).footer()).html(Intl.NumberFormat("de-DE").format(totales.totalPago));
              $(api.column(9).footer()).html(Intl.NumberFormat("de-DE").format(ultimosSaldos.saldoPago));

              $('[title]').tooltip();
            }
          })
        }

        // Función para sumar las cantidades de pedidos, retiros y pagos
        function sumarCantidades(datos) {
            let totalPedido = 0;
            let totalRetiro = 0;
            let totalPago = 0;

            datos.forEach(item => {
                if (item.cantidad_pedido !== "") {
                    totalPedido += parseInt(item.cantidad_pedido);
                }
                if (item.cantidad_retiro !== "") {
                    totalRetiro += parseInt(item.cantidad_retiro);
                }
                if (item.cantidad_pago !== "") {
                    totalPago += parseInt(item.cantidad_pago);
                }
            });

            return {
                totalPedido,
                totalRetiro,
                totalPago
            };
        }

        // Función para obtener el último saldo de retiro y pago
        function obtenerUltimosSaldos(datos) {
            const ultimoRetiro = datos.filter(item => item.saldo_retiro !== "").pop();
            const ultimoPago = datos.filter(item => item.saldo_pago !== "").pop();

            return {
                saldoRetiro: ultimoRetiro ? ultimoRetiro.saldo_retiro : 0,
                saldoPago: ultimoPago ? ultimoPago.saldo_pago : 0
            };
        }

      });
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
  </body>
</html>