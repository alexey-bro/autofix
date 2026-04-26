<?php

namespace common\models;

use common\models\query\FileQuery;
use Imagick;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $uuid Публичный идентификатор файла
 * @property int $user_id Кто создал
 * @property int $entity_id ID сущности
 * @property string folder Тип сущности: user, category, service
 * @property int $type Предназначение
 * @property int $sub_type Категория предназначение
 * @property string $original_name Оригинальное имя файла
 * @property string $extension Оригинальное имя файла
 * @property string $path Оригинальное имя файла
 * @property string $mime_type
 * @property string|null $description Описание
 * @property int $size Размер в байтах
 * @property string $file_hash Хеш файла для поиска дубликатов
 * @property string $created_at
 * @property string $updated_at
 */
class File extends \yii\db\ActiveRecord
{

    private $_file;

    public const WIDTH_FULL_HD = 1920;
    public const HEIGHT_FULL_HD = 1080;

    public const WIDTH_HD = 1280;
    public const HEIGHT_HD = 720;

    public const TYPE_AVATAR = 1;                           //  Аватар пользователя
    public const SUB_TYPE_AVATAR = 0;                       //

    public const TYPE_PROMOTION = 2;                        //  Постер промо акции
    public const SUB_TYPE_PROMOTION = 0;                    //

    public const TYPE_SERVICE = 3;                          //  Услуги пользователя
    public const SUB_TYPE_SERVICE = 0;                      //


    public const ENTITY_TYPE_USER = 'user';
    public const ENTITY_TYPE_PROMOTION = 'promotion';
    public const ENTITY_TYPE_SERVICE = 'service';


    //Разделы
    public static function listType(): array
    {
        return [
            self::TYPE_AVATAR => 'Аватар',
            self::TYPE_PROMOTION => 'Промо акция',
            self::TYPE_SERVICE => 'Услуга пользователя',
        ];
    }

    //Типы загрузок, у которых может быть только один файл, при загрузке нового, удаляем предыдущий
    public static function listTypeOneFile(): array
    {
        return [
            [
                'type' => self::TYPE_AVATAR,
                'sub_type' => self::SUB_TYPE_AVATAR,
            ]
        ];
    }

