<div class="page-sidebar">
  <div class="main-header-left d-none d-lg-block">
	<div class="logo-wrapper"><a href="dashboard.php"><img src="assets/images/tu logo azul.jpg" width="80px" alt=""></a></div>
  </div>
  <div class="sidebar custom-scrollbar">
	<ul class="sidebar-menu">
		
		<?php
    $id_perfil=$_SESSION['user']['id_perfil'];?>
		
    <li><a class="sidebar-header" href="listarPedidos.php"><i data-feather="file"></i><span>Pedidos</span><i class="fa fa-angle-right pull-right"></i></a></li>

    <li><a class="sidebar-header" href="listarClientes.php"><i data-feather="user"></i><span>Clientes</span><i class="fa fa-angle-right pull-right"></i></a></li>

    <li><a class="sidebar-header" href="listarProductos.php"><i data-feather="clipboard"></i><span>Productos</span><i class="fa fa-angle-right pull-right"></i></a></li>

    <li><a class="sidebar-header" href="listarParametros.php"><i data-feather="settings"></i><span>Configuraciones</span><i class="fa fa-angle-right pull-right"></i></a></li>

	</ul>
  </div>
</div>