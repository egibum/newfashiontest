<?php
    
    class Terapeak_SalesAnalytics_Helper_Util_Gender extends Terapeak_SalesAnalytics_Helper_Util_Abstract
    {
        const GENDER_CODE_MALE = "123";
        const GENDER_CODE_FEMALE = "124";
        
        /**
         * This method will give users gender as per its gender code : 123 for Male and 124 for Female
         *
         * @param type $genderCode
         * @return string
         */
        public function genderFromCode($genderCode)
        {
            $gender = "Male";
            if ($genderCode == self::GENDER_CODE_FEMALE)
            {
                $gender = "Female";
            }
            else if ($genderCode == self::GENDER_CODE_MALE)
            {
                $gender = "Male";
            }
            return $gender;
        }
    }
    
    ?>
