<?php
/*

 */

class plg_SessionNotUsingDB_SC_Helper_Session extends SC_Helper_Session {

    private $savePath;

    public function SC_Helper_Session() {
        var_dump(1);
        session_set_save_handler(
            array($this, 'sfSessOpen'),
            array($this, 'sfSessClose'),
            array($this, 'sfSessRead'),
            array($this, 'sfSessWrite'),
            array($this, 'sfSessDestroy'),
            array($this, 'sfSessGc'));

        register_shutdown_function('session_write_close');
    }


    public function sfSessOpen($savePath, $sessionName) {
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }
        return true;
    }


    public function sfSessClose() {
        return true;
    }


    public function sfSessRead($id) {
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }


    public function sfSessWrite($id, $data)
    {
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }


    public function sfSessDestroy($id)
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function sfSessGc($maxlifetime) {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + MAX_LIFETIME < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

}
