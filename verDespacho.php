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
$sql = '"SELECT d.id,date_format(d.fecha,"%d/%m/%Y"), c.razon_social, d.id_pedido, d.id_cliente_retira,d.campana,d.id_transporte, ch.nombre_apellido,ve.descripcion, d.patente2, lo.nombre, d.id_plantador, l.localidad, d.id_usuario, d.fecha_hora_alta FROM despachos d INNER JOIN despachos_detalle dd ON dd.id_despacho= d.id LEFT JOIN clientes c ON c.id = d.id_cliente INNER JOIN especies es ON dd.id_cultivo=es.id INNER JOIN transportes t ON d.id_transporte=t.id INNER JOIN choferes ch ON d.id_chofer=ch.id LEFT JOIN lotes lo ON d.id_lote = lo.id INNER JOIN localidades l ON d.id_localidad = l.id  INNER JOIN vehiculos ve ON d.id_vehiculo=ve.id INNER JOIN cultivos cu ON dd.id_cultivo=cu.id INNER JOIN especies e ON dd.id_especie=e.id INNER JOIN procedencias_especies pr ON dd.id_procedencia=pr.id WHERE id = ?"';
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
                  if($data['anulada']==1){
                    $style="background-color: rgb(255 0 0 / 50%);";
                    $texto="Anulada";
                    $link_volver="listardespachosAnuladas";
                  }?>
                  <div class="card-header" style="<?=$style?>">
                    <h5>Ver despacho <?=$texto;
                      if($data['anulada']==0){?>
                        <a class="btn btn-sm btn-primary" href="remito.php?id=<?= $id;?>" target="_blank"><i class="fa fa-print fa-lg" aria-hidden="true"></i> Remito</a><?php
                        if($data['facturacion']!="Sin factura"){?>
                          <a class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalGenerarNC"><i class="fa fa-minus-circle fa-lg" aria-hidden="true"></i> Nota de Crédito</a><?php
                        }
                      }?>
                    </h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="#">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-9"><?=$data['fecha_despacho']; ?></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-9"><?=$data['razon_social']; ?></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Facturacion</label>
                            <div class="col-sm-9"><?=$data['facturacion']; ?></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Productos</label>
                          <!-- </div>
                          <div class="form-group row"> -->
                            <div class="col-sm-9">
                              <table class="table display">
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
                                  $sql = " SELECT p.descripcion, vd.precio, vd.cantidad, vd.subtotal FROM despachos_detalle vd INNER JOIN despachos v ON v.id = vd.id_despacho INNER JOIN productos p ON p.id = vd.id_producto WHERE vd.id_despacho = ".$id;
                                  //var_dump($sql);
                                  
                                  foreach ($pdo->query($sql) as $row) {
                                    echo '<tr>';
                                    echo '<td>'. $row["descripcion"] . '</td>';
                                    echo '<td style="text-align:right">$'. number_format($row["precio"],2) . '</td>';
                                    echo '<td style="text-align:center">'. $row["cantidad"] . '</td>';
                                    echo '<td style="text-align:right">$'. number_format($row["subtotal"],2) . '</td>';
                                    echo '</tr>';
                                  }
                                  Database::disconnect();?>
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <th style="text-align:right" colspan="3">Total</th>
                                    <th style="text-align:right">$<?=number_format($data['total'],2); ?></th>
                                  </tr>
                                </tfoot>
                              </table>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Modalidad de pago</label>
                            <div class="col-sm-9"><?=$data['modalidad_pago']?></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Observaciones</label>
                            <div class="col-sm-9"><?=$data['observaciones']; ?></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Facturas</label>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12">
                              <table class="table display">
                                <thead>
                                  <tr style='text-align:center'>
                                    <th>Tipo Cbte.</th>
                                    <th>Nro Cbte.</th>
                                    <!-- <th>Estado</th> -->
                                    <th>Bruto</th>
                                    <th>Iva</th>
                                    <th>Neto</th>
                                    <!-- <th>CAE</th>
                                    <th>Fecha vto. CAE</th> -->
                                    <th>Cbte. Relacionado</th>
                                    <th>Acciones</th>
                                  </tr>
                                </thead>
                                <tbody><?php
                                  $pdo = Database::connect();
                                  $sql = "SELECT f.id AS id_factura,f.tipo_comprobante,f.total_bruto,f.total_neto,f.total_iva,f.estado,f.punto_despacho,f.numero_cbte,f.cae,date_format(f.fecha_vto_cae,'%d/%m/%Y') AS fecha_vencimiento_cae,f.id_cbte_relacionado FROM facturas f WHERE f.id_despacho = $id ";
                                  //echo $sql;
                                  $sumaTotal=0;
                                  foreach ($pdo->query($sql) as $row) {
                                    $estado=get_estado_comprobante($row['estado']);
                                    $cbte=format_numero_comprobante($row['punto_despacho'],$row['numero_cbte']);
                                    $tipo_cbte=get_nombre_comprobante($row["tipo_comprobante"]);
                                    $class="";
                                    if($row['estado']=="A"){
                                      $class="badge badge-success";
                                    }
                                    if($row['estado']=="R" or $row['estado']=="E"){
                                      $class="badge badge-danger";
                                    }
                                    if(in_array($row["tipo_comprobante"],["A","B"])){
                                      $sumaTotal+=$row["total_bruto"];
                                    }else{
                                      $sumaTotal-=$row["total_bruto"];
                                    }
                                    echo "<tr>";
                                    echo "<td><span class='$class'>$tipo_cbte</span></td>";
                                    echo "<td>$cbte</td>";
                                    //echo "<td>$cbte</td>";
                                    //echo "<td>$cbte</td>";
                                    //echo "<td><span class='$class'>$estado</span></td>";
                                    echo "<td style='text-align:right'>$".number_format($row["total_bruto"],2) ."</td>";
                                    echo "<td style='text-align:right'>$".number_format($row["total_iva"],2) ."</td>";
                                    echo "<td style='text-align:right'>$".number_format($row["total_neto"],2) ."</td>";
                                    /*echo "<td>".$row["cae"]."</td>";
                                    echo "<td>".$row["fecha_vencimiento_cae"]."</td>";*/
                                    echo "<td>";
                                    if(!is_null($row["id_cbte_relacionado"])){?>
                                      <a href="verdespacho.php?id=<?=$row["id_cbte_relacionado"]?>" target="_blank" title="Ver Comprobante relacionado">
                                        <img src="img/eye.png" width="24" height="15" border="0" alt="Ver despacho">
                                        <?=$row['id_cbte_relacionado']?>
                                      </a><?php
                                    }
                                    echo "</td>";
                                    echo "<td style='text-align:center'>";?>
                                      <a href="factura.php?id=<?= $row["id_factura"]?>" target="_blank"><img src="img/print.png" width="30" height="25" border="0" alt="Imprimir comprobante" title="Imprimir comprobante"></a><?php
                                    echo "</td>";
                                    echo "</tr>";
                                  }
                                  Database::disconnect();?>
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <th colspan="2"></th>
                                    <th style="text-align:right">$<?=number_format($sumaTotal,2)?></th>
                                    <th colspan="3"></th>
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
