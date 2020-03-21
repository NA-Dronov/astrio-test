<?php
/*
Написать класс-оболочку для работы с cookie. Класс должен содержать следующий
набор функций: установка куки, удаление куки, редактирование куки, считывание куки.
По умолчанию кука должна устанавливаться на 1 год, при этом должна быть
предусмотрена возможность указать произвольное время жизни куки. Класс должен быть
реализован таким образом, чтобы нельзя было создать более одного экземпляра класса.
*/
class CookieManager
{

    private static $instance = null;

    public static function getInstance()
    {
        return static::$instance ?? (static::$instance = new static());
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * 
     * Set or init cookie
     * 
     * @param string $name cookie name
     * @param mixed $value value to set
     * @param int|bool cookie lifetime. if set to false 1 year lifetime wil be used
     * 
     */
    public function set(string $name, $value, $expire = false)
    {
        $expire = $expire === false ? $expire : intval($expire);
        $expire = $expire === false ? strtotime('+1 year') : time() + $expire;
        setcookie($name, $value, $expire);
    }

    /**
     * 
     * Delete cookie value
     * 
     * @param string $name cookie name
     */
    public function delete(string $name)
    {
        $this->set($name, '', -3600);
    }

    /**
     * 
     * Get cookie value
     * 
     * @param string $name cookie name
     * 
     * @return mixed|null cookie value or null if cookie does not exists
     */
    public function get(string $name)
    {
        return $_COOKIE[$name] ?? null;
    }
}

//$cm = CookieManager::getInstance();
//$cm->set('test1', 66, 3600);
//$cm->set('test2', 166);
//echo $cm->get('test2');
//echo $cm->delete('test1');
