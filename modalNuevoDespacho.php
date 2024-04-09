<div class="modal fade" id="nuevoDespacho" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1000000000000" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width: 1200px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Nuevo Despacho de <?=$data['razon_social'];?> Pedido N° <span class="idPedido"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form theme-form" id="form_nuevo_despacho" role="form" method="post" action="nuevoDespacho.php?id_cliente=<?=$id?>">
        <input name="id_pedido_despacho" id="id_pedido_despacho" type="hidden">
        <div class="modal-body">
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
                  <table class="table table-striped table-bordered" id="detallePedidoParaDespacho" style="table-layout:fixed"">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 10%;">Servicio</th>
                        <th class="text-center" style="width: 20%;">Especie</th>
                        <th class="text-center" style="width: 20%;">Procedencia</th>
                        <th class="text-center" style="width: 20%;">Material</th>
                        <th class="text-center" style="width: 10%;">Total pedido</th>
                        <th class="text-center" style="width: 20%;">Despachar/Pendiente</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                  <div class="mensajeError" style="color: red; display: none;">Por favor, ingrese al menos una cantidad mayor a 0.</div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-2">
              <label for="id_contenedor_despachar">Contenedores</label>
            </div>
            <div class="form-group col-10">
              <select name="id_contenedor_despachar[]" id="id_contenedor_despachar" class="js-example-basic-single" style="width: 100%;" multiple required>
                <option value="">- Seleccione -</option><?php
                $pdo = Database::connect();
                $sql = "SELECT c.id AS id_contenedor,tp.tipo,c.cantidad_orificios,c.ancho,c.alto,c.largo FROM contenedores c INNER JOIN tipos_contenedores tp ON c.id_tipo_contenedor=tp.id WHERE c.activo=1";
                foreach ($pdo->query($sql) as $row) {
                  $mostrar=$row["tipo"]." ".$row["cantidad_orificios"]."u. ".$row["ancho"]."x".$row["alto"]."x".$row["largo"]?>
                  <option value="<?=$row["id_contenedor"]?>"><?=$mostrar?></option><?php
                }
                Database::disconnect();?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-gropu col-12">
              <!-- HTML de la tabla -->
              <table id="tablaContenedores" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>Contenedor</th>
                    <th>Cantidad</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Aquí se agregarán dinámicamente las filas -->
                </tbody>
              </table>
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

  function getDetallePedidoParaDespacho(id_pedido){
    $.ajax({
      //data: datosIniciales,
      url: 'ajaxGetDetallePedidoNuevoDespacho.php?id_pedido='+id_pedido,
      method: "post",
      cache: false,
      contentType: false,
      processData: false,
      success: function(respuesta){
        //console.log(respuesta);
        /*Convierto en json la respuesta del servidor*/
        respuestaJson = JSON.parse(respuesta);
        console.log(respuestaJson);

        // Obtener una referencia al tbody de la tabla
        var tbody = document.querySelector('#detallePedidoParaDespacho tbody');
        tbody.innerHTML="";

        //Genero los options del select de prioridades
        respuestaJson.forEach(cultivos => {
          let id_material = cultivos.id_material;
          let id_procedencia = cultivos.id_procedencia;

          let cantidad_plantines = parseInt(cultivos.cantidad_plantines);
          let plantines_retirados = parseInt(cultivos.plantines_retirados);
          let pendiente = cantidad_plantines - plantines_retirados;

          if(pendiente==0){

          }

          // Plantilla para cada fila
          let contenidoFila = `
            <td class="align-middle">
              <input type='hidden' name="id_pedido_detalle[]" value='${cultivos.id_pedido_detalle}'>
              <input type='hidden' name="id_servicio[]" value='${cultivos.id_servicio}'>${cultivos.servicio}
            </td>
            <td class="align-middle">
              <input type='hidden' name="id_especie[]" class='id_especie' value='${cultivos.id_especie}'>${cultivos.especie}
            </td>
            <td class="align-middle">
              ${id_procedencia > 0 ? `
                <input type='hidden' name="id_procedencia[]" value='${cultivos.id_procedencia}'>${cultivos.procedencia}
              ` : `
                ${pendiente > 0 ? `
                  <select name="id_procedencia[]" class="js-example-basic-single id_procedencia" style="width:100%">
                    ${generarOpciones(cultivos.aProcedencias, 'id', 'procedencia')}
                  </select>
                ` : `
                  <input type='hidden' name="id_procedencia[]">
                `}
              `}
            </td>
            <td class="align-middle">
              ${id_material > 0 ? `
                <input type='hidden' name="id_material[]" value='${cultivos.id_material}'>${cultivos.material}
              ` : `
                ${pendiente > 0 ? `
                  <select name="id_material[]" class="js-example-basic-single id_material" style="width:100%" ${id_procedencia > 0 ? '' : 'disabled'}>
                    ${generarOpciones(cultivos.aMateriales, 'id', 'material')}
                  </select>
                ` : `
                  <input type='hidden' name="id_material[]">
                `}
              `}
            </td>
            <td class="align-middle text-right">
              ${Intl.NumberFormat("de-DE").format(cultivos.cantidad_plantines)}
            </td>
            <td class="align-middle">
              <div class="input-group">
                ${pendiente > 0 ? `
                  <input type="number" name="cantidad_despachar[]" class="form-control cantidad_despachar" value="0" required>
                ` : `
                  <input type="number" class="form-control disabled" value="0" required disabled readonly>
                  <input type="hidden" name="cantidad_despachar[]" value="0">
                `}
                <div class="input-group-append">
                  <span class="input-group-text">/ ${Intl.NumberFormat("de-DE").format(pendiente)}</span>
                  <input type="hidden" name="pendiente_despachar[]" value="${pendiente}">
                </div>
              </div>
            </td>`;

          // Crear una nueva fila
          var newRow = document.createElement('tr');
          // Insertar el contenido HTML en la nueva fila
          newRow.innerHTML = contenidoFila;
          // Agregar la fila al tbody
          tbody.appendChild(newRow);
        });


        $("#detallePedidoParaDespacho").find(".id_procedencia").select2()
        $("#detallePedidoParaDespacho").find(".id_material").select2()

      }
    });
  }

  $(document).on("keyup change",".cantidad_despachar",function(){
    if(this.value>0){
      let fila=$(this).parents("tr")
      fila.find("select").attr("required",true)

    }
  })

  // Función para generar las opciones de los select
  function generarOpciones(arr, valueKey, textKey) {
    let opciones = `<option value="">Seleccione...</option>`;
    arr.forEach(item => {
      opciones += `<option value="${item[valueKey]}">${item[textKey]}</option>`;
    });
    return opciones;
  }

  $(document).ready(function() {
    // Inicializar Select2
    //$('#selectContenedores').select2();

    // Manejar evento de selección en Select2
    $('#id_contenedor_despachar').on('select2:select', function(e) {
      var contenedorSeleccionado = e.params.data.text;
      var valorContenedor = e.params.data.id;

      console.log($('#tablaContenedores'));
      console.log($('#tablaContenedores tbody'));
      // Crear una nueva fila en la tabla
      var newRow = $('<tr>');
      newRow.append('<td>' + contenedorSeleccionado + '</td>');
      newRow.append('<td><input type="hidden" name="id_contenedor[]" value="' + valorContenedor + '">' +
                    '<label><input type="number" class="form-control" name="cantidad_contenedores[]" required placeholder="Cantidad"></label></td>');
      console.log(newRow);
      $('#tablaContenedores tbody').append(newRow);
    });

    // Manejar evento de deselección en Select2
    $('#id_contenedor_despachar').on('select2:unselect', function(e) {
      var valorContenedor = e.params.data.id;

      // Eliminar la fila correspondiente en la tabla
      $('#tablaContenedores tbody tr').each(function() {
        if ($(this).find('input[name="id_contenedor[]"]').val() == valorContenedor) {
          $(this).remove();
          return false; // Salir del bucle each
        }
      });
    });

    // Manejar evento de cambio en la cantidad
    $('#cantidadInput').on('input', function() {
      var cantidad = $(this).val();

      // Actualizar la cantidad en los labels de la tabla
      $('#tablaContenedores tbody label').text(function() {
        return $(this).prev('input').val() + ' (' + cantidad + ')';
      });
    });
  });

  function getDetallePedido2(id_pedido){
    let table=$('#detallePedidoParaDespacho')
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
            // style="width:100%"
            let selectProcedencia=`<select name="id_procedencia[]" class="js-example-basic-single id_procedencia" required>
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
            if(row.id_procedencia>0){
              disabled=""
            }
            // style="width:100%"
            let selectMaterial=`<select name="id_material[]" class="js-example-basic-single id_material" required ${disabled}>
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
      "columnDefs": [
        { "className": "dt-body-right align-middle", "targets": [4] },
        { "className": "align-middle", "targets": "_all" },
        /*{ width: "12%", targets: 0 }, // Primera columna (Servicio)
        { width: "20%", targets: 1 }, // Segunda columna (Especie)
        { width: "20%", targets: 2 }, // Tercera columna (Procedencia)
        { width: "20%", targets: 3 }, // Cuarta columna (Material)
        { width: "14%", targets: 4 }, // Quinta columna (Cantidad total pedido)
        { width: "14%", targets: 5 }  // Sexta columna (Despachar/Pendiente)*/
      ],
      initComplete: function(settings, json){
        $("#detallePedidoParaDespacho").find(".id_procedencia").select2()
        $("#detallePedidoParaDespacho").find(".id_material").select2()
        //table.css("table-layout","fixed")
      },
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

  $("#form_nuevo_despacho").submit(function(e){
    e.preventDefault()
    let hay_cantidad=0
    let cantidad_despachar=$(".cantidad_despachar")
    cantidad_despachar.each(function(){
      if(this.value>0){
        hay_cantidad=1
      }
    })

    if(hay_cantidad==1){
      this.submit();
    }else{
      // Mostrar mensaje de error
      $(this).find('.mensajeError').fadeIn();
      // Resaltar los inputs vacíos temporalmente
      cantidad_despachar.addClass('input-error');
      setTimeout(function() {
          cantidad_despachar.removeClass('input-error');
      }, 2000);
    }
  })
</script>