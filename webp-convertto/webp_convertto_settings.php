<?php
	
	function convert_webp ($folder='') {
		$files = glob($folder."/*.*");
		$current_folder = $folder;
		for ($i=0; $i<count($files); $i++)	{
			$image = $files[$i];
			$supported_file = array(
			'gif',
			'jpg',
			'jpeg',
			'png'
			);

			$foldername =	pathinfo($image, PATHINFO_EXTENSION);
			$ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
			if (in_array($ext, $supported_file)) {
				if ($ext == 'jpeg' || $ext == 'jpg'){
					if ($image) {
					  header("Content-type: image/jpeg");
					  imagejpeg($image);
					}
					$outimg = imagecreatefromjpeg($image);
				}else if ($ext == 'png')
					$outimg = imagecreatefrompng($image);
				// получить размеры изображения
				$w = imagesx($outimg);
				$h = imagesy($outimg);;
				// создать холст
				$im = imagecreatetruecolor ($w, $h);
				imageAlphaBlending($im, false);
				imageSaveAlpha($im, true);
				// По умолчанию холст черный, поэтому сделал его прозрачным.
				$trans = imagecolorallocatealpha($im, 0, 0, 0, 127);
				imagefilledrectangle($im, 0, 0, $w - 1, $h - 1, $trans);
				// скопировать img на холст
				imagecopy($im, $outimg, 0, 0, 0, 0, $w, $h);
				// наконец, сохраняю холст как webp
				imagewebp($im, str_replace($ext, 'webp', $image));
				imagedestroy($im);
			  } else {
					convert_webp($current_folder.$foldername.'/'); //для вложенных папок
					continue;
			  }
		}
	}
	
	function listFolderFiles($dir){
		$ffs = scandir($dir);
		unset($ffs[array_search('.', $ffs, true)]);
		unset($ffs[array_search('..', $ffs, true)]);
		// prevent empty ordered elements
		if (count($ffs) < 1)
			return;
		echo '<ol>';
		foreach($ffs as $ff){
			echo '<li>'.$ff;
			if(is_dir($dir.'/'.$ff)) {
				listFolderFiles($dir.'/'.$ff);
				convert_webp($dir.'/'.$ff.'/');
				echo '<p>converting in subfolder: '.$dir.'/'.$ff.'</p>';
			}
			echo '</li>';
		}
		echo '</ol>';
	}
	
	if ($_POST['webp_convertto_run'] == 'yes') {
		listFolderFiles(wp_get_upload_dir()['basedir']);
		convert_webp(wp_get_upload_dir()['basedir']);
		
	}
	
	
?>

<div style="margin-top: 10px">
	<div>Создать Webp изображения в один клик в папке uploads</div>
	<div>Плагин автоматически создаст все webp-изображения в папке uploads на основе jpg,png,jpeg и заменит их, если они добавлены через функцию Wordpress - wp_get_attachment_image_src (hook: wp_get_attachment_image_src)</div>
	<div>
		<form method="post" action="" style="margin-top: 10px;">
			<button name="webp_convertto_run" value="yes">Создать Webp</button>
		</form>
	</div>
</div>