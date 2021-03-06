<h1>Класс EditImg предназначенный для изменения размера изображения.</h1>

<p>Имеет в себе методы для изменения размера изображения обрезая или вписывая исходное изображение в область заданного размера, пропорциональное изменения изображения по ширине или высоте а также изменение размера изображения в процентах от исходного изображения. Также имеется метод для загрузки изображения в заданную папку из массива $_FILES</p>

<h3><ins>uploadToDir</ins> статический метод класса EditImg</h3>

<p>array <b>uploadToDir</b>(array $files,string $to_dir,[boolean $uniq=false,[boolean $func_name = false]])</p>

<p>Загрузка файлов в папку из массив $_FILES</p>
<p>array <b>$files</b> - массив $_FILES можно загружать как один файл так несколько файлов сразу</p>
<p>string <b>$to_dir</b> - папка назначения в которую будут загружены файлы</p>
<p>boolean <b>$uniq</b> - если установить true то к имени файла будет добавлено уникальное значение функцией uniqid()</p>
<p>string <b>$func_name</b> - можно задать имя функции которой будет обработано имя файла, к примеру можно исходное имя файла обработать функцией translit чтобы в имени загруженных файлов не было кирелических символов</p>

<p><b>Возвращаемые значения</b><br>
Возвращает массив путей к загруженным файлам</p><br><br>

<h3><ins>crop</ins> метод класса EditImg</h3>

<p>string <b>crop</b>(int $width, int $height, string $img_input, [string $dir_save = null, [string $new_file_name = false, [string $new_ext = false, [int $crop_position = self::CROP_CENTER]]]])</p>

<p>Изменить размер и обрезать изображение</p>
<p>Пропорционально изменяет размер исходного изображения таким образом чтоб максимально эффективно вписать в него область шириной $width и высотой $height после чего создает новое изображение из исходного обрезав области не вошедшие в область шириной $width и высотой $height</p>

<p>int <b>$width</b> - ширина нового изображения</p>
<p>int <b>$heigh</b>t - высота нового изображения</p>
<p>string <b>$img_input</b> - путь к исходному изображению</p>
<p>string <b>$dir_save</b> - путь к папке в которой будет сохранено измененное изображение если $dir_save = null то изображение можно отдавать сразу в браузер</p>
<p>string <b>$new_file_name</b> - имя измененного изображения, если $new_file_name = false имя измененного изображения будет такое же как и у исходного</p>
<p>string <b>$new_ext</b> - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату</p>
<p><b>$crop_position</b> - может иметь значения: EditImg::CROP_CENTER, EditImg::CROP_TOP, EditImg::CROP_BOTTOM <br>
<b>EditImg::CROP_CENTER</b> - область вырежется из центра изображения <br>
<b>EditImg::CROP_TOP</b> - если изображение в книжном формате то область вырежется из верха изображения если в альбомном формате то область вырежется из левой части изображения<br>
<b>EditImg::CROP_BOTTOM</b> - если изображение в книжном формате то область вырежется из низа изображения если в альбомном формате то область вырежется из правой части изображения</p>

<p><b>Возвращаемые значения</b><br>
Возвращает путь к вновь созданному изображению, или null в случае если изображение выводится в браузер</p><br><br>



<h3><ins>inscribed</ins> метод класса EditImg</h3>

<p>string <b>inscribed</b>(int $width, int $height, string $img_input, [ string $dir_save = null, [ string $new_file_name = false, [ array $arr_bg = false, [ string $new_ext = false, [ int $crop_position = self::CROP_CENTER]]]]]);</p>

<p>Уменьшить и вписать изображение</p>
<p>Пропорционально изменяет размер исходного изображения таким образом чтоб максимально эффективно вписать его в область шириной $width и высотой $height после чего если в области шириной $width и высотой $height осталось незаполненное место зальет его цветом $arr_bg</p>

<p>int <b>$width</b> - ширина нового изображения</p>
<p>int <b>$heigh</b>t - высота нового изображения</p>
<p>string <b>$img_input</b> - путь к исходному изображению</p>
<p>string <b>$dir_save</b> - путь к папке в которой будет сохранено измененное изображение если $dir_save = null то изображение можно отдавать сразу в браузер</p>
<p>string <b>$new_file_name</b> - имя измененного изображения, если $new_file_name = false имя измененного изображения будет такое же как и у исходного</p>
<p>array <b>$arr_bg</b> - массив с цветом заливки пустого места в результирующем изображении. Пример [255,255,255] если $arr_bg = false - елси формат результирующего изображения будет png то цвет заливки будет прозрачный если другой формат то цвет заливки будет белый</p>
<p>string <b>$new_ext</b> - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату</p>
<p><b>$crop_position</b> - может иметь значения: EditImg::CROP_CENTER, EditImg::CROP_TOP, EditImg::CROP_BOTTOM <br>
<b>EditImg::CROP_CENTER</b> - исходное изображение окажется по центру вновь созданного изображения (если не будет занимать всю обасть) <br>
<b>EditImg::CROP_TOP</b> - если изображение в книжном формате то исходное изображение окажется вверху вновьсозданного (если не будет занимать всю обасть) если в альбомном формате то исходное изображение окажется в левой части вновьсозданного<br>
<b>EditImg::CROP_BOTTOM</b> - если изображение в книжном формате то исходное изображение окажется внизу вновьсозданного (если не будет занимать всю обасть) если в альбомном формате то исходное изображение окажется в правой части вновьсозданного</p>

