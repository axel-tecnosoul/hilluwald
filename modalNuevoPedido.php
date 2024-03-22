<div class="modal fade" id="nuevoPedido" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1000000000000" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width: 1200px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo Pedido de <?=$data['razon_social'];?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form theme-form" id="formPedido" role="form" method="post" action="nuevoPedido.php?id_cliente=<?=$id?>">
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-4">
              <label for="fecha_pedido">Fecha</label>
              <input name="fecha_pedido" id="fecha_pedido" type="date" class="form-control multiselect" value="<?=$hoy?>" required>
            </div>
            <div class="form-group col-4">
              <label for="campana_pedido">Campaña</label>
              <select name="campana_pedido" id="campana_pedido" style="width: 100%;" required class="js-example-basic-single"><?php
                // Generar las opciones del select
                for ($i = $anio_inicial; $i <= $anio_final; $i++) {
                  // Si el año es el actual, marcarlo como seleccionado por defecto
                  $selected = ($i == $anio_actual) ? "selected" : "";
                  echo "<option value='$i' $selected>$i</option>";
                }?>
              </select>
            </div>
            <div class="form-group col-4">
              <label for="sucursal_pedido">Sucursal</label>
              <select name="sucursal_pedido" id="sucursal_pedido" style="width: 100%;" required class="js-example-basic-single"><?php
                $pdo = Database::connect();
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sqlZon = "SELECT id_sucursal,s.nombre FROM cliente_sucursal cs INNER JOIN sucursales s ON cs.id_sucursal=s.id WHERE id_cliente = ".$id;
                $q = $pdo->prepare($sqlZon);
                $q->execute();
                $afe=$q->rowCount();
                if($afe>1){
                  echo "<option value=''>Seleccione...</option>";
                }
                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                  echo "<option value='".$fila['id_sucursal']."'";
                  echo ">".$fila['nombre']."</option>";
                }
                Database::disconnect();?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-2">
              <label for="observaciones_pedido" class="col-form-label">Observaciones:</label>
            </div>
            <div class="form-group col-10">
              <textarea name="observaciones_pedido" id="observaciones_pedido" class="form-control"></textarea>
            </div>
          </div><?php
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlZon = "SELECT id, servicio FROM servicios WHERE activo = 1";
            $q = $pdo->prepare($sqlZon);
            $q->execute();
            $aServicio=[];
            while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
              $aServicio[]=[
                "id"=>$fila['id'],
                "servicio"=>$fila['servicio'],
              ];
            }
            
            $sqlZon = "SELECT id, especie FROM especies WHERE activo = 1";
            $q = $pdo->prepare($sqlZon);
            $q->execute();
            $aEspecies=[];
            while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
              $aEspecies[]=[
                "id"=>$fila['id'],
                "especie"=>$fila['especie'],
              ];
            }

            $sqlZon = "SELECT id, procedencia FROM procedencias_especies WHERE activo = 1";
            $q = $pdo->prepare($sqlZon);
            $q->execute();
            $aProcedencias=[];
            while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
              $aProcedencias[]=[
                "id"=>$fila['id'],
                "procedencia"=>$fila['procedencia'],
              ];
            }

            $sqlZon = "SELECT id, material, id_procedencia, id_especie FROM cultivos WHERE activo = 1";
            $q = $pdo->prepare($sqlZon);
            $q->execute();
            $aMateriales=[];
            while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
              $aMateriales[]=[
                "id"=>$fila['id'],
                "material"=>$fila['material'],
                "id_procedencia"=>$fila['id_procedencia'],
                "id_especie"=>$fila['id_especie'],
              ];
            }

            Database::disconnect();?>
          <div class="row">
            <div class="col-sm-12">
              <table class="table-detalle table table-bordered table-hover text-center" id="tableCultivos" style="table-layout: fixed;">
                <thead>
                  <tr>
                    <th style="width: 17%;">Servicio</th>
                    <th style="width: 20%;">Especie</th>
                    <th style="width: 20%;">Procedencia</th>
                    <th style="width: 20%;">Material</th>
                    <th style="width: 13%;">Cantidad</th>
                    <th style="width: 10%;">Eliminar</th>
                  </tr>
                </thead>
                <tbody>
                  <tr id='addr0' data-id="0" style="display: none;">
                    <td data-name="id_servicio" class="text-left">
                      <select name="id_servicio[]" id="id_servicio-0" class="js-example-basic-single id_servicio" style="width: 100%;" data-required="1">
                        <option value="">Seleccione...</option><?php
                        foreach ($aServicio as $servicio) {
                          echo "<option value='".$servicio['id']."'";
                          echo ">".$servicio['servicio']."</option>";
                        }
                        Database::disconnect();?>
                      </select>
                    </td>
                    <td data-name="id_especie" class="text-left">
                      <select name="id_especie[]" id="id_especie-0" class="js-example-basic-single id_especie" style="width: 100%;" data-required="1">
                        <option value="">Seleccione...</option><?php
                        foreach ($aEspecies as $especie) {
                          echo "<option value='".$especie['id']."'";
                          echo ">".$especie['especie']."</option>";
                        }
                        Database::disconnect();?>
                      </select>
                    </td>
                    <td data-name="id_procedencia" class="text-left">
                      <select name="id_procedencia[]" id="id_procedencia-0" class="js-example-basic-single id_procedencia" style="width: 100%;" data-required="0" disabled>
                        <option value="">Seleccione una especie...</option>
                      </select>
                    </td>
                    <td data-name="id_material" class="text-left">
                      <select name="id_material[]" id="id_material-0" class="js-example-basic-single id_material" style="width: 100%;" data-required="0" disabled>
                        <option value="">Seleccione una procedencia...</option>
                      </select>
                    </td>
                    <td data-name="cantidad">
                      <input type="number" name="cantidad[]" id="cantidad-0" class="form-control cantidad" placeholder="Cantidad" min="1" data-required="1" disabled>
                    </td>
                    <td data-name="eliminar">
                      <span name="eliminar[]" title="Eliminar" class="btn btn-sm row-remove text-center" onClick="eliminarFila(this);">
                        <img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar">
                      </span>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="5" align='right'>
                      <input type="button" class="btn btn-dark" id="addRowCultivos" value="Agregar Cultivo">
                    </td>
                  </tr>
                </tfoot>
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
  $('#formPedido').submit(function(event) {
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
</script>