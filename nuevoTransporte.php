<?php
    require("config.php");
    if(empty($_SESSION['user']['id'])){
      header("Location: index.php");
      die("Redirecting to index.php"); 
    }else{
      var_dump($_SESSION['user']['id']);
    }
	
	require 'database.php';

	if ( !empty($_POST)) {
    // var_dump($_POST);
    // die;

		// insert data
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql = "INSERT INTO transportes(razon_social, cuit, domicilio,id_usuario, fecha_hora_alta) VALUES (?,?,?,?,now())";
		$q = $pdo->prepare($sql);
		$q->execute(array($_POST['razon_social'], $_POST['cuit'],$_POST['domicilio'],$_SESSION['user']['id']));
    $idTransporte = $pdo->lastInsertId();

    foreach($_POST["nombre_apellido"] as $key => $nombre_apellido){
      if($key==0){
        continue;
      }
      
      $sql = "INSERT INTO choferes (nombre_apellido, dni, id_transporte, id_usuario) VALUES (?,?,?,?) ";
      $q = $pdo->prepare($sql);
      $q->execute(array($nombre_apellido,$_POST["dni"][$key],$idTransporte,$_SESSION['user']['id']));
    }
    
    foreach($_POST["descripcion"] as $key => $descripcion){
      if($key==0){
        continue;
      }

      $sql = "INSERT INTO vehiculos (descripcion, patente, patente2, id_transporte, id_usuario) VALUES (?,?,?,?,?) ";
      $q = $pdo->prepare($sql);
      $q->execute(array($descripcion,$_POST["patente"][$key],$_POST["patente2"][$key],$idTransporte,$_SESSION['user']['id']));
    }
		
		Database::disconnect();
		
		header("Location: listarTransportes.php");
	}
	
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
	<link rel="stylesheet" type="text/css" href="assets/css/select2.css">
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
                      <li class="breadcrumb-item">Nuevo Transporte</li>
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
                      <h5>Nuevo Transporte</h5>
                    </div>
                    <form class="form theme-form" role="form" method="post" action="nuevoTransporte.php">
                      <div class="card-body">
                        <div class="row">
                          <div class="col">
                            <div class="form-group row">
                              <div class="form-group col-4">
                                <label for="razon_social">Razon Social</label>
                                <input type="text" class="form-control" id="razon_social" name="razon_social" aria-describedby="Razon Social" placeholder="Ingrese la Razon Social">
                                <!-- <small id="razon_social" class="form-text text-muted">Razon Social</small> -->
                              </div>

                              <div class="form-group col-4">
                                <label for="cuit">CUIT</label>
                                <input type="text" class="form-control" id="cuit" name="cuit" aria-describedby="cuit" placeholder="Introduzca el CUIT">
                                <!-- <small id="cuit" class="form-text text-muted">We'll never share your text with anyone else.</small> -->
                              </div>
                              
                              <div class="form-group col-4">
                                <label for="domicilio">Domicilio</label>
                                <input type="text" class="form-control" id="domicilio" name="domicilio" aria-describedby="domicilio" placeholder="Introduzca el Domicilio">
                                <!-- <small id="domicilio" class="form-text text-muted">We'll never share your text with anyone else.</small> -->
                              </div>
                            </div>
                          </div><!-- .col -->
                        </div><!-- .row -->
                    
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="card-header">
                                <h4 class="text-center">Choferes</h4>
                            </div>
                            <div class="card-body pt-0">
                              <div class="form-group row">
                                <table class="table-detalle table table-bordered table-hover text-center" id="tableChofer">
                                  <thead>
                                    <tr>
                                      <th>Nombre y Apellido</th>
                                      <th>DNI</th>
                                      <th>Eliminar</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr id='addr0' data-id="0" style="display: none;">
                                      <td data-name="nombre_apellido">
                                        <input type="text" class="form-control" placeholder="Nombre y Apellido" name="nombre_apellido[]" id="nombre-0"/>
                                      </td>
                                      <td data-name="DNI">
                                        <input type="text" class="form-control" placeholder="DNI" name="dni[]" id="dni-0"/>
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
                                      <td colspan="3" align='right'>
                                        <input type="button" class="btn btn-dark" id="addRowChofer" value="Agregar Chofer">
                                      </td>
                                    </tr>
                                  </tfoot>
                                </table>
                              </div><!--.form-group-->
                            </div><!--.card-body-->
                          </div><!--.col-sm-6-->

                          <div class="col-sm-6">
                            <div class="card-header">
                                <h4 class="text-center">Vehiculos</h4>
                            </div>
                            <div class="card-body pt-0">
                              <div class="form-group row">
                                <table class="table-detalle table table-bordered table-hover text-center" id="tableVehiculo">
                                  <thead>
                                    <tr>
                                      <th>Descripcion</th>
                                      <th>Patente</th>
                                      <th>Patente2</th>
                                      <th>Eliminar</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr id='addr0' data-id="0" style="display: none;">
                                      <td data-name="descripcion">
                                        <input type="text" class="form-control" placeholder="Descripcion" name="descripcion[]" id="descripcion-0"/>
                                      </td>
                                      <td data-name="patente">
                                        <input type="text" class="form-control" placeholder="Patente" name="patente[]" id="patente-0"/>
                                      </td>
                                      <td data-name="patente2">
                                        <input type="text" class="form-control" placeholder="Patente" name="patente2[]" id="patente2-0"/>
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
                                      <td colspan="4" align='right'>
                                        <input type="button" class="btn btn-dark " id="addRowVehiculo" value="Agregar Vehiculo">
                                      </td>
                                    </tr>
                                  </tfoot>
                                </table>
                              </div>  
                            </div><!--.card-body-->
                          </div><!--col-sm-6-->
                        </div>
                      </div><!-- .card-body -->
                      <div class="card-footer">
                        <div class="col-sm-12 offset-sm-4">
                          <button class="btn btn-primary" type="submit">Crear</button>
						              <a href="listarTransportes.php" class="btn btn-light">Volver</a>
                        </div>
                      </div>
                    </form><!--form -->
                  </div>
                </div>
              </div>
            </div><!--.row-->
          </div><!-- Container-fluid Ends-->
        </div><!--.page-body-->
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
    <script src="assets/js/typeahead/handlebars.js"></script>
    <script src="assets/js/typeahead/typeahead.bundle.js"></script>
    <script src="assets/js/typeahead/typeahead.custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <script src="assets/js/typeahead-search/handlebars.js"></script>
    <script src="assets/js/typeahead-search/typeahead-custom.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	<script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <script type="text/javascript">

      $(document).ready(function(){
          $("#addRowChofer").on('click', function(event) {
            event.preventDefault();
            addRowChofer();
          }).click();

          $("#addRowVehiculo").on('click', function(event) {
            event.preventDefault();
            addRowVehiculo();
          }).click();
      });

      function eliminarFila(t){
        var fila=$(t).closest("tr");
        fila.remove();
      }

      function addRowChofer(){
        //alert("hola");
        var newid = 0;
        var primero="";
        var ultimoRegistro=0;
        $.each($("#tableChofer tr"), function() {
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
        $.each($("#tableChofer tbody tr:nth(0) td"),function(){//loop through each td and create new elements with name of newid
          var cur_td = $(this); 
          var children = cur_td.children();
          if($(this).data("name")!=undefined){// add new td and element if it has a name
            var td = $("<td></td>", {
              "data-name": $(cur_td).data("name"),
              "class": this.className
            });
            var c = $(cur_td).find($(children[0]).prop('tagName')).clone();//.val("")
            
            var id=$(c).attr("id");
            $(c).attr("required",true);
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
              'text': $('#tableChofer tr').length
            }).appendTo($(tr));
          }
        });
        //console.log($(tr).find($("input[name='detalledni[]']")));
        //console.log(tr);//.find($("input"))
        $(tr).appendTo($('#tableChofer'));// add the new row
        if(newid>0){
          primero.focus();
          var sel2=$("#id_categoria-"+newid)
          //console.log(sel2);
          
          sel2.select2();//llamamos para inicializar select2
          sel2.select2('destroy');//como no se iniciliaza bien lo destruimos para que elimine las clases que arrastra de la clonacion
          sel2.select2();//volvemos a inicializar y ahora si se inicializa bien
          
        }
        return tr.attr("id");
      }

      function addRowVehiculo(){
        //alert("hola");
        var newid = 0;
        var primero="";
        var ultimoRegistro=0;
        $.each($("#tableVehiculo tr"), function() {
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
        $.each($("#tableVehiculo tbody tr:nth(0) td"),function(){//loop through each td and create new elements with name of newid
          var cur_td = $(this); 
          var children = cur_td.children();
          if($(this).data("name")!=undefined){// add new td and element if it has a name
            var td = $("<td></td>", {
              "data-name": $(cur_td).data("name"),
              "class": this.className
            });
            var c = $(cur_td).find($(children[0]).prop('tagName')).clone();//.val("")
            
            var id=$(c).attr("id");
            $(c).attr("required",true);
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
              'text': $('#tableVehiculo tr').length
            }).appendTo($(tr));
          }
        });
        //console.log($(tr).find($("input[name='detalledni[]']")));
        //console.log(tr);//.find($("input"))
        $(tr).appendTo($('#tableVehiculo'));// add the new row
        if(newid>0){
          primero.focus();
          var sel2=$("#id_categoria-"+newid)
          //console.log(sel2);
          
          sel2.select2();//llamamos para inicializar select2
          sel2.select2('destroy');//como no se iniciliaza bien lo destruimos para que elimine las clases que arrastra de la clonacion
          sel2.select2();//volvemos a inicializar y ahora si se inicializa bien
          
        }
        return tr.attr("id");
      }

    </script>
  </body>
</html>