<p><b>Возвращаемые значения</b><br>
Возвращает путь к вновь созданному изображению, или null в случае если изображение выводится в браузер</p><br><br>


<h3><ins>resizeWidth</ins> метод класса EditImg</h3>

<p>string <b>resizeWidth</b>(int $width, int $img_input, [ string $dir_save = null, [ string $new_file_name = false, [ string $new_ext = false]]])</p>

<p>Пропорциональное изменение размеров изображения по ширине</p>

<p>int <b>$width</b> - новая ширина изображения</p>
<p>string <b>$img_input</b> - путь к исходному изображению</p>
<p>string <b>$dir_save</b> - путь к папке в которой будет сохранено измененное изображение если $dir_save = null то изображение можно отдавать сразу в браузер</p>
<p>string <b>$new_file_name</b> - имя измененного изображения, если $new_file_name = false имя измененного изображения будет такое же как и у исходного</p>
<p>string <b>$new_ext</b> - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату</p>

<p><b>Возвращаемые значения</b><br>
Возвращает путь к вновь созданному изображению, или null в случае если изображение выводится в браузер</p><br><br>


<h3><ins>resizeHeight </ins> метод класса EditImg</h3>

<p>string <b>resizeHeight </b>(int $height, int $img_input, [ string $dir_save = null, [ string $new_file_name = false, [ string $new_ext = false]]])</p>

<p>Пропорциональное изменение размеров изображения по высоте</p>

<p>int <b>$height</b> - новая высота изображения</p>
<p>string <b>$img_input</b> - путь к исходному изображению</p>
<p>string <b>$dir_save</b> - путь к папке в которой будет сохранено измененное изображение если $dir_save = null то изображение можно отдавать сразу в браузер</p>
<p>string <b>$new_file_name</b> - имя измененного изображения, если $new_file_name = false имя измененного изображения будет такое же как и у исходного</p>
<p>string <b>$new_ext</b> - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату</p>

<p><b>Возвращаемые значения</b><br>
Возвращает путь к вновь созданному изображению, или null в случае если изображение выводится в браузер</p><br><br>


<h3><ins>resizeProcent  </ins> метод класса EditImg</h3>

<p>string <b>resizeProcent  </b>(int $procent, int $img_input, [ string $dir_save = null, [ string $new_file_name = false, [ string $new_ext = false]]])</p>

<p>изменение размеров изображения в процентах</p>

<p>int <b>$procent</b> - новый размер изображения в процентах от исходного</p>
<p>string <b>$img_input</b> - путь к исходному изображению</p>
<p>string <b>$dir_save</b> - путь к папке в которой будет сохранено измененное изображение если $dir_save = null то изображение можно отдавать сразу в браузер</p>
<p>string <b>$new_file_name</b> - имя измененного изображения, если $new_file_name = false имя измененного изображения будет такое же как и у исходного</p>
<p>string <b>$new_ext</b> - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату</p>

<p><b>Возвращаемые значения</b><br>
Возвращает путь к вновь созданному изображению, или null в случае если изображение выводится в браузер</p><br><br>



<h3><ins>resize</ins> метод класса EditImg</h3>

<p>string <b>resize</b>(int $wmax, int $hmax, string $img_input,[string $dir_save = null, [string  $new_file_name = false, [string $new_ext = false]]])</p>

<p>Делает изображение шириной равной $wmax или высотой равной $hmax таким образом что не высота и не ширина не превышала $wmax и $hmax соответственно</p>

<p>int <b>$wmax</b> - максимальная ширина в пикселях</p>
<p>int <b>$hmax</b> - максимальная высота в пикселях</p>
<p>string <b>$img_input</b> - путь к исходному изображению</p>
<p>string <b>$dir_save</b> - путь к папке в которой будет сохранено измененное изображение если $dir_save = null то изображение можно отдавать сразу в браузер</p>
<p>string <b>$new_file_name</b> - имя измененного изображения, если $new_file_name = false имя измененного изображения будет такое же как и у исходного</p>
<p>string <b>$new_ext</b> - 'jpg', 'png' или 'gif'. Изображение будет преобразовано к указанному формату</p>

<p><b>Возвращаемые значения</b><br>
Возвращает путь к вновь созданному изображению, или null в случае если изображение выводится в браузер</p><br><br>
