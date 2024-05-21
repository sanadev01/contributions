<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculator\CalculatorRequest;
use App\Models\Order;
use App\Repositories\Calculator\CalculatorRepository;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Services\Excel\Export\OrderUpdateExport;
use Exception;

class UpdateTracking extends Controller
{
    function container1()
    {
        $codes = [
            ['tracking' => 'IX030958507BR', 'warehouse' => 'TM2592092113BR']
        ];
        return $this->updateTracking($codes);
    }
    function container2()
    { 
        $codes = [
            ['tracking' => 'IX030886371BR', 'warehouse' => 'TM2595493453BR'],
            ['tracking' => 'IX030886178BR', 'warehouse' => 'TM2593837548BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container3()
    { 

        $codes = [
            ['tracking' => 'IX030885420BR', 'warehouse' => 'TM2573482946BR'],
            ['tracking' => 'IX030886270BR', 'warehouse' => 'TM2594849156BR'],
            ['tracking' => 'IX030886297BR', 'warehouse' => 'TM2593164304BR'],
            ['tracking' => 'IX030886323BR', 'warehouse' => 'TM2590964832BR'],
            ['tracking' => 'IX030886337BR', 'warehouse' => 'HD2593070228BR'],
            ['tracking' => 'IX030886399BR', 'warehouse' => 'TM2592797453BR'],
            ['tracking' => 'IX030958011BR', 'warehouse' => 'TM2581388816BR'],
            ['tracking' => 'IX030958025BR', 'warehouse' => 'TM2585590329BR'],
            ['tracking' => 'IX030958250BR', 'warehouse' => 'TM2594237722BR'],
            ['tracking' => 'IX030958382BR', 'warehouse' => 'TM2594757453BR'],
            ['tracking' => 'IX030958419BR', 'warehouse' => 'HD2594562042BR'],
            ['tracking' => 'IX030958453BR', 'warehouse' => 'TM2590876921BR'],
            ['tracking' => 'IX030958467BR', 'warehouse' => 'TM2595377332BR'],
            ['tracking' => 'IX030958515BR', 'warehouse' => 'TM2591494245BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container4()
    {  
        $codes =  [
            ['tracking' => 'NC515475956BR','warehouse'=>'TM2554829643BR'],
            ['tracking' => 'NC605558866BR','warehouse'=>'TM2590701231BR'],
            ['tracking' => 'NC605559977BR','warehouse'=>'TM2592030531BR'],
            ['tracking' => 'NC605560215BR','warehouse'=>'TM2592434205BR'],
            ['tracking' => 'NC605560887BR','warehouse'=>'TM2594350633BR'],
            ['tracking' => 'NC605560927BR','warehouse'=>'TM2590351214BR'],
            ['tracking' => 'NC605561030BR','warehouse'=>'TM2590353715BR'],
            ['tracking' => 'NC605561065BR','warehouse'=>'TM2591954046BR'],
            ['tracking' => 'NC605561247BR','warehouse'=>'TM2594257357BR'],
            ['tracking' => 'NC605561397BR','warehouse'=>'TM2593861253BR'],
            ['tracking' => 'NC605561499BR','warehouse'=>'TM2592064909BR'],
            ['tracking' => 'NC605561689BR','warehouse'=>'TM2590569444BR'],
            ['tracking' => 'NC605562021BR','warehouse'=>'TM2594277149BR'],
            ['tracking' => 'NC620166252BR','warehouse'=>'TM2593905856BR'],
            ['tracking' => 'NC620166460BR','warehouse'=>'TM2593813443BR'],
            ['tracking' => 'NC620166527BR','warehouse'=>'TM2593915343BR'],
            ['tracking' => 'NC620166646BR','warehouse'=>'TM2595319614BR'],
            ['tracking' => 'NC620167479BR','warehouse'=>'TM2594142028BR'],
            ['tracking' => 'NC620167743BR','warehouse'=>'TM2591149518BR'],
            ['tracking' => 'NC620167828BR','warehouse'=>'TM2592251852BR'],
            ['tracking' => 'NC620167947BR','warehouse'=>'TM2591655444BR'],
            ['tracking' => 'NC620167978BR','warehouse'=>'TM2593156549BR'],
            ['tracking' => 'NC620168222BR','warehouse'=>'TM2594762154BR'],
            ['tracking' => 'NC620168545BR','warehouse'=>'TM2592868544BR'],
            ['tracking' => 'NC620168562BR','warehouse'=>'TM2592270110BR'],
            ['tracking' => 'NC620168580BR','warehouse'=>'TM2595970909BR'],
            ['tracking' => 'NC620168837BR','warehouse'=>'TM2590576828BR'],
            ['tracking' => 'NC620169452BR','warehouse'=>'TM2590891601BR'],
            ['tracking' => 'NC620169554BR','warehouse'=>'TM2595993558BR'],
            ['tracking' => 'NC620169894BR','warehouse'=>'TM2603003245BR'],
            ['tracking' => 'NC653642365BR','warehouse'=>'TM2594888805BR'],
            ['tracking' => 'NC653642405BR','warehouse'=>'TM2592292350BR'],
            ['tracking' => 'NC653642453BR','warehouse'=>'TM2590893624BR'],
            ['tracking' => 'NC653642498BR','warehouse'=>'TM2592394312BR'],
            ['tracking' => 'NC653642612BR','warehouse'=>'TM2593196144BR'],
            ['tracking' => 'NC653642881BR','warehouse'=>'TM2605802302BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container5()
    {
        $codes = [
           ['tracking' =>'NC574417163BR','warehouse'=>'TM2580354111BR'],
           ['tracking' =>'NC575448065BR','warehouse'=>'TM2582225609BR'],
           ['tracking' =>'NC575449074BR','warehouse'=>'TM2584052054BR'],
           ['tracking' =>'NC575449919BR','warehouse'=>'TM2582874555BR'],
           ['tracking' =>'NC575450214BR','warehouse'=>'TM2580480511BR'],
           ['tracking' =>'NC575450364BR','warehouse'=>'TM2581385046BR'],
           ['tracking' =>'NC605557830BR','warehouse'=>'TM2585974757BR'],
           ['tracking' =>'NC605559447BR','warehouse'=>'TM2590919709BR'],
           ['tracking' =>'NC605561131BR','warehouse'=>'TM2592155547BR'],
           ['tracking' =>'NC605561145BR','warehouse'=>'TM2592255749BR'],
           ['tracking' =>'NC605561159BR','warehouse'=>'TM2592355814BR'],
           ['tracking' =>'NC605561162BR','warehouse'=>'TM2592456001BR'],
           ['tracking' =>'NC605561176BR','warehouse'=>'TM2592456124BR'],
           ['tracking' =>'NC605561437BR','warehouse'=>'TM2595462223BR'],
           ['tracking' =>'NC605561936BR','warehouse'=>'TM2594475354BR'],
           ['tracking' =>'NC620167955BR','warehouse'=>'TM2592255623BR'],
           ['tracking' =>'NC620168001BR','warehouse'=>'TM2593756907BR'],
           ['tracking' =>'NC620168797BR','warehouse'=>'TM2595375852BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container6()
    {
        $codes =  [
            ['tracking' =>'NC575449851BR','warehouse'=>'HD2583072648BR'],
            ['tracking' =>'NC605557843BR','warehouse'=>'TM2584274846BR'],
            ['tracking' =>'NC605557857BR','warehouse'=>'TM2581274954BR'],
            ['tracking' =>'NC605559169BR','warehouse'=>'TM2594309710BR'],
            ['tracking' =>'NC605559209BR','warehouse'=>'TM2592111037BR'],
            ['tracking' =>'NC605561012BR','warehouse'=>'TM2595153344BR'],
            ['tracking' =>'NC605561114BR','warehouse'=>'TM2590255013BR'],
            ['tracking' =>'NC605561406BR','warehouse'=>'TM2592258405BR'],
            ['tracking' =>'NC605561423BR','warehouse'=>'TM2594561941BR'],
            ['tracking' =>'NC605561511BR','warehouse'=>'TM2594765316BR'],
            ['tracking' =>'NC605561675BR','warehouse'=>'TM2594869114BR'],
            ['tracking' =>'NC605561967BR','warehouse'=>'TM2595375714BR'],
            ['tracking' =>'NC605562052BR','warehouse'=>'TM2593178121BR'],
            ['tracking' =>'NC620166399BR','warehouse'=>'TM2594309600BR'],
            ['tracking' =>'NC620167933BR','warehouse'=>'TM2591355323BR'],
            ['tracking' =>'NC620168196BR','warehouse'=>'TM2594261624BR'],
            ['tracking' =>'NC620168219BR','warehouse'=>'TM2594561838BR'],
            ['tracking' =>'NC620168253BR','warehouse'=>'TM2590762556BR'],
            ['tracking' =>'NC620168620BR','warehouse'=>'TM2595171718BR'],
            ['tracking' =>'NC620168678BR','warehouse'=>'TM2595872646BR'],
            ['tracking' =>'NC620168681BR','warehouse'=>'TM2590372732BR'],
            ['tracking' =>'NC620168695BR','warehouse'=>'TM2591172851BR'],
            ['tracking' =>'NC620168783BR','warehouse'=>'TM2595075533BR'],
            ['tracking' =>'NC620168806BR','warehouse'=>'TM2591475925BR'],
            ['tracking' =>'NC620169418BR','warehouse'=>'TM2592688434BR'],
            ['tracking' =>'NC620169506BR','warehouse'=>'TM2592792601BR'],
            ['tracking' =>'NC620169510BR','warehouse'=>'TM2593092716BR'],
            ['tracking' =>'NC620169761BR','warehouse'=>'TM2602000304BR'],
            ['tracking' =>'NC620169877BR','warehouse'=>'HD2600002419BR'],
            ['tracking' =>'NC653642382BR','warehouse'=>'TM2594090653BR'],
            ['tracking' =>'NC653642440BR','warehouse'=>'TM2595193244BR'],
            ['tracking' =>'NC653642630BR','warehouse'=>'TM2595796528BR'],
            ['tracking' =>'NC653642657BR','warehouse'=>'TM2591296805BR'],
            ['tracking' =>'NC653642895BR','warehouse'=>'HD2602202705BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container7()
    {
        $codes =  [
            ['tracking'=>'NC574414771BR','warehouse'=>'TM2570394817BR'],
            ['tracking'=>'NC575450205BR','warehouse'=>'TM2584080419BR'],
            ['tracking'=>'NC605557786BR','warehouse'=>'HD2582472536BR'],
            ['tracking'=>'NC605558115BR','warehouse'=>'TM2581280355BR'],
            ['tracking'=>'NC605560428BR','warehouse'=>'TM2595538233BR'],
            ['tracking'=>'NC605561088BR','warehouse'=>'TM2595154752BR'],
            ['tracking'=>'NC605561202BR','warehouse'=>'TM2592556423BR'],
            ['tracking'=>'NC605561295BR','warehouse'=>'TM2593059008BR'],
            ['tracking'=>'NC605561445BR','warehouse'=>'TM2591262844BR'],
            ['tracking'=>'NC605561613BR','warehouse'=>'TM2593967445BR'],
            ['tracking'=>'NC605561763BR','warehouse'=>'TM2593770658BR'],
            ['tracking'=>'NC605561865BR','warehouse'=>'TM2592573618BR'],
            ['tracking'=>'NC605562004BR','warehouse'=>'TM2595776721BR'],
            ['tracking'=>'NC620165773BR','warehouse'=>'HD2583494800BR'],
            ['tracking'=>'NC620167292BR','warehouse'=>'TM2595538358BR'],
            ['tracking'=>'NC620168046BR','warehouse'=>'TM2591457916BR'],
            ['tracking'=>'NC620168355BR','warehouse'=>'TM2592564148BR'],
            ['tracking'=>'NC620168633BR','warehouse'=>'TM2590271834BR'],
            ['tracking'=>'NC620169421BR','warehouse'=>'TM2595191019BR'],
            ['tracking'=>'NC620169466BR','warehouse'=>'TM2591591822BR'],
            ['tracking'=>'NC620169483BR','warehouse'=>'TM2591892052BR'],
            ['tracking'=>'NC620169537BR','warehouse'=>'TM2594993030BR'],
            ['tracking'=>'NC620169568BR','warehouse'=>'TM2591193743BR'],
            ['tracking'=>'NC620169585BR','warehouse'=>'TM2592794455BR'],
            ['tracking'=>'NC653642379BR','warehouse'=>'TM2595689103BR'],
            ['tracking'=>'NC653642507BR','warehouse'=>'TM2593394636BR'],
            ['tracking'=>'NC653642572BR','warehouse'=>'TM2590295541BR'],
            ['tracking'=>'NC653642590BR','warehouse'=>'TM2591195739BR'],
            ['tracking'=>'NC653642609BR','warehouse'=>'TM2591895906BR'],
            ['tracking'=>'NC653642643BR','warehouse'=>'TM2590296758BR'],
            ['tracking'=>'NC653642731BR','warehouse'=>'TM2594598745BR'],
            ['tracking'=>'NC653642921BR','warehouse'=>'TM2603803733BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container8()
    {
        $codes = [
           ['tracking'=>'IX030886116BR','warehouse'=>'TM2593319252BR'],
           ['tracking'=>'IX030886120BR','warehouse'=>'TM2593719507BR'],
           ['tracking'=>'IX030886218BR','warehouse'=>'TM2591942502BR'],
           ['tracking'=>'IX030886221BR','warehouse'=>'TM2591942607BR'],
           ['tracking'=>'IX030886235BR','warehouse'=>'TM2591942711BR'],
           ['tracking'=>'IX030886249BR','warehouse'=>'TM2591942816BR'],
           ['tracking'=>'IX030886306BR','warehouse'=>'TM2593264512BR'],
           ['tracking'=>'IX030886310BR','warehouse'=>'TM2593264617BR'],
           ['tracking'=>'IX030958201BR','warehouse'=>'TM2593419348BR'],
           ['tracking'=>'IX030958215BR','warehouse'=>'TM2593719402BR'],
           ['tracking'=>'IX030958277BR','warehouse'=>'TM2591942920BR'],
           ['tracking'=>'IX030958351BR','warehouse'=>'TM2593250347BR'],
           ['tracking'=>'IX030958422BR','warehouse'=>'TM2593264408BR'],
           ['tracking'=>'IX030958436BR','warehouse'=>'TM2591569735BR'],
           ['tracking'=>'IX030958440BR','warehouse'=>'TM2593574119BR'],
           ['tracking'=>'IX030958498BR','warehouse'=>'HD2594890908BR'],
        ];
        return $this->updateTracking($codes);
    }
    function container9()
    {
        $codes = [
           ['tracking'=>'NC479476979BR','warehouse'=>'TM2545684158BR'],
           ['tracking'=>'NC522420975BR','warehouse'=>'TM2554829520BR'],
           ['tracking'=>'NC522420989BR','warehouse'=>'TM2554929708BR'],
           ['tracking'=>'NC546503127BR','warehouse'=>'TM2565158613BR'],
           ['tracking'=>'NC605559328BR','warehouse'=>'TM2593515211BR'],
           ['tracking'=>'NC605560405BR','warehouse'=>'HD2594237824BR'],
           ['tracking'=>'NC605561091BR','warehouse'=>'TM2595554814BR'],
           ['tracking'=>'NC605561105BR','warehouse'=>'TM2590154950BR'],
           ['tracking'=>'NC605561128BR','warehouse'=>'TM2590655116BR'],
           ['tracking'=>'NC605561216BR','warehouse'=>'TM2593256653BR'],
           ['tracking'=>'NC605561278BR','warehouse'=>'TM2591858318BR'],
           ['tracking'=>'NC605561539BR','warehouse'=>'TM2592865638BR'],
           ['tracking'=>'NC605561635BR','warehouse'=>'TM2594867950BR'],
           ['tracking'=>'NC605561644BR','warehouse'=>'TM2594968050BR'],
           ['tracking'=>'NC605561661BR','warehouse'=>'TM2594669019BR'],
           ['tracking'=>'NC605561715BR','warehouse'=>'TM2591669857BR'],
           ['tracking'=>'NC605561729BR','warehouse'=>'TM2591770052BR'],
           ['tracking'=>'NC605561777BR','warehouse'=>'TM2593970711BR'],
           ['tracking'=>'NC605561785BR','warehouse'=>'TM2591171041BR'],
           ['tracking'=>'NC605561940BR','warehouse'=>'TM2594775410BR'],
           ['tracking'=>'NC605561953BR','warehouse'=>'TM2595075637BR'],
           ['tracking'=>'NC605561984BR','warehouse'=>'TM2595376558BR'],
           ['tracking'=>'NC605562018BR','warehouse'=>'TM2592577020BR'],
           ['tracking'=>'NC620165760BR','warehouse'=>'HD2584594903BR'],
           ['tracking'=>'NC620166513BR','warehouse'=>'TM2593415147BR'],
           ['tracking'=>'NC620167876BR','warehouse'=>'TM2594452913BR'],
           ['tracking'=>'NC620167916BR','warehouse'=>'TM2594754401BR'],
           ['tracking'=>'NC620167920BR','warehouse'=>'TM2591255219BR'],
           ['tracking'=>'NC620167981BR','warehouse'=>'TM2593356717BR'],
           ['tracking'=>'NC620167995BR','warehouse'=>'TM2593556811BR'],
           ['tracking'=>'NC620168050BR','warehouse'=>'TM2592658523BR'],
           ['tracking'=>'NC620168240BR','warehouse'=>'TM2590362450BR'],
           ['tracking'=>'NC620168275BR','warehouse'=>'TM2591062705BR'],
           ['tracking'=>'NC620168284BR','warehouse'=>'TM2591262946BR'],
           ['tracking'=>'NC620168307BR','warehouse'=>'TM2591563112BR'],
           ['tracking'=>'NC620168338BR','warehouse'=>'TM2591863751BR'],
           ['tracking'=>'NC620168386BR','warehouse'=>'TM2594364710BR'],
           ['tracking'=>'NC620168528BR','warehouse'=>'TM2594767833BR'],
           ['tracking'=>'NC620168576BR','warehouse'=>'TM2594870833BR'],
           ['tracking'=>'NC620168593BR','warehouse'=>'TM2591571159BR'],
           ['tracking'=>'NC620168602BR','warehouse'=>'TM2593471344BR'],
           ['tracking'=>'NC620168616BR','warehouse'=>'TM2593971438BR'],
           ['tracking'=>'NC620168647BR','warehouse'=>'TM2590771946BR'],
           ['tracking'=>'NC620168655BR','warehouse'=>'TM2590972003BR'],
           ['tracking'=>'NC620168810BR','warehouse'=>'TM2593976000BR'],
           ['tracking'=>'NC620169404BR','warehouse'=>'TM2591588329BR'],
           ['tracking'=>'NC620169470BR','warehouse'=>'TM2591791942BR'],
           ['tracking'=>'NC620169497BR','warehouse'=>'TM2592192245BR'],
           ['tracking'=>'NC620169545BR','warehouse'=>'TM2595293328BR'],
           ['tracking'=>'NC620169713BR','warehouse'=>'TM2595499346BR'],
           ['tracking'=>'NC653642419BR','warehouse'=>'TM2592492436BR'],
           ['tracking'=>'NC653642436BR','warehouse'=>'TM2595093155BR'],
           ['tracking'=>'NC653642515BR','warehouse'=>'TM2593894711BR'],
           ['tracking'=>'NC653642665BR','warehouse'=>'TM2592097020BR'],
           ['tracking'=>'NC653642688BR','warehouse'=>'TM2593497707BR'],
           ['tracking'=>'NC653642776BR','warehouse'=>'TM2590399404BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container10()
    {
        $codes = [
           ['tracking'=>'NC574417702BR','warehouse'=>'TM2583065548BR'],
           ['tracking'=>'NC574417716BR','warehouse'=>'TM2583165615BR'],
           ['tracking'=>'NC574417764BR','warehouse'=>'TM2583466457BR'],
           ['tracking'=>'NC574417778BR','warehouse'=>'TM2583566525BR'],
           ['tracking'=>'NC575449627BR','warehouse'=>'TM2583466328BR'],
           ['tracking'=>'NC575449785BR','warehouse'=>'TM2583270844BR'],
           ['tracking'=>'NC575449794BR','warehouse'=>'TM2583270950BR'],
           ['tracking'=>'NC605559226BR','warehouse'=>'TM2594411512BR'],
           ['tracking'=>'NC605559274BR','warehouse'=>'TM2590814113BR'],
           ['tracking'=>'NC605560003BR','warehouse'=>'TM2593431001BR'],
           ['tracking'=>'NC605560799BR','warehouse'=>'TM2592947858BR'],
           ['tracking'=>'NC605560992BR','warehouse'=>'TM2594052857BR'],
           ['tracking'=>'NC605561009BR','warehouse'=>'TM2594853009BR'],
           ['tracking'=>'NC605561057BR','warehouse'=>'TM2590953909BR'],
           ['tracking'=>'NC605561508BR','warehouse'=>'TM2592565059BR'],
           ['tracking'=>'NC605561975BR','warehouse'=>'TM2595176236BR'],
           ['tracking'=>'NC620165813BR','warehouse'=>'TM2585998658BR'],
           ['tracking'=>'NC620166102BR','warehouse'=>'TM2592401536BR'],
           ['tracking'=>'NC620166371BR','warehouse'=>'TM2591908455BR'],
           ['tracking'=>'NC620166408BR','warehouse'=>'TM2595510019BR'],
           ['tracking'=>'NC620166972BR','warehouse'=>'TM2594227545BR'],
           ['tracking'=>'NC620167261BR','warehouse'=>'TM2592337120BR'],
           ['tracking'=>'NC620167275BR','warehouse'=>'TM2593237350BR'],
           ['tracking'=>'NC620167425BR','warehouse'=>'TM2594141200BR'],
           ['tracking'=>'NC620167465BR','warehouse'=>'TM2593841904BR'],
           ['tracking'=>'NC620167505BR','warehouse'=>'TM2594243305BR'],
           ['tracking'=>'NC620167598BR','warehouse'=>'TM2592446353BR'],
           ['tracking'=>'NC620167669BR','warehouse'=>'TM2591848113BR'],
           ['tracking'=>'NC620168236BR','warehouse'=>'TM2595862317BR'],
           ['tracking'=>'NC620168267BR','warehouse'=>'TM2590962608BR'],
           ['tracking'=>'NC620169449BR','warehouse'=>'TM2590691532BR'],
           ['tracking'=>'NC620169523BR','warehouse'=>'TM2593592805BR'],
           ['tracking'=>'NC653642935BR','warehouse'=>'TM2604303805BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container11()
    {
        $codes =
            [
                ['tracking'=>'NC574417680BR','warehouse'=>'TM2582865256BR'],
                ['tracking'=>'NC575449595BR','warehouse'=>'TM2582965323BR'],
                ['tracking'=>'NC575449613BR','warehouse'=>'TM2583265937BR'],
                ['tracking'=>'NC575450510BR','warehouse'=>'TM2583791515BR'],
                ['tracking'=>'NC605557684BR','warehouse'=>'TM2583270514BR'],
                ['tracking'=>'NC605558424BR','warehouse'=>'TM2581490928BR'],
                ['tracking'=>'NC605558591BR','warehouse'=>'TM2580098728BR'],
                ['tracking'=>'NC605558605BR','warehouse'=>'TM2580198948BR'],
                ['tracking'=>'NC605559005BR','warehouse'=>'TM2591904233BR'],
                ['tracking'=>'NC605559107BR','warehouse'=>'TM2595907350BR'],
                ['tracking'=>'NC605559124BR','warehouse'=>'TM2590908002BR'],
                ['tracking'=>'NC605559172BR','warehouse'=>'TM2590110157BR'],
                ['tracking'=>'NC605559243BR','warehouse'=>'TM2595812138BR'],
                ['tracking'=>'NC605559331BR','warehouse'=>'TM2590715849BR'],
                ['tracking'=>'NC605559380BR','warehouse'=>'TM2592718047BR'],
                ['tracking'=>'NC605559393BR','warehouse'=>'TM2593318303BR'],
                ['tracking'=>'NC605559416BR','warehouse'=>'TM2594518757BR'],
                ['tracking'=>'NC605559420BR','warehouse'=>'TM2595718815BR'],
                ['tracking'=>'NC605559464BR','warehouse'=>'TM2590720205BR'],
                ['tracking'=>'NC605559597BR','warehouse'=>'TM2594622249BR'],
                ['tracking'=>'NC605559875BR','warehouse'=>'TM2594827852BR'],
                ['tracking'=>'NC605559889BR','warehouse'=>'TM2591528439BR'],
                ['tracking'=>'NC605559950BR','warehouse'=>'TM2590730359BR'],
                ['tracking'=>'NC605559963BR','warehouse'=>'TM2590930413BR'],
                ['tracking'=>'NC605560034BR','warehouse'=>'TM2594631608BR'],
                ['tracking'=>'NC605560082BR','warehouse'=>'TM2591732240BR'],
                ['tracking'=>'NC605560184BR','warehouse'=>'TM2591833757BR'],
                ['tracking'=>'NC605560198BR','warehouse'=>'TM2592033828BR'],
                ['tracking'=>'NC605560207BR','warehouse'=>'TM2592334011BR'],
                ['tracking'=>'NC605560269BR','warehouse'=>'TM2594535626BR'],
                ['tracking'=>'NC605560357BR','warehouse'=>'TM2592137038BR'],
                ['tracking'=>'NC605560365BR','warehouse'=>'TM2592437233BR'],
                ['tracking'=>'NC605560388BR','warehouse'=>'TM2594037653BR'],
                ['tracking'=>'NC605560391BR','warehouse'=>'TM2594737949BR'],
                ['tracking'=>'NC605560533BR','warehouse'=>'TM2593741145BR'],
                ['tracking'=>'NC605560555BR','warehouse'=>'TM2594142157BR'],
                ['tracking'=>'NC605560595BR','warehouse'=>'TM2593043112BR'],
                ['tracking'=>'NC605560635BR','warehouse'=>'TM2595544853BR'],
                ['tracking'=>'NC605560666BR','warehouse'=>'TM2591545500BR'],
                ['tracking'=>'NC605560683BR','warehouse'=>'TM2595046019BR'],
                ['tracking'=>'NC605560697BR','warehouse'=>'TM2590346116BR'],
                ['tracking'=>'NC605560706BR','warehouse'=>'TM2593146421BR'],
                ['tracking'=>'NC605560825BR','warehouse'=>'TM2593749041BR'],
                ['tracking'=>'NC605560839BR','warehouse'=>'TM2590549459BR'],
                ['tracking'=>'NC605560961BR','warehouse'=>'TM2593552416BR'],
                ['tracking'=>'NC605561043BR','warehouse'=>'TM2590553820BR'],
                ['tracking'=>'NC605561233BR','warehouse'=>'TM2594157248BR'],
                ['tracking'=>'NC605561318BR','warehouse'=>'TM2593259723BR'],
                ['tracking'=>'NC605561335BR','warehouse'=>'TM2593360013BR'],
                ['tracking'=>'NC605561370BR','warehouse'=>'HD2593460433BR'],
                ['tracking'=>'NC605561471BR','warehouse'=>'TM2591663526BR'],
                ['tracking'=>'NC605561485BR','warehouse'=>'TM2592263945BR'],
                ['tracking'=>'NC605561627BR','warehouse'=>'TM2594267638BR'],
                ['tracking'=>'NC605561803BR','warehouse'=>'TM2591372156BR'],
                ['tracking'=>'NC605561817BR','warehouse'=>'TM2593472312BR'],
                ['tracking'=>'NC605561879BR','warehouse'=>'TM2592773804BR'],
                ['tracking'=>'NC605561922BR','warehouse'=>'TM2594275257BR'],
                ['tracking'=>'NC605561998BR','warehouse'=>'TM2595576613BR'],
                ['tracking'=>'NC620165739BR','warehouse'=>'HD2582094712BR'],
                ['tracking'=>'NC620165742BR','warehouse'=>'TM2585795043BR'],
                ['tracking'=>'NC620165756BR','warehouse'=>'TM2580295201BR'],
                ['tracking'=>'NC620165787BR','warehouse'=>'TM2584398151BR'],
                ['tracking'=>'NC620166080BR','warehouse'=>'TM2590901319BR'],
                ['tracking'=>'NC620166195BR','warehouse'=>'TM2591703859BR'],
                ['tracking'=>'NC620166266BR','warehouse'=>'TM2594606327BR'],
                ['tracking'=>'NC620166337BR','warehouse'=>'TM2590707938BR'],
                ['tracking'=>'NC620166354BR','warehouse'=>'TM2591308205BR'],
                ['tracking'=>'NC620166425BR','warehouse'=>'TM2590810512BR'],
                ['tracking'=>'NC620166439BR','warehouse'=>'TM2592511350BR'],
                ['tracking'=>'NC620166456BR','warehouse'=>'TM2590012227BR'],
                ['tracking'=>'NC620166473BR','warehouse'=>'TM2593913535BR'],
                ['tracking'=>'NC620166500BR','warehouse'=>'TM2592015031BR'],
                ['tracking'=>'NC620166969BR','warehouse'=>'TM2591927407BR'],
                ['tracking'=>'NC620167010BR','warehouse'=>'TM2590128200BR'],
                ['tracking'=>'NC620167023BR','warehouse'=>'TM2593728910BR'],
                ['tracking'=>'NC620167045BR','warehouse'=>'TM2594029130BR'],
                ['tracking'=>'NC620167125BR','warehouse'=>'TM2594531511BR'],
                ['tracking'=>'NC620167139BR','warehouse'=>'TM2594832741BR'],
                ['tracking'=>'NC620167142BR','warehouse'=>'TM2590633310BR'],
                ['tracking'=>'NC620167173BR','warehouse'=>'TM2592334113BR'],
                ['tracking'=>'NC620167195BR','warehouse'=>'TM2592634620BR'],
                ['tracking'=>'NC620167235BR','warehouse'=>'TM2590636202BR'],
                ['tracking'=>'NC620167448BR','warehouse'=>'TM2590241610BR'],
                ['tracking'=>'NC620167482BR','warehouse'=>'TM2592843027BR'],
                ['tracking'=>'NC620167496BR','warehouse'=>'TM2593843250BR'],
                ['tracking'=>'NC620167522BR','warehouse'=>'TM2595344104BR'],
                ['tracking'=>'NC620167567BR','warehouse'=>'TM2592045651BR'],
                ['tracking'=>'NC620167584BR','warehouse'=>'TM2591346233BR'],
                ['tracking'=>'NC620167607BR','warehouse'=>'TM2592246543BR'],
                ['tracking'=>'NC620167672BR','warehouse'=>'TM2592348210BR'],
                ['tracking'=>'NC620167686BR','warehouse'=>'TM2592748333BR'],
                ['tracking'=>'NC620167730BR','warehouse'=>'TM2595949232BR'],
                ['tracking'=>'NC620167814BR','warehouse'=>'TM2591651606BR'],
                ['tracking'=>'NC620167845BR','warehouse'=>'TM2592652113BR'],
                ['tracking'=>'NC620167859BR','warehouse'=>'TM2593152322BR'],
                ['tracking'=>'NC620167880BR','warehouse'=>'TM2595053241BR'],
                ['tracking'=>'NC620168103BR','warehouse'=>'TM2593059341BR'],
                ['tracking'=>'NC620168125BR','warehouse'=>'TM2593159655BR'],
                ['tracking'=>'NC620168148BR','warehouse'=>'TM2593460305BR'],
                ['tracking'=>'NC620168341BR','warehouse'=>'TM2591963859BR'],
                ['tracking'=>'NC620168369BR','warehouse'=>'TM2592964209BR'],
                ['tracking'=>'NC620168372BR','warehouse'=>'HD2592364049BR'],
                ['tracking'=>'NC620168390BR','warehouse'=>'TM2592865138BR'],
                ['tracking'=>'NC620168409BR','warehouse'=>'TM2593565226BR'],
                ['tracking'=>'NC620168664BR','warehouse'=>'TM2592472202BR'],
                ['tracking'=>'NC620168845BR','warehouse'=>'TM2592177832BR'],
                ['tracking'=>'NC620168854BR','warehouse'=>'TM2594478301BR'],
                ['tracking'=>'NC620168868BR','warehouse'=>'TM2595078510BR'],
                ['tracking'=>'NC620168871BR','warehouse'=>'TM2595078646BR'],
                ['tracking'=>'NC653642422BR','warehouse'=>'TM2594092954BR'],
                ['tracking'=>'NC653642524BR','warehouse'=>'TM2594194811BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container12()
    {
        $codes =  [
            ['tracking'=>'NC575449763BR','warehouse'=>'TM2583270738BR'],
            ['tracking'=>'NC605557707BR','warehouse'=>'TM2583270633BR'],
            ['tracking'=>'NC605558455BR','warehouse'=>'TM2581490821BR'],
            ['tracking'=>'NC605558574BR','warehouse'=>'TM2584498246BR'],
            ['tracking'=>'NC605558720BR','warehouse'=>'TM2583599556BR'],
            ['tracking'=>'NC605558999BR','warehouse'=>'TM2591703701BR'],
            ['tracking'=>'NC605559141BR','warehouse'=>'TM2592709033BR'],
            ['tracking'=>'NC605559230BR','warehouse'=>'TM2594511602BR'],
            ['tracking'=>'NC605559265BR','warehouse'=>'TM2592513048BR'],
            ['tracking'=>'NC605559402BR','warehouse'=>'TM2593818648BR'],
            ['tracking'=>'NC605559455BR','warehouse'=>'TM2594820019BR'],
            ['tracking'=>'NC605559570BR','warehouse'=>'TM2592522015BR'],
            ['tracking'=>'NC605559892BR','warehouse'=>'TM2592228648BR'],
            ['tracking'=>'NC605559901BR','warehouse'=>'TM2593028857BR'],
            ['tracking'=>'NC605560326BR','warehouse'=>'TM2591536738BR'],
            ['tracking'=>'NC605560343BR','warehouse'=>'TM2591836931BR'],
            ['tracking'=>'NC605560578BR','warehouse'=>'TM2595842304BR'],
            ['tracking'=>'NC605560811BR','warehouse'=>'TM2593448711BR'],
            ['tracking'=>'NC605561026BR','warehouse'=>'TM2595853504BR'],
            ['tracking'=>'NC605561074BR','warehouse'=>'TM2592254109BR'],
            ['tracking'=>'NC605561658BR','warehouse'=>'TM2593468815BR'],
            ['tracking'=>'NC605562049BR','warehouse'=>'TM2591677706BR'],
            ['tracking'=>'NC605562066BR','warehouse'=>'TM2593778221BR'],
            ['tracking'=>'NC620165950BR','warehouse'=>'TM2581399040BR'],
            ['tracking'=>'NC620165963BR','warehouse'=>'TM2581799116BR'],
            ['tracking'=>'NC620165994BR','warehouse'=>'TM2582499438BR'],
            ['tracking'=>'NC620166120BR','warehouse'=>'TM2595601858BR'],
            ['tracking'=>'NC620166345BR','warehouse'=>'TM2590908122BR'],
            ['tracking'=>'NC620166411BR','warehouse'=>'TM2590310217BR'],
            ['tracking'=>'NC620167006BR','warehouse'=>'TM2595928014BR'],
            ['tracking'=>'NC620167108BR','warehouse'=>'TM2593330928BR'],
            ['tracking'=>'NC620167451BR','warehouse'=>'TM2593541839BR'],
            ['tracking'=>'NC620167575BR','warehouse'=>'TM2594245947BR'],
            ['tracking'=>'NC620167615BR','warehouse'=>'TM2592846609BR'],
            ['tracking'=>'NC620167690BR','warehouse'=>'TM2593148454BR'],
            ['tracking'=>'NC620167712BR','warehouse'=>'TM2593448817BR'],
            ['tracking'=>'NC620167726BR','warehouse'=>'TM2593448924BR'],
            ['tracking'=>'NC620167791BR','warehouse'=>'TM2595751001BR'],
            ['tracking'=>'NC620167893BR','warehouse'=>'TM2595453423BR'],
            ['tracking'=>'NC620168085BR','warehouse'=>'TM2593059116BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13()
    {
        $codes =  [
           ['tracking'=>'NC605558557BR','warehouse'=>'TM2582495610BR'],
           ['tracking'=>'NC605558565BR','warehouse'=>'TM2584398047BR'],
           ['tracking'=>'NC605558870BR','warehouse'=>'TM2595501602BR'],
           ['tracking'=>'NC605558883BR','warehouse'=>'TM2595701955BR'],
           ['tracking'=>'NC605558937BR','warehouse'=>'TM2590702735BR'],
           ['tracking'=>'NC605559098BR','warehouse'=>'TM2595806954BR'],
           ['tracking'=>'NC605559155BR','warehouse'=>'TM2593809526BR'],
           ['tracking'=>'NC605559186BR','warehouse'=>'TM2590410349BR'],
           ['tracking'=>'NC605559190BR','warehouse'=>'TM2591010630BR'],
           ['tracking'=>'NC605559212BR','warehouse'=>'TM2592211128BR'],
           ['tracking'=>'NC605559288BR','warehouse'=>'TM2590914247BR'],
           ['tracking'=>'NC605559433BR','warehouse'=>'TM2590218937BR'],
           ['tracking'=>'NC605559946BR','warehouse'=>'TM2595130155BR'],
           ['tracking'=>'NC605559985BR','warehouse'=>'TM2592730610BR'],
           ['tracking'=>'NC605559994BR','warehouse'=>'TM2592930821BR'],
           ['tracking'=>'NC605560017BR','warehouse'=>'TM2593531148BR'],
           ['tracking'=>'NC605560025BR','warehouse'=>'TM2594131318BR'],
           ['tracking'=>'NC605560048BR','warehouse'=>'TM2595031755BR'],
           ['tracking'=>'NC605560051BR','warehouse'=>'TM2595931808BR'],
           ['tracking'=>'NC605560065BR','warehouse'=>'TM2590631923BR'],
           ['tracking'=>'NC605560079BR','warehouse'=>'TM2591632157BR'],
           ['tracking'=>'NC605560140BR','warehouse'=>'TM2590432904BR'],
           ['tracking'=>'NC605560167BR','warehouse'=>'TM2591333409BR'],
           ['tracking'=>'NC605560238BR','warehouse'=>'TM2592434449BR'],
           ['tracking'=>'NC605560330BR','warehouse'=>'TM2591636859BR'],
           ['tracking'=>'NC605560547BR','warehouse'=>'TM2594241358BR'],
           ['tracking'=>'NC605560564BR','warehouse'=>'TM2594442243BR'],
           ['tracking'=>'NC605560670BR','warehouse'=>'TM2593345829BR'],
           ['tracking'=>'NC605560842BR','warehouse'=>'TM2591349603BR'],
           ['tracking'=>'NC605560913BR','warehouse'=>'TM2595951113BR'],
           ['tracking'=>'NC605560958BR','warehouse'=>'TM2591751721BR'],
           ['tracking'=>'NC605561304BR','warehouse'=>'TM2593159407BR'],
           ['tracking'=>'NC605561321BR','warehouse'=>'TM2593259847BR'],
           ['tracking'=>'NC605561349BR','warehouse'=>'TM2593360139BR'],
           ['tracking'=>'NC605561701BR','warehouse'=>'TM2590969649BR'],
           ['tracking'=>'NC620166093BR','warehouse'=>'TM2591101453BR'],
           ['tracking'=>'NC620166164BR','warehouse'=>'TM2590802932BR'],
           ['tracking'=>'NC620166181BR','warehouse'=>'TM2591503508BR'],
           ['tracking'=>'NC620166249BR','warehouse'=>'TM2593305437BR'],
           ['tracking'=>'NC620166385BR','warehouse'=>'TM2593809402BR'],
           ['tracking'=>'NC620166561BR','warehouse'=>'TM2591216444BR'],
           ['tracking'=>'NC620166629BR','warehouse'=>'TM2590619028BR'],
           ['tracking'=>'NC620166632BR','warehouse'=>'TM2593119148BR'],
           ['tracking'=>'NC620166650BR','warehouse'=>'TM2595220119BR'],
           ['tracking'=>'NC620166990BR','warehouse'=>'TM2595727954BR'],
           ['tracking'=>'NC620167037BR','warehouse'=>'TM2593929008BR'],
           ['tracking'=>'NC620167068BR','warehouse'=>'TM2594429513BR'],
           ['tracking'=>'NC620167099BR','warehouse'=>'TM2592830729BR'],
           ['tracking'=>'NC620167111BR','warehouse'=>'TM2593631245BR'],
           ['tracking'=>'NC620167156BR','warehouse'=>'TM2591333559BR'],
           ['tracking'=>'NC620167655BR','warehouse'=>'TM2591648047BR'],
           ['tracking'=>'NC620168559BR','warehouse'=>'TM2594168947BR'],
           ['tracking'=>'NC620169639BR','warehouse'=>'HD2591495854BR'],
        ];
        return $this->updateTracking($codes);
    }

    function updateTracking($codes)
    {
        set_time_limit(1500);
        //update standard
        $ordersDetails = null;
        try {
            foreach ($codes as $code) {
                $flag = false;

                if (Order::where('warehouse_number', $code['warehouse'])
                    ->where('corrios_tracking_code', 'like', 'IX%')
                    ->update(['shipping_service_id' => 42])
                ) {
                    $flag = true;
                }
                if (Order::where('warehouse_number', $code['warehouse'])
                    ->where('corrios_tracking_code', 'like', 'NC%')
                    ->update(['shipping_service_id' => 16])
                ) {
                    $flag = true;
                }

                $order = Order::where('warehouse_number',  $code['warehouse'])->first();

                if (!$flag) {
                    $ordersDetails[] = [
                        'tracking_old' =>  $code['tracking'],
                        'warehouse' => $code['warehouse'],
                        'tracking_new' => 'not found',
                        'link' => 'not found',
                        'poboxName' => 'not found',

                    ];
                } else { 
                    if($code['tracking']==$order->corrios_tracking_code){
                        $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
                        $corrieosBrazilLabelRepository->run($order, true);
                    }
                   $ordersDetails[] = [
                        'tracking_old' => $code['tracking'],
                        'warehouse' => $order->warehouse_number,
                        'tracking_new' => $order->corrios_tracking_code,
                        'link' => route('order.label.download', encrypt($order->id)),
                        'poboxName' => $order->user->pobox_name,
                    ];  
                }
            }
        } catch (Exception $e) {
            \Log::info(['error' => $e->getMessage()]);
        }

        if ($ordersDetails) {
            $exports = new OrderUpdateExport($ordersDetails);
            return response()->download($exports->handle());
        } else {
            echo 'order not found';
            dd($codes);
        }
    }

}
