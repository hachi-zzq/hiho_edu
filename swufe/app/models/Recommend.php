<?php

class Recommend extends \Eloquent {


    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
}