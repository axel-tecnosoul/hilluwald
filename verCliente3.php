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

Database::disconnect();
	
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
	<link rel="stylesheet" type="text/css" href="assets/css/select2.css">
	<link rel="stylesheet" type="text/css" href="assets/css/datatables.css">
  </head>
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
                  </div>

                  <div class="card-body">
                    <ul class="nav nav-tabs border-tab" id="pills-tab" role="tablist" style="margin-bottom: 40px;margin-top: -20px;">
                      <li class="nav-item"><a class="nav-link active" id="pills-contacto-tab" data-toggle="pill" href="#pills-contacto" role="tab" aria-controls="pills-contacto" aria-selected="true"><i class="icofont icofont-ui-user"></i>Datos</a></li>

                      <li class="nav-item"><a class="nav-link" id="pills-pedidos-tab" data-toggle="pill" href="#pills-pedidos" role="tab" aria-controls="pills-pedidos" aria-selected="false"><i class="fa fa-shopping-cart"></i>Pedidos</a></li>

                      <li class="nav-item"><a class="nav-link" id="pills-retiros-tab" data-toggle="pill" href="#pills-retiros" role="tab" aria-controls="pills-retiros" aria-selected="false"><i class="fa fa-truck"></i></i>Retiros</a></li>

                      <li class="nav-item"><a class="nav-link" id="pills-pagos-tab" data-toggle="pill" href="#pills-pagos" role="tab" aria-controls="pills-pagos" aria-selected="false"><i class="fa fa-usd"></i>Pagos</a></li>

                      <li class="nav-item"><a class="nav-link" id="pills-contenedores-tab" data-toggle="pill" href="#pills-contenedores" role="tab" aria-controls="pills-contenedores" aria-selected="false"><i class="fa fa-th"></i>Contenedores</a></li>
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
                      <div class="tab-pane fade" id="pills-pedidos" role="tabpanel" aria-labelledby="pills-pedidos-tab"><?php

                        //include_once("tablaPedidos.php");?>

                        <!-- <h2>Listado de Pedidos</h2> -->
                        <table id="pedidosTable" class="table table-striped table-bordered tablas_cliente" style="width:100%">
                          <thead>
                            <tr>
                              <th>ID Pedido</th>
                              <th>Fecha</th>
                              <th>Cultivo</th>
                              <th>Campaña</th>
                              <th>Pedido</th>
                              <th>Retirado</th>
                              <th>Pagado</th>
                              <!-- <th>Pago Completo</th>
                              <th>Retiro Completo</th>
                              <th>Motivo Saldado</th> -->
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>1</td>
                              <td>01-02-2024</td>
                              <td>Yerba</td>
                              <td>2024</td>
                              <td>1.000</td>
                              <td>200</td>
                              <td>500</td>
                            </tr>
                            <tr>
                              <td>1</td>
                              <td>01-02-2024</td>
                              <td>Pino Taeda semilla Bosques del plata</td>
                              <td>2024</td>
                              <td>2.000</td>
                              <td>1.000</td>
                              <td>500</td>
                            </tr>
                            <tr>
                              <td>2</td>
                              <td>05-02-2024</td>
                              <td>Pino Taeda semilla Bosques del plata</td>
                              <td>2024</td>
                              <td>500</td>
                              <td>0</td>
                              <td>0</td>
                            </tr>
                            <!-- Agregar más filas con datos ficticios según sea necesario -->
                          </tbody>
                        </table>

                      </div>
                      <div class="tab-pane fade" id="pills-retiros" role="tabpanel" aria-labelledby="pills-retiros-tab"><?php

                        //include_once("formRetiros.php");?>

                        <table id="remitosTable" class="table table-striped table-bordered tablas_cliente" style="width:100%">
                          <thead>
                            <tr>
                              <th>ID Remito</th>
                              <th>Transporte</th>
                              <th>Chofer</th>
                              <th>Cantidad de Contenedores</th>
                              <th>Contenedores Devueltas</th>
                              <th>Fecha</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>1</td>
                              <td>Transporte 1</td>
                              <td>Chofer 1</td>
                              <td>50</td>
                              <td>5</td>
                              <td>2024-02-10</td>
                            </tr>
                            <tr>
                              <td>2</td>
                              <td>Transporte 2</td>
                              <td>Chofer 2</td>
                              <td>75</td>
                              <td>10</td>
                              <td>2024-02-15</td>
                            </tr>
                            <!-- Agregar más filas según sea necesario -->
                          </tbody>
                        </table>
                        
                      </div>
                      <div class="tab-pane fade" id="pills-pagos" role="tabpanel" aria-labelledby="pills-pagos-tab"><?php

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
                      <div class="tab-pane fade" id="pills-contenedores" role="tabpanel" aria-labelledby="pills-contenedores-tab"><?php

                        //include_once("tablaContenedores.php");?>
                        <table id="contenedoresTable" class="table table-striped table-bordered tablas_cliente" style="width:100%">
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
        $('.tablas_cliente').DataTable({
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

        // Inicializar DataTable
        /*$(document).ready(function() {
            $('#remitosTable').DataTable();
        });*/
      });
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
  </body>
</html>