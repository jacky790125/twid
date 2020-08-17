<?php

namespace Meditate\IdentityCard;

class TaiwanIdentityCard
{
    /**
     * The number which the first letter represents.
     *
     * @var array
     */
    protected $locations = [
        'A' =>  1, 'B' => 10, 'C' => 19, 'D' => 28, 'E' => 37, 'F' => 46, 'G' => 55,
        'H' => 64, 'I' => 39, 'J' => 73, 'K' => 82, 'L' =>  2, 'M' => 11, 'N' => 20,
        'O' => 48, 'P' => 29, 'Q' => 38, 'R' => 47, 'S' => 56, 'T' => 65, 'U' => 74,
        'V' => 83, 'W' => 21, 'X' =>  3, 'Y' => 12, 'Z' => 30
    ];

    /**
     * The weights which the every numbers represents.
     *
     * @var array
     */
    protected $weights = [8, 7, 6, 5, 4, 3, 2, 1, 1];

    /**
     * 外籍人士性別
     *
     * @var array
     */
    protected $genders = [
        'A' => 0, 'B' => 8, 'C' => 6, 'D' => 4,
    ];

    /**
     * Validate ID number.
     *
     * @param  string  $id_number
     * @return boolean
     */
    public function check(string $id_number = '')
    {
        // 身份證
        if ($this->checkIdNumberFormat($id_number)) {
            return $this->checkNormal($id_number);
        }

        // 統一證號
        if ($this->checkForeignIdNumberFormat($id_number)) {
            return $this->checkForeign($id_number);
        }

        // 新型統一證號
        if ($this->checkNewForeignIdNumberFormat($id_number)) {
            return $this->checkNewForeign($id_number);
        }

        return false;
    }

    /**
     * Make a fake ID number.
     *
     * @param  string|null  $location
     * @param  integer|null  $gender
     * @return string
     */
    public function make($location = null, $gender = null)
    {
        if (is_null($location)) {
            $location = array_rand($this->locations);
        } elseif (!(is_string($location) && array_key_exists($location, $this->locations))) {
            throw new \Exception("Argument 1 must be of the char 'A' to 'Z'", 1);
        }

        if (is_null($gender)) {
            $gender = random_int(1, 2);
        } elseif (!(is_int($gender) && in_array($gender, [1, 2]))) {
            throw new \Exception("Argument 2 must be of the integer 1 or 2", 1);
        }

        $fake_id_number = $location . $gender . random_int(1000000, 9999999);

        $id_number_chars = str_split($fake_id_number);

        $count = $this->locations[$id_number_chars[0]];
        foreach ($this->weights as $i => $weight) {
            if ($i == 8) {
                break;
            }
            $count += $id_number_chars[$i + 1] * $weight;
        }

        $fake_id_number .= ($count % 10 == 0) ? 0 : (10 - ($count % 10));

        return $fake_id_number;
    }

    /**
     * Check ID number format.
     *
     * @param  string  $id_number
     * @return boolean
     */
    private function checkIdNumberFormat($id_number)
    {
        return (preg_match('/(^[A-Z][1-2][0-9]{8})/u', $id_number) === 1) ? true : false;
    }

    /**
     * Check ID number format.
     *
     * @param  string  $id_number
     * @return boolean
     */
    private function checkForeignIdNumberFormat($id_number)
    {
        return (preg_match('/(^[A-Z][A-D][0-9]{8})/u', $id_number) === 1) ? true : false;
    }

    /**
     * Check ID number format.
     *
     * @param  string  $id_number
     * @return boolean
     */
    private function checkNewForeignIdNumberFormat($id_number)
    {
        return (preg_match('/(^[A-Z][8-9][0-9]{8})/u', $id_number) === 1) ? true : false;
    }

    /**
     * 身份證字號
     *
     * @param string $id_number
     * @return boolean
     */
    private function checkNormal($id_number)
    {
        $id_number_chars = str_split($id_number);

        $count = $this->locations[$id_number_chars[0]];
        foreach ($this->weights as $i => $weight) {
            $count += $id_number_chars[$i + 1] * $weight;
        }

        return ($count % 10 === 0) ? true : false;
    }

    /**
     * 外籍人士統一證號
     *
     * @param string $id_number
     * @return boolean
     */
    private function checkForeign($id_number)
    {
        $id_number_chars = str_split($id_number);

        $count = 0;
        $count += $this->locations[$id_number_chars[0]];
        $count += $this->genders[$id_number_chars[1]];

        for ($i = 2; $i < 9; $i++) {
            $count += ($id_number_chars[$i] * (9 - $i) % 10);
        }

        $check = (10 - ($count % 10)) % 10;

        return ($id_number_chars[9] == $check) ? true : false;
    }

    /**
     * 新型外籍人士統一證號
     *
     * @param string $id_number
     * @return boolean
     */
    private function checkNewForeign($id_number)
    {
        $id_number_chars = str_split($id_number);

        $count = 0;
        $count += $this->locations[$id_number_chars[0]];

        for ($i = 1; $i < 9; $i++) {
            $count += ($id_number_chars[$i] * (9 - $i) % 10);
        }

        $check = (10 - ($count % 10)) % 10;

        return ($id_number_chars[9] == $check) ? true : false;
    }
}
