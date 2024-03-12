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
  header("Location: listarBandejas.php");
}

if ( !empty($_POST)) {
  
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $sql = "UPDATE bandejas set cantidad_orificios = ?, alto = ?, ancho = ?, activo = ?, id_usuario = ? where id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['cantidad_orificios'],$_POST['alto'], $_POST['ancho'], $_POST['activo'], $_SESSION["user"]["id"], $_GET['id']));
  
  Database::disconnect();
  
  header("Location: listarBandejas.php");

} else {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT cantidad_orificios, alto, ancho, activo FROM bandejas WHERE id = ? ";
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
                      <li class="breadcrumb-item">Modificar Bandeja</li>
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
                    <h5>Modificar Bandeja</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="modificarBandeja.php?id=<?php echo $id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cantidad de Orificios</label>
                            <div class="col-sm-9"><input name="cantidad_orificios" type="text" maxlength="99" class="form-control" value="<?=$data['cantidad_orificios']?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Ancho</label>
                            <div class="col-sm-9"><input name="ancho" type="text" maxlength="25" class="form-control" value="<?=$data['ancho']?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Alto</label>
                            <div class="col-sm-9"><input name="alto" type="text" maxlength="199" class="form-control" value="<?=$data['alto']?>" required></div>
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
						            <a href='listarBandejas.php' class="btn btn-light">Volver</a>
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