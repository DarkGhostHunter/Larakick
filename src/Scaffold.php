<?php
/**
 * This class allows to centralize the raw data parsed and the scaffold information from each
 */

namespace DarkGhostHunter\Larakick;

use Illuminate\Support\Fluent;

/**
 * Class Scaffold
 *
 * @package DarkGhostHunter\Larakick
 *
 * @property \Illuminate\Config\Repository $rawDatabase
 * @property \Illuminate\Config\Repository $rawHttp
 * @property \Illuminate\Config\Repository $rawAuth
 *
 * @property \DarkGhostHunter\Larakick\Lexing\ScaffoldDatabase $database
 * @property \DarkGhostHunter\Larakick\Lexing\ScaffoldHttp $http
 * @property \DarkGhostHunter\Larakick\Lexing\ScaffoldAuth $auth
 */
class Scaffold extends Fluent
{
    /**
     * Returns a given model from the raw database scaffold, or one of its given keys.
     *
     * @param  string  $key
     * @param  string|null  $sub
     * @return array
     */
    public function getRawModel(string $key, string $sub = null)
    {
        $key = $sub ? "$key.$sub" : $key;

        return $this->rawDatabase->get("models.{$key}");
    }

    /**
     * Returns all the models from the database scaffold.
     *
     * @return array
     */
    public function getRawModels()
    {
        return $this->rawDatabase->get('models');
    }
}
