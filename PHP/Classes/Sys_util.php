<?php
/**
 * My System Class
 *
 * @author  Indigo Dev Team
 * @since   Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Clase con funciones para interactuar con el sistema
 * Crear carpetas, eliminar archivos, 
 */
class Sys_util {

  /**
   * Regresa el mes en letra
   * @param  int $mes [Numero de mes]
   * @return string   [Nombre del Mes]
   */
  public static function mes_to_string($mes)
  {
    switch(floatval($mes))
    {
      case 1: return 'ENERO'; break;
      case 2: return 'FEBRERO'; break;
      case 3: return 'MARZO'; break;
      case 4: return 'ABRIL'; break;
      case 5: return 'MAYO'; break;
      case 6: return 'JUNIO'; break;
      case 7: return 'JULIO'; break;
      case 8: return 'AGOSTO'; break;
      case 9: return 'SEPTIEMBRE'; break;
      case 10: return 'OCTUBRE'; break;
      case 11: return 'NOVIEMBRE'; break;
      case 12: return 'DICIEMBRE'; break;
    }
  }
  
  /**
   * Valida si el directorio espesificado existe o si no lo crea.
   * @param  string $tipo [Tipo de carpeta anio o mes]
   * @param  [type] $path [Directorio donde se creara la carpeta del año o mes]
   * @return [type]       [description]
   */
  public static function valida_dir($tipo, $path_source)
  {
    $path = $path_source;
    
    if($tipo == 'anio')
    {  
      $directorio = date("Y");
    }
    else
    {
      echo 'entro'.'<br>';
      $directorio = self::mes_to_string(date("n"));
    }
    if( ! file_exists($path.$directorio."/"))
      self::crear_folder($path, $directorio."/");
    
    return $directorio;
  }
  
/**
   * Crea un folder en el servidor.
   * @param $path_directorio: string. ruta donde se creara el directorio.
   * @param $nombre_directorio: string. nombre del folder a crear.
   */
  public static function crear_folder($path_directorio, $nombre_directorio)
  {
    if($nombre_directorio != "" && file_exists($path_directorio))
    {
      if( ! file_exists($path_directorio.$nombre_directorio))
        return mkdir($path_directorio.$nombre_directorio, 0777);
      else
        return true;
    }
    else
      return false;
  }

}

/* End of file SysUtil.php */
/* Location: ./application/core/system/SysUtil.php */