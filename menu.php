<div class="page-sidebar">
  <div class="main-header-left d-none d-lg-block">
	<div class="logo-wrapper"><a href="dashboard.php"><img src="assets/images/logoBackendHilluwald.png" width="200px" alt=""></a></div>
  </div>
  <div class="sidebar custom-scrollbar">
	<ul class="sidebar-menu">
		
		<?php
    $id_perfil=$_SESSION['user']['id_perfil'];?>
		
    <li><a class="sidebar-header" href="listarPedidos.php"><i data-feather="file"></i><span>Pedidos</span><i class="fa fa-angle-right pull-right"></i></a></li>

    <li><a class="sidebar-header" href="listarClientes.php"><i data-feather="user"></i><span>Clientes</span><i class="fa fa-angle-right pull-right"></i></a></li>

    <!-- <li><a class="sidebar-header" href="listarProductos.php"><i data-feather="clipboard"></i><span>Productos</span><i class="fa fa-angle-right pull-right"></i></a></li> -->

    <li><a class="sidebar-header" href="#"><i data-feather="database"></i><span>Maestros</span><i class="fa fa-angle-right pull-right"></i></a>
        <ul class="sidebar-submenu">
          <li><a href="listarSucursales.php"><i class="fa fa-circle"></i>Sucursales</a></li>
          <li><a href="listarContenedores.php"><i class="fa fa-circle"></i>Contenedores</a></li>
          <li><a href="listarTiposContenedores.php"><i class="fa fa-circle"></i>Tipo de Contenedores</a></li>
          <li><a href="listarCultivos.php"><i class="fa fa-circle"></i>Cultivos</a></li>
          <li><a href="listarTransportes.php"><i class="fa fa-circle"></i>Transportes</a></li>
          <li><a href="listarProcedenciasEspecies.php"><i class="fa fa-circle"></i>Procedencias de Especies</a></li>
          <li><a href="listarEspecies.php"><i class="fa fa-circle"></i>Especies</a></li>
          <li><a href="listarLocalidades.php"><i class="fa fa-circle"></i>Localidades</a></li>
          <li><a href="listarProvincias.php"><i class="fa fa-circle"></i>Provincias</a></li>
          <li><a href="listarPaises.php"><i class="fa fa-circle"></i>Paises</a></li>
          <li><a href="listarUsuarios.php"><i class="fa fa-circle"></i>Usuarios</a></li>
        </ul>
      </li>

    <!-- <li><a class="sidebar-header" href="listarParametros.php"><i data-feather="settings"></i><span>Configuraciones</span><i class="fa fa-angle-right pull-right"></i></a></li> -->

	</ul>
  </div>
</div>