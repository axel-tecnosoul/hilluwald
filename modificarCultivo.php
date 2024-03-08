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
  header("Location: listarCultivos.php");
}

if ( !empty($_POST)) {
  // var_dump($_POST);
  // die;
  
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $sql = "UPDATE cultivos set nombre = ?, nombre_corto = ?, precio = ?, icono = ?, color =?, id_usuario = ? where id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['nombre'], $_POST['nombre_corto'],$_POST['precio'],$_POST['icon'],$_POST['basic-color'],$_SESSION['user']['id'],$_GET['id']));
  
  Database::disconnect();
  
  header("Location: listarCultivos.php");

} else {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT id, nombre, nombre_corto, precio, icono, color, id_usuario FROM cultivos WHERE id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);
  
  Database::disconnect();
}?>
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
                      <li class="breadcrumb-item">Modificar Cultivo</li>
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
                    <h5>Modificar Cultivo</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="modificarCultivo.php?id=<?php echo $id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Nombre</label>
                          <div class="col-sm-9"><input name="nombre" type="text" maxlength="99" class="form-control" value="<?php echo $data['nombre']; ?>" required="required"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Nombre Corto</label>
                          <div class="col-sm-9"><input name="nombre_corto" type="text" maxlength="99" class="form-control" value="<?php echo $data['nombre_corto']; ?>" required="required"></div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Precio</label>
                          <div class="col-sm-9"><input name="precio" type="text" maxlength="99" class="form-control" value="<?php echo $data['precio']; ?>" required="required"></div>
                        </div>
                        <!-- <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Icono</label>
                          <div class="col-sm-9"><?php
                            $valor_defecto = $data['icono'];?>
                            <input type="hidden" id="icono_cargado" name="icono_cargado" list="icono_cargado" value="<?=$valor_defecto?>">
                              <div class="btn-group btn-group-toggle" data-toggle="buttons"><?php
                                foreach ($aIconos as $icono) {
                                  $checked = "";
                                  if($data['icono'] == $icono){
                                    $checked = "active";
                                  }?>
                                  <label class="btn btn-outline-primary <?=$checked;?>">
                                    <input type="radio" value="<?=$icono?>" name="icon" id="icon" autocomplete="off">
                                    <i class="<?=$icono?>" aria-hidden="true"></i>
                                  </label><?php
                                }?>
                              </div>
                            </div>
                          </div>
                        </div> -->
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Icono</label>
                          <div class="col-sm-9">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons"><?php
                              foreach ($aIconos as $icono) {
                                $class = $checked = "";
                                if($data['icono'] == $icono){
                                  $checked = "checked";
                                  $class = "active";
                                }?>
                                <label class="btn btn-outline-primary <?=$class;?>">
                                  <input type="radio" value="<?=$icono?>" name="icon" id="icon" <?=$checked;?>>
                                  <i class="<?=$icono?>" aria-hidden="true"></i>
                                </label><?php
                              }?>
                            </div>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Color del icono</label>
                          <div class="col-sm-9"><?php
                            $valor_defecto = $data['color'];?>
                            <input type="color" id="basic-color" name="basic-color" list="basic-colors" value="<?=$valor_defecto?>">
                            <datalist id="basic-colors"><?php
                              foreach ($aColores as $codigo => $nombre): ?>
                                <option value="<?=$codigo?>" <?php if ($data['color'] == $codigo) echo "selected"; ?> autocomplete="off"><?=$nombre?></option><?php
                              endforeach; ?>
                            </datalist>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
                        <a href='listarCultivos.php' class="btn btn-light">Volver</a>
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
  </body>
</html>