<?php
    
    abstract class Terapeak_SalesAnalytics_Model_Request_Abstract extends Varien_Object
    {
        /*
         * Use this function to set the datamembers for all your request model classes.
         */
        
        abstract public function setModelData($data);
        
        /**
         * Set/Get attribute wrapper override
         *
         * @param   string $method
         * @param   array $args
         * @return  mixed
         */
        public function __call($method, $args)
        {
            switch (substr($method, 0, 3))
            {
                case 'get' :
                    $key = lcfirst(substr($method, 3));
                    $data = $this->getData($key, isset($args[0]) ? $args[0] : null);
                    return $data;
                    
                case 'set' :
                    $key = lcfirst(substr($method, 3));
                    $result = $this->setData($key, isset($args[0]) ? $args[0] : null);
                    return $result;
                    
                case 'uns' :
                    $key = lcfirst(substr($method, 3));
                    $result = $this->unsetData($key);
                    return $result;
                    
                case 'has' :
                    $key = lcfirst(substr($method, 3));
                    return isset($this->_data[$key]);
            }
            throw new Varien_Exception("Invalid method " . get_class($this) . "::" . $method . "(" . print_r($args, 1) . ")");
        }
        
    }
    
    ?>
