<?php
  require("config.php");
  if(empty($_SESSION['user']['id'])){
    header("Location: index.php");
    die("Redirecting to index.php"); 
  }
	
	require 'database.php';

	$id_cliente = null;
	if ( !empty($_GET['id'])) {
		$id_cliente = $_REQUEST['id'];
	}
	
	if ( null==$id_cliente ) {
		header("Location: listarClientes.php");
	}
	
	if ( !empty($_POST)) {
		//var_dump($_POST);
    //die;

		// insert data
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql = "UPDATE clientes set razon_social = ?, direccion = ?, telefono = ?, email = ?, cuit = ?, cond_fiscal = ?, id_usuario = ? where id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($_POST['razon_social'],$_POST['direccion'],$_POST['telefono'],$_POST['email'],$_POST['cuit'],$_POST['cond_fiscal'],$_SESSION['user']['id'],$_GET['id']));

    foreach($_POST["nombre_lotes"] as $key => $nombre_lotes){
      if($key==0 || $_POST["direccion_lotes"] == 0){
        continue;
      }
      
      if($_POST["id_lotes"][$key] == 0){
        $sql = "INSERT INTO lotes (id_cliente,nombre, direccion, id_localidad, id_usuario) VALUES (?,?,?,?,?) ";
        $q = $pdo->prepare($sql);
        $q->execute(array($id_cliente,$nombre_lotes,$_POST["direccion_lotes"][$key], $_POST["id_localidad"][$key], $_SESSION['user']['id']));
      }else{
        $sql2 = "UPDATE lotes set nombre = ?, direccion = ?, id_localidad = ?, activo = ?, id_usuario = ? WHERE id = ?";
        $q2 = $pdo->prepare($sql2);
        $q2->execute(array($nombre_lotes,$_POST["direccion_lotes"][$key], $_POST["id_localidad"][$key], $_POST["lotes_activo"][$key], $_SESSION['user']['id'], $_POST['id_lotes'][$key]));
      }
    }
    
    foreach($_POST["nombre_plantadores"] as $key => $nombre_plantadores){
      if($key==0){
        continue;
      }

      if($_POST["id_plantadores"][$key] == 0){
        $sql = "INSERT INTO plantadores (id_cliente,nombre, dni, telefono, id_usuario) VALUES (?,?,?,?,?) ";
        $q = $pdo->prepare($sql);
        $q->execute(array($id_cliente,$nombre_plantadores,$_POST["dni_plantadores"][$key],$_POST["telefono_plantadores"][$key],  $_SESSION['user']['id']));
      }else{
        $sql3 = "UPDATE plantadores set nombre = ?, dni = ?, telefono = ?, activo = ?, id_usuario = ? WHERE id = ?";
        $q3 = $pdo->prepare($sql3);
        $q3->execute(array($nombre_plantadores,$_POST["dni_plantadores"][$key],$_POST["telefono_plantadores"][$key],$_POST["plantadores_activo"][$key],$_SESSION['user']['id'], $_POST['id_plantadores'][$key]));
      }
    }
		
		Database::disconnect();
		
		header("Location: listarClientes.php");
	
	} else {
		
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT c.id, c.razon_social, c.direccion, c.telefono, c.email, c.cuit, c.cond_fiscal, c.activo, c.id_usuario FROM clientes c WHERE c.id = ? ";
		$q = $pdo->prepare($sql);
		$q->execute(array($id_cliente));
		$data = $q->fetch(PDO::FETCH_ASSOC);
    
    $lotes[]=[
      "id_lotes"  =>0,
      "nombre" =>"",
      "direccion" =>"",
      "id_localidad" =>"",
      "lotes_activo" => ""
    ];

    $sql = "SELECT id,nombre,direccion, id_localidad, activo FROM `lotes` WHERE id_cliente = ".$id_cliente;
    foreach ($pdo->query($sql) as $row) {
        $lotes[]=[
          "id_lotes" =>$row["id"],
          "nombre" =>$row["nombre"],
          "direccion" =>$row["direccion"],
          "id_localidad" =>$row["id_localidad"],
          "lotes_activo" =>$row["activo"]
        ];
    }

    $plantadores[]=[
      "id_plantadores" =>0,
      "nombre" =>"",
      "dni" =>"",
      "telefono" =>"",
      "plantadores_activo" =>""
    ];

    $sql = "SELECT id, nombre, dni, telefono, activo FROM `plantadores` WHERE id_cliente = ".$id_cliente;
    foreach ($pdo->query($sql) as $row) {
        $plantadores[]=[
          "id_plantadores" =>$row["id"],
          "nombre" =>$row["nombre"],
          "dni" =>$row["dni"],
          "telefono" =>$row["telefono"],
          "plantadores_activo" =>$row["activo"]
        ];
    }
    
		Database::disconnect();

    $aOptionsActivo = [
      [
        "value"=>0,
        "id"=>"activo_no",
        "label"=>"No",
        "checked"=>$data['activo'] ==0? true : false,
        "disabled" => false,
      ],
      [
        "value" => 1,
        "id" => "activo_si",
        "label" => "Si",
        "checked" => $data['activo'] ==1 ? true : false,
        "disabled" => false,
      ],
    ];
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
                      <li class="breadcrumb-item">Modificar Cliente</li>
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
                    <h5>Modificar Cliente</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="modificarCliente.php?id=<?php echo $id_cliente?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <div class="form-group col-4">
                              <label for="razon_social">Razon Social</label>
                              <input type="text" class="form-control" id="razon_social" name="razon_social" aria-describedby="Razon Social" placeholder="Ingrese la Razon Social" value="<?= $data['razon_social']; ?>">
                              <!-- <small id="razon_social" class="form-text text-muted">Razon Social</small> -->
                            </div>

                            <div class="form-group col-4">
                              <label for="direccion">Direccion</label>
                              <input type="text" class="form-control" id="direccion" name="direccion" aria-describedby="direccion" placeholder="Introduzca el direccion" value="<?= $data['direccion']; ?>">
                              <!-- <small id="direccion" class="form-text text-muted">We'll never share your text with anyone else.</small> -->
                            </div>

                            <div class="form-group col-4">
                              <label for="telefono">Telefono</label>
                              <input type="text" class="form-control" id="telefono" name="telefono" aria-describedby="telefono" placeholder="Introduzca el telefono" value="<?= $data['telefono']; ?>">
                              <!-- <small id="direccion" class="form-text text-muted">We'll never share your text with anyone else.</small> -->
                            </div>

                            <div class="form-group col-4">
                              <label for="email">E-mail</label>
                              <input type="email" class="form-control" id="email" name="email" aria-describedby="email" placeholder="Introduzca el email" value="<?= $data['email']; ?>">
                              <!-- <small id="direccion" class="form-text text-muted">We'll never share your text with anyone else.</small> -->
                            </div>

                            <div class="form-group col-4">
                              <label for="cuit">CUIT</label>
                              <input type="text" class="form-control" id="cuit" name="cuit" aria-describedby="cuit" placeholder="Introduzca el CUIT" value="<?= $data['cuit']; ?>">
                              <!-- <small id="cuit" class="form-text text-muted">We'll never share your text with anyone else.</small> -->
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
                                    <th>Localidad</th>
                                    <th>Activo</th>
                                  </tr>
                                </thead>
                                <tbody><?php
                                foreach ($lotes as $clave => $valor) {
                                    $style="";
                                    if ($clave==0 || $valor['lotes_activo'] == 0) {
                                        $style="display:none";
                                    } ?>
                                  <tr id='addr<?=$clave?>' data-id="<?=$clave?>" style="<?=$style?>">

                                    <td data-name="nombre">
                                      <input type="hidden" class="form-control" name="id_lotes[]" value="<?= $valor['id_lotes']; ?>"id="id_lotes-<?=$clave?>">
                                      <input type="text" class="form-control" placeholder="Nombre" name="nombre_lotes[]" value="<?=$valor['nombre']?>" id="nombre-<?=$clave?>" data-required="1"/>
                                    </td>
                                    <td data-name="direccion">
                                      <input type="text" class="form-control" placeholder="Direccion" name="direccion_lotes[]" value="<?=$valor['direccion']?>" id="direccion-<?=$clave?>" data-required="1"/>
                                    </td>
                                    <td data-name="localidad">
                                      <select class="form-control"  name="id_localidad[]" id="id_localidad-0" class="js-example-basic-single col-sm-12 ">
                                        <option value="">Seleccione...</option><?php
                                        $pdo = Database::connect();
                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                        $sqlZon = "SELECT l.id, l.localidad, p.provincia FROM `localidades` l left join provincias p on l.id = p.id  WHERE 1";
                                        $q = $pdo->prepare($sqlZon);
                                        $q->execute();
                                        while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                          echo "<option value='".$fila['id']."'";
                                          if ($fila['id'] == $valor['id_localidad']) {
                                            echo " selected ";
                                          }
                                          
                                          echo ">".$fila['localidad'] . " - " . $fila['provincia']."</option>";
                                        }
                                        Database::disconnect();
                                        ?>
                                      </select>
                                    </td>
                                    <td data-name="activo">
                                      <select class="form-control" name="lotes_activo[]" id="lotes_activo-<?=$clave?>" class="js-example-basic-hide-search">
                                        <option value="1" <?php if ($valor['lotes_activo']==1) echo " selected ";?>>Si</option>
                                        <option value="0" <?php if ($valor['lotes_activo']==0) echo " selected ";?>>No</option>
                                      </select>
                                    </td>
                                  </tr><?php
                                }?>
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
                                    <th>Activo</th>
                                  </tr>
                                </thead>
                                <tbody><?php
                                foreach ($plantadores as $clave => $valor) {
                                    $style="";
                                    if ($clave==0 || $valor['plantadores_activo'] == 0) {
                                        $style="display:none";
                                    } ?>
                                  <tr id='addr<?=$clave?>' data-id="<?=$clave?>" style="<?=$style?>">
                                    <td data-name="nombre">
                                      <input type="text" class="form-control" placeholder="Nombre" name="nombre_plantadores[]" value="<?=$valor['nombre']?>" id="nombre-<?=$clave?>" data-required="1"/>
                                    </td>
                                    <td data-name="dni">
                                      <input type="text" class="form-control" placeholder="DNI" name="dni_plantadores[]" value="<?=$valor['dni']?>" id="dni-<?=$clave?>" data-required="1"/>
                                    </td>
                                    <td data-name="telefono">
                                      <input type="text" class="form-control" placeholder="Telefono" name="telefono_plantadores[]" value="<?=$valor['telefono']?>" id="telefono-<?=$clave?>" data-required="0"/>
                                    </td>
                
                                    <td data-name="activo">
                                      <select name="plantadores_activo[]" class="form-control" id="plantadores_activo-<?=$clave?>" class="js-example-basic-hide-search">
                                      <option value="1" <?php if ($valor['plantadores_activo']==1) echo " selected ";?>>Si</option>
                                      <option value="0" <?php if ($valor['plantadores_activo']==0) echo " selected ";?>>No</option>
                                      </select>
                                    </td>
                                    <input type="hidden" class="form-control" name="id_plantadores[]" value="<?= $valor['id_plantadores']; ?>" id="id_plantadores-<?=$clave?>">
                                  </tr><?php
                                }?>
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <td colspan="4" align='right'>
                                      <input type="button" class="btn btn-dark " id="addRowPlantadores" value="Agregar Plantadores">
                                      <input type="hidden" name="eliminar_plantadores" id="eliminar_plantadores">
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
                        <button class="btn btn-primary" type="submit">Guardar</button>
						            <a href="listarClientes.php" class="btn btn-light">Volver</a>
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
    <script type="text/javascript">

      $(document).ready(function(){
          $("#addRowLotes").on('click', function(event) {
            event.preventDefault();
            addRowLotes();
          });

          $("#addRowPlantadores").on('click', function(event) {
            event.preventDefault();
            addRowPlantadores();
          });
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
          primero.focus();
          var sel2=$("#nombre_lotes-"+newid)
          //console.log(sel2);
          
          sel2.select2();//llamamos para inicializar select2
          sel2.select2('destroy');//como no se iniciliaza bien lo destruimos para que elimine las clases que arrastra de la clonacion
          sel2.select2();//volvemos a inicializar y ahora si se inicializa bien
          
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
          primero.focus();
          var sel2=$("#nombre_plantadores-"+newid)
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