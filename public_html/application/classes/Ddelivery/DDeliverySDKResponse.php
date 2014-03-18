<?php
/**
 * User: DnAp
 * Date: 18.03.14
 * Time: 23:12
 */

class DDeliverySDKResponse {
    /**
     * Возвращает true при успехе
     * @var bool
     */
    public $success;
    /**
     * Сообщение об ошибке
     * @var null|string
     */
    public $errorMessage = null;
    /**
     * @var array
     */
    public $response = array();

    /**
     * @param string $jsonRaw
     */
    public function __construct($jsonRaw)
    {
        $jsonData = json_decode($jsonRaw, true);
        if(!$jsonData) {
            $this->success = false;
            $this->errorMessage = 'Unknown error';
            return;
        }

        $this->success = (bool)$jsonData['success'];
        if($this->success) {
            $this->response = $jsonData['response'];
        }elseif(isset($jsonData['response']) && isset($jsonData['response']['message'])) {
            $this->errorMessage = $jsonData['response']['message'];
        }else {
            $this->errorMessage = 'Unknown error';
        }
    }

} 