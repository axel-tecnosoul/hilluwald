<div class="modal fade" id="nuevaDevolucionContenedores" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1000000000000" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width: 1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Nueva Devolucion de Contenedores de <?=$data['razon_social'];?> Despacho N° <span class="idDespacho"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form theme-form" id="form_nueva_devolucion_contenedores" role="form" method="post" action="nuevaDevolucionContenedores.php?id_cliente=<?=$id?>">
        <input name="id_despacho_devolucion_contenedores" id="id_despacho_devolucion_contenedores" type="hidden">
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-4">
              <label for="fecha_devolucion_contenedores">Fecha</label>
              <input name="fecha_devolucion_contenedores" id="fecha_devolucion_contenedores" type="date" class="form-control multiselect" value="<?=$hoy?>" required>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-2">
              <label for="observaciones_devolucion_contenedores" class="col-form-label">Observaciones:</label>
            </div>
            <div class="form-group col-10">
              <textarea name="observaciones_devolucion_contenedores" id="observaciones_devolucion_contenedores" class="form-control"></textarea>
            </div>
          </div>
          <div class="row">
            <div class="form-gropu col-12">
              <!-- HTML de la tabla -->
              <table id="tablaContenedoresDespachadas" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th style="width: 50%">Contenedor</th>
                    <th style="width: 20%">Cantidad</th>
                    <th style="width: 30%">Devolver/Pendiente</th>
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

  function getDetalleContenedoresDespachados(id_despacho){
    $.ajax({
      //data: datosIniciales,
      url: 'ajaxGetDetalleContenedoresDespachados.php?id_despacho='+id_despacho,
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
        var tbody = document.querySelector('#tablaContenedoresDespachadas tbody');
        tbody.innerHTML="";

        //Genero los options del select de prioridades
        respuestaJson.forEach(row => {
          let id_material = row.id_material;
          let id_procedencia = row.id_procedencia;

          let cantidad_despachada = parseInt(row.cantidad_despachada);
          let cantidad_devuelta = parseInt(row.cantidad_devuelta);
          let pendiente = cantidad_despachada - cantidad_devuelta;

          if(pendiente==0){

          }

          // Plantilla para cada fila
          let contenidoFila = `
            <td class="align-middle">
              <input type='hidden' name="id_despacho_contenedor[]" value='${row.id_despacho_contenedor}'>
              <input type='hidden' name="id_contenedor[]" value='${row.id_contenedor}'>${row.contenedor}
            </td>
            <td class="align-middle text-right">
              ${Intl.NumberFormat("de-DE").format(cantidad_despachada)}
            </td>
            <td class="align-middle">
              <div class="input-group">
                ${row.requiere_devolucion == 1 ? `
                  ${pendiente > 0 ? `
                    <input type="number" name="cantidad_devolver[]" class="form-control cantidad_devolver" value="0" required>
                  ` : `
                    <input type="number" class="form-control disabled" value="0" required disabled readonly>
                    <input type="hidden" name="cantidad_devolver[]" value="0">
                  `}
                  <div class="input-group-append">
                    <span class="input-group-text">/ ${Intl.NumberFormat("de-DE").format(pendiente)}</span>
                    <input type="hidden" name="pendiente_devolver[]" value="${pendiente}">
                  </div>
                ` : `
                  No requiere devolucion.
                  <input type="hidden" name="cantidad_devolver[]" value="0">
                  <input type="hidden" name="pendiente_devolver[]" value="0">
                `}
              </div>
            </td>`;

          // Crear una nueva fila
          var newRow = document.createElement('tr');
          // Insertar el contenido HTML en la nueva fila
          newRow.innerHTML = contenidoFila;
          // Agregar la fila al tbody
          tbody.appendChild(newRow);
        });

      }
    });
  }

  $(document).on("keyup change",".cantidad_despachar",function(){
    if(this.value>0){
      let fila=$(this).parents("tr")
      fila.find("select").attr("required",true)

    }
  })

  $("#form_nueva_devolucion_contenedores").submit(function(e){
    e.preventDefault()
    let hay_cantidad=0
    let cantidad_devolver=$(".cantidad_devolver")
    cantidad_devolver.each(function(){
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
      cantidad_devolver.addClass('input-error');
      setTimeout(function() {
          cantidad_devolver.removeClass('input-error');
      }, 2000);
    }
  })
</script>