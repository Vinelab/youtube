<?php namespace Vinelab\Youtube\Validators;

use Illuminate\Validation\Factory as ValidatorFactory;

abstract class Validator implements Contracts\ValidatorInterface {

    /**
     * The validator instance.
     *
     * @var Illuminate\Validation\Factory
     */
    protected $validator;

    /**
     * The rules to validate against.
     *
     * @var array
     */
    protected $rules;

    /**
     * Create a new validator instance.
     *
     * @param Illuminate\Validator\Factory $validator
     */
    public function __construct(ValidatorFactory $validator)
    {
        $this->validator = $validator;
    }

    public function validation($attributes)
    {
        return $this->validator->make($attributes, $this->rules);
    }

    abstract public function validate($attributes);

    /**
     * check if the keys in the reponse are valid
     * @param  array   $attributes
     * @param  array   $expected
     * @return boolean
     */
    protected function expectedKeys($attributes, $expected)
    {
        $result = $this->intersect($attributes, $expected);

        return ($result === $attributes) ? true : false;
    }

    /**
     * Recursively check the key intersections of two arrays
     * @param  array $attributes
     * @param  array $expected
     * @return array
     */
    protected function intersect(array $attributes, array $expected)
    {
        $attributes = array_intersect_key($attributes, $expected);
        foreach ($attributes as $key => &$value) {
            if (is_array($value) && is_array($expected[$key])) {
                $value = $this->intersect($value, $expected[$key]);
            }
        }

        return $attributes;
    }

    /**
     * Convert an StdClass to an array.
     * recursively loop through all sub objects
     * if they exist and convert them as well.
     * @param  StdClass $obj
     * @return array
     */
    protected function objectToArray($obj)
    {
        if (is_object($obj)) {
            $obj = (array) $obj;
        }
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = $this->objectToArray($val);
            }
        } else {
            $new = $obj;
        }

        return $new;
    }
}
