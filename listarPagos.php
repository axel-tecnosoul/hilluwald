<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}
include_once("funciones.php");
$hasta=$hoy=date("Y-m-d");
$desde=date("Y-m-d",strtotime($hoy." -1 year"))?>
<!DOCTYPE html>
<html lang="en">
  <head>
	  <?php include('head_tables.php');?>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css">
  </head>
  <style>
    td.child {
      background-color: beige;
    }
    .multiselect{
      color:#212529 !important;
      background-color:#fff;
      border-color:#ccc;
    }
  </style>
  <body class="light-only">
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
      <!-- Page Header Start-->
      <?php include('header.php');?>
     
      <!-- Page Header Ends                              -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <?php include('menu.php');?>
        <!-- Page Sidebar Ends-->
        <!-- Right sidebar Start-->
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
                      <li class="breadcrumb-item">Pagos</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a  target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?=$hoy?>"><i data-feather="calendar"></i></a></li>
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
              <!-- Zero Configuration  Starts-->
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Pagos
                      <!-- &nbsp;<a href="nuevoPedido.php"><img src="img/icon_alta.png" width="24" height="25" border="0" alt="Nuevo Pedido" title="Nuevo Pedido"></a>
                      &nbsp;<a href="listarPedidosAnuladas.php"><img src="img/canceled.png" width="24" height="25" border="0" alt="Pedidos Eliminados" title="Pedidos Eliminados"></a> -->
                      <!-- &nbsp;<a href="exportPedidos.php"><img src="img/xls.png" width="24" height="25" border="0" alt="Exportar Pedidos" title="Exportar Pedidos"></a> -->
                    </h5>
                  </div>
                  <div class="card-body">
                  <div class="row">
                      <table class="table">
                        <tr>
                          <td class="text-right border-0 p-1">Desde: </td>
                          <td class="border-0 p-1"><input type="date" id="desde" value="<?=$desde?>" class="form-control form-control-sm filtraTabla"></td>
                          <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Cliente:</td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <select id="id_cliente" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-live-search="true" data-selected-text-format="count > 1" data-actions-box="true" multiple><?php
                              include 'database.php';
                              $pdo = Database::connect();
                              $sql = " SELECT id, razon_social FROM clientes";
                              foreach ($pdo->query($sql) as $row) {?>
                                <option value="<?=$row["id"]?>"><?=$row["razon_social"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="d-none text-right border-0 p-1">Facturacion:</td>
                          <td rowspan="2" style="vertical-align: middle;" class="d-none border-0 p-1">
                            <select id="tipo_comprobante" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-selected-text-format="count > 1" multiple>
                              <option value="Consumidor Final">Consumidor Final</option>
                              <option value="Cliente">Cliente</option>
                              <option value="Sin factura">Sin factura</option>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td class="text-right border-0 p-1">Hasta: </td>
                          <td class="border-0 p-1"><input type="date" id="hasta" value="<?=$hasta?>" class="form-control form-control-sm filtraTabla"></td>
                        </tr>
                      </table>
                    </div>
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th style="width:10%">ID</th>
                            <th style="width:20%">Fecha</th>
                            <th style="width:30%">Cliente</th>
                            <th style="width:20%">Monto Total</th>
                            <!-- <th>Pago Completo</th>
                            <th>Despacho Completo</th> -->
                            <th>Opciones</th>
                            <th class="none"></th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th style="text-align: right" colspan="3">Total</th>
                            <th style="text-align: right"></th>
                            <th></th>
                            <th></th>
                          </tr>
                        </tfoot>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Zero Configuration  Ends-->
              <!-- Feature Unable /Disable Order Starts-->
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
        <?php include("footer.php"); ?>
      </div>
    </div>

    <div class="modal fade" id="eliminarModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Confirmación</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          </div>
          <div class="modal-body">¿Está seguro que desea eliminar el pedido?</div>
          <div class="modal-footer">
            <button class="btn btn-light" type="button" data-dismiss="modal" aria-label="Close">Volver</button>
            <a id="btnEliminarPedido" class="btn btn-primary">Eliminar</a>
          </div>
        </div>
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

    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>
    <!-- Plugins JS start-->
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
    <script src="assets/js/script.js"></script>
	  <script>

    function openModalEliminarPedido(idPedido){
      $('#eliminarModal').modal("show");
      document.getElementById("btnEliminarPedido").href="anularPedido.php?id="+idPedido;
    }

		$(document).ready(function() {

      getPedidos();
      $(".filtraTabla").on("change",getPedidos);

		});

    function getPedidos(){
      let desde=$("#desde").val();
      let hasta=$("#hasta").val();
      let id_cliente=$("#id_cliente").val();

      let id_perfil="<?=$_SESSION["user"]["id_perfil"]?>";

      let table=$('#dataTables-example666')
      table.DataTable().destroy();
      table.DataTable({
        //dom: 'rtip',
        serverSide: true,
        processing: true,
        ajax:{url:'ajaxListarPagos.php?desde='+desde+'&hasta='+hasta+'&id_cliente='+id_cliente},
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
          {"data": "id_pedido"},
          {"data": "fecha"},
          /*{render: function(data, type, row, meta) {
            return row.fecha_hora+"hs";
          }},*/
          {"data": "razon_social"},
          {render: function(data, type, row, meta) {
            return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.monto_total);
          }},
          /*{"data": "pago_completo"},
          {"data": "despacho_completo"},*/
          {render: function(data, type, row, meta) {
            let btnVer='<a href="verPedido.php?id='+row.id_pedido+'"><img src="img/eye.png" width="24" height="15" border="0" alt="Ver Pedido" title="Ver Pedido"></a>&nbsp;&nbsp;'
            let btnImprimir='<a href="PDFPedido.php?id='+row.id_pedido+'" target="_blank"><img src="img/print.png" width="30" height="20" border="0" alt="Imprimir Remito" title="Imprimir Remito"></a>&nbsp;&nbsp;'
            let btnAnular="";
            let facturacion="";
            if(facturacion=="Sin factura"){
              //btnAnular='<a href="#" data-toggle="modal" data-original-title="Confirmación" data-target="#eliminarModal_'+row["id"]+'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Anular" title="Anular"></a>&nbsp;&nbsp;'
              btnAnular='<a href="#" title="Eliminar" onclick="openModalEliminarPedido('+row.id_pedido+')"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar"></a>&nbsp;&nbsp;'
            }
            return btnVer+btnImprimir+btnAnular;
          }},
          {
            "data": "detalle_cultivos",
            className: 'bg-secondary',
          },
        ],
        "columnDefs": [
          { "className": "dt-body-right", "targets": [3] },
        ],
        initComplete: function(settings, json){
          let total_pagos=json.queryInfo.total_pagos
          /*let total_pagos=0;
          json.data.forEach(function (data) {
            total_pagos+=parseFloat(data.monto_total);
          });*/

          var api = this.api();
          // Update footer
          $(api.column(3).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_pagos));

          $('[title]').tooltip();
        }
			})
    }
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>