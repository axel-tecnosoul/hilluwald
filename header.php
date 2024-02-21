<div class="page-main-header">
        <div class="main-header-right row">
          <div class="main-header-left d-lg-none">
            <div class="logo-wrapper"><a href="dashboard.php"></a></div>
          </div>
          <div class="mobile-sidebar d-block">
            <div class="media-body text-right switch-sm">
              <label class="switch"><a href="#"><i id="sidebar-toggle" data-feather="align-left"></i></a></label>
            </div>
          </div>
          <div class="nav-right col p-0">
            <ul class="nav-menus">
              <li style="text-align: left;width:100%"><?php
                $vencimientoCRT="2024-09-26 10:55:18";
                //$vencimientoCRT="2023-04-26 10:55:18";//pruebas
                $fechaActual=date("Y-m-d");
                $fechaVencimiento=date("Y-m-d",strtotime($vencimientoCRT."- 1 month"));
                if(strtotime($fechaActual)>strtotime($fechaVencimiento)){
                  setlocale(LC_TIME, "es_AR");
                  $dia=date("d",strtotime($vencimientoCRT));
                  $mes=date("F",strtotime($vencimientoCRT));
                  $anio=date("Y",strtotime($vencimientoCRT));
                  $hora=date("H:i:s",strtotime($vencimientoCRT));
                  $fecha_formateada=$dia." de ".$mes." de ".$anio." a las ".$hora;
                  //echo "$fechaActual es mayor a $fechaVencimiento.";?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 0;padding: 7px;">
                    <strong>Atencion!</strong> El <?=$fecha_formateada//echo strftime("%d de %B de %Y a las %H:%M:%S",strtotime($vencimientoCRT))?> vence el certificado de AFIP que permite la facturacion electronica. Por favor pongase en contacto con el desarrollador del sistema para generar un nuevo.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                  </div><?php
                }else{
                  //echo "$fechaActual NO es mayor a $fechaVencimiento";
                }?>
              </li>
              <li class="onhover-dropdown">
                  <h6><b><?php echo $_SESSION['user']['usuario']?></b></h6>
              </li>
              <li class="onhover-dropdown">
                <div class="media align-items-center"><a href="logout.php"><img class="align-self-center pull-right rounded-circle" src="assets/images/cerrar-sesion.png" width="25px" alt="header-user"></a>
                </div>
              </li>
            </ul>
            <div class="d-lg-none mobile-toggle pull-right"><i data-feather="more-horizontal"></i></div>
          </div>
        </div>
      </div>