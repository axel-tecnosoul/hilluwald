<?php
    require("config.php");
    if(empty($_SESSION['user']['id'])){
      header("Location: index.php");
      die("Redirecting to index.php"); 
    }
	
	require 'database.php';

	if ( !empty($_POST)) {
    //var_dump($_POST);
    //die;

		// insert data
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql = "INSERT INTO clientes(razon_social, direccion, telefono, email, cuit, cond_fiscal, id_localidad, id_usuario, fecha_hora_alta) VALUES (?,?,?,?,?,?,?,?,now())";
		$q = $pdo->prepare($sql);
		$q->execute(array($_POST['razon_social'], $_POST['direccion_cliente'],$_POST['telefono_cliente'],$_POST['email'],$_POST['cuit'],$_POST['cond_fiscal'],$_POST['localidad'],$_SESSION['user']['id']));
    $idCliente = $pdo->lastInsertId();

    foreach ($_POST["sucursales"] as $key => $id_sucursal) {
      $sql = "INSERT INTO cliente_sucursal (id_cliente, id_sucursal) VALUES (?,?)";
      $q = $pdo->prepare($sql);
      $q->execute(array($idCliente,$id_sucursal));
    }

    foreach($_POST["nombre_lotes"] as $key => $nombre_lotes){
      if($key==0){
        continue;
      }
      
      $sql = "INSERT INTO lotes (id_cliente, nombre, direccion, id_localidad, id_usuario, fecha_hora_alta) VALUES (?,?,?,?,?,now()) ";
      $q = $pdo->prepare($sql);
      $q->execute(array($idCliente,$nombre_lotes,$_POST["direccion"][$key],$_POST["id_localidad"][$key],$_SESSION['user']['id']));
    }
    
    foreach($_POST["nombre_plantadores"] as $key => $nombre_plantadores){
      if($key==0){
        continue;
      }

      $sql = "INSERT INTO plantadores (id_cliente,nombre, dni, telefono, id_usuario, fecha_hora_alta) VALUES (?,?,?,?,?,now()) ";
      $q = $pdo->prepare($sql);
      $q->execute(array($idCliente,$nombre_plantadores,$_POST["dni"][$key],$_POST["telefono"][$key],$_SESSION['user']['id']));
    }
		
		Database::disconnect();
		
		header("Location: listarClientes.php");
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
                      <li class="breadcrumb-item">Nuevo Cliente</li>
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
                      <h5>Nuevo Cliente</h5>
                    </div>
                    <form class="form theme-form" role="form" method="post" action="nuevoCliente.php">
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
                                <label class="cond_fiscal">Condicion Fiscal</label>
                                  <select name="cond_fiscal" class="form-control">
                                    <option value="">- Seleccione -</option><?php
                                    foreach ($aCondicionesFiscales as $value) {
                                      $selected="";
                                      if($value==$data["cond_fiscal"]){
                                        $selected="selected";
                                      }?>
                                      <option <?=$selected?>><?=$value?></option><?php
                                    }?>
                                  </select>
                              </div>

                              <div class="form-group col-4">
                                <label for="cuit">CUIT</label>
                                <input type="number" class="form-control" id="cuit" name="cuit" aria-describedby="cuit" placeholder="Introduzca el CUIT">
                                <!-- <small id="cuit" class="form-text text-muted">We'll never share your text with anyone else.</small> -->
                              </div>

                              <div class="form-group col-4">
                                <label for="localidad">Localidad</label>
                                  <select name="localidad" id="localidad" class="js-example-basic-single col-sm-12" data-required="1">
                                    <option value="">Seleccione...</option><?php
                                    $pdo = Database::connect();
                                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                    $sqlZon = "SELECT l.id, l.localidad, p.provincia FROM `localidades` l left join provincias p on l.id_provincia = p.id  WHERE 1";
                                    $q = $pdo->prepare($sqlZon);
                                    $q->execute();
                                    while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                      echo "<option value='".$fila['id']."'";
                                      echo ">".$fila['localidad'] . " - " . $fila['provincia']."</option>";
                                    }
                                    Database::disconnect();?>
                                  </select>
                              </div>

                              <div class="form-group col-4">
                                <label for="direccion_cliente">Direccion</label>
                                <input type="text" class="form-control" id="direccion_cliente" name="direccion_cliente" aria-describedby="direccion_cliente" placeholder="Introduzca la Direccion">
                                <!-- <small id="direccion" class="form-text text-muted">We'll never share your text with anyone else.</small> -->
                              </div>

                              <div class="form-group col-4">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" aria-describedby="email" placeholder="Introduzca el email">
                                <!-- <small id="direccion" class="form-text text-muted">We'll never share your text with anyone else.</small> -->
                              </div>

                              <div class="form-group col-4">
                                <label for="telefono_cliente">Telefono</label>
                                <input type="text" class="form-control" id="telefono_cliente" name="telefono_cliente" aria-describedby="telefono_cliente" placeholder="Introduzca el Telefono">
                                <!-- <small id="direccion" class="form-text text-muted">We'll never share your text with anyone else.</small> -->
                              </div>
                              
                              <div class="form-group col-4">
                                <label for="sucursales">Sucursales</label>
                                <select name="sucursales[]" id="sucursales[]" class="js-example-basic-single col-sm-12" multiple><?php
                                  $pdo = Database::connect();
                                  $sql = " SELECT id, nombre FROM sucursales";
                                  foreach ($pdo->query($sql) as $row) {?>
                                    <option value="<?=$row["id"]?>"><?=$row["nombre"]?></option><?php
                                  }?>
                                </select>
                              </div>
                            </div>
                          </div><!-- .col -->
                        </div><!-- .row -->
                    
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="card-header">
                                <h4 class="text-center">Lotes</h4>
                            </div>
                            <div class="card-body pt-0">
                              <div class="form-group row">
                                <table class="table-detalle table table-bordered table-hover text-center" id="tableLotes">
                                  <thead>
                                    <tr>
                                      <th>Nombre</th>
                                      <th>Direccion</th>
                                      <th>Localidades</th>
                                      <th>Eliminar</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr id='addr0' data-id="0" style="display: none;">
                                      <td data-name="nombre_lotes">
                                        <input type="text" class="form-control" placeholder="Nombre" name="nombre_lotes[]" id="nombre_lotes-0" data-required="1"/>
                                      </td>
                                      <td data-name="direccion">
                                        <input type="text" class="form-control" placeholder="Direccion" name="direccion[]" id="direccion-0" data-required="1"/>
                                      </td>
                                      <td data-name="localidad">
                                        <select name="id_localidad[]" id="id_localidad-0" class="js-example-basic-single col-sm-12 form-control" data-required="1">
                                          <option value="">Seleccione...</option><?php
                                          $pdo = Database::connect();
                                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                          $sqlZon = "SELECT l.id, l.localidad, p.provincia FROM `localidades` l left join provincias p on l.id_provincia = p.id  WHERE 1";
                                          $q = $pdo->prepare($sqlZon);
                                          $q->execute();
                                          while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='".$fila['id']."'";
                                            echo ">".$fila['localidad'] . " - " . $fila['provincia']."</option>";
                                          }
                                          Database::disconnect();?>
                                        </select>
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
                                        <input type="button" class="btn btn-dark" id="addRowLotes" value="Agregar Lotes">
                                      </td>
                                    </tr>
                                  </tfoot>
                                </table>
                              </div><!--.form-group-->
                            </div><!--.card-body-->
                          </div><!--.col-sm-6-->

                          <div class="col-sm-6">
                            <div class="card-header">
                                <h4 class="text-center">Plantadores</h4>
                            </div>
                            <div class="card-body pt-0">
                              <div class="form-group row">
                                <table class="table-detalle table table-bordered table-hover text-center" id="tablePlantadores">
                                  <thead>
                                    <tr>
                                      <th>Nombre</th>
                                      <th>DNI</th>
                                      <th>Telefono</th>
                                      <th>Eliminar</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr id='addr0' data-id="0" style="display: none;">
                                      <td data-name="nombre_plantadores">
                                        <input type="text" class="form-control" placeholder="Nombre" name="nombre_plantadores[]" id="nombre_plantadores-0" data-required="1"/>
                                      </td>
                                      <td data-name="dni">
                                        <input type="text" class="form-control" placeholder="DNI" name="dni[]" id="dni-0" data-required="1"/>
                                      </td>
                                      <td data-name="telefono">
                                        <input type="text" class="form-control" placeholder="Telefono" name="telefono[]" id="telefono-0" data-required="1"/>
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
                                        <input type="button" class="btn btn-dark " id="addRowPlantadores" value="Agregar Plantadores">
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
						              <a href="listarClientes.php" class="btn btn-light">Volver</a>
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
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	<script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <script type="text/javascript">

      $(document).ready(function(){
        $("#addRowLotes").on('click', function(event) {
          event.preventDefault();
          addRowLotes();
        }).click();

        $("#addRowPlantadores").on('click', function(event) {
          event.preventDefault();
          addRowPlantadores();
        }).click();
      });

      function eliminarFila(t){
        var fila=$(t).closest("tr");
        fila.remove();
      }

      function addRowLotes(){
        //alert("hola");
        var newid = 0;
        var primero="";
        var ultimoRegistro=0;
        $.each($("#tableLotes tr"), function() {
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
        $.each($("#tableLotes tbody tr:nth(0) td"),function(){//loop through each td and create new elements with name of newid
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
              'text': $('#tableLotes tr').length
            }).appendTo($(tr));
          }
        });
        //console.log($(tr).find($("input[name='detalledireccion[]']")));
        //console.log(tr);//.find($("input"))
        $(tr).appendTo($('#tableLotes'));// add the new row
        if(newid>0){
          //primero.focus();
          var sel2=$("#id_localidad-"+newid)
          console.log(sel2);
          
          sel2.select2();//llamamos para inicializar select2
          //lo destruimos para que elimine las clases que arrastra de la clonacion y volvemos a inicializar
          sel2.select2('destroy');
          sel2.select2();
          sel2.css('width', '100%');
          
        }
        return tr.attr("id");
      }

      function addRowPlantadores(){
        //alert("hola");
        var newid = 0;
        var primero="";
        var ultimoRegistro=0;
        $.each($("#tablePlantadores tr"), function() {
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
        $.each($("#tablePlantadores tbody tr:nth(0) td"),function(){//loop through each td and create new elements with name of newid
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
              'text': $('#tablePlantadores tr').length
            }).appendTo($(tr));
          }
        });
        //console.log($(tr).find($("input[name='detalledireccion[]']")));
        //console.log(tr);//.find($("input"))
        $(tr).appendTo($('#tablePlantadores'));// add the new row
        if(newid>0){
          //primero.focus();
          var sel2=$("#id_categoria-"+newid)
          //console.log(sel2);
          sel2.css("width","100%");
          //sel2.select2();//llamamos para inicializar select2
          sel2.select2('destroy');//como no se iniciliaza bien lo destruimos para que elimine las clases que arrastra de la clonacion
          sel2.select2();//volvemos a inicializar y ahora si se inicializa bien
          
        }
        return tr.attr("id");
      }

    </script>
  </body>
</html>