<?php
function fileVersion($file, $return = false)
{
    if (file_exists(DJC_PATH . $file)) {
        $mtime = filemtime(DJC_PATH . $file);
        $file  = DJC_URL . $file . "?" . $mtime;
    } else {
        if ($return == true) {
            $file = "";
        }
    }

    if ($return == false) {
        echo $file;
    } else {
        return $file;
    }
}

function vrfRecaptcha()
{
    if (DJC_SANDBOX === false) {
        $recaptcha = false;

        if (isset($_POST['g-recaptcha-response'])) {
            $url  = "https://www.google.com/recaptcha/api/siteverify";
            $data = [
                'secret'   => reCAPTCHA_SECRET_KEY,
                'response' => $_POST['g-recaptcha-response'],
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $response = curl_exec($ch);

            curl_close($ch);

            $response = json_decode($response);

            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    if ($response->success === true) {
                        $recaptcha = true;
                    }

                    break;
            }
        }
    } else {
        $recaptcha = true;
    }

    return $recaptcha;
}
