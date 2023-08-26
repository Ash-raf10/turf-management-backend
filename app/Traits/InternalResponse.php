<?php

namespace App\Traits;


class InternalResponseObject
{
    public bool $success;
    public mixed $data;
    public string $msg;

    public function __construct(bool $success, mixed $data, string $msg)
    {
        $this->success = $success;
        $this->data = $data;
        $this->msg = $msg;
    }
}

trait InternalResponse
{
    /**
     * SendResponse send json response
     *
     * @param bool        $status     true/false
     * @param mixed $data     data or result
     * @param string $msg  message
     * @return InternalResponseObject An Object containing result.
     *               - 'success'   : boool
     *               - 'data' : mixed  can be array|string|object
     *               - 'message' : a meaningfull message if any
     */
    protected function response(
        bool $status = true,
        mixed $data = "",
        string $msg = ""
    ): InternalResponseObject {

        return new InternalResponseObject($status, $data, $msg);
    }
}
