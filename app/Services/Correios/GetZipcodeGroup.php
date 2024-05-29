<?php
namespace App\Services\Correios;
class GetZipcodeGroup
{
    private $zipcode;
    function __construct($zipcode)
    {
        $this->zipcode = str_replace('-', '', $zipcode);
    }
    function binarySearchGroupRange($groupRanges, $orderZipcode)
    {
        $left = 0;
        $right = count($groupRanges) - 1;
        while ($left <= $right) {
            $mid = floor(($left + $right) / 2);
            $start = $groupRanges[$mid]['start'];
            $end = $groupRanges[$mid]['end'];

            if ($orderZipcode >= $start && $orderZipcode <= $end) {
                return $groupRanges[$mid];
            } elseif ($orderZipcode < $start) {
                $right = $mid - 1;
            } else {
                $left = $mid + 1;
            }
        }
        return null;
    }

    function getZipcodeGroup()
    {  
        if (!$this->zipcode) {
            return null;
        }
            $groupRanges = [
                            ["start" => 1000000, "end" => 11599999, "group" => 1],
                            ["start" => 11600000, "end" => 19999999, "group" => 2],
                            ["start" => 13172651, "end" => 13172651, "group" => 2],
                            ["start" => 20000000, "end" => 28999999, "group" => 3],
                            ["start" => 29000000, "end" => 29999999, "group" => 5],
                            ["start" => 29164340, "end" => 29164340, "group" => 5],
                            ["start" => 29166651, "end" => 29166651, "group" => 5],
                            ["start" => 30000000, "end" => 48999999, "group" => 4],
                            ["start" => 49000000, "end" => 49099999, "group" => 5],
                            ["start" => 49100000, "end" => 49139999, "group" => 4],
                            ["start" => 49140000, "end" => 49169999, "group" => 5],
                            ["start" => 49170000, "end" => 49199999, "group" => 4],
                            ["start" => 49200000, "end" => 49219999, "group" => 5],
                            ["start" => 49220000, "end" => 49399999, "group" => 4],
                            ["start" => 49400000, "end" => 49479999, "group" => 5],
                            ["start" => 49480000, "end" => 49499999, "group" => 4],
                            ["start" => 49500000, "end" => 49511999, "group" => 5],
                            ["start" => 49512000, "end" => 49999999, "group" => 4],
                            ["start" => 50000000, "end" => 53689999, "group" => 4],
                            ["start" => 53690000, "end" => 53699999, "group" => 5],
                            ["start" => 53700000, "end" => 53989999, "group" => 4],
                            ["start" => 53990000, "end" => 53999999, "group" => 5],
                            ["start" => 54000000, "end" => 55119999, "group" => 4],
                            ["start" => 55120000, "end" => 55189999, "group" => 5],
                            ["start" => 55190000, "end" => 55199999, "group" => 4],
                            ["start" => 55200000, "end" => 55289999, "group" => 5],
                            ["start" => 55290000, "end" => 55304999, "group" => 4],
                            ["start" => 55305000, "end" => 55599999, "group" => 5],
                            ["start" => 55600000, "end" => 55619999, "group" => 4],
                            ["start" => 55620000, "end" => 56299999, "group" => 5],
                            ["start" => 56300000, "end" => 56354999, "group" => 4],
                            ["start" => 56355000, "end" => 57129999, "group" => 5],
                            ["start" => 57130000, "end" => 57149999, "group" => 4],
                            ["start" => 57150000, "end" => 57179999, "group" => 5],
                            ["start" => 57180000, "end" => 57199999, "group" => 4],
                            ["start" => 57200000, "end" => 57209999, "group" => 5],
                            ["start" => 57210000, "end" => 57229999, "group" => 4],
                            ["start" => 57230000, "end" => 57249999, "group" => 5],
                            ["start" => 57250000, "end" => 57264999, "group" => 4],
                            ["start" => 57265000, "end" => 57269999, "group" => 5],
                            ["start" => 57270000, "end" => 57299999, "group" => 4],
                            ["start" => 57300000, "end" => 57319999, "group" => 5],
                            ["start" => 57320000, "end" => 57479999, "group" => 4],
                            ["start" => 57480000, "end" => 57489999, "group" => 5],
                            ["start" => 57490000, "end" => 57499999, "group" => 4],
                            ["start" => 57500000, "end" => 57509999, "group" => 5],
                            ["start" => 57510000, "end" => 57599999, "group" => 4],
                            ["start" => 57600000, "end" => 57614999, "group" => 5],
                            ["start" => 57615000, "end" => 57799999, "group" => 4],
                            ["start" => 57800000, "end" => 57819999, "group" => 5],
                            ["start" => 57820000, "end" => 57839999, "group" => 4],
                            ["start" => 57840000, "end" => 57859999, "group" => 5],
                            ["start" => 57860000, "end" => 57954999, "group" => 4],
                            ["start" => 57955000, "end" => 57959999, "group" => 5],
                            ["start" => 57960000, "end" => 57999999, "group" => 4],
                            ["start" => 58000000, "end" => 58114999, "group" => 5],
                            ["start" => 58115000, "end" => 58116999, "group" => 4],
                            ["start" => 58117000, "end" => 58118999, "group" => 5],
                            ["start" => 58119000, "end" => 58199999, "group" => 4],
                            ["start" => 58200000, "end" => 58207999, "group" => 5],
                            ["start" => 58208000, "end" => 58279999, "group" => 4],
                            ["start" => 58280000, "end" => 58288999, "group" => 5],
                            ["start" => 58289000, "end" => 58299999, "group" => 4],
                            ["start" => 58300000, "end" => 58314999, "group" => 5],
                            ["start" => 58315000, "end" => 58319999, "group" => 4],
                            ["start" => 58320000, "end" => 58321999, "group" => 5],
                            ["start" => 58322000, "end" => 58336999, "group" => 4],
                            ["start" => 58337000, "end" => 58337999, "group" => 5],
                            ["start" => 58338000, "end" => 58339999, "group" => 4],
                            ["start" => 58340000, "end" => 58341999, "group" => 5],
                            ["start" => 58342000, "end" => 58347999, "group" => 4],
                            ["start" => 58347999, "end" => 58399999, "group" => 4],
                            ["start" => 58348000, "end" => 58349999, "group" => 5],
                            ["start" => 58400000, "end" => 58440999, "group" => 5],
                            ["start" => 58441000, "end" => 58442999, "group" => 4],
                            ["start" => 58443000, "end" => 58449999, "group" => 5],
                            ["start" => 58450000, "end" => 58469999, "group" => 4],
                            ["start" => 58470000, "end" => 58479999, "group" => 5],
                            ["start" => 58480000, "end" => 58499999, "group" => 4],
                            ["start" => 58500000, "end" => 58509999, "group" => 5],
                            ["start" => 58510000, "end" => 58514999, "group" => 4],
                            ["start" => 58515000, "end" => 58519999, "group" => 5],
                            ["start" => 58520000, "end" => 58699999, "group" => 4],
                            ["start" => 58700000, "end" => 58700000, "group" => 5],
                            ["start" => 58710000, "end" => 58732999, "group" => 4],
                            ["start" => 58733000, "end" => 58733999, "group" => 5],
                            ["start" => 58734000, "end" => 58799999, "group" => 4],
                            ["start" => 58800000, "end" => 58814999, "group" => 5],
                            ["start" => 58815000, "end" => 58864999, "group" => 4],
                            ["start" => 58865000, "end" => 58869999, "group" => 5],
                            ["start" => 58870000, "end" => 58883999, "group" => 4],
                            ["start" => 58884000, "end" => 58886999, "group" => 5],
                            ["start" => 58887000, "end" => 58899999, "group" => 4],
                            ["start" => 58900000, "end" => 58907999, "group" => 5],
                            ["start" => 58908000, "end" => 58918999, "group" => 4],
                            ["start" => 58919000, "end" => 58919999, "group" => 5],
                            ["start" => 58920000, "end" => 58999999, "group" => 4],
                            ["start" => 59000000, "end" => 59161999, "group" => 5],
                            ["start" => 59162000, "end" => 59279999, "group" => 4],
                            ["start" => 59280000, "end" => 59309999, "group" => 5],
                            ["start" => 59310000, "end" => 59379999, "group" => 4],
                            ["start" => 59380000, "end" => 59389999, "group" => 5],
                            ["start" => 59390000, "end" => 59569999, "group" => 4],
                            ["start" => 59570000, "end" => 59574999, "group" => 5],
                            ["start" => 59575000, "end" => 59599999, "group" => 4],
                            ["start" => 59600000, "end" => 59654999, "group" => 5],
                            ["start" => 59655000, "end" => 59699999, "group" => 4],
                            ["start" => 59700000, "end" => 59729999, "group" => 5],
                            ["start" => 59730000, "end" => 59899999, "group" => 4],
                            ["start" => 59900000, "end" => 59901999, "group" => 5],
                            ["start" => 59902000, "end" => 59999999, "group" => 4],
                            ["start" => 60000000, "end" => 63999999, "group" => 4],
                            ["start" => 64000000, "end" => 64999999, "group" => 5],
                            ["start" => 65000000, "end" => 65034999, "group" => 4],
                            ["start" => 65035000, "end" => 65079999, "group" => 5],
                            ["start" => 65080000, "end" => 65089999, "group" => 4],
                            ["start" => 65090000, "end" => 69999999, "group" => 5],
                            ["start" => 70000000, "end" => 76799999, "group" => 4],
                            ["start" => 74150070, "end" => 74150070, "group" => 4],
                            ["start" => 74230022, "end" => 74230022, "group" => 4],
                            ["start" => 74230022, "end" => 74230022, "group" => 4],
                            ["start" => 74280210, "end" => 74280210, "group" => 4],
                            ["start" => 74343240, "end" => 74343240, "group" => 4],
                            ["start" => 74603190, "end" => 74603190, "group" => 4],
                            ["start" => 74603190, "end" => 74603190, "group" => 4],
                            ["start" => 76799999, "end" => 89999999, "group" => 4],
                            ["start" => 76800000, "end" => 79999999, "group" => 5],
                            ["start" => 90000000, "end" => 99999999, "group" => 4]
                        ];
            return $this->binarySearchGroupRange($groupRanges, $this->zipcode)['group']; 
    }
}
