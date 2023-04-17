<?php

namespace App\Service;
class PlayerService
{
    public function getKeySongs($songs, $song) : int|String
    {
        $selectedSongKey = null;
        foreach ($songs as $key => $value) {
            if ($value->getId() === $song->getId()) {
                $selectedSongKey = $key;
            }
        }

        return $selectedSongKey;
    }
    public function getSongsArray($item) : array
    {
        return $item->getSongs()->toArray();
    }
}