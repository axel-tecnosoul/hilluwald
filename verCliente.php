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

$sql = " SELECT c.id, c.nombre FROM cultivos c INNER JOIN pedidos_detalle pd ON pd.id_cultivo=c.id INNER JOIN pedidos p ON pd.id_pedido=p.id WHERE p.anulado=0 AND p.id_cliente=".$id." GROUP BY c.id";
//$sql = " SELECT c.id, c.nombre FROM cultivos c";
$aCultivosPedidos=[];
foreach ($pdo->query($sql) as $row) {
  $aCultivosPedidos[]=[
    "id_cultivo"=>$row["id"],
    "nombre"=>$row["nombre"],
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
                        <a href="nuevoPedido.php" class="btn pedidos" style="text-transform: none;" title="Nuevo Pedido" data-toggle="modal" data-target="#nuevoPedido"><i class="fa fa-plus"></i> Pedido</a>
                        <a href="nuevoRetiro.php" class="btn retiros" style="text-transform: none;" title="Nuevo Retiro"><i class="fa fa-plus"></i> Retiro</a>
                        <a href="nuevoPago.php" class="btn pagos" style="text-transform: none;" title="Nuevo Pago"><i class="fa fa-plus"></i> Pago</a>
                        
                      </div>
                    </div>
                  </div>

                  <!-- <i class="fa fa-pagelines" aria-hidden="true"></i>
                  <i class="fa fa-tree" aria-hidden="true"></i>
                  <i class="fa fa-leaf" aria-hidden="true"></i>
                  <i class="fa fa-envira" aria-hidden="true"></i>
                  
                  <label for="basic-color">Selecciona un color básico:</label>
                  <input type="color" id="basic-color" name="basic-color" list="basic-colors">
                  <datalist id="basic-colors">
                    <option value="#ff0000">Rojo</option>
                    <option value="#ff7f00">Naranja</option>
                    <option value="#964b00">Marrón Oscuro</option>
                    <option value="#a52a2a">Marrón Claro</option>
                    <option value="#ffff00">Amarillo</option>
                    <option value="#00ff00">Verde</option>
                    <option value="#006400">Verde Oscuro</option>
                    <option value="#0000ff">Azul</option>
                    <option value="#8a2be2">Violeta</option>
                    <option value="#00ffff">Celeste</option>
                  </datalist> -->

                  <div class="card-body">
                    <ul class="nav nav-tabs border-tab" id="pills-tab" role="tablist" style="margin-top: -20px;">

                      <li class="nav-item"><a class="nav-link active" id="pills-contacto-tab" data-toggle="pill" href="#pills-contacto" role="tab" aria-controls="pills-contacto" aria-selected="true"><i class="icofont icofont-ui-user"></i>Datos</a></li><?php
                      
                      foreach ($aCultivosPedidos as $cultivo_pedido) {?>
                        <li class="nav-item">
                          <a class="nav-link nav_id_cultivo" id="pills-cta_cte-tab" data-toggle="pill" href="#pills-cta_cte" role="tab" aria-controls="pills-cta_cte" aria-selected="false" data-id_cultivo="<?=$cultivo_pedido["id_cultivo"]?>"><i class="fa fa-pagelines"></i><?=$cultivo_pedido["nombre"]?></a>
                        </li><?php
                      }?>

                      <!-- <li class="nav-item"><a class="nav-link" id="pills-id_producto_yerba-tab" data-toggle="pill" href="#pills-id_producto_yerba" role="tab" aria-controls="pills-id_producto_yerba" aria-selected="false"><i class="fa fa-truck"></i></i>Yerba</a></li>

                      <li class="nav-item"><a class="nav-link" id="pills-id_producto_eucaliptus-tab" data-toggle="pill" href="#pills-id_producto_eucaliptus" role="tab" aria-controls="pills-id_producto_eucaliptus" aria-selected="false"><i class="fa fa-usd"></i>Eucaliptus</a></li>

                      <li class="nav-item"><a class="nav-link" id="pills-bandejas-tab" data-toggle="pill" href="#pills-bandejas" role="tab" aria-controls="pills-bandejas" aria-selected="false"><i class="fa fa-th"></i>Bandejas</a></li> -->

                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                      
                      <div class="tab-pane fade show active" id="pills-contacto" role="tabpanel" aria-labelledby="pills-contacto-tab">

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
                            <td class="border-0 p-1"><input type="date" id="desde" value="<?=date("Y-m-d")?>" class="form-control form-control-sm filtraTabla"></td>
                            <!-- <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Cultivo:</td>
                            <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                              <select id="id_cultivo" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-live-search="true" data-selected-text-format="count > 1" data-actions-box="true" multiple>
                              </select>
                            </td> -->
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

                        <table  class="table table-striped tablas_cta_cte" style="width:100%">
                          <thead>
                            <tr>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Fecha</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Comprobante</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Campaña</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Cantidad pedida</th>
                              <th style="text-align: center;vertical-align: middle;" colspan="2">Cta Cte Retiros</th>
                              <th style="text-align: center;vertical-align: middle;" colspan="2">Cta Cte Pagos</th>
                            </tr>
                            <tr>
                              <th style="text-align: center;vertical-align: middle;">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;">Saldo</th>
                              <th style="text-align: center;vertical-align: middle;">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;">Saldo</th>
                            </tr>
                          </thead>
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

                      <div class="tab-pane fade" id="pills-id_producto_pino" role="tabpanel" aria-labelledby="pills-id_producto_pino-tab"><?php

                        //include_once("tablaPedidos.php");?>

                        <!-- <h2>Listado de Pedidos</h2> -->

                        <table  class="table table-striped tablas_cliente" style="width:100%">
                          <thead>
                            <tr>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Fecha</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Comprobante</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Campaña</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;" colspan="2">Cta Cte Retiros</th>
                              <th style="text-align: center;vertical-align: middle;" colspan="2">Cta Cte Pagos</th>
                            </tr>
                            <tr>
                              <th style="text-align: center;vertical-align: middle;">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;">Saldo</th>
                              <th style="text-align: center;vertical-align: middle;">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;">Saldo</th>
                            </tr>
                          </thead>
                          <tbody>
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
                          </tbody>
                        </table>

                      </div>
                      <div class="tab-pane fade" id="pills-id_producto_yerba" role="tabpanel" aria-labelledby="pills-id_producto_yerba-tab"><?php

                        //include_once("formid_producto_yerba.php");?>

                        <table  class="table table-striped tablas_cliente" style="width:100%">
                          <thead>
                            <tr>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Fecha</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Comprobante</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Campaña</th>
                              <th style="text-align: center;vertical-align: middle;" rowspan="2">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;" colspan="2">Cta Cte Retiros</th>
                              <th style="text-align: center;vertical-align: middle;" colspan="2">Cta Cte Pagos</th>
                            </tr>
                            <tr>
                              <th style="text-align: center;vertical-align: middle;">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;">Saldo</th>
                              <th style="text-align: center;vertical-align: middle;">Cantidad</th>
                              <th style="text-align: center;vertical-align: middle;">Saldo</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>1 02 2024</td>
                              <td>Pedido N° 1</td>
                              <td>2024</td>
                              <td align="right">800</td>
                              <td align="right"></td>
                              <td align="right">800</td>
                              <td align="right"></td>
                              <td align="right">800</td>
                            </tr>
                            <tr>
                              <td>1 02 2024</td>
                              <td>Pedido N° 3</td>
                              <td>2024</td>
                              <td align="right">300</td>
                              <td align="right"></td>
                              <td align="right">1.100</td>
                              <td align="right"></td>
                              <td align="right">1.100</td>
                            </tr>
                            <tr>
                              <td>5 02 2024</td>
                              <td>Pago N° 1</td>
                              <td>2024</td>
                              <td align="right"></td>
                              <td align="right"></td>
                              <td align="right">1.100</td>
                              <td align="right">500</td>
                              <td align="right">600</td>
                            </tr>
                            <tr>
                              <td>6 02 2024</td>
                              <td>Pago N° 2</td>
                              <td>2024</td>
                              <td align="right"></td>
                              <td align="right"></td>
                              <td align="right">1.100</td>
                              <td align="right">500</td>
                              <td align="right">100</td>
                            </tr>
                            <tr>
                              <td>6 02 2024</td>
                              <td>Retiro N° 2</td>
                              <td>2024</td>
                              <td align="right"></td>
                              <td align="right">200</td>
                              <td align="right">900</td>
                              <td align="right"></td>
                              <td align="right">100</td>
                            </tr>
                            <tr>
                              <td>7 02 2024</td>
                              <td>Retiro N° 3</td>
                              <td>2024</td>
                              <td align="right"></td>
                              <td align="right">200</td>
                              <td align="right">700</td>
                              <td align="right"></td>
                              <td align="right">100</td>
                            </tr>
                            <tr>
                              <td><strong>Totales</strong></td>
                              <td></td>
                              <td></td>
                              <td align="right"><strong>1.100</strong></td>
                              <td align="right"><strong>400</strong></td>
                              <td><strong></strong></td>
                              <td align="right"><strong>1.00</strong></td>
                              <td><strong></strong></td>
                            </tr>
                          </tbody>
                        </table>
                        
                      </div>
                      <div class="tab-pane fade" id="pills-id_producto_eucaliptus" role="tabpanel" aria-labelledby="pills-id_producto_eucaliptus-tab"><?php

                        //include_once("tablaPagos.php");?>
                        <table id="pagosTable" class="table table-striped table-bordered tablas_cliente" style="width:100%">
                          <thead>
                            <tr>
                              <th>ID Pago</th>
                              <th>ID Pedido</th>
                              <th>Monto</th>
                              <th>Cantidad de Plantines</th>
                              <th>Foto del Comprobante</th>
                              <th>Fecha</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>1</td>
                              <td>101</td>
                              <td>150.00</td>
                              <td>50</td>
                              <td>imagen1.jpg</td>
                              <td>2024-02-05</td>
                            </tr>
                            <tr>
                              <td>2</td>
                              <td>102</td>
                              <td>200.00</td>
                              <td>75</td>
                              <td>imagen2.jpg</td>
                              <td>2024-02-10</td>
                            </tr>
                            <!-- Agregar más filas según sea necesario -->
                          </tbody>
                        </table>
                        
                      </div>
                      <div class="tab-pane fade" id="pills-bandejas" role="tabpanel" aria-labelledby="pills-bandejas-tab"><?php

                        //include_once("tablaBandejas.php");?>
                        <table id="bandejasTable" class="table table-striped table-bordered tablas_cliente" style="width:100%">
                          <thead>
                            <tr>
                              <th>ID Devolución</th>
                              <th>ID Remito</th>
                              <th>Fecha</th>
                              <th>Cantidad Devuelta</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>1</td>
                              <td>101</td>
                              <td>2024-02-05</td>
                              <td>10</td>
                            </tr>
                            <tr>
                              <td>2</td>
                              <td>102</td>
                              <td>2024-02-10</td>
                              <td>15</td>
                            </tr>
                            <!-- Agregar más filas según sea necesario -->
                          </tbody>
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

          <div class="modal fade" id="nuevoPedido" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
        $('.tablas_cta_cte').DataTable({
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
        });

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
            serverSide: true,
            processing: true,
            ajax:{url:'ajaxGetCtaCte.php?desde='+desde+'&hasta='+hasta+'&tipo_comprobante='+tipo_comprobante+'&id_cliente='+id_cliente+'&id_cultivo='+id_cultivo},
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
              }
            },
            "columns":[
              {"data": "fecha"},
              {render: function(data, type, row, meta) {
                //let estado=row.estado;
                let clase="pedidos";
                /*if(estado=="A"){
                  clase="badge badge-success";
                }
                if(estado=="R" || estado=="E"){
                  clase="badge badge-danger";
                }*/
                return '<span class="badge '+clase+'">Pedido N° '+row.id_pedido+'</span>';
              }},
              {"data": "campana"},
              {"data": "cantidad"},
              {render: function(data, type, row, meta) {
                return '0';
              }},
              {
                render: function(data, type, row, meta) {
                  if (typeof row.cantidad !== 'undefined') {
                    //return new Intl.NumberFormat('es-AR', {style: 'decimal'}).format(row.cantidad);
                    return row.cantidad;
                  } else {
                    return '';
                  }
                },
                className: 'dt-body-right text-right',
              },
              {render: function(data, type, row, meta) {
                return '0';
              }},
              {
                render: function(data, type, row, meta) {
                  if (typeof row.cantidad !== 'undefined') {
                    //return new Intl.NumberFormat('es-AR', {style: 'decimal'}).format(row.cantidad);
                    return row.cantidad;
                  } else {
                    return '';
                  }
                },
                className: 'dt-body-right text-right',
              },
            ],
            initComplete: function(settings, json){
              /*let total_facturas_recibos=json.queryInfo.total_facturas_recibos

              var api = this.api();
              // Update footer
              $(api.column(4).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_facturas_recibos));*/

              $('[title]').tooltip();
            }
          })
        }

        // Inicializar DataTable
        /*$(document).ready(function() {
            $('#remitosTable').DataTable();
        });*/
      });
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
  </body>
</html>