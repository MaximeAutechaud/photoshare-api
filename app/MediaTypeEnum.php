<?php

namespace App;

enum MediaTypeEnum: string
{
    case JPG = 'image/jpeg';
    case PNG = 'image/png';
    case MP4 = 'video/mp4';
    case MOV = 'video/mov';
    case AVI = 'video/avi';
    case WEBM = 'video/webm';
}
