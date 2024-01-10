<?php

namespace System\Modules\Core\Interfaces;

enum RequestContentType: string
{
        // Data
    case JSON = 'application/json';
    case XML = 'application/xml';
    case TEXT = 'text/plain';

        // Rich text
    case HTML = 'text/html';

        // Forms
    case FORM = 'application/x-www-form-urlencoded';
    case MULTIPART = 'multipart/form-data';

        // Not set
    case NONE = 'none';

    public static function fromString(string $contentType): RequestContentType
    {
        $contentType = strtolower($contentType);
        if (strpos($contentType, ';') !== false) {
            $contentType = substr($contentType, 0, strpos($contentType, ';'));
        }
        if (strpos($contentType, ',') !== false) {
            $contentType = substr($contentType, 0, strpos($contentType, ','));
        }

        switch ($contentType) {
            case 'application/json':
                return self::JSON;
            case 'application/xml':
                return self::XML;
            case 'text/plain':
                return self::TEXT;
            case 'text/html':
                return self::HTML;
            case 'application/x-www-form-urlencoded':
                return self::FORM;
            case 'multipart/form-data':
                return self::MULTIPART;
            default:
                return self::NONE;
        }
    }
}
