<div class="modal fade" id="nuevoPago" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1000000000000" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Nuevo Pedido de <?=$data['razon_social'];?> Pedido N° <span class="idPedido"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form theme-form" role="form" method="post" action="nuevoPedido.php?id_cliente=<?=$id?>">
        <input name="id_pedido_pago" id="id_pedido_pago" type="hidden">
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Fecha</label>
                <div class="col-sm-3"><input name="fecha_pago" id="fecha_pago" type="date" class="form-control" value="<?=$hoy?>" required></div>
                <label class="col-sm-3 col-form-label">Campaña</label>
                <div class="col-sm-3">
                  <select name="campana_pago" id="campana_pago" class="form-control"><?php
                    // Generar las opciones del select
                    for ($i = $anio_inicial; $i <= $anio_final; $i++) {
                      // Si el año es el actual, marcarlo como seleccionado por defecto
                      $selected = ($i == $anio_actual) ? "selected" : "";
                      echo "<option value='$i' $selected>$i</option>";
                    }?>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <!-- <label class="col-sm-12 col-form-label">Cultivos</label> -->
                <div class="col-sm-12">
                  <table class="table table-striped">
                    <tr>
                      <th>Cultivo</th>
                      <th>Cantidad</th>
                    </tr><?php
                    //include 'database.php';
                    /*$pdo = Database::connect();
                    $sql = " SELECT id, nombre FROM cultivos";
                    foreach ($pdo->query($sql) as $row) {?>
                      <tr>
                        <td><?=$row["nombre"]?></td>
                        <td>
                          <input type="hidden" name="id_cultivo[]" value="<?=$row["id"]?>">
                          <input type="number" name="cantidad[]" class="form-control" placeholder="Cantidad">
                        </td>
                      </tr><?php
                    }
                    Database::disconnect();*/?>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="col-sm-9 offset-sm-3">
            <button class="btn btn-primary" type="submit">Crear</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>