<?php

namespace App\Utils;

/**
 * a custom app response object
 */
class AppResponse
{
    /**
     * if the response is successful or not
     * @var bool
     */
    public bool $success = false;

    /**
     * any errors associated with the response
     * @var array
     */
    public array $errors = [];

    /**
     * the data  of the response
     *
     * @var mixed|null
     */
    public $data = null;

    /**
     * the http error code
     *
     * @var int
     */
    public int $code = 500;

    /**
     * create a new app response instance
     *
     * @param bool $success
     * @param array $errors
     * @param $data
     */
    public function __construct(bool $success = false, array $errors = [], $data = null)
    {
        $this->success = $success;
        $this->errors = $errors;
        $this->data = $data;
    }

    /**
     * set an error
     *
     * @param string $error
     * @return $this
     */
    public function setError(string $error): self
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * set errors
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * set the success
     *
     * @param bool $success
     * @return $this
     */
    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    /**
     * set the data
     *
     * @param $data
     * @return $this
     */
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * get the errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * get the success
     *
     * @return bool
     */
    public function getSuccess(): bool
    {
        return $this->success;
    }

    /**
     * get the data
     *
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * create a new instance from an array
     *
     * @param array $items
     * @return static
     */
    public static function fromArray(array $items): self
    {
        return new self(
            array_key_exists('success', $items) ? $items['success'] : false,
            array_key_exists('errors', $items) ? $items['errors'] : [],
            array_key_exists('data', $items) ? $items['data'] : null
        );
    }

    /**
     * get a json instance of the object
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode(array($this));
    }

    /**
     * set the code
     *
     * @param int $code
     * @return $this
     */
    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * get the code
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }


    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'errors' => $this->errors,
            'data' => $this->data
        ];
    }

}
