<?php

namespace Lazada\DemoApi\Api\Data;

interface ProductInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */

     public function setId($id);   
}