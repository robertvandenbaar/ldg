<?php

namespace Ldg\Model;


class Video extends File
{
    public function getPlayUrl()
    {
        return BASE_URL . '/video_stream' . $this->getRelativeLocation();
    }
}