<?php
function get_nombre_comprobante($tipo_comprobante){
  switch ($tipo_comprobante) {
    case 'R':
      $tipo_cbte="Recibo";
      break;
    case 'A':
      $tipo_cbte="Factura A";
      break;
    case 'B':
      $tipo_cbte="Factura B";
      break;
    case 'NCA':
      $tipo_cbte="Nota de Crédito A";
      break;
    case 'NCB':
      $tipo_cbte="Nota de Crédito B";
      break;
    default:
      $tipo_cbte="";
      break;
  }
  return $tipo_cbte;
}

function get_estado_comprobante($estado_abreviado){
  switch ($estado_abreviado) {
    case 'R':
      $estado_completo="Rechazado";
      break;
    case 'A':
      $estado_completo="Aprobado";
      break;
    case 'E':
      $estado_completo="ERROR";
      break;
    default:
      $estado_completo="";
      break;
  }
  return $estado_completo;
}

function format_numero_comprobante($punto_venta,$numero_comprobante){
  return str_pad($punto_venta,4,"0",STR_PAD_LEFT)."-".str_pad($numero_comprobante,8,"0",STR_PAD_LEFT);
}

function acortarPalabras($texto) {
  // Convertimos el texto en un array de palabras
  $palabras = explode(" ", $texto);
  
  // Creamos un array para almacenar las palabras acortadas
  $palabrasAcortadas = array();

  // Recorremos cada palabra y la acortamos si tiene más de dos letras
  foreach ($palabras as $palabra) {
      if (strlen($palabra) > 2) {
          // Obtenemos la primera letra de la palabra y la agregamos al array de palabras acortadas
          $palabrasAcortadas[] = $palabra[0];
      } else {
          // Si la palabra tiene dos letras o menos, la agregamos sin modificar al array de palabras acortadas
          $palabrasAcortadas[] = $palabra;
      }
  }
  
  // Unimos las palabras acortadas en una sola cadena separada por espacios
  return implode("", $palabrasAcortadas);
}