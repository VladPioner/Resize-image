<?

class EditImg {
    
    private $ext;
    private $filename;
    const CROP_CENTER = 1;
    const CROP_TOP = 2;
    const CROP_BOTTOM = 3;
    
   /* Загрузка файлов в папку из массив $_FILES
    * $files - массив $_FILES можно загружать как один файл так несколько файлов сразу
    * $to_dir - папка назначения в которую будут загружены файлы
    * $uniq - если установить true то к имени файла будет добавлено уникальное значение функцией uniqid()
    * $func_name - можно задать имя функции которой будет обработано имя файла,
    * к примеру можно исходное имя файла обработать функцией translit чтобы в имени загруженных файлов не было кирелических символов
    */
    static function uploadToDir($files,$to_dir,$uniq=false,$func_name = false){
        $to_dir = trim(trim(trim($to_dir),'/'),'\\').'/';
        if(!file_exists($to_dir)){
            mkdir($to_dir);
        }
        $arr_name_files = array();
        foreach($files as $val){
            if(is_array($val['name'])){
                for($i = 0;$i<count($val['name']);$i++){
                    $name_file = !$func_name ? $val['name'][$i] : $func_name($val['name'][$i]);
                    $name_file = $uniq ? pathinfo($name_file,PATHINFO_FILENAME).'_'.uniqid().'.'.pathinfo($name_file,PATHINFO_EXTENSION) : $name_file;
                    if(move_uploaded_file($val['tmp_name'][$i], $to_dir.$name_file))
                        $arr_name_files[] = $to_dir.$name_file;
                }
            }else{
                $name_file = !$func_name ? $val['name'] : $func_name($val['name']);
                $name_file = $uniq ? pathinfo($name_file,PATHINFO_FILENAME).'_'.uniqid().'.'.pathinfo($name_file,PATHINFO_EXTENSION) : $name_file;
                if(move_uploaded_file($val['tmp_name'], $to_dir.$name_file))
                    $arr_name_files[] = $to_dir.$name_file;
            }
        }
        return $arr_name_files;
    }

    /* изменить размер и обрезать изображение
     * пропорционально изменяет размер исходного изображения таким образом чтоб максимально эффективно вписать
     * в него область шириной $width и высотой $height после чего создает новое изображение из исходного обрезав
     * области не вошедшие в область шириной $width и высотой $height
     * int $width - ширина нового изображения
     * int $height - высота нового изображения
     * string $img_input - путь к исходному изображению
     * string $dir_save - путь к папке в которой будет сохранено измененное изображение
     * если $dir_save = null то изображение можно отдавать сразу в браузер
     * string $new_file_name - имя измененного изображения,
     * если $new_file_name = false имя измененного изображения будет такое же как и у исходного
     * string $new_ext - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату
     * $crop_position - может иметь значения: EditImg::CROP_CENTER, EditImg::CROP_TOP, EditImg::CROP_BOTTOM
     * EditImg::CROP_CENTER - область вырежется из центра изображения
     * EditImg::CROP_TOP - если изображение в книжном формате то область вырежется из верха изображения
     * если в альбомном формате то область вырежется из правой части изображения
     * EditImg::CROP_BOTTOM - если изображение в книжном формате то область вырежется из низа изображения
     * если в альбомном формате то область вырежется из левой части изображения
     */
    function crop($width, $height, $img_input, $dir_save = null, $new_file_name = false, $new_ext = false,$crop_position = self::CROP_CENTER){
        
        $src = $this->createDest($img_input);
        if(!$src) return false;
        
        if($new_ext) $this->ext = $new_ext;
        
        $w_orig = imagesx($src); 
        $h_orig = imagesy($src);
        
        //пропорционально уменьшаем изображение таким образом
        //чтоб конечные размеры вписывались внутрь этого изображения
        $arg = $h_orig/$height;
        $w_new = round($w_orig/$arg);
        $h_new = $height;
        
        if($w_new < $width){
            $arg1 = round($width/$w_new);
            $w_new = $width;
            $h_new = $height*$arg1;
        }
        
        $min_img = imagecreatetruecolor($w_new,$h_new);
        
        $this->alphaToPng($min_img, $this->ext);
        
        imagecopyresized($min_img, $src, 0, 0, 0, 0, $w_new, $h_new, $w_orig, $h_orig);

        // обрезаем изображение по краям
        $finish_img = imagecreatetruecolor($width,$height);
        
        $this->alphaToPng($finish_img, $this->ext);
        
        if($h_new == $height){
            $shift = ($w_new - $width)/2;
            switch ($crop_position){
                case self::CROP_CENTER:
                    $shift_crop = $shift;
                    break;
                case self::CROP_TOP:
                    $shift_crop = 0;
                    break;
                case self::CROP_BOTTOM;
                    $shift_crop = $w_new - $width;
                    break;
                default:
                    $shift_crop = $shift;
            }
            imagecopyresized($finish_img, $min_img, 0, 0, $shift_crop, 0, $width, $height, $w_new-2*$shift, $h_new);
        }else{
            $shift = ($h_new - $height)/2;
            switch ($crop_position){
                case self::CROP_CENTER:
                    $shift_crop = $shift;
                    break;
                case self::CROP_TOP:
                    $shift_crop = 0;
                    break;
                case self::CROP_BOTTOM;
                    $shift_crop = $h_new - $height;
                    break;
                default:
                    $shift_crop = $shift;
            }
            imagecopyresized($finish_img, $min_img, 0, 0, 0, $shift_crop, $width, $height, $w_new, $h_new-2*$shift);
        }
        
        
        $file_name = $this->saveImg($this->ext, $finish_img, $dir_save, $new_file_name);
        imageDestroy($src);
        imageDestroy($min_img);
        imageDestroy($finish_img);
        return $file_name;
    }

