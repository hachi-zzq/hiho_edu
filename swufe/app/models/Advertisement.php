<?php

class Advertisement extends \Eloquent {


    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

}