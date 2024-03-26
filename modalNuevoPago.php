<div class="modal fade" id="nuevoPago" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1000000000000" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width: 1200px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo Pago de <?=$data['razon_social'];?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form theme-form" id="formPago" role="form" method="post" action="nuevoPago.php?id_cliente=<?=$id?>" enctype="multipart/form-data">
        <input name="id_pedido_pago" id="id_pedido_pago" type="hidden">
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-4">
              <label for="fecha_pago">Fecha</label>
              <input name="fecha_pago" id="fecha_pago" type="date" class="form-control multiselect" value="<?=$hoy?>" required>
            </div>
            <div class="form-group col-4 d-none">
              <label for="campana_pago">Campaña</label>
              <select name="campana_pago" id="campana_pago" style="width: 100%;" required class="js-example-basic-single"><?php
                // Generar las opciones del select
                for ($i = $anio_inicial; $i <= $anio_final; $i++) {
                  // Si el año es el actual, marcarlo como seleccionado por defecto
                  $selected = ($i == $anio_actual) ? "selected" : "";
                  echo "<option value='$i' $selected>$i</option>";
                }?>
              </select>
            </div>
            <div class="form-group col-4">
              <label for="foto_cbte">Foto comprobante</label>
              <input type="file" class="form-control" accept="image/*, application/pdf" name="cbtes[]" multiple>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-2">
              <label for="observaciones_pago" class="col-form-label">Observaciones:</label>
            </div>
            <div class="form-group col-10">
              <textarea name="observaciones_pago" id="observaciones_pago" class="form-control"></textarea>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="form-group row">
                <div class="col-sm-12">
                  <table class="table table-striped table-bordered" id="detallePedidoParaPago" style="table-layout:fixed"">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 10%;">Servicio</th>
                        <th class="text-center" style="width: 30%;">Cultivo</th>
                        <th class="text-center" style="width: 10%;">Total pedido</th>
                        <th class="text-center" style="width: 20%;">Cobrar/Pendiente</th>
                        <th class="text-center" style="width: 15%;">Precio Unitario</th>
                        <th class="text-center" style="width: 15%;">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                      <tr>
                        <th class="text-right" colspan="5">Total:</th>
                        <th class="text-right">
                          <span class="total_pago">$ 0</span>
                        </th>
                      </tr>
                    </tfoot>
                  </table>
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
  $('#formPago').submit(function(event) {
    //event.preventDefault(); // Evitar el envío del formulario
    $(".id_especie").attr("disabled",false)
    //console.log($(".id_especie"));
    $(".id_procedencia").attr("disabled",false)
    //console.log($(".id_procedencia"));
    $(".id_material").attr("disabled",false)
    //console.log($(".id_material"));
    $(".cantidad").attr("disabled",false)
    //console.log($(".id_material"));
  })

  function getDetallePedidoParaPagos(id_pedido){
    $.ajax({
      //data: datosIniciales,
      url: 'ajaxGetDetallePedidoNuevoPago.php?id_pedido='+id_pedido,
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
        var tbody = document.querySelector('#detallePedidoParaPago tbody');
        tbody.innerHTML="";

        //Genero los options del select de prioridades
        respuestaJson.forEach(cultivos => {
          let id_material = cultivos.id_material;
          let id_procedencia = cultivos.id_procedencia;

          let cantidad_plantines = parseInt(cultivos.cantidad_plantines);
          let plantines_retirados = parseInt(cultivos.plantines_retirados);
          let pendiente = cantidad_plantines - plantines_retirados;

          // Plantilla para cada fila
          let contenidoFila = `
            <td class="align-middle">
              <input type='hidden' name="id_servicio[]" value='${cultivos.id_servicio}'>${cultivos.servicio}
            </td>
            <td class="align-middle">
              <input type='hidden' name="id_especie[]" class='id_especie' value='${cultivos.id_especie}'>
              <input type='hidden' name="id_procedencia[]" value='${cultivos.id_procedencia}'>
              <input type='hidden' name="id_material[]" value='${cultivos.id_material}'>
              ${cultivos.especie+" "+cultivos.procedencia+" "+cultivos.material}
            </td>
            <td class="align-middle">
              ${Intl.NumberFormat("de-DE").format(cultivos.cantidad_plantines)}
            </td>
            <td class="align-middle">
              <div class="input-group">
                <input type="number" name="cantidad_pagar[]" class="form-control cantidad_pagar calcula_subtotal" required>
                <div class="input-group-append">
                  <span class="input-group-text">/ ${Intl.NumberFormat("de-DE").format(pendiente)}</span>
                </div>
              </div>
            </td>
            <td class="align-middle">
              <input type="number" name="precio_unitario[]" step="0.01" class="form-control precio_unitario calcula_subtotal" required>
            </td>
            <td class="align-middle text-right">
              <input type="hidden" name="subtotal[]" class="subtotal">
              <span class="subtotal_formatted"></span>
            </td>`;

          // Crear una nueva fila
          var newRow = document.createElement('tr');
          // Insertar el contenido HTML en la nueva fila
          newRow.innerHTML = contenidoFila;
          // Agregar la fila al tbody
          tbody.appendChild(newRow);
        });


        $("#detallePedidoParaPago").find(".id_procedencia").select2()
        $("#detallePedidoParaPago").find(".id_material").select2()

      }
    });

    $(document).on("keyup change",".cantidad_pagar, .precio_unitario",function(){
      let fila=$(this).parents("tr");
      let inputSubtotal=fila.find(".subtotal")
      let mostrarSubtotal=fila.find(".subtotal_formatted")
      let cantidad_pagar=parseFloat(fila.find(".cantidad_pagar").val())
      let precio_unitario=parseFloat(fila.find(".precio_unitario").val())
      let subtotal=cantidad_pagar*precio_unitario;
      console.log(subtotal);
      if(isNaN(subtotal)){
        subtotal=0;
      }
      console.log(subtotal);
      inputSubtotal.val(subtotal)
      mostrarSubtotal.html(Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(subtotal))

      calcularTotal();
    })

    function calcularTotal(){
      let total_pago=0;
      $(".subtotal").each(function(){
        let valor=parseFloat(this.value);
        if(isNaN(valor)){
          valor=0;
        }
        total_pago+=valor;
      })

      $(".total_pago").html(Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_pago))
    }
  }
</script>