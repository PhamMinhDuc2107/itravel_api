<?php

namespace App\Constant;

class UploadConstant
{
    // System Config
    public const DISK = 'public';
    public const DEFAULT_MODULE = 'others';
    public const AVATAR_MODULE = 'avatar';
    public const TOUR_MODULE = 'tour';
    public const HOTEL_MODULE = 'hotel';
    public const BANNER_MODULE = 'banner';
    public const AMENITY_MODULE = 'amenity';

    // Image Config
    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    // Blacklist
    public const BLACKLIST_EXTENSIONS = ['php', 'php3', 'php4', 'phtml', 'exe', 'sh', 'bat', 'html', 'js'];

    // Size & Quality
    public const IMAGE_MAX_SIZE = 5120; // KB
    public const IMAGE_MAX_WIDTH = 1920;
    public const IMAGE_QUALITY = 85;
    public const WEBP_QUALITY = 80;
    public const IMAGE_TARGET_FORMAT = 'webp';

    // Processing Flags
    public const OPTIMIZE_IMAGE = true;

    public static function getImageMimesString(): string
    {
        return implode(',', self::IMAGE_EXTENSIONS);
    }
}
