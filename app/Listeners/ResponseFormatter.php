<?php

namespace app\Listeners;

use Dingo\Api\Event\ResponseWasMorphed;

class ResponseFormatter
{
    public function handle(ResponseWasMorphed $event)
    {
        if (is_array($event->content)) {
            if (array_has($event->content, 'error')) {
                $event->content = ['success' => false, 'error' => $event->content['error']];
            }
            elseif (array_has($event->content, 'status_code') && $event->content['status_code'] == 422) {
                $retArr = ['success' => false, 'error' => $event->content['message']];
                $retArr['data'] = $event->content['errors']->getMessages();
                $event->content = $retArr;
            }
            elseif (array_has($event->content, 'status_code') && $event->content['status_code'] != 200) {
                $retArr = ['success' => false, 'error' => $event->content['message']];
                if (env('API_DEBUG') === true) {
                    $retArr['data'] = ['debug' => $event->content['debug']];
                }
                $event->content = $retArr;
            }
            elseif (!array_has($event->content, 'success')) {
                $event->content = ['success' => true, 'data' => $event->content];
            }
        } elseif (is_object($event->content)) {
            $event->content = ['success' => true, 'data' => $event->content];
        }elseif(in_array($event->response->getStatusCode(), [200, 201])){
            $event->content = ['success' => true];
        }
    }
}