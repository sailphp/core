<?php


namespace SailPHP\Auth;


use SailPHP\Exception\NoAuthableLoggedInException;
use SailPHP\Session\Session;

class SessionAuthAdapter implements AuthAdapter
{
    protected $config = [];
    protected $session;
    protected $field = 'id';

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    public function login(Authable $authable, $field = 'id')
    {
        $this->field = $field;
        if($this->loggedIn()) {
            return $this->getUser();
        }

        $data = $this->session->serialize($authable->serialize($this->field));

        $this->session->put($this->config['session_key'], $data);

        return $authable;
    }

    public function loggedIn()
    {
        return $this->session->has($this->config['session_key']);
    }

    public function user()
    {
       if(!$this->loggedIn()) {
           throw new NoAuthableLoggedInException;
       }

       return $this->getUser();
    }

    private function getUser()
    {
        
       $data = $this->session->get($this->config['session_key']);

        try {
            $unserialised = $this->session->unserialize($data);

            if(!isset($unserialised->id)) {
                throw new \Exception;
            }

            $model = $this->config['auth_model'];
            

            if(method_exists($model, 'byId')) {
                $user = $model::byId($unserialised->id);
            } else {
                $user = $model::where($this->field, $unserialised->id)->first();
            }

            if(!($user instanceof Authable)) {
                throw new \Exception;
            }
        } catch(\Exception $e) {
            $this->logout();
            return null;
        }

        return $user;
    }

    public function logout()
    {
        if(!$this->loggedIn()) {
            return false;
        }

        $this->session->delete($this->config['session_key']);

        return true;
    }
}