<?php

namespace App\Services;

use DfaFilter\SensitiveHelper;
use Illuminate\Support\Facades\Storage;

class SensitiveWords
{
    protected static $handle = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * 获取实例
     */
    public static function getInstance($word_path = [])
    {
        if (!self::$handle) {
            if (!file_exists(storage_path('dict/words.txt'))) {
                self::getfile();// 如果本地不存在敏感词词库 下载到本地
            } else {
                $time = Storage::disk('public')->lastModified('dict/words.txt');// public上的文件时间戳
                $time2 = Storage::disk('dict')->lastModified('dict/words.txt');// 本地文件时间戳
                if ($time2 < $time) {
                    self::getfile();// 如果本地文件时间 < 云存储上文件时间,下载文件到本地
                }
            }
            $default_path = [
                storage_path('dict/words.txt'),
            ];

            $paths = array_merge($default_path, $word_path);

            self::$handle = SensitiveHelper::init();

            if (!empty($paths)) {
                foreach ($paths as $path) {
                    self::$handle->setTreeByFile($path);
                }
            }
        }
        return self::$handle;
    }

    /**
     * 检测是否含有敏感词
     */
    public static function isLegal($content)
    {
        return self::getInstance()->islegal($content);
    }

    /**
     * 敏感词过滤
     */
    public static function replace($content, $replace_char = '', $repeat = false, $match_type = 1)
    {
        return self::getInstance()->replace($content, $replace_char, $repeat, $match_type);
    }

    /**
     * 标记敏感词
     */
    public static function mark($content, $start_tag, $end_tag, $match_type = 1)
    {
        return self::getInstance()->mark($content, $start_tag, $end_tag, $match_type);
    }

    /**
     * 获取文本中的敏感词
     */
    public static function getBadWord($content, $match_type = 1, $word_num = 0)
    {
        return self::getInstance()->getBadWord($content, $match_type, $word_num);
    }

    /**
     *  下载S3上的敏感词到本地
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getfile()
    {
        $contents = Storage::disk('public')->get('dict/words.txt');// 获取public的敏感词词库
        Storage::disk('dict')->put('dict/words.txt', $contents, 'public');// 写入本地
    }
}