    /* уменьшить и вписать изображение
     * пропорционально изменяет размер исходного изображения таким образом чтоб максимально эффективно вписать его
     * в область шириной $width и высотой $height после чего если в области шириной $width и высотой $height осталось
     * незаполненное место зальет его цветом $arr_bg
     * int $width - ширина нового изображения
     * int $height - высота нового изображения
     * string $img_input - путь к исходному изображению
     * string $dir_save - путь к папке в которой будет сохранено измененное изображение
     * если $dir_save = null то изображение можно отдавать сразу в браузер
     * string $new_file_name - имя измененного изображения,
     * если $new_file_name = false имя измененного изображения будет такое же как и у исходного
     * array $arr_bg - массив с цветом заливки пустого места в результирующем изображении. Пример [255,255,255]
     * если $arr_bg = false - елси формат результирующего изображения будет png то цвет заливки будет прозрачный
     * если другой формат то цвет заливки будет белый
     * string $new_ext - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату
     * $crop_position - может иметь значения: EditImg::CROP_CENTER, EditImg::CROP_TOP, EditImg::CROP_BOTTOM
     * EditImg::CROP_CENTER - область вырежется из центра изображения
     * EditImg::CROP_TOP - если изображение в книжном формате то область вырежется из верха изображения
     * если в альбомном формате то область вырежется из правой части изображения
     * EditImg::CROP_BOTTOM - если изображение в книжном формате то область вырежется из низа изображения
     * если в альбомном формате то область вырежется из левой части изображения
     */
    function inscribed($width, $height, $img_input, $dir_save = null, $new_file_name = false, $arr_bg = false, $new_ext = false,$crop_position = self::CROP_CENTER){
        
        $src = $this->createDest($img_input);
        if(!$src) return false;
        
        if($new_ext) $this->ext = $new_ext;
        
        $w_orig = imagesx($src); 
        $h_orig = imagesy($src);
        
        //пропорционально уменьшаем изображение таким образом чтоб это изображение вписалось конечные размеры
        $arg = $h_orig/$height;
        $w_new = round($w_orig/$arg);
        $h_new = $height;
        
        if($w_new > $width){
            $arg1 = $w_new/$width;
            $w_new = $width;
            $h_new = round($height/$arg1);
        }
        
        $min_img = imagecreatetruecolor($w_new,$h_new);
        
        $this->alphaToPng($min_img, $this->ext);
        
        imagecopyresized($min_img, $src, 0, 0, 0, 0, $w_new, $h_new, $w_orig, $h_orig);
        
        // вписываем зображение
        $finish_img = imagecreatetruecolor($width,$height);
        
        if($arr_bg === false){
            if($this->ext != "png"){
                $arr_bg = array(255,255,255);
                $bg = imagecolorallocate($finish_img,$arr_bg[0],$arr_bg[1],$arr_bg[2]);
                imageFill($finish_img,0,0,$bg);
            }else{
                $this->alphaToPng($finish_img, $this->ext);
            }
        }else{
            $bg = imagecolorallocate($finish_img,$arr_bg[0],$arr_bg[1],$arr_bg[2]);
            imageFill($finish_img,0,0,$bg);
        }
        
        
        if($h_new == $height){
            $shift = ($width - $w_new)/2;
            switch ($crop_position){
                case self::CROP_CENTER:
                    $shift_crop = $shift;
                    break;
                case self::CROP_TOP:
                    $shift_crop = 0;
                    break;
                case self::CROP_BOTTOM;
                    $shift_crop = $width - $w_new;
                    break;
                default:
                    $shift_crop = $shift;
            }
            imagecopyresized($finish_img, $min_img, $shift_crop, 0, 0, 0, $width-2*$shift, $height, $w_new, $h_new);
        }else{
            $shift = ($height - $h_new)/2;
            switch ($crop_position){
                case self::CROP_CENTER:
                    $shift_crop = $shift;
                    break;
                case self::CROP_TOP:
                    $shift_crop = 0;
                    break;
                case self::CROP_BOTTOM;
                    $shift_crop = $height - $h_new;
                    break;
                default:
                    $shift_crop = $shift;
            }
            imagecopyresized($finish_img, $min_img, 0, $shift_crop, 0, 0, $width, $height-2*$shift, $w_new, $h_new);
        }
        
        $file_name = $this->saveImg($this->ext, $finish_img, $dir_save, $new_file_name);
        imageDestroy($min_img);
        imageDestroy($finish_img);
        return $file_name;
    }

