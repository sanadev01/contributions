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

    function container9a()
    {
        $codes = [
            ['tracking' => 'NC479476979BR', 'warehouse' => 'TM2545684158BR'],
            ['tracking' => 'NC522420975BR', 'warehouse' => 'TM2554829520BR'],
            ['tracking' => 'NC522420989BR', 'warehouse' => 'TM2554929708BR'],
            ['tracking' => 'NC546503127BR', 'warehouse' => 'TM2565158613BR'],
            ['tracking' => 'NC605559328BR', 'warehouse' => 'TM2593515211BR'],
        ];
        return $this->updateTracking($codes);
    }
   

    function container9f()
    {
        $codes = [
            ['tracking' => 'NC605560405BR', 'warehouse' => 'HD2594237824BR'],
            ['tracking' => 'NC605561091BR', 'warehouse' => 'TM2595554814BR'],
            ['tracking' => 'NC605561105BR', 'warehouse' => 'TM2590154950BR'],
            ['tracking' => 'NC605561128BR', 'warehouse' => 'TM2590655116BR'],
            ['tracking' => 'NC605561216BR', 'warehouse' => 'TM2593256653BR'],
            ['tracking' => 'NC605561278BR', 'warehouse' => 'TM2591858318BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container9b()
    {

        $codes = [
            ['tracking' => 'NC605561539BR', 'warehouse' => 'TM2592865638BR'],
            ['tracking' => 'NC605561635BR', 'warehouse' => 'TM2594867950BR'],
            ['tracking' => 'NC605561644BR', 'warehouse' => 'TM2594968050BR'],
            ['tracking' => 'NC605561661BR', 'warehouse' => 'TM2594669019BR'],
            ['tracking' => 'NC605561715BR', 'warehouse' => 'TM2591669857BR'],
            ['tracking' => 'NC605561729BR', 'warehouse' => 'TM2591770052BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container9g()
    {
        $codes = [
            ['tracking' => 'NC605561777BR', 'warehouse' => 'TM2593970711BR'],
            ['tracking' => 'NC605561785BR', 'warehouse' => 'TM2591171041BR'],
            ['tracking' => 'NC605561940BR', 'warehouse' => 'TM2594775410BR'], //
            ['tracking' => 'NC605561953BR', 'warehouse' => 'TM2595075637BR'],
            ['tracking' => 'NC605561984BR', 'warehouse' => 'TM2595376558BR'],
            ['tracking' => 'NC605562018BR', 'warehouse' => 'TM2592577020BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container9c()
    {

        $codes = [
            ['tracking' => 'NC620165760BR', 'warehouse' => 'HD2584594903BR'],
            ['tracking' => 'NC620166513BR', 'warehouse' => 'TM2593415147BR'],
            ['tracking' => 'NC620167876BR', 'warehouse' => 'TM2594452913BR'],
            ['tracking' => 'NC620167916BR', 'warehouse' => 'TM2594754401BR'],
            ['tracking' => 'NC620167920BR', 'warehouse' => 'TM2591255219BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container9h()
    {
        $codes = [
            ['tracking' => 'NC620167981BR', 'warehouse' => 'TM2593356717BR'],
            ['tracking' => 'NC620167995BR', 'warehouse' => 'TM2593556811BR'],
            ['tracking' => 'NC620168050BR', 'warehouse' => 'TM2592658523BR'],
            ['tracking' => 'NC620168240BR', 'warehouse' => 'TM2590362450BR'],
            ['tracking' => 'NC620168275BR', 'warehouse' => 'TM2591062705BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container9d()
    {
        $codes = [
            ['tracking' => 'NC620168284BR', 'warehouse' => 'TM2591262946BR'],
            ['tracking' => 'NC620168307BR', 'warehouse' => 'TM2591563112BR'], //NC620168307BR
            ['tracking' => 'NC620168338BR', 'warehouse' => 'TM2591863751BR'],
            ['tracking' => 'NC620168386BR', 'warehouse' => 'TM2594364710BR'],
            ['tracking' => 'NC620168528BR', 'warehouse' => 'TM2594767833BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container9i()
    {
        $codes = [
            ['tracking' => 'NC620168576BR', 'warehouse' => 'TM2594870833BR'],
            ['tracking' => 'NC620168593BR', 'warehouse' => 'TM2591571159BR'],
            ['tracking' => 'NC620168602BR', 'warehouse' => 'TM2593471344BR'],
            ['tracking' => 'NC620168616BR', 'warehouse' => 'TM2593971438BR'],
            ['tracking' => 'NC620168647BR', 'warehouse' => 'TM2590771946BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container9e()
    {
        $codes = [
            ['tracking' => 'NC620168655BR', 'warehouse' => 'TM2590972003BR'],
            ['tracking' => 'NC620168810BR', 'warehouse' => 'TM2593976000BR'],
            ['tracking' => 'NC620169404BR', 'warehouse' => 'TM2591588329BR'],
            ['tracking' => 'NC620169470BR', 'warehouse' => 'TM2591791942BR'],
            ['tracking' => 'NC620169497BR', 'warehouse' => 'TM2592192245BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container9j()
    {
        $codes = [
            ['tracking' => 'NC620169545BR', 'warehouse' => 'TM2595293328BR'],
            ['tracking' => 'NC620169713BR', 'warehouse' => 'TM2595499346BR'],
            ['tracking' => 'NC653642419BR', 'warehouse' => 'TM2592492436BR'],
            ['tracking' => 'NC653642436BR', 'warehouse' => 'TM2595093155BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container9k()
    {
        $codes = [
            ['tracking' => 'NC653642515BR', 'warehouse' => 'TM2593894711BR'],
            ['tracking' => 'NC653642665BR', 'warehouse' => 'TM2592097020BR'],
            ['tracking' => 'NC653642688BR', 'warehouse' => 'TM2593497707BR'],
            ['tracking' => 'NC653642776BR', 'warehouse' => 'TM2590399404BR'], //NC653642776BR
        ];
        return $this->updateTracking($codes);
    }

    function container10e()
    {
        $codes = [
            ['tracking' => 'NC574417702BR', 'warehouse' => 'TM2583065548BR'],
            ['tracking' => 'NC574417716BR', 'warehouse' => 'TM2583165615BR'],
            ['tracking' => 'NC574417764BR', 'warehouse' => 'TM2583466457BR'],
            ['tracking' => 'NC574417778BR', 'warehouse' => 'TM2583566525BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container10a()
    {
        $codes = [
            ['tracking' => 'NC575449627BR', 'warehouse' => 'TM2583466328BR'],
            ['tracking' => 'NC575449785BR', 'warehouse' => 'TM2583270844BR'],
            ['tracking' => 'NC575449794BR', 'warehouse' => 'TM2583270950BR'],
            ['tracking' => 'NC605559226BR', 'warehouse' => 'TM2594411512BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container10b()
    {
        $codes = [
            ['tracking' => 'NC605559274BR', 'warehouse' => 'TM2590814113BR'],
            ['tracking' => 'NC605560003BR', 'warehouse' => 'TM2593431001BR'],
            ['tracking' => 'NC605560799BR', 'warehouse' => 'TM2592947858BR'],
            ['tracking' => 'NC605560992BR', 'warehouse' => 'TM2594052857BR'],
            ['tracking' => 'NC605561009BR', 'warehouse' => 'TM2594853009BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container10c()
    {
        $codes = [
            ['tracking' => 'NC605561057BR', 'warehouse' => 'TM2590953909BR'],
            ['tracking' => 'NC605561508BR', 'warehouse' => 'TM2592565059BR'],
            ['tracking' => 'NC605561975BR', 'warehouse' => 'TM2595176236BR'],
            ['tracking' => 'NC620165813BR', 'warehouse' => 'TM2585998658BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container10d()
    {
        $codes = [
            ['tracking' => 'NC620166102BR', 'warehouse' => 'TM2592401536BR'],
            ['tracking' => 'NC620166371BR', 'warehouse' => 'TM2591908455BR'],
            ['tracking' => 'NC620166408BR', 'warehouse' => 'TM2595510019BR'],
            ['tracking' => 'NC620166972BR', 'warehouse' => 'TM2594227545BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container10h()
    {
        $codes = [
            ['tracking' => 'NC620167261BR', 'warehouse' => 'TM2592337120BR'],
            ['tracking' => 'NC620167275BR', 'warehouse' => 'TM2593237350BR'],
            ['tracking' => 'NC620167425BR', 'warehouse' => 'TM2594141200BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container10f()
    {
        $codes = [
            ['tracking' => 'NC620167465BR', 'warehouse' => 'TM2593841904BR'],
            ['tracking' => 'NC620167505BR', 'warehouse' => 'TM2594243305BR'],
            ['tracking' => 'NC620167598BR', 'warehouse' => 'TM2592446353BR'],
            ['tracking' => 'NC620167669BR', 'warehouse' => 'TM2591848113BR'],
            ['tracking' => 'NC620168236BR', 'warehouse' => 'TM2595862317BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container10g()
    {
        $codes = [
            ['tracking' => 'NC620168267BR', 'warehouse' => 'TM2590962608BR'],
            ['tracking' => 'NC620169449BR', 'warehouse' => 'TM2590691532BR'],
            ['tracking' => 'NC620169523BR', 'warehouse' => 'TM2593592805BR'],
            ['tracking' => 'NC653642935BR', 'warehouse' => 'TM2604303805BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container11a()
    {
        $codes =
            [
                ['tracking' => 'NC574417680BR', 'warehouse' => 'TM2582865256BR'],
                ['tracking' => 'NC575449595BR', 'warehouse' => 'TM2582965323BR'],
                ['tracking' => 'NC575449613BR', 'warehouse' => 'TM2583265937BR'],
                ['tracking' => 'NC575450510BR', 'warehouse' => 'TM2583791515BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11b()
    {
        $codes =
            [
                ['tracking' => 'NC605557684BR', 'warehouse' => 'TM2583270514BR'],
                ['tracking' => 'NC605558424BR', 'warehouse' => 'TM2581490928BR'],
                ['tracking' => 'NC605558591BR', 'warehouse' => 'TM2580098728BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11c()
    {
        $codes =
            [
                ['tracking' => 'NC605558605BR', 'warehouse' => 'TM2580198948BR'],
                ['tracking' => 'NC605559005BR', 'warehouse' => 'TM2591904233BR'],
                ['tracking' => 'NC605559107BR', 'warehouse' => 'TM2595907350BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11d()
    {
        $codes =
            [
                ['tracking' => 'NC605559124BR', 'warehouse' => 'TM2590908002BR'],
                ['tracking' => 'NC605559172BR', 'warehouse' => 'TM2590110157BR'],
                ['tracking' => 'NC605559243BR', 'warehouse' => 'TM2595812138BR'],
                ['tracking' => 'NC605559331BR', 'warehouse' => 'TM2590715849BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11e()
    {
        $codes =
            [
                ['tracking' => 'NC605559380BR', 'warehouse' => 'TM2592718047BR'],
                ['tracking' => 'NC605559393BR', 'warehouse' => 'TM2593318303BR'],
                ['tracking' => 'NC605559416BR', 'warehouse' => 'TM2594518757BR'],
                ['tracking' => 'NC605559420BR', 'warehouse' => 'TM2595718815BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11f()
    {
        $codes =
            [
                ['tracking' => 'NC605559464BR', 'warehouse' => 'TM2590720205BR'],
                ['tracking' => 'NC605559597BR', 'warehouse' => 'TM2594622249BR'],
                ['tracking' => 'NC605559875BR', 'warehouse' => 'TM2594827852BR'],
                ['tracking' => 'NC605559889BR', 'warehouse' => 'TM2591528439BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11g()
    {
        $codes =
            [
                ['tracking' => 'NC605559950BR', 'warehouse' => 'TM2590730359BR'],
                ['tracking' => 'NC605559963BR', 'warehouse' => 'TM2590930413BR'],
                ['tracking' => 'NC605560034BR', 'warehouse' => 'TM2594631608BR'],
                ['tracking' => 'NC605560082BR', 'warehouse' => 'TM2591732240BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11h()
    {
        $codes =
            [
                ['tracking' => 'NC605560184BR', 'warehouse' => 'TM2591833757BR'],
                ['tracking' => 'NC605560198BR', 'warehouse' => 'TM2592033828BR'],
                ['tracking' => 'NC605560207BR', 'warehouse' => 'TM2592334011BR'],
                ['tracking' => 'NC605560269BR', 'warehouse' => 'TM2594535626BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11i()
    {
        $codes =
            [
                ['tracking' => 'NC605560357BR', 'warehouse' => 'TM2592137038BR'],
                ['tracking' => 'NC605560365BR', 'warehouse' => 'TM2592437233BR'],
                ['tracking' => 'NC605560388BR', 'warehouse' => 'TM2594037653BR'],
                ['tracking' => 'NC605560391BR', 'warehouse' => 'TM2594737949BR'],
                ['tracking' => 'NC605560533BR', 'warehouse' => 'TM2593741145BR'],
                ['tracking' => 'NC605560555BR', 'warehouse' => 'TM2594142157BR'],
                ['tracking' => 'NC605560595BR', 'warehouse' => 'TM2593043112BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11j()
    {
        $codes =
            [
                ['tracking' => 'NC605560635BR', 'warehouse' => 'TM2595544853BR'],
                ['tracking' => 'NC605560666BR', 'warehouse' => 'TM2591545500BR'],
                ['tracking' => 'NC605560683BR', 'warehouse' => 'TM2595046019BR'],
                ['tracking' => 'NC605560697BR', 'warehouse' => 'TM2590346116BR'],
                ['tracking' => 'NC605560706BR', 'warehouse' => 'TM2593146421BR'],
                ['tracking' => 'NC605560825BR', 'warehouse' => 'TM2593749041BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11k()
    {
        $codes =
            [
                ['tracking' => 'NC605560839BR', 'warehouse' => 'TM2590549459BR'],
                ['tracking' => 'NC605560961BR', 'warehouse' => 'TM2593552416BR'],
                ['tracking' => 'NC605561043BR', 'warehouse' => 'TM2590553820BR'],
                ['tracking' => 'NC605561233BR', 'warehouse' => 'TM2594157248BR'],
                ['tracking' => 'NC605561318BR', 'warehouse' => 'TM2593259723BR'],
                ['tracking' => 'NC605561335BR', 'warehouse' => 'TM2593360013BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11l()
    {
        $codes =
            [
                ['tracking' => 'NC605561370BR', 'warehouse' => 'HD2593460433BR'],
                ['tracking' => 'NC605561471BR', 'warehouse' => 'TM2591663526BR'],
                ['tracking' => 'NC605561485BR', 'warehouse' => 'TM2592263945BR'],
                ['tracking' => 'NC605561627BR', 'warehouse' => 'TM2594267638BR'],
                ['tracking' => 'NC605561803BR', 'warehouse' => 'TM2591372156BR'],
                ['tracking' => 'NC605561817BR', 'warehouse' => 'TM2593472312BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11m()
    {
        $codes =
            [
                ['tracking' => 'NC605561879BR', 'warehouse' => 'TM2592773804BR'],
                ['tracking' => 'NC605561922BR', 'warehouse' => 'TM2594275257BR'],
                ['tracking' => 'NC605561998BR', 'warehouse' => 'TM2595576613BR'],
                ['tracking' => 'NC620165739BR', 'warehouse' => 'HD2582094712BR'],
                ['tracking' => 'NC620165742BR', 'warehouse' => 'TM2585795043BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11n()
    {
        $codes =
            [
                ['tracking' => 'NC620165756BR', 'warehouse' => 'TM2580295201BR'],
                ['tracking' => 'NC620165787BR', 'warehouse' => 'TM2584398151BR'],
                ['tracking' => 'NC620166080BR', 'warehouse' => 'TM2590901319BR'],
                ['tracking' => 'NC620166195BR', 'warehouse' => 'TM2591703859BR'],
                ['tracking' => 'NC620166266BR', 'warehouse' => 'TM2594606327BR'],
                ['tracking' => 'NC620166337BR', 'warehouse' => 'TM2590707938BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11o()
    {
        $codes =
            [
                ['tracking' => 'NC620166354BR', 'warehouse' => 'TM2591308205BR'],
                ['tracking' => 'NC620166425BR', 'warehouse' => 'TM2590810512BR'],
                ['tracking' => 'NC620166439BR', 'warehouse' => 'TM2592511350BR'],
                ['tracking' => 'NC620166456BR', 'warehouse' => 'TM2590012227BR'],
                ['tracking' => 'NC620166473BR', 'warehouse' => 'TM2593913535BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11p()
    {
        $codes =
            [
                ['tracking' => 'NC620166500BR', 'warehouse' => 'TM2592015031BR'],
                ['tracking' => 'NC620166969BR', 'warehouse' => 'TM2591927407BR'],
                ['tracking' => 'NC620167010BR', 'warehouse' => 'TM2590128200BR'],
                ['tracking' => 'NC620167023BR', 'warehouse' => 'TM2593728910BR'],
                ['tracking' => 'NC620167045BR', 'warehouse' => 'TM2594029130BR'],
                ['tracking' => 'NC620167125BR', 'warehouse' => 'TM2594531511BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11q()
    {
        $codes =
            [
                ['tracking' => 'NC620167139BR', 'warehouse' => 'TM2594832741BR'],
                ['tracking' => 'NC620167142BR', 'warehouse' => 'TM2590633310BR'],
                ['tracking' => 'NC620167173BR', 'warehouse' => 'TM2592334113BR'],
                ['tracking' => 'NC620167195BR', 'warehouse' => 'TM2592634620BR'],
                ['tracking' => 'NC620167235BR', 'warehouse' => 'TM2590636202BR'],
                ['tracking' => 'NC620167448BR', 'warehouse' => 'TM2590241610BR'],
                ['tracking' => 'NC620167482BR', 'warehouse' => 'TM2592843027BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11r()
    {
        $codes =
            [
                ['tracking' => 'NC620167496BR', 'warehouse' => 'TM2593843250BR'],
                ['tracking' => 'NC620167522BR', 'warehouse' => 'TM2595344104BR'],
                ['tracking' => 'NC620167567BR', 'warehouse' => 'TM2592045651BR'],
                ['tracking' => 'NC620167584BR', 'warehouse' => 'TM2591346233BR'],
                ['tracking' => 'NC620167607BR', 'warehouse' => 'TM2592246543BR'],
                ['tracking' => 'NC620167672BR', 'warehouse' => 'TM2592348210BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11s()
    {
        $codes =
            [
                ['tracking' => 'NC620167686BR', 'warehouse' => 'TM2592748333BR'],
                ['tracking' => 'NC620167730BR', 'warehouse' => 'TM2595949232BR'],
                ['tracking' => 'NC620167814BR', 'warehouse' => 'TM2591651606BR'],
                ['tracking' => 'NC620167845BR', 'warehouse' => 'TM2592652113BR'],
                ['tracking' => 'NC620167859BR', 'warehouse' => 'TM2593152322BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11t()
    {
        $codes =
            [
                ['tracking' => 'NC620167880BR', 'warehouse' => 'TM2595053241BR'],
                ['tracking' => 'NC620168103BR', 'warehouse' => 'TM2593059341BR'],
                ['tracking' => 'NC620168125BR', 'warehouse' => 'TM2593159655BR'],

                ['tracking' => 'NC620168148BR', 'warehouse' => 'TM2593460305BR'],
                ['tracking' => 'NC620168341BR', 'warehouse' => 'TM2591963859BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11u()
    {
        $codes =
            [
                ['tracking' => 'NC620168369BR', 'warehouse' => 'TM2592964209BR'],
                ['tracking' => 'NC620168372BR', 'warehouse' => 'HD2592364049BR'],
                ['tracking' => 'NC620168390BR', 'warehouse' => 'TM2592865138BR'],
                ['tracking' => 'NC620168409BR', 'warehouse' => 'TM2593565226BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11v()
    {
        $codes =
            [
                ['tracking' => 'NC620168664BR', 'warehouse' => 'TM2592472202BR'],
                ['tracking' => 'NC620168845BR', 'warehouse' => 'TM2592177832BR'],
                ['tracking' => 'NC620168854BR', 'warehouse' => 'TM2594478301BR'],
                ['tracking' => 'NC620168868BR', 'warehouse' => 'TM2595078510BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container11w()
    {
        $codes =
            [
                ['tracking' => 'NC620168871BR', 'warehouse' => 'TM2595078646BR'],
                ['tracking' => 'NC653642422BR', 'warehouse' => 'TM2594092954BR'],
                ['tracking' => 'NC653642524BR', 'warehouse' => 'TM2594194811BR'],
            ];
        return $this->updateTracking($codes);
    }

    function container12a()
    {
        $codes =  [
            ['tracking' => 'NC575449763BR', 'warehouse' => 'TM2583270738BR'],
            ['tracking' => 'NC605557707BR', 'warehouse' => 'TM2583270633BR'],
            ['tracking' => 'NC605558455BR', 'warehouse' => 'TM2581490821BR'],
            ['tracking' => 'NC605558574BR', 'warehouse' => 'TM2584498246BR'],
            ['tracking' => 'NC605558720BR', 'warehouse' => 'TM2583599556BR'],
            ['tracking' => 'NC605558999BR', 'warehouse' => 'TM2591703701BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container12b()
    {
        $codes =  [
            ['tracking' => 'NC605559141BR', 'warehouse' => 'TM2592709033BR'],
            ['tracking' => 'NC605559230BR', 'warehouse' => 'TM2594511602BR'],
            ['tracking' => 'NC605559265BR', 'warehouse' => 'TM2592513048BR'],
            ['tracking' => 'NC605559402BR', 'warehouse' => 'TM2593818648BR'],
            ['tracking' => 'NC605559455BR', 'warehouse' => 'TM2594820019BR'],
            ['tracking' => 'NC605559570BR', 'warehouse' => 'TM2592522015BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container12c()
    {
        $codes =  [
            ['tracking' => 'NC605559892BR', 'warehouse' => 'TM2592228648BR'],
            ['tracking' => 'NC605559901BR', 'warehouse' => 'TM2593028857BR'],
            ['tracking' => 'NC605560326BR', 'warehouse' => 'TM2591536738BR'],
            ['tracking' => 'NC605560343BR', 'warehouse' => 'TM2591836931BR'],
            ['tracking' => 'NC605560578BR', 'warehouse' => 'TM2595842304BR'],
            ['tracking' => 'NC605560811BR', 'warehouse' => 'TM2593448711BR'],
            ['tracking' => 'NC605561026BR', 'warehouse' => 'TM2595853504BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container12d()
    {
        $codes =  [
            ['tracking' => 'NC605561074BR', 'warehouse' => 'TM2592254109BR'],
            ['tracking' => 'NC605561658BR', 'warehouse' => 'TM2593468815BR'],
            ['tracking' => 'NC605562049BR', 'warehouse' => 'TM2591677706BR'],
            ['tracking' => 'NC605562066BR', 'warehouse' => 'TM2593778221BR'],
            ['tracking' => 'NC620165950BR', 'warehouse' => 'TM2581399040BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container12e()
    {
        $codes =  [
            ['tracking' => 'NC620165963BR', 'warehouse' => 'TM2581799116BR'],
            ['tracking' => 'NC620165994BR', 'warehouse' => 'TM2582499438BR'],
            ['tracking' => 'NC620166120BR', 'warehouse' => 'TM2595601858BR'],
            ['tracking' => 'NC620166345BR', 'warehouse' => 'TM2590908122BR'],
            ['tracking' => 'NC620166411BR', 'warehouse' => 'TM2590310217BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container12f()
    {
        $codes =  [
            ['tracking' => 'NC620167006BR', 'warehouse' => 'TM2595928014BR'],
            ['tracking' => 'NC620167108BR', 'warehouse' => 'TM2593330928BR'],
            ['tracking' => 'NC620167451BR', 'warehouse' => 'TM2593541839BR'],
            ['tracking' => 'NC620167575BR', 'warehouse' => 'TM2594245947BR'],
            ['tracking' => 'NC620167615BR', 'warehouse' => 'TM2592846609BR'],
            ['tracking' => 'NC620167690BR', 'warehouse' => 'TM2593148454BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container12g()
    {
        $codes =  [
            ['tracking' => 'NC620167712BR', 'warehouse' => 'TM2593448817BR'],
            ['tracking' => 'NC620167726BR', 'warehouse' => 'TM2593448924BR'],
            ['tracking' => 'NC620167791BR', 'warehouse' => 'TM2595751001BR'],
            ['tracking' => 'NC620167893BR', 'warehouse' => 'TM2595453423BR'],
            ['tracking' => 'NC620168085BR', 'warehouse' => 'TM2593059116BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13a()
    {
        $codes =  [
            ['tracking' => 'NC605558557BR', 'warehouse' => 'TM2582495610BR'],
            ['tracking' => 'NC605558565BR', 'warehouse' => 'TM2584398047BR'],
            ['tracking' => 'NC605558870BR', 'warehouse' => 'TM2595501602BR'],
            ['tracking' => 'NC605558883BR', 'warehouse' => 'TM2595701955BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13b()
    {
        $codes =  [
            ['tracking' => 'NC605558937BR', 'warehouse' => 'TM2590702735BR'],
            ['tracking' => 'NC605559098BR', 'warehouse' => 'TM2595806954BR'],
            ['tracking' => 'NC605559155BR', 'warehouse' => 'TM2593809526BR'],
            ['tracking' => 'NC605559186BR', 'warehouse' => 'TM2590410349BR'],
            ['tracking' => 'NC605559190BR', 'warehouse' => 'TM2591010630BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13c()
    {
        $codes =  [
            ['tracking' => 'NC605559212BR', 'warehouse' => 'TM2592211128BR'],
            ['tracking' => 'NC605559288BR', 'warehouse' => 'TM2590914247BR'],
            ['tracking' => 'NC605559433BR', 'warehouse' => 'TM2590218937BR'],
            ['tracking' => 'NC605559946BR', 'warehouse' => 'TM2595130155BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13d()
    {
        $codes =  [
            ['tracking' => 'NC605559985BR', 'warehouse' => 'TM2592730610BR'],
            ['tracking' => 'NC605559994BR', 'warehouse' => 'TM2592930821BR'],
            ['tracking' => 'NC605560017BR', 'warehouse' => 'TM2593531148BR'],
            ['tracking' => 'NC605560025BR', 'warehouse' => 'TM2594131318BR'],
            ['tracking' => 'NC605560048BR', 'warehouse' => 'TM2595031755BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13e()
    {
        $codes =  [
            ['tracking' => 'NC605560051BR', 'warehouse' => 'TM2595931808BR'],
            ['tracking' => 'NC605560065BR', 'warehouse' => 'TM2590631923BR'],
            ['tracking' => 'NC605560079BR', 'warehouse' => 'TM2591632157BR'],
            ['tracking' => 'NC605560140BR', 'warehouse' => 'TM2590432904BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13f()
    {
        $codes =  [
            ['tracking' => 'NC605560167BR', 'warehouse' => 'TM2591333409BR'],
            ['tracking' => 'NC605560238BR', 'warehouse' => 'TM2592434449BR'],
            ['tracking' => 'NC605560330BR', 'warehouse' => 'TM2591636859BR'],
            ['tracking' => 'NC605560547BR', 'warehouse' => 'TM2594241358BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13g()
    {
        $codes =  [
            ['tracking' => 'NC605560564BR', 'warehouse' => 'TM2594442243BR'],
            ['tracking' => 'NC605560670BR', 'warehouse' => 'TM2593345829BR'],
            ['tracking' => 'NC605560842BR', 'warehouse' => 'TM2591349603BR'],
            ['tracking' => 'NC605560913BR', 'warehouse' => 'TM2595951113BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13h()
    {
        $codes =  [
            ['tracking' => 'NC605560958BR', 'warehouse' => 'TM2591751721BR'],
            ['tracking' => 'NC605561304BR', 'warehouse' => 'TM2593159407BR'],
            ['tracking' => 'NC605561321BR', 'warehouse' => 'TM2593259847BR'],
            ['tracking' => 'NC605561349BR', 'warehouse' => 'TM2593360139BR'],
            ['tracking' => 'NC605561701BR', 'warehouse' => 'TM2590969649BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13i()
    {
        $codes =  [
            ['tracking' => 'NC620166093BR', 'warehouse' => 'TM2591101453BR'],
            ['tracking' => 'NC620166164BR', 'warehouse' => 'TM2590802932BR'],
            ['tracking' => 'NC620166181BR', 'warehouse' => 'TM2591503508BR'],
            ['tracking' => 'NC620166249BR', 'warehouse' => 'TM2593305437BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13j()
    {
        $codes =  [
            ['tracking' => 'NC620166385BR', 'warehouse' => 'TM2593809402BR'],
            ['tracking' => 'NC620166561BR', 'warehouse' => 'TM2591216444BR'],
            ['tracking' => 'NC620166629BR', 'warehouse' => 'TM2590619028BR'],
            ['tracking' => 'NC620166632BR', 'warehouse' => 'TM2593119148BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13k()
    {
        $codes =  [
            ['tracking' => 'NC620166650BR', 'warehouse' => 'TM2595220119BR'],
            ['tracking' => 'NC620166990BR', 'warehouse' => 'TM2595727954BR'],
            ['tracking' => 'NC620167037BR', 'warehouse' => 'TM2593929008BR'],
            ['tracking' => 'NC620167068BR', 'warehouse' => 'TM2594429513BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13l()
    {
        $codes =  [
            ['tracking' => 'NC620167099BR', 'warehouse' => 'TM2592830729BR'],
            ['tracking' => 'NC620167111BR', 'warehouse' => 'TM2593631245BR'],
            ['tracking' => 'NC620167156BR', 'warehouse' => 'TM2591333559BR'],
        ];
        return $this->updateTracking($codes);
    }

    function container13m()
    {
        $codes =  [
            ['tracking' => 'NC620167655BR', 'warehouse' => 'TM2591648047BR'],
            ['tracking' => 'NC620168559BR', 'warehouse' => 'TM2594168947BR'],
            ['tracking' => 'NC620169639BR', 'warehouse' => 'HD2591495854BR'],
        ];
        return $this->updateTracking($codes);
    }

    function updateTracking($codes)
    {
        set_time_limit(400);
        //update standard 
        try {
            foreach ($codes as $code) {

                $order = Order::where('warehouse_number',  $code['warehouse'])->first();

                if ($code['tracking'] == $order->corrios_tracking_code) {

                    if (Order::where('warehouse_number', $code['warehouse'])
                        ->where('corrios_tracking_code', 'like', 'IX%')
                        ->update(['shipping_service_id' => 42])
                    ) {
                        $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
                        $corrieosBrazilLabelRepository->run($order, true);
                    }
                    if (Order::where('warehouse_number', $code['warehouse'])
                        ->where('corrios_tracking_code', 'like', 'NC%')
                        ->update(['shipping_service_id' => 16])
                    ) {
                        $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
                        $corrieosBrazilLabelRepository->run($order, true);
                    }
                }
            }
            dd('done');
        } catch (Exception $e) {
            dd(['error' => $e->getMessage()]);
        }
    }
}
