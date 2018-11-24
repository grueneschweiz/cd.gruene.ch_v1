<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 19.04.18
 * Time: 18:36
 */

namespace App\Controller;


use App\Model\Entity\User;
use Cake\Http\Response;

class ProtectedController extends AppController
{

    /**
     * Grant access to every logged in user
     *
     * @param User|array $user
     *
     * @return boolean
     */
    public function isAuthorized($user)
    {
        return true;
    }

    /**
     * Serve file with the given path relative to the folder 'protected'
     *
     * @param string $path
     *
     * @return Response
     */
    public function serve(string $path)
    {
        $file = ROOT . DS . 'protected' . DS . $path;
        $this->response = $this->response->withCache(filemtime($file), '+30 days');

        return $this->response->withFile($file);
    }

}