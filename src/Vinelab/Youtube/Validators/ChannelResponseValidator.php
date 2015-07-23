<?php

namespace Vinelab\Youtube\Validators;

use Vinelab\Youtube\Exceptions\InvalidResponseException;

class ChannelResponseValidator extends Validator
{
    protected $rules = [
        'kind' => 'required',
        'etag' => 'required',
    ];

    public function validate($attributes)
    {
        // convert the StdObj to array
        $attributes = $this->objectToArray($attributes);

        /*
         * check if the converted object has the same keys
         * as the expected response from youtube.
         * if not, throw InvalidResponseException.
         */
        if (!$this->expectedKeys($attributes, ExpectedYoutubeResponse::channel())) {
            throw new InvalidResponseException();
        }

        $validation = $this->validation($attributes);

        if ($validation->fails()) {
            throw new InvalidResponseException($validation->messages()->all());
        }

        return true;
    }
}
