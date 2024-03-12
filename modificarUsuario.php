<?php
    require("config.php");
    if(empty($_SESSION['user']))
    {
        header("Location: index.php");
        die("Redirecting to index.php"); 
    }
	
	require 'database.php';

	$id = null;
	if ( !empty($_GET['id'])) {
		$id = $_REQUEST['id'];
	}
	
	if ( null==$id ) {
		header("Location: listarUsuarios.php");
	}
	
	if ( !empty($_POST)) {
		
		// insert data
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql = "UPDATE `usuarios` set `usuario` = ?, `clave` = ?, nombre_apellido = ?, email = ?, `id_perfil` = ?, `id_sucursal` = ?, `activo` = ? WHERE id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($_POST['usuario'],$_POST['clave'],$_POST['nombre_apellido'], $_POST['email'],$_POST['id_perfil'],$_POST['id_sucursal'],$_POST['activo'],$_GET['id']));
		
		Database::disconnect();
		
		header("Location: listarUsuarios.php");
	
	} else {
		
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT `id`, `usuario`, `clave`, nombre_apellido, email, `id_perfil`, `id_sucursal` , `activo`FROM `usuarios` WHERE id = ? ";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		
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
                      <li class="breadcrumb-item">Modificar Usuario</li>
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
                <div class="card">
                  <div class="card-header">
                    <h5>Modificar Usuario</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="modificarUsuario.php?id=<?=$id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Usuario</label>
                            <div class="col-sm-9"><input name="usuario" type="text" maxlength="99" class="form-control" required="required" value="<?=$data['usuario']; ?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Clave</label>
                            <div class="col-sm-9"><input name="clave" type="text" maxlength="99" class="form-control" required="required" value="<?=$data['clave']; ?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nombre y Apellido</label>
                            <div class="col-sm-9"><input name="nombre_apellido" type="text" maxlength="99" class="form-control" required="required" value="<?=$data['nombre_apellido']; ?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9"><input name="email" type="email" maxlength="99" class="form-control" required="required" value="<?=$data['email']; ?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Perfil</label>
                            <div class="col-sm-9">
                              <select name="id_perfil" id="id_perfil" class="js-example-basic-single col-sm-12" required="required" onchange="jsAlmacen();">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT `id`, `perfil` FROM `perfiles` WHERE 1";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  if ($fila['id'] == $data['id_perfil']) {
                                    echo " selected ";
                                  }
                                  echo ">".$fila['perfil']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Sucursales</label>
                            <div class="col-sm-9">
                              <select name="id_sucursal" id="id_sucursal" class="js-example-basic-single col-sm-12" <?php if ($data['id_perfil']==1) echo 'disabled="disabled"'; ?>>
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT `id`, `nombre` FROM `sucursales` WHERE 1";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  if ($fila['id'] == $data['id_sucursal']) {
                                    echo " selected ";
                                  }
                                  echo ">".$fila['nombre']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Activo</label>
                            <div class="col-sm-9"><?php
                              foreach ($aOptionsActivo as $option) {?>
                                <label class="d-block" for="<?=$option["id"]?>">
                                <input type="radio" name="activo" class="radio_animated" id="<?=$option["id"]?>" value="<?=$option["value"]?>" required<?php
                                  if($option["checked"]) echo " checked";
                                  if($option["disabled"]) echo " disabled";?>
                                >
                                <label for="<?=$option["id"]?>"><?=$option["label"]?></label>
                                </label><?php
                              }?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
						            <a href='listarUsuarios.php' class="btn btn-light">Volver</a>
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
	<script>
	function jsAlmacen() {
		if (document.getElementById("id_perfil").value != 1) {
			document.getElementById("id_sucursal").disabled = "";
			document.getElementById("id_sucursal").required = "required";
		} else {
			document.getElementById("id_sucursal").disabled = "disabled";
			document.getElementById("id_sucursal").required = "";			
		}
	}
	</script>
  </body>
</html>