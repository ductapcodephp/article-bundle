<?php

namespace AmzsCMS\ArticleBundle\DataType;

class ArticleStatusType
{
    /** STATUS IS_HOT */
    const HOT_TYPE_NORMAL = 1;
    const HOT_TYPE_HOT = 2;

    /** STATUS IS_NEW */
    const NEW_TYPE_NORMAL = 1;
    const NEW_TYPE_NEW = 2;

    /** STATUS PUBLISHED */
    const PUBLISH_TYPE_PUBLISHED = 2;
    const PUBLISH_TYPE_DRAFT = 1;

    public static function getNameByPublishType(int $publishType): string
    {
        switch ($publishType) {
            case self::PUBLISH_TYPE_DRAFT:
                return "Draft";
            case self::PUBLISH_TYPE_PUBLISHED:
                return "Published";
            default:
                return ""; // PHP 7.4 cần giá trị trả về mặc định nếu không khớp
        }
    }

    public static function getNameHotType(?int $hotType): string
    {
        switch ($hotType) {
            case self::HOT_TYPE_HOT:
                return "Hot";
            default:
                return "Normal";
        }
    }

    public static function getNameNewType(?int $newType): string
    {
        switch ($newType) {
            case self::NEW_TYPE_NEW:
                return "New";
            default:
                return "Normal";
        }
    }
}
