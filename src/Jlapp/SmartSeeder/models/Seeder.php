<?php


class Seeder extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'seeds';

    protected $fillable = array(
        'seed',
        'env',
        'batch'
    );
}
