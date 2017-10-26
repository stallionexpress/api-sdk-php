<?php

namespace MyParcelCom\Sdk\Resources\Traits;

trait JsonSerializable
{
    /**
     * This function puts all object properties in an array and returns it.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->arrayValuesToArray(get_object_vars($this));
    }

    /**
     * Helper function to recursively convert all values in an array to scalar
     * values or arrays with scalar values.
     *
     * @param array $arrayValues
     * @return array
     */
    private function arrayValuesToArray(array $arrayValues)
    {
        $array = [];
        foreach ($arrayValues as $key => $value) {
            if (is_scalar($value)) {
                $array[$key] = $value;
            } elseif (is_array($value)) {
                $array[$key] = $this->arrayValuesToArray($value);
            } elseif (method_exists($value, 'jsonSerialize')) {
                $array[$key] = $value->jsonSerialize();
            }
        }

        return $array;
    }
}
