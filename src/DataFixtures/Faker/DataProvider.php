<?php

namespace App\DataFixtures\Faker;


// $faker->addProvider(new \Faker\Provider\Movie($faker));

class DataProvider extends \Faker\Provider\Base
{
    protected static $tags = [
        'Sport',
        'Jeux',
        'Informatique',
        'Musique',
        'Bien-être',
        'Montagne',
        'Lac',
        'Bar',
        'Cinéma',
        'Musée',
        'plein air',
        'Concert',
        'Soirée',
        'Inauguration'
    ];



    /**
     * @return string
     */
    public function tagName()
    {
        return static::randomElement(static::$tags);
    }
}