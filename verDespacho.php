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
  header("Location: listarDespachos.php");
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT d.id,date_format(d.fecha,'%d/%m/%Y') as fecha, c.razon_social, d.id_pedido, d.id_cliente_retira,d.campana,d.id_transporte, ch.nombre_apellido,ve.descripcion,ve.patente, ve.patente2, lo.nombre, d.id_plantador, t.razon_social as transporte, l.localidad, p.provincia, d.id_usuario, d.fecha_hora_alta FROM despachos d INNER JOIN despachos_detalle dd ON dd.id_despacho= d.id LEFT JOIN clientes c ON c.id = d.id_cliente INNER JOIN especies es ON dd.id_cultivo=es.id INNER JOIN transportes t ON d.id_transporte=t.id INNER JOIN choferes ch ON d.id_chofer=ch.id LEFT JOIN lotes lo ON d.id_lote = lo.id INNER JOIN localidades l ON d.id_localidad = l.id INNER JOIN provincias p on l.id_provincia = p.id INNER JOIN vehiculos ve ON d.id_vehiculo=ve.id INNER JOIN cultivos cu ON dd.id_cultivo=cu.id INNER JOIN especies e ON dd.id_especie=e.id INNER JOIN procedencias_especies pr ON dd.id_procedencia=pr.id WHERE d.id = ?";
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
                      <li class="breadcrumb-item">Ver Despacho</li>
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
                  $link_volver="listarDespachos";
                  // if($data['anulada']==1){
                  //   $style="background-color: rgb(255 0 0 / 50%);";
                  //   $texto="Anulada";
                  //   $link_volver="listardespachosAnuladas";
                  // }?>
                  <div class="card-header">
                    <h5>Ver despacho</h5>
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
                            <div class="col-sm-9"><input name="fecha" type="text" axlength="99" class="form-control" value="<?=$data['razon_social'];?>"disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Transporte</label>
                            <div class="col-sm-9"><input name="fecha" type="text" axlength="99" class="form-control" value="<?=$data['transporte'];?>"disabled></div>
                          </div>
                          
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Chofer</label>
                            <div class="col-sm-9"><input name="fecha" type="text" axlength="99" class="form-control" value="<?=$data['nombre_apellido'];?>"disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Vehiculo</label><div class="col-sm-9"><input name="fecha" type="text" axlength="99" class="form-control" value="<?=$data['descripcion'] . " - " . $data['patente'];?>"disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Patente2</label>
                            <div class="col-sm-9"><input name="fecha" type="text" axlength="99" class="form-control" value="<?=($data['patente2'] == "") ? "Sin Patente" : $data['patente2']; ?>"disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Localidad</label>
                            <div class="col-sm-9"><input name="fecha" type="text" axlength="99" class="form-control" value="<?=$data['localidad'] . " - ". $data['provincia'];?>"disabled></div>
                          </div>
                          <div class="form-group row ">
                            <div class="col-sm-9 ">
                              <table class="table display">
                                <thead>
                                  <tr>
                                    <th>Servicio</th>
                                    <th>Especie</th>
                                    <th>Procedencia</th>
                                    <th>Material</th>
                                    <th>Cantidad</th>
                                  </tr>
                                </thead>
                                <tbody><?php
                                  $pdo = Database::connect();
                                  $sql = " SELECT cu.material, se.servicio, es.especie, cu.material, pr.procedencia, dd.cantidad_plantines FROM despachos_detalle dd INNER JOIN despachos v ON v.id = dd.id_despacho INNER JOIN cultivos cu ON dd.id_material = cu.id INNER JOIN servicios se ON dd.id_servicio = se.id INNER JOIN especies es ON dd.id_especie = es.id INNER JOIN procedencias_especies pr ON dd.id_procedencia = pr.id WHERE dd.id_despacho = ".$id;
                                  $cantidad_plantines = 0;
                                  //var_dump($sql);
                                  
                                  foreach ($pdo->query($sql) as $row) {
                                    $cantidad_plantines += $row['cantidad_plantines'];
                                    echo '<tr>';
                                    echo '<td>'. $row["servicio"] . '</td>';
                                    echo '<td>'. $row["especie"] . '</td>';
                                    echo '<td>'. $row["procedencia"] . '</td>';
                                    echo '<td>'. $row["material"] . '</td>';
                                    echo '<td>'. $row["cantidad_plantines"] . '</td>';
                                    echo '</tr>';
                                  }
                                  Database::disconnect();?>
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <th style="text-align:right" colspan="4">Total</th>
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
                            ¿Está seguro que desea generar un Nota de Crédito para esta factura y eliminar la despacho?
                          </div>
                          <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-light">Volver</button>
                            <a href="nota_credito.php?id=<?=$id?>" id="btnGenerarNC" class="btn btn-primary">Generar NC y eliminar despacho</a>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3"><?php
                        if(isset($data["tipo_comprobante"]) and $data["tipo_comprobante"]!="R" and $data["estado"]=="A" and is_null($data["id_despacho_cbte_relacionado"])){?>
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
