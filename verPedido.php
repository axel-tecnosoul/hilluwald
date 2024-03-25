<?php
require("config.php");
if(empty($_SESSION['user'])){
    header("Location: index.php");
    die("Redirecting to index.php"); 
}
require 'database.php';
require 'funciones.php';
include 'vendor/afip/Afip.php';
include 'config_facturacion_electronica.php';//poner $homologacion=1 para facturar en modo homologacion. Retorna $aInitializeAFIP.

$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarpedidos.php");
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT pd.id,date_format(pd.fecha,'%d/%m/%Y') as fecha, c.razon_social as cliente, su.nombre as sucursal, pd.campana, pd.observaciones, pd.pago_completo, pd.despacho_completo, pd.motivo_saldado, pd.id_usuario, pd.fecha_hora_alta FROM pedidos pd INNER JOIN pedidos_detalle pde ON pde.id_pedido= pd.id INNER JOIN clientes c ON pd.id_cliente = c.id LEFT JOIN sucursales su ON pd.id_sucursal = su.id WHERE pd.id = ?";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

Database::disconnect();?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
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
                      <li class="breadcrumb-item">Ver pedido</li>
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
                  $style="";
                  $texto="";
                  $link_volver="listarpedidos";
                  // if($data['anulada']==1){
                  //   $style="background-color: rgb(255 0 0 / 50%);";
                  //   $texto="Anulada";
                  //   $link_volver="listarpedidosAnuladas";
                  // }?>
                  <div class="card-header">
                    <h5>Ver pedido</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="#">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-9"><input name="fecha" type="text" axlength="99" class="form-control" value="<?=$data['fecha'];?>" disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-9"><input name="cliente" type="text" axlength="99" class="form-control" value="<?=$data['cliente'];?>"disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Campaña</label>
                            <div class="col-sm-9"><input name="campana" type="text" axlength="99" class="form-control" value="<?=$data['campana'];?>"disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Sucursal</label>
                            <div class="col-sm-9"><input name="sucursal" type="text" axlength="99" class="form-control" value="<?=($data['sucursal'] == "") ? "Sin Sucursal" : $data['sucursal']; ?>"disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Observaciones</label>
                            <div class="col-sm-9"><input name="observaciones" type="text" axlength="99" class="form-control" value="<?=$data['observaciones'];?>"disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Pago Completo</label><div class="col-sm-9"><input name="pago_completo" type="text" axlength="99" class="form-control" value="<?=$data['pago_completo'];?>"disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Despacho Completo</label><div class="col-sm-9"><input name="despacho_completo" type="text" axlength="99" class="form-control" value="<?=$data['despacho_completo'];?>"disabled></div>
                          </div>

                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Motivo saldado</label>
                            <div class="col-sm-9"><input name="motivo_saldado" type="text" axlength="99" class="form-control" value="<?=($data['motivo_saldado'] == "") ? "Sin motivo saldado" : $data['motivo_saldado']; ?>"disabled></div>
                          </div>
                          <div class="form-group row ">
                            <div class="col-sm-12">
                              <table class="table table-bordered ">
                                <thead>
                                  <tr>
                                    <th>Servicio</th>
                                    <th>Especie</th>
                                    <th>Procedencia</th>
                                    <th>Material</th>
                                    <th>Largo</th>
                                    <th>Cantidad</th>
                                    <th>Cantidad Pagados</th>
                                    <th>Cantidad Retirados</th>
                                  </tr>
                                </thead>
                                <tbody><?php
                                  $pdo = Database::connect();
                                  $sql = " SELECT cu.material, se.servicio, es.especie, cu.material, pr.procedencia, pde.cantidad_plantines, pde.plantines_pagados, pde.plantines_retirados FROM pedidos_detalle pde INNER JOIN pedidos pd ON pd.id = pde.id_pedido LEFT JOIN cultivos cu ON pde.id_material = cu.id INNER JOIN servicios se ON pde.id_servicio = se.id INNER JOIN especies es ON pde.id_especie = es.id LEFT JOIN procedencias_especies pr ON pde.id_procedencia = pr.id WHERE pde.id_pedido = ".$id;
                                  $cantidad_plantines = 0;
                                  //var_dump($sql);
                                  
                                  foreach ($pdo->query($sql) as $row) {
                                    $cantidad_plantines += $row['cantidad_plantines'];
                                    echo '<tr>';
                                    echo '<td>'. $row["servicio"] . '</td>';
                                    echo '<td>'. $row["especie"] . '</td>';
                                    echo '<td>'. $row["procedencia"] . '</td>';
                                    echo '<td>'. $row["material"] . '</td>';
                                    echo '<td>'. "12" . "cm" . '</td>';
                                    echo '<td>'. $row["cantidad_plantines"] . '</td>';
                                    echo '<td>'. $row["plantines_pagados"] . '</td>';
                                    echo '<td>'. $row["plantines_retirados"] . '</td>';
                                    echo '</tr>';
                                  }
                                  Database::disconnect();?>
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <th style="text-align:right" colspan="7">Total</th>
                                    <th style="text-align:right"><?=$cantidad_plantines; ?></th>
                                  </tr>
                                </tfoot>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="modal fade" id="modalGenerarNC" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Confirmación</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                          </div>
                          <div class="modal-body">
                            <h6><?php
                              if (extension_loaded('soap')) {
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
                                      if($ok==0){
                                        echo "AFIP informa los siguientes errores:<br>";
                                      }
                                      $ok++;
                                      echo "<b>".$key.":</b> ".$value."<br>";
                                    }
                                  }
                                }
                              }?>
                            </h6>
                            ¿Está seguro que desea generar un Nota de Crédito para esta factura y eliminar el pedido?
                          </div>
                          <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-light">Volver</button>
                            <a href="nota_credito.php?id=<?=$id?>" id="btnGenerarNC" class="btn btn-primary">Generar NC y eliminar pedido</a>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3"><?php
                        if(isset($data["tipo_comprobante"]) and $data["tipo_comprobante"]!="R" and $data["estado"]=="A" and is_null($data["id_pedido_cbte_relacionado"])){?>
                          <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalGenerarNC">Generar NC</button><?php
                        }?>
                        <a href='<?=$link_volver?>.php' class="btn btn-light">Volver</a>
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
    <script>
      $(document).ready(function () {
        $("#btnGenerarNC").on("click",function(){
          $(this).addClass("disabled")
        })
      });
    </script>
  </body>
</html>