    /* пропорциональное изменение размеров изображения по ширине
     * int $width - новая ширина изображения
     * string $img_input - путь к исходному изображению
     * string $dir_save - путь к папке в которой будет сохранено измененное изображение
     * если $dir_save = null то изображение можно отдавать сразу в браузер
     * string $new_file_name - имя измененного изображения,
     * если $new_file_name = false имя измененного изображения будет такое же как и у исходного
     * string $new_ext - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату
     */
    function resizeWidth($width, $img_input, $dir_save = null, $new_file_name = false, $new_ext = false){
        
        $src = $this->createDest($img_input);
        if(!$src) return false;
        
        if($new_ext) $this->ext = $new_ext;
        
        $w_orig = imagesx($src); 
        $h_orig = imagesy($src);
        
        $arg = $w_orig/$width;
        $w_new = $width;
        $h_new = round($h_orig/$arg);
        
        $min_img = imagecreatetruecolor($w_new,$h_new);
        imagecopyresized($min_img, $src, 0, 0, 0, 0, $w_new, $h_new, $w_orig, $h_orig);
        
        $file_name = $this->saveImg($this->ext, $min_img, $dir_save, $new_file_name);
        imageDestroy($min_img);
        return $file_name;
    }

    /* пропорциональное изменение размеров изображения по высоте
     * int $height - новая высота изображения
     * string $img_input - путь к исходному изображению
     * string $dir_save - путь к папке в которой будет сохранено измененное изображение
     * если $dir_save = null то изображение можно отдавать сразу в браузер
     * string $new_file_name - имя измененного изображения,
     * если $new_file_name = false имя измененного изображения будет такое же как и у исходного
     * string $new_ext - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату
     */
    function resizeHeight($height, $img_input, $dir_save = null, $new_file_name = false, $new_ext = false){
        
        $src = $this->createDest($img_input);
        if(!$src) return false;
        
        if($new_ext) $this->ext = $new_ext;
        
        $w_orig = imagesx($src); 
        $h_orig = imagesy($src);
        
        $arg = $h_orig/$height;
        $h_new = $height;
        $w_new = round($w_orig/$arg);
        
        $min_img = imagecreatetruecolor($w_new,$h_new);
        imagecopyresized($min_img, $src, 0, 0, 0, 0, $w_new, $h_new, $w_orig, $h_orig);
        
        $file_name = $this->saveImg($this->ext, $min_img, $dir_save, $new_file_name);
        imageDestroy($min_img);
        return $file_name;
    }

    /* изменение размеров изображения в процентах
     * int $procent - новый размер изображения в процентах от исходного
     * string $img_input - путь к исходному изображению
     * string $dir_save - путь к папке в которой будет сохранено измененное изображение
     * если $dir_save = null то изображение можно отдавать сразу в браузер
     * string $new_file_name - имя измененного изображения,
     * если $new_file_name = false имя измененного изображения будет такое же как и у исходного
     * string $new_ext - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату
     */
    function resizeProcent($procent, $img_input, $dir_save = null, $new_file_name = false, $new_ext = false){
                
        $src = $this->createDest($img_input);
        if(!$src) return false;
        
        if($new_ext) $this->ext = $new_ext;
        
        $w_orig = imagesx($src); 
        $h_orig = imagesy($src);
        
        $arg = $procent/100;
        $h_new = round($h_orig*$arg);
        $w_new = round($w_orig*$arg);
        
        $min_img = imagecreatetruecolor($w_new,$h_new);
        imagecopyresized($min_img, $src, 0, 0, 0, 0, $w_new, $h_new, $w_orig, $h_orig);
        
        $file_name = $this->saveImg($this->ext, $min_img, $dir_save, $new_file_name);
        imageDestroy($min_img);
        return $file_name;
    }

