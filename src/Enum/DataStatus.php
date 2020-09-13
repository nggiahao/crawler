<?php


namespace Nggiahao\Crawler\Enum;


use MyCLabs\Enum\Enum;

class DataStatus extends Enum
{
    use ToOptions;

    public const DATA_INIT = 0;
    public const DATA_YES = 1;
    public const DATA_NO = -1;
    public const DATA_ERROR = -5;


}