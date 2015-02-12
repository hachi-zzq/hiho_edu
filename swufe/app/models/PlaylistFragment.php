<?php

class PlaylistFragment extends \Eloquent
{

    protected $table = 'playlists_fragments';

    protected $softDeletes = FALSE;

    public function fragment()
    {
        return $this->belongsTo('Fragment');
    }
}