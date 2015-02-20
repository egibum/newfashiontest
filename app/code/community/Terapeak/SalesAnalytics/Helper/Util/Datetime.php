<?php
    
    class Terapeak_SalesAnalytics_Helper_Util_DateTime extends Terapeak_SalesAnalytics_Helper_Util_Abstract
    {
        public function toAPIDateFormat($inputDate)
        {
            $result = $inputDate;
            if (!is_null($inputDate) && !empty($inputDate))
            {
                $dateObj = new DateTime($inputDate);
                $result = $dateObj->format('Y-m-d\TH:i:s');
            }
            return $result;
        }
        
        public function currentDate()
        {
            return date('Y-m-d H:i:s');
        }
        
        public function currentTimeInMillis($inputDate)
        {
            $dateTime = new DateTime();
            return $dateTime->getTimestamp() * 1000;
        }
        
        public function yearsSinceDate($inputDate)
        {
            $result = 0;
            if (!is_null($inputDate) && !empty($inputDate))
            {
                $date = new DateTime();
                $dob = new DateTime($inputDate);
                $result = (int) (($date->getTimestamp() - $dob->getTimestamp()) / (365 * 24 * 3600));
            }
            return $result;
        }
    }
    
    ?>