    //папки разделов
    public static array $folderByType = [
        self::TYPE_AVATAR => self::ENTITY_TYPE_USER,
        self::TYPE_PROMOTION => self::ENTITY_TYPE_PROMOTION,
        self::TYPE_SERVICE => self::ENTITY_TYPE_SERVICE,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entity_id', 'type', 'sub_type', 'description'], 'default', 'value' => null],
            [['uuid', 'user_id', 'original_name', 'mime_type', 'size', 'file_hash', 'type', 'sub_type', 'extension', 'path'], 'required'],
            [['user_id', 'entity_id', 'size', 'type', 'sub_type'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['uuid'], 'string', 'max' => 36],
            [['extension'], 'string', 'max' => 50],
            [['original_name', 'path'], 'string', 'max' => 255],
            [['mime_type'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 150],
            [['file_hash'], 'string', 'max' => 64],
            [['uuid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uuid',
            'user_id' => 'User ID',
            'entity_id' => 'Entity ID',
//            'folder' => 'Entity Type',
            'type' => 'Type',
            'sub_type' => 'Sub Type',
            'original_name' => 'Original Name',
            'mime_type' => 'Mime Type',
            'extension' => 'Extension',
            'description' => 'Description',
            'size' => 'Size',
            'path' => 'Path',
            'file_hash' => 'File Hash',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getMimeType($type)
    {
        $mimeImg = ['image/png', 'image/jpg', 'image/jpeg', 'image/heic', 'image/webp'];
        $mimeExcel = ['application/excel', 'application/vnd.ms-excel', 'application/x-excel', 'application/x-msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel.sheet.macroenabled.12', 'application/vnd.ms-office'];
        $mimeArchive = ['application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip', 'application/x-webarchive', 'application/x-rar', 'application/x-rar-compressed', 'application/x-7z-compressed'];
        $mimeOpenDocument = ['application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.presentation', 'application/vnd.oasis.opendocument.graphics', 'application/vnd.ms-office'];
        $mimeOtherMicrosoftOffice = ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $mimeVideo = ['video/mpeg', 'video/mp4', 'video/ogg', 'video/quicktime', 'video/webm', 'video/x-ms-wmv', 'video/x-flv', 'video/x-msvideo', 'video/3gpp', 'video/3gpp2', 'video/h264', 'video/h265', 'video/avi'];
        $otherText = ['text/rtf', 'application/rtf'];
        $other = ['application/octet-stream'];

        $all = array_unique(array_merge($mimeImg, $mimeExcel, $mimeArchive, $mimeOpenDocument, $mimeOtherMicrosoftOffice, $otherText, $other, $mimeVideo, ['application/pdf'], ['image/tiff']));

        $mimeType = [
            self::TYPE_AVATAR => $mimeImg,
            self::TYPE_PROMOTION => $mimeImg,
            self::TYPE_SERVICE => $mimeImg,
        ];

        return $mimeType[$type];
    }

    public function loadFile(UploadedFile $file): void
    {
        $this->_file = $file;

        $this->setHashFile();
        $this->setOriginalName();
        $this->setMimeType();
        $this->setExtension();
        $this->setUuid();
        $this->setSize();

    }

    public static function checkMime($type, $mime, $gain = true)
    {

        $mimeJpeg = ['image/jpg', 'image/jpeg'];
        $mimeImg = ['image/png', 'image/jpg', 'image/jpeg', 'image/heic', 'image/webp'];
        $mimeExcel = ['application/excel', 'application/vnd.ms-excel', 'application/x-excel', 'application/x-msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel.sheet.macroenabled.12', 'application/vnd.ms-office'];
        $mimeArchive = ['application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip', 'application/x-webarchive', 'application/x-rar', 'application/x-rar-compressed', 'application/x-7z-compressed'];
        $mimeOpenDocument = ['application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.presentation', 'application/vnd.oasis.opendocument.graphics', 'application/vnd.ms-office'];
        $mimeOtherMicrosoftOffice = ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $mimeVideo = ['video/mpeg', 'video/mp4', 'video/ogg', 'video/quicktime', 'video/webm', 'video/x-ms-wmv', 'video/x-flv', 'video/x-msvideo', 'video/3gpp', 'video/3gpp2', 'video/h264', 'video/h265', 'video/avi'];
        $otherText = ['text/rtf', 'application/rtf'];
        $other = ['application/octet-stream'];

        $all = array_unique(array_merge($mimeImg, $mimeExcel, $mimeArchive, $mimeOpenDocument, $mimeOtherMicrosoftOffice, $otherText, $other, $mimeVideo, ['application/pdf'], ['image/tiff']));

        $mimeType = [
            self::TYPE_AVATAR => $mimeImg,
            self::TYPE_PROMOTION => $mimeImg,
            self::TYPE_SERVICE => $mimeImg,
        ];

        if ($gain) {
            return in_array($mime, $mimeType[$type]);
        }

        return $mimeType[$type];
    }

    // Какие типы сжимать
    public static function listCompress(): array
    {
        return [
            self::TYPE_AVATAR => 'Аватар пользователя',
            self::TYPE_SERVICE => 'Услуги пользователя',
            self::TYPE_PROMOTION => 'Промо акция',
        ];
    }

    protected function saveOnDisk(): bool
    {

        $file = $this->_file;

        $pathFrontend = Yii::getAlias('@frontend');
        $filePath = \Yii::getAlias($pathFrontend . '/web/files/');

        if (!file_exists($filePath)) {
            if (!mkdir($filePath, 0775, true) && !is_dir($filePath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $filePath));
            }
        }

        $folder = self::FindFreeFolder($filePath);
        $freeFolder = self::SetFreeFolder($filePath, $folder);

        $newFileName = $this->uuid . '.' . $this->extension;
        $newFilePath = $freeFolder . $newFileName;

        $access = true;
        $mime = $file->type;

        $this->path = '/files/' . $folder . DIRECTORY_SEPARATOR;

        $trueMime = mime_content_type($file->tempName);
        $checkMime = self::checkMime($this->type, $trueMime, true);

        if ($checkMime) {

            if (isset(self::listCompress()[$this->type])) {
                try {

                    list($width, $height) = getimagesize($file->tempName);
                    $image = new Imagick($file->tempName);

                    if ($width > self::WIDTH_FULL_HD || $height > self::HEIGHT_FULL_HD) {
                        $image->resizeImage(self::WIDTH_FULL_HD, self::HEIGHT_FULL_HD, Imagick::FILTER_UNDEFINED, 1, true);
                    } else if (($width <= self::WIDTH_FULL_HD && $width > self::WIDTH_HD) || ($height <= self::HEIGHT_FULL_HD && $height > self::HEIGHT_HD)) {
                        $image->resizeImage(self::WIDTH_HD, self::HEIGHT_HD, Imagick::FILTER_UNDEFINED, 1, true);
                    }
                    $image->setImageCompressionQuality(70);
                    $image->writeImage($newFilePath);
                    chmod($newFilePath, 0775);

                } catch (\Exception $e) {
                    $access = false;
                }

            } else {
                $file->saveAs($newFilePath);
            }
        }

        return $access;
    }

    public static function FindFreeFolder($path)
    {
        // Ищем последнюю папку
        $truefindfolder = false;
        $arrFolder = array_filter(scandir($path), static function ($var) {
            return is_numeric($var);
        });

        arsort($arrFolder);
        $numfolder = current($arrFolder) ?: 1;

        $freeFolder = '';

        while ($truefindfolder == false) {

            $freeFolder = $path . $numfolder;

            if (file_exists($freeFolder)) {
                if (is_dir($freeFolder)) {

                    $c = 0;
                    $d = dir($freeFolder);

                    while ($str = $d->read()) {
                        if ($str[0] != '.') {
                            $c++;
                        };
                    }
                    $d->close();

                    if ($c < 999) {
                        $truefindfolder = true;
                    } else {
                        $numfolder++;
                    }

                }
            } else {
                $truefindfolder = true;
            }

        }

        return $numfolder;

    }

    public static function SetFreeFolder($path, $num)
    {

        // Устанавливаем свободную папку
        $freeFolder = $path . $num . '/';

        if (!file_exists($freeFolder)) {
            if (!mkdir($freeFolder, 0755) && !is_dir($freeFolder)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $freeFolder));
            }
            @chmod($freeFolder, 0755);
        }

        return $freeFolder;

    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function setSubType($subType): void
    {
        $this->sub_type = $subType;
    }

    public function saveFile(): bool
    {
        $this->saveOnDisk();

        return $this->save();
    }

    private function checkOnlyOneFile(): bool
    {
        $listOnlyOneFile = $this->listTypeOneFile();
    }

    public function setUserId($userId): void
    {
        $this->user_id = $userId;
    }


    /**
     * Хеш содержимого файла — для поиска дубликатов.
     * sha256 даёт 64-символьную строку, храним в VARCHAR(64).
     */
    protected function setHashFile(): void
    {
        $filePath = $this->_file->tempName;

        if (!is_readable($filePath)) {
            throw new \RuntimeException("Файл недоступен для чтения");
        }

        $this->file_hash = hash_file('sha256', $filePath);
    }

    protected function setUuid(): void
    {
        $this->uuid = $this->generateUuid();
    }

    protected function setSize(): void
    {
        $this->size = $this->_file->size;
    }

    public function setEntityId($id): void
    {
        $this->entity_id = $id;
    }

    protected function setOriginalName(): void
    {
        $this->original_name = $this->_file->name;
    }

    protected function setMimeType(): void
    {
        $filePath = $this->_file->tempName;

        $mimeType = mime_content_type($filePath);
        $this->mime_type = $mimeType;

        //TODO: сделать валидацию mime type
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // This block runs only on 'insert' (new records)
        if ($insert) {

            // Проверка существует ли ключ и соответствует ли значение
            $listTypeOneFile = $this->listTypeOneFile();

            $exists = !empty(array_filter($listTypeOneFile, fn($item) =>
                $item['type'] === $this->type && $item['sub_type'] === $this->sub_type
            ));

            if ($exists) {

                $File = self::find()
                    ->where([
                        'type' => $this->type,
                        'sub_type' => $this->sub_type,
                        'entity_id' => $this->entity_id,
                    ])
                    ->one();

                if ($File) {
                    $File->delete();
                }
            }
        }

        return true;
    }

    protected function setExtension(): void
    {
        $this->extension = $this->_file->extension;
    }

    public function getPathRootFile()
    {
        return Yii::getAlias('@frontend' . $this->path . $this->uuid . '.' . $this->extension);
    }

    public function getPathRootDir()
    {
        return Yii::getAlias('@frontend' . $this->path . $this->uuid . '.' . $this->extension);
    }


    public function getUrl(): string
    {
        return Yii::getAlias('@frontWeb') . $this->path . $this->uuid . '.' . $this->extension;
    }

    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        $path = $this->getPathRootFile();

        if (file_exists($path)) {
            @unlink($path);
        }

        @rmdir($this->getPathRootDir());

        return true;
    }

    /**
     * @param string $path
     * @param string $extension
     * @return string
     * @throws \Exception
     */
    public static function generateUuid(): string
    {
        // Генерация названия
        $trueFindFile = false;
        $name = '';

        while ($trueFindFile === false) {
            $newName = bin2hex(random_bytes(16));

            if (!self::find()->where(['uuid' => $newName])->exists()) {
                $trueFindFile = true;
                $name = $newName;
            }
        }

        return $name;
    }

    /**
     * {@inheritdoc}
     * @return FileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FileQuery(get_called_class());
    }

}
