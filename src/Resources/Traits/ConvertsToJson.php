<?php

namespace MyParcelCom\Sdk\Resources\Traits;

trait ConvertsToJson
{
    /**
     * This function puts all object properties in an array and returns it.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach (get_object_vars($this) as $key => $value) {
            if (is_scalar($value) || is_array($value)) {
                $array[$key] = $value;
            } elseif (method_exists($value, 'toArray')) {
                $array[$key] = $value->toArray();
            }
        }

        return $array;
    }

    /**
     * Converts this object into a json encoded string. Uses the `toArray()`
     * method to get the to be encoded data.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
