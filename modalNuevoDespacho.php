<div class="modal fade" id="nuevoDespacho" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1000000000000" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width: 1200px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Nuevo Despacho de <?=$data['razon_social'];?> Pedido N° <span class="idPedido"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form theme-form" role="form" method="post" action="nuevoDespacho.php?id_cliente=<?=$id?>">
        <input name="id_pedido_despacho" id="id_pedido_despacho" type="hidden">
        <div class="modal-body">
          <!-- <div class="row">
            <div class="form-group col-4">
              <label for="fecha_despacho">Fecha</label>
              <input name="fecha_despacho" id="fecha_despacho" type="date" class="form-control multiselect" value="<?=$hoy?>" required>
            </div>
            <div class="form-group col-4">
              <label for="campana_despacho">Campaña</label><br>
              <select name="campana_despacho" id="campana_despacho" style="width: 100%;" required class="js-example-basic-single"><?php
              // data-style="multiselect" data-live-search="true"
                // Generar las opciones del select
                for ($i = $anio_inicial; $i <= $anio_final; $i++) {
                  // Si el año es el actual, marcarlo como seleccionado por defecto
                  $selected = ($i == $anio_actual) ? "selected" : "";
                  echo "<option value='$i' $selected>$i</option>";
                }?>
              </select>
            </div>
            <div class="form-group col-4">
              <label for="id_cliente_retira">Razon social</label>
              <select name="id_cliente_retira" id="id_cliente_retira" style="width: 100%;" required class="js-example-basic-single"><?php
                $pdo = Database::connect();
                $sql = " SELECT id, razon_social FROM clientes";
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>"><?=$row["razon_social"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-4">
              <label>Transporte</label>
              <select name="id_transporte" id="id_transporte" class="js-example-basic-single" required style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, razon_social FROM transportes";
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>"><?=$row["razon_social"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-4">
              <label for="id_chofer">Chofer</label>
              <select name="id_chofer" id="id_chofer" class="js-example-basic-single" required disabled style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, nombre_apellido, id_transporte FROM choferes";
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>" data-id-transporte="<?=$row["id_transporte"]?>"><?=$row["nombre_apellido"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-4">
              <label for="id_vehiculo">Vehiculo</label>
              <select name="id_vehiculo" id="id_vehiculo" class="js-example-basic-single" required disabled style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, descripcion, patente, patente2, id_transporte FROM vehiculos";
                foreach ($pdo->query($sql) as $row) {
                  $patente=$row["patente"];
                  if(!is_null($row["patente2"])){
                    $patente.=" - ".$row["patente2"];
                  }
                  $mostrar=$row["descripcion"]." (".$patente.")"?>
                  <option value="<?=$row["id"]?>" data-id-transporte="<?=$row["id_transporte"]?>"><?=$mostrar?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-4">
              <label>Lote</label>
              <select name="id_lote" id="id_lote" class="js-example-basic-single" style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT l.id, nombre, direccion, localidad, provincia FROM lotes l INNER JOIN localidades l2 ON l.id_localidad=l2.id INNER JOIN provincias p ON l2.id_provincia=p.id WHERE l.id_cliente=".$id;
                foreach ($pdo->query($sql) as $row) {
                  $mostrar=$row["nombre"]." (".$row["direccion"]." ".$row["localidad"]." ".$row["provincia"].")"?>
                  <option value="<?=$row["id"]?>"><?=$mostrar?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-4">
              <label for="id_plantador">Plantador</label>
              <select name="id_plantador" id="id_plantador" class="js-example-basic-single" style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, nombre FROM plantadores WHERE id_cliente=".$id;
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>"><?=$row["nombre"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-4">
              <label for="id_vehiculo">Patente 2</label>
              <input type="text" name="patente2" id="patente2" class="form-control" style="width: 100%;">
            </div>
          </div>
          <div class="row">
            <div class="form-group col-4">
              <label for="id_provincia">Provincia</label>
              <select name="id_provincia" id="id_provincia" class="js-example-basic-single" style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, provincia FROM provincias";
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>"><?=$row["provincia"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-4">
              <label for="id_localidad">Localidad</label>
              <select name="id_localidad" id="id_localidad" class="js-example-basic-single" style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, localidad, id_provincia FROM localidades";
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>" data-id-provincia="<?=$row["id_provincia"]?>"><?=$row["localidad"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-4">
              <label for="id_vehiculo">Lugar de entrega</label>
              <input type="text" name="lugar_entrega" id="lugar_entrega" class="form-control" style="width: 100%;">
            </div>
          </div>
          <div class="row">
            <div class="form-group col-2">
              <label for="observaciones_pedido" class="col-form-label">Observaciones:</label>
            </div>
            <div class="form-group col-10">
              <textarea name="observaciones_pedido" id="observaciones_pedido" class="form-control"></textarea>
            </div>
          </div> -->
          <div class="row">
            <div class="form-group col-4">
              <label for="fecha_despacho">Fecha</label>
              <input name="fecha_despacho" id="fecha_despacho" type="date" class="form-control multiselect" value="<?=$hoy?>" required>
            </div>
            <div class="form-group col-4">
              <label for="campana_despacho">Campaña</label><br>
              <select name="campana_despacho" id="campana_despacho" style="width: 100%;" required class="js-example-basic-single"><?php
              // data-style="multiselect" data-live-search="true"
                // Generar las opciones del select
                for ($i = $anio_inicial; $i <= $anio_final; $i++) {
                  // Si el año es el actual, marcarlo como seleccionado por defecto
                  $selected = ($i == $anio_actual) ? "selected" : "";
                  echo "<option value='$i' $selected>$i</option>";
                }?>
              </select>
            </div>
            <div class="form-group col-4">
              <label for="id_cliente_retira">Razon social</label>
              <select name="id_cliente_retira" id="id_cliente_retira" style="width: 100%;" required class="js-example-basic-single">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, razon_social FROM clientes";
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>"><?=$row["razon_social"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-3">
              <label>Transporte</label>
              <select name="id_transporte" id="id_transporte" class="js-example-basic-single" required style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, razon_social FROM transportes";
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>"><?=$row["razon_social"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-3">
              <label for="id_chofer">Chofer</label>
              <select name="id_chofer" id="id_chofer" class="js-example-basic-single" required disabled style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, nombre_apellido, id_transporte FROM choferes";
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>" data-id-transporte="<?=$row["id_transporte"]?>"><?=$row["nombre_apellido"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-3">
              <label for="id_vehiculo">Vehiculo</label>
              <select name="id_vehiculo" id="id_vehiculo" class="js-example-basic-single" required disabled style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, descripcion, patente, patente2, id_transporte FROM vehiculos";
                foreach ($pdo->query($sql) as $row) {
                  $patente=$row["patente"];
                  if(!is_null($row["patente2"])){
                    $patente.=" - ".$row["patente2"];
                  }
                  $mostrar=$row["descripcion"]." (".$patente.")"?>
                  <option value="<?=$row["id"]?>" data-id-transporte="<?=$row["id_transporte"]?>" data-patente2="<?=$row["patente2"]?>"><?=$mostrar?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-3">
              <label for="id_vehiculo">Patente 2</label>
              <input type="text" name="patente2" id="patente2" class="form-control" style="width: 100%;">
            </div>
          </div>
          <div class="row">
            <!-- <div class="form-group col-3">
              <label for="id_provincia">Provincia</label>
              <select name="id_provincia" id="id_provincia" class="js-example-basic-single" style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                /*$pdo = Database::connect();
                $sql = " SELECT id, provincia FROM provincias";
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>"><?=$row["provincia"]?></option><?php
                }
                Database::disconnect();*/?>
              </select>
            </div> -->
            <div class="form-group col-3">
              <label for="id_localidad">Localidad</label>
              <select name="id_localidad" id="id_localidad" class="js-example-basic-single" style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT l.id, l.localidad, p.provincia FROM localidades l INNER JOIN provincias p ON l.id_provincia=p.id";
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>"><?=$row["localidad"]." - ".$row["provincia"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-3">
              <label>Lote</label>
              <select name="id_lote" id="id_lote" class="js-example-basic-single" style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT l.id, nombre, direccion, localidad, provincia FROM lotes l INNER JOIN localidades l2 ON l.id_localidad=l2.id INNER JOIN provincias p ON l2.id_provincia=p.id WHERE l.id_cliente=".$id;
                foreach ($pdo->query($sql) as $row) {
                  $mostrar=$row["nombre"]." (".$row["direccion"]." ".$row["localidad"]." ".$row["provincia"].")"?>
                  <option value="<?=$row["id"]?>"><?=$mostrar?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
            <div class="form-group col-3">
              <label for="id_vehiculo">Lugar de entrega</label>
              <input type="text" name="lugar_entrega" id="lugar_entrega" class="form-control" style="width: 100%;">
            </div>
            <div class="form-group col-3">
              <label for="id_plantador">Plantador</label>
              <select name="id_plantador" id="id_plantador" class="js-example-basic-single" style="width: 100%;">
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = " SELECT id, nombre FROM plantadores WHERE id_cliente=".$id;
                foreach ($pdo->query($sql) as $row) {?>
                  <option value="<?=$row["id"]?>"><?=$row["nombre"]?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-2">
              <label for="observaciones_despacho" class="col-form-label">Observaciones:</label>
            </div>
            <div class="form-group col-10">
              <textarea name="observaciones_despacho" id="observaciones_despacho" class="form-control"></textarea>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="form-group row">
                <div class="col-sm-12">
                  <table class="table table-striped table-bordered" id="detallePedido" style="table-layout: fixed;">
                    <thead>
                      <tr>
                        <th style="width: 12%;">Servicio</th>
                        <th style="width: 20%;">Especie</th>
                        <th style="width: 20%;">Procedencia</th>
                        <th style="width: 20%;">Material</th>
                        <th style="width: 14%;">Cantidad total pedido</th>
                        <th style="width: 14%;">Despachar/Pendiente</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                  <div class="mensajeError" style="color: red; display: none;">Por favor, ingrese al menos una cantidad.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="col-12 text-center">
            <button class="btn btn-primary" type="submit">Crear</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>

  function getDetallePedido(id_pedido){
    let table=$('#detallePedido')
    table.DataTable().destroy();
    table.DataTable({
      //dom: 'rtip',
      //serverSide: true,
      processing: true,
      ajax:{url:'ajaxGetDetallePedidoNuevoDespacho.php?id_pedido='+id_pedido,dataSrc:""},
      stateSave: true,
      //responsive: true,

      dom: 'rtip',
      ordering: false,
      paginate: false,
      //scrollY: '100vh',
      scrollCollapse: true,
      
      language: {
        "decimal": "",
        "emptyTable": "No hay información",
        "info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
        "infoEmpty": "Mostrando 0 to 0 of 0 Registros",
        "infoFiltered": "(Filtrado de _MAX_ total registros)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Mostrar _MENU_ Registros",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "search": "Buscar:",
        "zeroRecords": "No hay resultados",
        "paginate": {
            "first": "Primero",
            "last": "Ultimo",
            "next": "Siguiente",
            "previous": "Anterior"
        }
      },
      "columns":[
        {render: function(data, type, row, meta) {
          return `<input type='hidden' name="id_servicio[]" value='${row.id_servicio}'>`+row.servicio;
        }},
        {render: function(data, type, row, meta) {
          return `<input type='hidden' name="id_especie[]" class='id_especie' value='${row.id_especie}'>`+row.especie;
        }},
        {render: function(data, type, row, meta) {
          let id_procedencia=row.id_procedencia;
          let clase;
          if(id_procedencia>0){
            return `<input type='hidden' name="id_procedencia[]" value='${row.id_procedencia}'>`+row.procedencia;
          }else{
            let selectProcedencia=`<select name="id_procedencia[]" class="js-example-basic-single id_procedencia" required style="width:100%">
              <option value="">Seleccione...</option>`;
              row.aProcedencias.forEach((procedencia)=>{
                selectProcedencia+=`<option value="${procedencia.id}">${procedencia.procedencia}</option>`;
              });
              selectProcedencia+=`</select>`;

            return selectProcedencia;
          }
        }},
        {render: function(data, type, row, meta) {
          let id_material=row.id_material;
          let clase;
          if(id_material>0){
            return `<input type='hidden' name="id_material[]" value='${row.id_material}'>`+row.material;
          }else{
            let disabled="disabled";
            console.log(row.id_procedencia);
            if(row.id_procedencia>0){
              disabled=""
            }
            let selectMaterial=`<select name="id_material[]" class="js-example-basic-single id_material" required ${disabled} style="width:100%">
              <option value="">Seleccione una procedencia...</option>`;
              row.aMateriales.forEach((material)=>{
                selectMaterial+=`<option value="${material.id}">${material.material}</option>`;
              });
              selectMaterial+=`</select>`;
            return selectMaterial;
          }
        }},
        {render: function(data, type, row, meta) {
          return Intl.NumberFormat("de-DE").format(row.cantidad_plantines);
        }},
        {render: function(data, type, row, meta) {
          let cantidad_plantines=parseInt(row.cantidad_plantines);
          let plantines_retirados=parseInt(row.plantines_retirados);
          let pendiente=cantidad_plantines-plantines_retirados
          return `<div class="input-group">
            <input type="number" name="cantidad_despachar[]" class="form-control" required>
            <div class="input-group-append">
              <span class="input-group-text">/ ${Intl.NumberFormat("de-DE").format(pendiente)}</span>
            </div>
          </div>`;
        }},
      ],
      initComplete: function(settings, json){
        $("#detallePedido").find(".id_procedencia").select2()
        $("#detallePedido").find(".id_material").select2()
      },
      "columnDefs": [
        { "className": "dt-body-right align-middle", "targets": [4] },
        { "className": "align-middle", "targets": "_all" },
      ],
    })
  }

  $("#id_transporte").on("change",function(){
    let id_transporte=$(this).val();
    getChoferes(id_transporte);
    getVehiculos(id_transporte);
  })

  function getChoferes(id_transporte){
    let selectChofer=$("#id_chofer")
    selectChofer.val("").change()
    if(id_transporte>0){
      selectChofer.attr("disabled",false)
      selectChofer.find("option").each(function(){
        $(this).attr("disabled",true);
        if(this.value=="" || this.dataset.idTransporte==id_transporte){
          $(this).attr("disabled",false);
        }
        
      })
      
      // Destruir y volver a aplicar Select2
      selectChofer.select2('destroy');
      selectChofer.select2();
    }else{
      selectChofer.attr("disabled",true)
      selectChofer.val("").change()
    }
  }

  function getVehiculos(id_transporte){
    let selectVehiculo=$("#id_vehiculo")
    selectVehiculo.val("").change()
    if(id_transporte>0){
      selectVehiculo.attr("disabled",false)
      selectVehiculo.find("option").each(function(){
        $(this).attr("disabled",true);
        if(this.value=="" || this.dataset.idTransporte==id_transporte){
          $(this).attr("disabled",false);
        }
        
      })
      
      // Destruir y volver a aplicar Select2
      selectVehiculo.select2('destroy');
      selectVehiculo.select2();
    }else{
      selectVehiculo.attr("disabled",true)
    }
    resetPatente2()
  }

  function resetPatente2(){
    var patente2 = $("#id_vehiculo").find('option:selected').data('patente2');
    $("#patente2").val(patente2)
    console.log(patente2)
  }

  $("#id_vehiculo").on("change",function(){
    resetPatente2()
  })
</script>