    /* делает изображение шириной равной $wmax или высотой равной $hmax
     * таким образом что не высота и не ширина не превышала $wmax и $hmax соответственно
     * int $wmax - максимальная ширина в пикселях
     * int $hmax - максимальная высота в пикселях
     * string $img_input - путь к исходному изображению
     * string $dir_save - путь к папке в которой будет сохранено измененное изображение
     * если $dir_save = null то изображение можно отдавать сразу в браузер
     * string $new_file_name - имя измененного изображения,
     * если $new_file_name = false имя измененного изображения будет такое же как и у исходного
     * string $new_ext - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату
     */
    function resize($wmax, $hmax, $img_input, $dir_save = null, $new_file_name = false, $new_ext = false){

        list($w_orig, $h_orig) = getimagesize($img_input);
        $ratio = $w_orig / $h_orig; // =1 - квадрат, <1 - альбомная, >1 - книжная

        if(($wmax / $hmax) > $ratio){
            $wmax = $hmax * $ratio;
        }else{
            $hmax = $wmax / $ratio;
        }


        $img = $this->createDest($img_input);
        if(!$img) return false;

        if($new_ext) $this->ext = $new_ext;

        $newImg = imagecreatetruecolor($wmax, $hmax); // создаем оболочку для новой картинки

        $this->alphaToPng($newImg, $this->ext);



        imagecopyresampled($newImg, $img, 0, 0, 0, 0, $wmax, $hmax, $w_orig, $h_orig); // копируем и ресайзим изображение

        $file_name = $this->saveImg($this->ext, $newImg, $dir_save, $new_file_name);
        imagedestroy($newImg);
        return $file_name;
    }

/* ===== создание ресурса изображения из исходного изображения ===== */  
    private function createDest($img_input){
        
        $arr_info_file = pathinfo($img_input);
        $this->ext = strtolower($arr_info_file['extension']);
        $this->filename = $arr_info_file['filename'];
        
        if(!$img_input) return false;
        
        //создаем изображение из исходного файла
        if($this->ext == "jpg" or $this->ext == "jpeg") $func = 'imagecreatefromjpeg';
        else if($this->ext == "png") $func = 'imagecreatefrompng';
        else if($this->ext == "gif") $func = 'imagecreatefromgif';
        else return false;
        
        return $func($img_input);
    }
/* ===== END создание ресурса изображения из исходного изображения ===== */
 
/* ===== сохранение изображения в нужный формат или вывод изображения в брауээер ===== */  
    private function saveImg($ext, $resource_img, $dir_save = null, $new_file_name = false){
        
        $dir_save = trim($dir_save, DIRECTORY_SEPARATOR);
        $dir_save = trim($dir_save, "\\");
        $dir_save = trim($dir_save, "/");
        $new_file_name = $new_file_name ? $new_file_name.'.'.$ext : $this->filename.'.'.$ext;
        $file_save = $dir_save == null ? null : $dir_save.DIRECTORY_SEPARATOR.$new_file_name;
        
        switch($ext){
            case("gif"):
                if($file_save == null) header("Content-type: image/gif");
                imagegif($resource_img, $file_save);
                break;
            case("png"):
                if($file_save == null) header("Content-type: image/png");
                imagepng($resource_img, $file_save, 0);
                break;
            default:
                if($file_save == null) header("Content-type: image/jpeg");
                imagejpeg($resource_img, $file_save, 100);
         }
        return $file_save;
     }
/* ===== END сохранение изображения в нужный формат или вывод изображения в брауээер ===== */

/* ===== прозрачный слой для png файлов ===== */
    private function alphaToPng($img_resource, $ext){
        if($ext == "png"){
           imagesavealpha($img_resource, true); // сохранение альфа канала
           $transPng = imagecolorallocatealpha($img_resource,0,0,0,127); // добавляем прозрачность
           imagefill($img_resource, 0, 0, $transPng); // заливка 
       }
    }
/* ===== END прозрачный слой для png файлов ===== */

}

