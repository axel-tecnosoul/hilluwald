<div class="page-sidebar">
  <div class="main-header-left d-none d-lg-block">
	<div class="logo-wrapper"><a href="dashboard.php"><img src="assets/images/tu logo azul.jpg" width="80px" alt=""></a></div>
  </div>
  <div class="sidebar custom-scrollbar">
	<ul class="sidebar-menu">
		
		<?php
    $id_perfil=$_SESSION['user']['id_perfil'];?>
		
    <li><a class="sidebar-header" href="listarVentas.php"><i data-feather="file"></i><span>Ventas</span><i class="fa fa-angle-right pull-right"></i></a></li>

    <li><a class="sidebar-header" href="listarClientes.php"><i data-feather="user"></i><span>Clientes</span><i class="fa fa-angle-right pull-right"></i></a></li>

    <li><a class="sidebar-header" href="listarProductos.php"><i data-feather="clipboard"></i><span>Productos</span><i class="fa fa-angle-right pull-right"></i></a></li>

    <li><a class="sidebar-header" href="#"><i data-feather="database"></i><span>Maestros</span><i class="fa fa-angle-right pull-right"></i></a>
        <ul class="sidebar-submenu">
          <li><a href="listarSucursales.php"><i class="fa fa-circle"></i>Sucursales</a></li>
          <li><a href="listarChoferes.php"><i class="fa fa-circle"></i>Choferes</a></li>
          <li><a href="listarCultivos.php"><i class="fa fa-circle"></i>Cultivos</a></li>
          <li><a href="listarTransportes.php"><i class="fa fa-circle"></i>Transportes</a></li>
          <li><a href="listarUsuarios.php"><i class="fa fa-circle"></i>Usuarios</a></li>
        </ul>
      </li>

    <li><a class="sidebar-header" href="listarParametros.php"><i data-feather="settings"></i><span>Configuraciones</span><i class="fa fa-angle-right pull-right"></i></a></li>

	</ul>
  </div>
</div>