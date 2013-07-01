<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * My CodeIgniter Library
 *
 * @author  Indigo Dev Team
 * @since   Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------
/**
 * @category	Uploads Archivos | Manipulacion de imagenes [Resize y Crop]
 */
class My_upload {

  // Vars
	var $do_resize = FALSE; // Indica si redimensionara una imagen
	var $do_crop   = FALSE; // Indica si se cortara una imagen
  
  var $config; 				// Configuracion para la lib "upload"
  var $config_resize; // Configuracion para la lib "image_lib"
  
  private $CI; 				// Instancia de Codeigniter

  /**
   * Constructor
   * @param  array $config=array()
   */
  public function __construct($config=array())
  {
  	// Obtiene la instancia de condeigniter
    $this->CI =& get_instance();
    
    //	Verifica si no existe una instancia de la libreria Upload
    //	Si no existe la carga, esto evita la sobrecarga
    if (! isset($this->CI->upload))
    	$this->CI->load->library('upload');
    
    if (count($config) > 0)
    	$this->initialize($config);

    log_message('debug', "myupload Class Initialized");
  }
  
  /**
   * Inicializa la configuracion para loa libreria Upload de CI.
   * @param  array  $config [description]
   * @return [type]         [description]
   */
  public function initialize($config=array())
  {
  	$this->config = $config;
  	$this->CI->upload->initialize($config);  	
  }
  
  /**
   * Cambia el directorio donde se hara el upload
   * @param string $new_path Nuevo directorio
   */
  public function change_upload_path($new_path)
  {
  	$this->config['upload_path'] = $this->CI->upload->upload_path = $new_path;
  }
  
  /**
   * Realiza el upload del archivo
   * @param  string $post_field [nombre del input del formulario] Default: archivo
   * @return array [Array que contiene todos los datos relacionados con el archivo que se subio]
   */
  public function do_upload($post_field='archivo')
  {
  	if( ! $this->CI->upload->do_upload($post_field))
    {      
      return array(FALSE, 'msg'=>'Error: ' . $this->CI->upload->display_errors());
    } 
    $file_data = $this->CI->upload->data();
    
    // Si se redimecionará una imagen la variable $do_resize tiene que ser TRUE
    if ($this->do_resize)
    	if ($file_data['is_image']) 
	    {
	      $result_resize = $this->resize_image($this->config['upload_path'].$file_data['file_name']);
	      if (! $result_resize)
	        return array(FALSE, 'msg'=>'Error: ' . $this->CI->image_lib->display_errors());
	    }

	  return $file_data;
  }
  
  /**
   * Redimenciona una imagen
   * @param  string $path_img [Directorio completo de la imagen con su nombre Ej: images/imagen.jpg]
   * @return boolean [True: Se redimenciono con exito la imagen | False: Hubo un problema en el proceso]
   */
  public function resize_image($path_img)
  {
  	//	Verifica si no existe una instancia de la libreria image_lib
    //	Si no existe la carga, esto evita la sobrecarga
  	if ( ! isset($this->CI->image_lib))
  		$this->CI->load->library('image_lib');
  	
  	$this->config_resize['source_image'] = $path_img;
  	$this->CI->image_lib->initialize($this->config_resize);
  	
  	if ( ! $this->CI->image_lib->resize())
    {
      $this->CI->image_lib->clear();
      return FALSE;
    }
    
    $this->CI->image_lib->clear();
    return TRUE;
  }
  
  /**
   * Realiza un recorte(crop) de una imagen.
   * @param  string  $thumb_image_name [FullPath de la nueva imagen cropeada Ej. app/images/cropeada.jpg]
   * @param  string  $image            [FullPath de la imagen a recortar (fuente) ]
   * @param  integer $newImageWidth    [Indica el ancho de la nueva imagen cropeada]
   * @param  integer $newImageHeight   [Indica el alto de la nueva imagen cropeada]
   * @param  array  $conf              [Array con la configuracion para la cropeo Ej: array('width'=>500,'height'=>500,'x'=>800,'y'=>600)]
   * @return string                    [FullPath de la iamgen cropeada]
   */
  public function crop_image($thumb_image_name, $image, $newImageWidth=50,$newImageHeight=50, $conf=null)
  {
	  list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	  $imageType = image_type_to_mime_type($imageType);

	  if($conf == null)
	  {
	  	if($imagewidth >= $imageheight)
	  	{
				$des_width  = ceil($imagewidth * $newImageWidth / $imageheight);
				$des_height = $newImageWidth;
				$des_x      = ceil((($des_width - $des_height) / 2) * $imagewidth / $des_width);
				$des_y      = 0;
	  	}
	  	else
	  	{
				$des_width  = $newImageHeight;
				$des_height = ceil($imageheight * $newImageHeight/$imagewidth);
				$des_x      = 0;
				$des_y      = ceil((($des_height - $des_width) / 2) * $imageheight / $des_height);
	  	}
	  }
	  else
	  {
			$des_width  = $imagewidth = $conf["width"];
			$des_height = $imageheight = $conf["height"];
			$des_x      = $conf["x"];
			$des_y      = $conf["y"];
	  }

	  $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
	  
	  switch($imageType) 
	  {
	  	case "image/gif":
	  		$source=imagecreatefromgif($image); 
	  	break;
	  	case "image/pjpeg":
	  	case "image/jpeg":
	  	case "image/jpg":
	  		$source=imagecreatefromjpeg($image); 
	  	break;
	  	case "image/png":
	  	case "image/x-png":
	  		$source=imagecreatefrompng($image); 
	  	break;
	  }
	  
	  imagecopyresampled($newImage, $source, 0, 0, $des_x, $des_y, $des_width, $des_height, $imagewidth, $imageheight);
	  
	  switch($imageType) 
	  {
	  	case "image/gif":
	  		imagegif($newImage,$thumb_image_name); 
	  	break;
	  	case "image/pjpeg":
	  	case "image/jpeg":
	  	case "image/jpg":
	  		imagejpeg($newImage,$thumb_image_name,90); 
	  	break;
	  	case "image/png":
	  	case "image/x-png":
	  		imagepng($newImage,$thumb_image_name); 
	  	break;
	  }
	  
	  chmod($thumb_image_name, 0777);
	  return $thumb_image_name;
}
			
}
/* End of file myupload.php */
/* Location: ./application/libraries/myupload.php */