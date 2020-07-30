<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 21.07.2020
 * Time: 21:08
 */

namespace app\traits;

trait FileTrait
{
    protected function downloadLocal($name = null)
    {
        if (!$name) {
            $name = $this->input->getString('name');
        }

        if (!$name) {
            die("Error: Wrong input");
        }

        $path = ROOTPATH . '/output/' . $name;
        if (file_exists($path)) {

            header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
            header("Cache-Control: public"); // needed for internet explorer
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length:" . filesize(ROOTPATH . '/output/' . $name));
            header("Content-Disposition: attachment; filename=$name");
            readfile(ROOTPATH . '/output/' . $name);
            echo '<script>window.close();</script>';
            die();
        } else {
            die("Error: File not found.");
        }
    }
}