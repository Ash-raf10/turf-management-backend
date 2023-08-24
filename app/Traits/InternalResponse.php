<?php

namespace App\Traits;


class InternalResponseObject
{
    public bool $success;
    public mixed $data;

    public function __construct(bool $success, mixed $data)
    {
        $this->success = $success;
        $this->data = $data;
    }
}

trait InternalResponse
{
    /**
     * SendResponse send json response
     *
     * @param bool        $status     true/false
     * @param mixed $data     data or result
     * @return InternalResponseObject An Object containing result.
     *               - 'success'   : boool
     *               - 'data' : mixed  can be array|string|object
     */
    protected function response(
        bool $status = true,
        mixed $data = "",
    ): InternalResponseObject {

        return new InternalResponseObject($status, $data);
    }
}
