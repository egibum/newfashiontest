
<?php
    
    class Terapeak_SalesAnalytics_Helper_Util extends Mage_Core_Helper_Abstract
    {
        
        const GENDER_CODE_MALE = "123";
        const GENDER_CODE_FEMALE = "124";
        
        /**
         *
         * This method will convert Date to terapeak api required date format
         *
         * @param type $inputDate
         * @return type
         */
        public function convertToAPIDateFormat($inputDate)
        {
            $result = $inputDate;
            if (!is_null($inputDate) && !empty($inputDate))
            {
                $dateObj = new DateTime($inputDate);
                $result = $dateObj->format('Y-m-d\TH:i:s');
            }
            return $result;
        }
        
        /**
         * This method will return current Date string in Y-m-d H:i:s format
         *
         * @return type
         */
        public function getCurrentDate()
        {
            return date('Y-m-d H:i:s');
        }
        
        /**
         * This method will calculate age of user from its date of birth
         *
         * @param type $inputDate
         * @return type
         */
        public function calculateAgeFromDOB($inputDate)
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
        
        /**
         * This method will give current Time In Milli Seconds
         *
         * @param type $inputDate
         * @return type
         */
        public function getCurrentTimeInMilliSeconds($inputDate)
        {
            $dateTime = new DateTime();
            return $dateTime->getTimestamp() * 1000;
        }
        
        /**
         * This method will give users gender as per its gender code : 123 for Male and 124 for Female
         *
         * @param type $genderCode
         * @return string
         */
        public function getGenderFromGenderCode($genderCode)
        {
            $gender = "";
            if ($genderCode == self::GENDER_CODE_FEMALE)
            {
                $gender = "Female";
            }
            else if ($genderCode == self::GENDER_CODE_MALE)
            {
                $gender = "Male";
            }
            else
            {
                $gender = "Male";
            }
            return $gender;
        }
        
    }
    
    ?>
