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

if ( !empty($_POST)) {
  
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $sql = "UPDATE clientes set razon_social = ?, direccion = ?, cuit = ?, cond_fiscal = ?, email = ?, telefono = ?, fecha_alta = ?, activo = ?, id_usuario = ? where id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['razon_social'],$_POST['direccion'],str_replace("-","",$_POST['cuit']),$_POST["cond_fiscal"],$_POST['email'],$_POST['telefono'],$_POST['fecha_alta'],$_POST['activo'],$_SESSION["user"]["id"],$_GET['id']));
  
  Database::disconnect();
  
  header("Location: listarClientes.php");

} else {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT razon_social, direccion, cuit, cond_fiscal, email, telefono, fecha_alta, activo FROM clientes WHERE id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);
  
  Database::disconnect();

  $aOptionsActivo=[
    [
      "value"=>0,
      "id"=>"activo_no",
      "label"=>"No",
      "checked"=>$data["activo"]==0 ? true : false,
      "disabled"=>false,
    ],[
      "value"=>1,
      "id"=>"activo_si",
      "label"=>"Si",
      "checked"=>$data["activo"]==1 ? true : false,
      "disabled"=>false,
    ]
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
				          <form class="form theme-form" role="form" method="post" action="modificarCliente.php?id=<?php echo $id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Razon Social</label>
                            <div class="col-sm-9"><input name="razon_social" type="text" maxlength="99" class="form-control" value="<?=$data['razon_social']?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">CUIT</label>
                            <div class="col-sm-9"><input name="cuit" type="text" maxlength="25" class="form-control" value="<?=$data['cuit']?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Condicion Fiscal</label>
                            <div class="col-sm-9">
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
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Direccion</label>
                            <div class="col-sm-9"><input name="direccion" type="text" maxlength="199" class="form-control" value="<?=$data['direccion']?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">E-Mail</label>
                            <div class="col-sm-9"><input name="email" type="email" maxlength="99" class="form-control" value="<?=$data['email']?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Teléfono</label>
                            <div class="col-sm-9"><input name="telefono" type="text" maxlength="99" class="form-control" value="<?=$data['telefono']?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha de alta</label>
                            <div class="col-sm-9"><input name="fecha_alta" type="date" maxlength="99" class="form-control" value="<?=$data['fecha_alta']?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Activo</label>
                            <div class="col-sm-9"><?php
                              foreach ($aOptionsActivo as $option) {?>
                                <label class="d-block" for="<?=$option["id"]?>">
                                  <input type="radio" name="activo" class="radio_animated" id="<?=$option["id"]?>" value="<?=$option["value"]?>" required<?php
                                    if($option["checked"]) echo "checked";
                                    if($option["disabled"]) echo "disabled";?>
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
						            <a href='listarClientes.php' class="btn btn-light">Volver</a>
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
  </body>
</html>