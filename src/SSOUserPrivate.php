<?php

namespace SPSOstrov\SSOBundle;

/**
 * This class is intened to keep the user data of the SSOUser class.
 * We don't want to store these data in the session, while don't affect
 * the sleep/wakeup functions of the SSOUser object and keeping the possibility
 * to customize the serialization of the object in class descendants.
 *
 * This class therefore just handles the no-serialization functionality
 * of the stored data.
 */
final class SSOUserPrivate
{   
    private $data = null;

    public function getData()
    {   
        return $this->data;
    }   
    
    public function setData($data): self
    {   
        $this->data = $data;
        return $this;
    }   
    
    public function __sleep(): array
    {   
        return [];
    }   
}
