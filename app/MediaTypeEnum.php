<?php

namespace App;

enum MediaTypeEnum: string
{
    case JPG = 'image/jpeg';
    case PNG = 'image/png';
    case WEBP = 'image/webp';
    case HEIC = 'image/heic';
    case TIFF = 'image/tiff';
    case BMP = 'image/bmp';
    case AVIF = 'image/avif';
    case MP4 = 'video/mp4';
    case MOV = 'video/mov';
    case AVI = 'video/avi';
    case WEBM = 'video/webm';
}
